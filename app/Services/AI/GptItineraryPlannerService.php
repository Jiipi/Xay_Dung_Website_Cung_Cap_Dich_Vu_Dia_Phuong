<?php

namespace App\Services\AI;

use App\Models\DanhGia;
use App\Models\User;
use App\Repositories\Contracts\Service\ServiceRepositoryInterface;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class GptItineraryPlannerService
{
    public function __construct(
        protected ServiceRepositoryInterface $serviceRepository
    ) {}

    public function generatePlan(array $input, ?User $user = null): array
    {
        $input = $this->normalizeInput($input);
        $context = $this->buildPlannerContext($input);

        if ($this->isConfigured()) {
            try {
                $plan = $this->requestPlanFromOpenAi($input, $context, $user);

                return $this->normalizePlan($plan, $input, $context, $this->configuredProvider());
            } catch (Throwable $exception) {
                Log::warning('AI planner fell back to local generation.', [
                    'message' => $exception->getMessage(),
                ]);
            }
        }

        return $this->buildFallbackPlan(
            $input,
            $context,
            $this->isConfigured()
                ? 'AI tạm thời bận, hệ thống đã chuyển sang gợi ý nội bộ.'
                : 'AI chưa được cấu hình, hệ thống đang dùng gợi ý nội bộ.'
        );
    }

    public function chat(array $input, ?User $user = null): array
    {
        $input = $this->normalizeInput($input);
        $context = $this->buildPlannerContext($input);

        if ($this->isConfigured()) {
            try {
                $chat = $this->requestChatFromOpenAi($input, $context, $user);

                return $this->normalizeChat($chat, $input, $context, $this->configuredProvider());
            } catch (Throwable $exception) {
                Log::warning('AI planner chat fell back to local reply.', [
                    'message' => $exception->getMessage(),
                ]);
            }
        }

        return $this->buildFallbackChat(
            $input,
            $context,
            $this->isConfigured()
                ? 'AI tạm thời bận nên mình trả lời bằng bộ quy tắc nội bộ.'
                : 'AI chưa được cấu hình nên mình trả lời bằng bộ quy tắc nội bộ.'
        );
    }

    protected function buildPlannerContext(array $input): array
    {
        $servicesRaw = $this->serviceRepository->getActivePublicServices();
        $providerIds = $servicesRaw->pluck('nha_cung_cap_id')->filter()->unique()->values();
        $reviewCounts = DanhGia::whereIn('nha_cung_cap_id', $providerIds)
            ->selectRaw('nha_cung_cap_id, count(*) as total')
            ->groupBy('nha_cung_cap_id')
            ->pluck('total', 'nha_cung_cap_id');

        $preferences = collect($input['preferences'] ?? [])
            ->filter(fn ($value) => is_string($value) && $value !== '')
            ->values()
            ->all();

        $services = $servicesRaw->map(function ($service) use ($reviewCounts) {
            $profile = $service->nhaCungCap?->hoSoNhaCungCap;
            $categorySlug = $service->danhMuc?->parent?->slug ?? $service->danhMuc?->slug ?? 'khac';
            $categoryName = $service->danhMuc?->parent?->ten_danh_muc ?? $service->danhMuc?->ten_danh_muc ?? 'Khac';
            $keywords = collect($service->the_tu_khoa ?? [])
                ->filter(fn ($keyword) => is_string($keyword) && $keyword !== '')
                ->values()
                ->all();

            return [
                'id' => $service->id,
                'title' => $service->ten_dich_vu,
                'provider' => $profile?->ten_thuong_hieu ?? $service->nhaCungCap?->ho_ten ?? 'Nha cung cap',
                'rating' => round((float) ($profile?->diem_danh_gia ?? 0), 1),
                'reviews' => (int) ($reviewCounts[$service->nha_cung_cap_id] ?? 0),
                'price' => (int) round((float) $service->gia_tu),
                'price_to' => $service->gia_den ? (int) round((float) $service->gia_den) : null,
                'category' => $categorySlug,
                'category_name' => $categoryName,
                'location' => trim((string) ($service->dia_chi_hien_thi ?? '')),
                'image' => (is_array($service->danh_sach_anh) && count($service->danh_sach_anh) > 0)
                    ? $service->danh_sach_anh[0]
                    : 'https://picsum.photos/seed/' . md5((string) $service->id) . '/400/250',
                'description' => Str::limit((string) ($service->mo_ta_chi_tiet ?? ''), 180),
                'keywords' => $keywords,
            ];
        })->values();

        $budget = max(0, (int) ($input['budget_amount'] ?? 0));
        $people = max(1, (int) ($input['num_people'] ?? 1));
        $days = $this->parseDays((string) ($input['duration'] ?? '1N'));
        $intent = $this->detectIntent($input);
        $keywords = $this->extractIntentKeywords($input, $intent);
        $recommended = $this->rankServices(
            $services->all(),
            (string) ($input['location'] ?? ''),
            $keywords,
            $budget,
            $people
        );

        return [
            'resolved_input' => $input,
            'intent' => $intent,
            'days' => $days,
            'budget' => $budget,
            'people' => $people,
            'preferences' => $preferences,
            'keywords' => $keywords,
            'recommended_services' => $recommended,
            'service_lookup' => collect($services)->keyBy('id')->all(),
            'highlights' => $this->getCityHighlights((string) ($input['location'] ?? '')),
        ];
    }

    protected function requestPlanFromOpenAi(array $input, array $context, ?User $user = null): array
    {
        $schema = [
            'type' => 'object',
            'additionalProperties' => false,
            'required' => ['title', 'subtitle', 'summary', 'totalBudget', 'budgetBreakdown', 'days', 'recommendedServiceIds', 'insights'],
            'properties' => [
                'title' => ['type' => 'string'],
                'subtitle' => ['type' => 'string'],
                'summary' => ['type' => 'string'],
                'totalBudget' => ['type' => 'integer'],
                'budgetBreakdown' => [
                    'type' => 'object',
                    'additionalProperties' => false,
                    'required' => ['accommodation', 'transport', 'sightseeing', 'food', 'misc'],
                    'properties' => [
                        'accommodation' => ['type' => 'integer'],
                        'transport' => ['type' => 'integer'],
                        'sightseeing' => ['type' => 'integer'],
                        'food' => ['type' => 'integer'],
                        'misc' => ['type' => 'integer'],
                    ],
                ],
                'days' => [
                    'type' => 'array',
                    'minItems' => 1,
                    'maxItems' => max(1, (int) $context['days']),
                    'items' => [
                        'type' => 'object',
                        'additionalProperties' => false,
                        'required' => ['day', 'title', 'activities'],
                        'properties' => [
                            'day' => ['type' => 'integer'],
                            'title' => ['type' => 'string'],
                            'activities' => [
                                'type' => 'array',
                                'minItems' => 2,
                                'maxItems' => 6,
                                'items' => [
                                    'type' => 'object',
                                    'additionalProperties' => false,
                                    'required' => ['time', 'name', 'desc', 'costEstimate', 'icon', 'serviceId'],
                                    'properties' => [
                                        'time' => ['type' => 'string'],
                                        'name' => ['type' => 'string'],
                                        'desc' => ['type' => 'string'],
                                        'costEstimate' => ['type' => 'integer'],
                                        'icon' => ['type' => 'string'],
                                        'serviceId' => ['type' => ['integer', 'null']],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'recommendedServiceIds' => [
                    'type' => 'array',
                    'minItems' => 0,
                    'maxItems' => 6,
                    'items' => ['type' => 'integer'],
                ],
                'insights' => [
                    'type' => 'array',
                    'minItems' => 2,
                    'maxItems' => 4,
                    'items' => ['type' => 'string'],
                ],
            ],
        ];

        $serviceLines = collect($context['recommended_services'])
            ->take(8)
            ->map(function (array $service) {
                return sprintf(
                    '#%d | %s | %s | %s | %s | gia tu %s | rating %.1f | reviews %d',
                    $service['id'],
                    $service['title'],
                    $service['category_name'],
                    $service['provider'],
                    $this->shortLocation($service['location']),
                    number_format($service['price'], 0, ',', '.'),
                    $service['rating'],
                    $service['reviews']
                );
            })
            ->implode("\n");

        $system = "Bạn là trợ lý AI du lịch và gợi ý dịch vụ địa phương cho người dùng Việt Nam.\n"
            . "Hãy trả về JSON hợp lệ, không thêm markdown, không giải thích ngoài schema, luôn dùng tiếng Việt có dấu.\n"
            . "Nếu nhu cầu nghiêng về sửa chữa/gọi thợ/yêu cầu gấp, hãy tạo kế hoạch xử lý trong 1 ngày.\n"
            . "Chỉ được đề xuất serviceId nằm trong danh sách đã cung cấp.\n"
            . "Văn phong rõ ràng, thực tế, tối ưu ngân sách.";

        $prompt = "Thông tin người dùng:\n"
            . "- Tên: " . ($user?->ho_ten ?? 'Khách') . "\n"
            . "- Nhu cầu của khách hàng (QUAN TRỌNG NHẤT): " . trim((string) ($input['prompt'] ?? '')) . "\n";

        if (!empty($input['location'])) {
            $prompt .= "- Địa điểm đang chọn: " . $input['location'] . "\n";
        }
        if (!empty($input['duration'])) {
            $prompt .= "- Thời gian đang chọn: " . $input['duration'] . " (" . $context['days'] . " ngày)\n";
        }
        if (!empty($context['budget']) && $context['budget'] > 0) {
            $prompt .= "- Ngân sách tổng: " . number_format((int) $context['budget'], 0, ',', '.') . " VND\n";
        }

        $prompt .= "- Số người: " . $context['people'] . "\n";

        if (!empty($context['preferences'])) {
            $prompt .= "- Sở thích: " . implode(', ', $context['preferences']) . "\n";
        }

        $prompt .= "- Kiểu nhu cầu: " . $context['intent'] . "\n\n"
            . "Dịch vụ có sẵn ưu tiên đề xuất:\n"
            . ($serviceLines !== '' ? $serviceLines : "- Chưa có dịch vụ phù hợp rõ ràng") . "\n\n"
            . "YÊU CẦU TUYỆT ĐỐI QUAN TRỌNG: Bạn phải đọc và bám sát phần 'Nhu cầu của khách hàng' để hiểu họ đang muốn gì và muốn xử lý vấn đề ở đâu. Các thông tin như Địa điểm/Thời gian/Ngân sách chỉ mang tính bổ trợ nếu họ có nhập; nếu để trống thì tự quyết định theo nhu cầu thực tế. Tạo lịch trình hoặc gợi ý thông minh nhất dựa vào đó.";

        $payload = [
            'systemInstruction' => [
                'parts' => [
                    ['text' => $system],
                ],
            ],
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [
                        ['text' => $prompt],
                    ],
                ],
            ],
            'generationConfig' => [
                'responseMimeType' => 'application/json',
                'responseJsonSchema' => $schema,
                'temperature' => 0.7,
                'topP' => 0.9,
                'maxOutputTokens' => 2048,
            ],
        ];

        return $this->callOpenAiPlanner($payload);
    }

    protected function requestChatFromOpenAi(array $input, array $context, ?User $user = null): array
    {
        $schema = [
            'type' => 'object',
            'additionalProperties' => false,
            'required' => ['reply', 'fieldUpdates', 'suggestedServiceIds', 'quickActions'],
            'properties' => [
                'reply' => ['type' => 'string'],
                'fieldUpdates' => [
                    'type' => 'object',
                    'additionalProperties' => false,
                    'required' => ['location', 'duration', 'budgetAmount', 'numPeople', 'prompt'],
                    'properties' => [
                        'location' => ['type' => ['string', 'null']],
                        'duration' => ['type' => ['string', 'null']],
                        'budgetAmount' => ['type' => ['integer', 'null']],
                        'numPeople' => ['type' => ['integer', 'null']],
                        'prompt' => ['type' => ['string', 'null']],
                    ],
                ],
                'suggestedServiceIds' => [
                    'type' => 'array',
                    'maxItems' => 3,
                    'items' => ['type' => 'integer'],
                ],
                'quickActions' => [
                    'type' => 'array',
                    'maxItems' => 3,
                    'items' => ['type' => 'string'],
                ],
            ],
        ];

        $serviceLines = collect($context['recommended_services'])
            ->take(5)
            ->map(fn (array $service) => sprintf(
                '#%d %s | %s | %s | %.1f sao | %s',
                $service['id'],
                $service['title'],
                $service['provider'],
                $service['category_name'],
                $service['rating'],
                number_format($service['price'], 0, ',', '.')
            ))
            ->implode("\n");

        $system = "Bạn là trợ lý hỏi đáp cho AI Planner. Trả về JSON hợp lệ, gọn, thân thiện và bằng tiếng Việt có dấu.\n"
            . "Nếu người dùng đề cập địa điểm/ngân sách/thời gian/số người mới thì đưa vào fieldUpdates.\n"
            . "Chỉ đề xuất serviceId nằm trong danh sách cho sẵn.\n"
            . "Nếu cần, hướng người dùng bấm nút tạo lịch trình.";

        $prompt = "Trạng thái hiện tại:\n"
            . "- Người dùng: " . ($user?->ho_ten ?? 'Khách') . "\n"
            . "- Nhu cầu chính của họ trước đó: " . ($input['prompt'] ?? '') . "\n";

        if (!empty($input['location'])) {
            $prompt .= "- Địa điểm: " . $input['location'] . "\n";
        }
        if (!empty($input['duration'])) {
            $prompt .= "- Thời gian: " . $input['duration'] . "\n";
        }
        if (!empty($context['budget']) && $context['budget'] > 0) {
            $prompt .= "- Ngân sách: " . number_format((int) $context['budget'], 0, ',', '.') . " VND\n";
        }

        $prompt .= "- Số người: " . $context['people'] . "\n";
        if (!empty($context['preferences'])) {
            $prompt .= "- Sở thích: " . implode(', ', $context['preferences']) . "\n";
        }

        $prompt .= "- Danh sách dịch vụ có thể đề xuất:\n"
            . ($serviceLines !== '' ? $serviceLines : "- Chưa có dịch vụ nổi bật") . "\n\n"
            . "YÊU CẦU: Bạn phải bám sát vào tin nhắn mới nhất và nhu cầu chính của khách. Các field khác chỉ là phụ.\n\n"
            . "Tin nhắn mới của người dùng: " . trim((string) ($input['message'] ?? ''));

        $payload = [
            'systemInstruction' => [
                'parts' => [
                    ['text' => $system],
                ],
            ],
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [
                        ['text' => $prompt],
                    ],
                ],
            ],
            'generationConfig' => [
                'responseMimeType' => 'application/json',
                'responseJsonSchema' => $schema,
                'temperature' => 0.8,
                'topP' => 0.95,
                'maxOutputTokens' => 1024,
            ],
        ];

        return $this->callOpenAiPlanner($payload);
    }

    protected function callOpenAiPlanner(array $payload): array
    {
        if (! filled(config('services.openai.key'))) {
            throw new \RuntimeException('OpenAI API key chưa được cấu hình.');
        }

        return $this->callOpenAi($payload);
    }

    protected function callOpenAi(array $payload): array
    {
        $model = config('services.openai.model', 'gpt-4o-mini');
        $key = config('services.openai.key');
        $timeout = (int) config('services.openai.timeout', 20);
        $system = data_get($payload, 'systemInstruction.parts.0.text', 'Trả về JSON hợp lệ bằng tiếng Việt có dấu.');
        $prompt = data_get($payload, 'contents.0.parts.0.text', '');
        $schema = data_get($payload, 'generationConfig.responseJsonSchema');

        $body = [
            'model' => $model,
            'messages' => [
                ['role' => 'system', 'content' => $system],
                ['role' => 'user', 'content' => $prompt],
            ],
            'temperature' => (float) data_get($payload, 'generationConfig.temperature', 0.7),
            'max_tokens' => (int) data_get($payload, 'generationConfig.maxOutputTokens', 2048),
        ];

        if (is_array($schema)) {
            $body['response_format'] = [
                'type' => 'json_schema',
                'json_schema' => [
                    'name' => 'ai_planner_response',
                    'strict' => true,
                    'schema' => $schema,
                ],
            ];
        } else {
            $body['response_format'] = ['type' => 'json_object'];
        }

        $response = Http::timeout($timeout)
            ->withToken($key)
            ->acceptJson()
            ->asJson()
            ->post('https://api.openai.com/v1/chat/completions', $body);

        if (! $response->successful()) {
            $error = $response->json('error.message') ?? Str::limit($response->body(), 240);

            throw new \RuntimeException(sprintf('OpenAI request failed with status %s: %s', $response->status(), $error));
        }

        $text = data_get($response->json(), 'choices.0.message.content');

        if (! is_string($text) || trim($text) === '') {
            throw new \RuntimeException('OpenAI response was empty.');
        }

        $decoded = json_decode(trim($text), true);

        if (! is_array($decoded)) {
            throw new \RuntimeException('OpenAI JSON could not be decoded.');
        }

        return $decoded;
    }

    protected function buildFallbackPlan(array $input, array $context, string $note): array
    {
        $recommended = collect($context['recommended_services'])->take(6)->values()->all();
        $days = $context['days'];
        $highlights = $context['highlights'];
        $isServiceRequest = $context['intent'] === 'service';
        $budget = max(0, (int) $context['budget']);

        if ($isServiceRequest) {
            $primary = $recommended[0] ?? null;
            $daysData = [[
                'day' => 1,
                'title' => 'Xử lý nhu cầu ưu tiên',
                'activities' => array_values(array_filter([
                    [
                        'time' => '08:00',
                        'name' => 'Xác nhận nhu cầu',
                        'desc' => 'Chốt vấn đề cần xử lý, mức độ gấp và địa chỉ thực hiện.',
                        'cost' => 0,
                        'icon' => 'mappin',
                        'serviceId' => null,
                    ],
                    $primary ? [
                        'time' => '09:00',
                        'name' => $primary['title'],
                        'desc' => sprintf(
                            '%s - %s, đánh giá %.1f sao. Phù hợp để liên hệ đặt lịch nhanh.',
                            $primary['provider'],
                            $primary['category_name'],
                            $primary['rating']
                        ),
                        'cost' => $primary['price'],
                        'icon' => $this->iconForService($primary),
                        'serviceId' => $primary['id'],
                    ] : null,
                    [
                        'time' => '11:00',
                        'name' => 'Xác nhận báo giá',
                        'desc' => 'So sánh phạm vi công việc, thời gian đến và chi phí phát sinh nếu có.',
                        'cost' => max(100000, (int) round($budget * 0.08)),
                        'icon' => 'car',
                        'serviceId' => null,
                    ],
                    [
                        'time' => '14:00',
                        'name' => 'Theo dõi và nghiệm thu',
                        'desc' => 'Kiểm tra kết quả, lưu thông tin nhà cung cấp uy tín cho lần sau.',
                        'cost' => max(0, (int) round($budget * 0.05)),
                        'icon' => 'coffee',
                        'serviceId' => null,
                    ],
                ])),
            ]];
        } else {
            $daysData = [];
            $servicePool = collect($recommended)->values();

            for ($index = 0; $index < $days; $index++) {
                $morning = $highlights['landmarks'][$index % max(1, count($highlights['landmarks']))];
                $afternoon = $highlights['landmarks'][($index + 1) % max(1, count($highlights['landmarks']))];
                $food = $highlights['food'][$index % max(1, count($highlights['food']))];
                $service = $servicePool->get($index) ?? $servicePool->get($index % max(1, $servicePool->count()));

                $activities = [];

                if ($index === 0) {
                    $activities[] = [
                        'time' => '08:30',
                        'name' => $highlights['hotel']['name'],
                        'desc' => $highlights['hotel']['desc'],
                        'cost' => (int) round($budget * 0.2 / max(1, $days)),
                        'icon' => 'bed',
                        'serviceId' => null,
                    ];
                }

                if ($service) {
                    $activities[] = [
                        'time' => $index === 0 ? '10:00' : '09:00',
                        'name' => $service['title'],
                        'desc' => sprintf(
                            '%s - %s. Khớp nhu cầu "%s" và ưu tiên khu vực %s.',
                            $service['provider'],
                            $service['category_name'],
                            trim((string) ($input['prompt'] ?? 'nhu cầu hiện tại')),
                            $this->shortLocation($service['location'])
                        ),
                        'cost' => $service['price'],
                        'icon' => $this->iconForService($service),
                        'serviceId' => $service['id'],
                    ];
                } else {
                    $activities[] = [
                        'time' => $index === 0 ? '10:00' : '09:00',
                        'name' => $morning['name'],
                        'desc' => $morning['desc'],
                        'cost' => $morning['cost'],
                        'icon' => 'mappin',
                        'serviceId' => null,
                    ];
                }

                $activities[] = [
                    'time' => '12:00',
                    'name' => $food['name'],
                    'desc' => $food['desc'],
                    'cost' => $food['cost'],
                    'icon' => $food['icon'],
                    'serviceId' => null,
                ];

                if ($servicePool->count() > 1) {
                    $secondaryService = $servicePool->get($index + 1);
                    if ($secondaryService) {
                        $activities[] = [
                            'time' => '13:30',
                            'name' => $secondaryService['title'],
                            'desc' => sprintf(
                                '%s - %s, đánh giá %.1f sao. Gợi ý bổ sung để tối ưu hành trình.',
                                $secondaryService['provider'],
                                $secondaryService['category_name'],
                                $secondaryService['rating']
                            ),
                            'cost' => $secondaryService['price'],
                            'icon' => $this->iconForService($secondaryService),
                            'serviceId' => $secondaryService['id'],
                        ];
                    }
                }

                $activities[] = [
                    'time' => '16:00',
                    'name' => $afternoon['name'],
                    'desc' => $afternoon['desc'],
                    'cost' => $afternoon['cost'],
                    'icon' => 'mappin',
                    'serviceId' => null,
                ];

                $daysData[] = [
                    'day' => $index + 1,
                    'title' => $service ? 'Dịch vụ thật + trải nghiệm địa phương' : $this->dayTitle($index),
                    'activities' => $activities,
                ];
            }
        }

        $plan = [
            'title' => sprintf('Lịch trình %s tại %s', $input['duration'], $input['location']),
            'subtitle' => sprintf(
                'Dành cho %d người - ưu tiên %s',
                max(1, (int) $input['num_people']),
                $this->preferenceSummary($context['preferences'])
            ),
            'summary' => $note,
            'totalBudget' => $budget,
            'budgetBreakdown' => $this->buildBudgetBreakdown($budget, $context['intent']),
            'days' => $daysData,
            'recommendedServiceIds' => array_values(array_map(
                fn (array $service) => $service['id'],
                array_slice($recommended, 0, 3)
            )),
            'insights' => $this->buildInsights($input, $context, $recommended),
        ];

        return $this->normalizePlan($plan, $input, $context, 'fallback');
    }

    protected function buildFallbackChat(array $input, array $context, string $note): array
    {
        $message = Str::lower(Str::ascii((string) ($input['message'] ?? '')));
        $recommended = collect($context['recommended_services'])->take(3)->values()->all();
        $reply = $note . "\n\n";
        $fieldUpdates = [
            'location' => null,
            'duration' => null,
            'budgetAmount' => null,
            'numPeople' => null,
            'prompt' => null,
        ];

        foreach (['da lat' => 'Da Lat', 'ha noi' => 'Ha Noi', 'da nang' => 'Da Nang', 'ho chi minh' => 'Ho Chi Minh', 'sai gon' => 'Ho Chi Minh', 'phu quoc' => 'Phu Quoc', 'vung tau' => 'Vung Tau'] as $needle => $label) {
            if (Str::contains($message, $needle)) {
                $fieldUpdates['location'] = $label;
                $reply .= "Minh da nhan ra ban dang muon doi dia diem sang {$label}. ";
                break;
            }
        }

        if (preg_match('/(\d+)\s*(n|ngay)/', $message, $matches)) {
            $fieldUpdates['duration'] = ((int) $matches[1]) . 'N';
        }

        if (preg_match('/(\d+)\s*(nguoi|ng)/', $message, $matches)) {
            $fieldUpdates['numPeople'] = max(1, min(20, (int) $matches[1]));
        }

        if (Str::contains($message, ['re', 'gia re', 'tiet kiem', 'budget'])) {
            $reply .= "Muc uu tien cua ban dang nghieng ve phuong an tiet kiem. ";
        }

        if (count($recommended) > 0) {
            $reply .= "Minh thay co " . count($recommended) . " dich vu dang kha hop luc nay:\n";
            foreach ($recommended as $service) {
                $reply .= sprintf(
                    "- %s (%s, %.1f sao, tu %s)\n",
                    $service['title'],
                    $service['provider'],
                    $service['rating'],
                    number_format($service['price'], 0, ',', '.') . 'd'
                );
            }
            $reply .= 'Ban co the bam "Tao lich trinh bang AI" de nhan ke hoach chi tiet hon.';
        } else {
            $reply .= 'Minh chua thay service that su sat nhu cau, nhung ban van co the bam nut tao lich trinh de he thong tu to chuc ke hoach tong quat.';
        }

        return $this->normalizeChat([
            'reply' => trim($reply),
            'fieldUpdates' => $fieldUpdates,
            'suggestedServiceIds' => array_values(array_map(fn (array $service) => $service['id'], $recommended)),
            'quickActions' => [
                'Tang hoac giam ngan sach',
                'Doi dia diem',
                'Tao lich trinh chi tiet',
            ],
        ], $input, $context, 'fallback');
    }

    protected function normalizePlan(array $plan, array $input, array $context, string $source): array
    {
        $lookup = $context['service_lookup'];
        $recommendedIds = collect($plan['recommendedServiceIds'] ?? [])
            ->filter(fn ($id) => is_numeric($id) && isset($lookup[(int) $id]))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if ($recommendedIds->isEmpty()) {
            $recommendedIds = collect($context['recommended_services'])
                ->take(3)
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->values();
        }

        $days = collect($plan['days'] ?? [])
            ->filter(fn ($day) => is_array($day))
            ->values()
            ->map(function (array $day, int $index) use ($lookup) {
                $activities = collect($day['activities'] ?? [])
                    ->filter(fn ($activity) => is_array($activity))
                    ->values()
                    ->map(function (array $activity) use ($lookup) {
                        $serviceId = Arr::get($activity, 'serviceId');
                        $serviceId = is_numeric($serviceId) && isset($lookup[(int) $serviceId]) ? (int) $serviceId : null;

                        return [
                            'time' => (string) ($activity['time'] ?? '09:00'),
                            'name' => (string) ($activity['name'] ?? 'Hoat dong'),
                            'desc' => (string) ($activity['desc'] ?? 'Dang cap nhat mo ta.'),
                            'cost' => max(0, (int) ($activity['costEstimate'] ?? $activity['cost'] ?? 0)),
                            'icon' => $this->normalizeIcon((string) ($activity['icon'] ?? 'mappin')),
                            'serviceId' => $serviceId,
                        ];
                    })
                    ->take(6)
                    ->values()
                    ->all();

                if (count($activities) === 0) {
                    $activities[] = [
                        'time' => '09:00',
                        'name' => 'Kham pha tu do',
                        'desc' => 'He thong dang bo sung chi tiet cho buoi nay.',
                        'cost' => 0,
                        'icon' => 'mappin',
                        'serviceId' => null,
                    ];
                }

                return [
                    'day' => max(1, (int) ($day['day'] ?? ($index + 1))),
                    'title' => (string) ($day['title'] ?? $this->dayTitle($index)),
                    'activities' => $activities,
                ];
            })
            ->take(max(1, (int) $context['days']))
            ->values()
            ->all();

        if (count($days) === 0) {
            $days = [[
                'day' => 1,
                'title' => 'Khoi dong hanh trinh',
                'activities' => [[
                    'time' => '09:00',
                    'name' => 'Cap nhat lai ke hoach',
                    'desc' => 'He thong dang dung lich trinh toi gian de tranh mat du lieu.',
                    'cost' => 0,
                    'icon' => 'mappin',
                    'serviceId' => null,
                ]],
            ]];
        }

        $budget = $this->normalizeBudgetBreakdown(
            (array) ($plan['budgetBreakdown'] ?? []),
            max(0, (int) ($plan['totalBudget'] ?? $context['budget']))
        );

        $recommendedServices = $recommendedIds
            ->map(fn ($id) => $lookup[$id])
            ->values()
            ->all();

        return [
            'source' => $source,
            'resolvedInput' => [
                'prompt' => (string) ($context['resolved_input']['prompt'] ?? $input['prompt'] ?? ''),
                'location' => (string) ($context['resolved_input']['location'] ?? $input['location'] ?? ''),
                'duration' => (string) ($context['resolved_input']['duration'] ?? $input['duration'] ?? ''),
                'budgetAmount' => max(0, (int) ($context['resolved_input']['budget_amount'] ?? $input['budget_amount'] ?? 0)),
                'numPeople' => max(1, (int) ($context['resolved_input']['num_people'] ?? $input['num_people'] ?? 1)),
                'preferences' => array_values($context['resolved_input']['preferences'] ?? $input['preferences'] ?? []),
            ],
            'result' => [
                'title' => (string) ($plan['title'] ?? sprintf('Lich trinh %s tai %s', $input['duration'], $input['location'])),
                'subtitle' => (string) ($plan['subtitle'] ?? sprintf('Danh cho %d nguoi', max(1, (int) $input['num_people']))),
                'summary' => (string) ($plan['summary'] ?? 'Da phan tich tu nhu cau hien tai va dich vu dang co tren he thong.'),
                'totalBudget' => max(0, (int) ($plan['totalBudget'] ?? $context['budget'])),
                'budgetBreakdown' => $budget,
                'days' => $days,
                'recommendedServices' => $recommendedServices,
                'insights' => collect($plan['insights'] ?? [])
                    ->filter(fn ($value) => is_string($value) && trim($value) !== '')
                    ->take(4)
                    ->values()
                    ->all(),
            ],
        ];
    }

    protected function normalizeChat(array $chat, array $input, array $context, string $source): array
    {
        $lookup = $context['service_lookup'];
        $suggestedIds = collect($chat['suggestedServiceIds'] ?? [])
            ->filter(fn ($id) => is_numeric($id) && isset($lookup[(int) $id]))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        return [
            'source' => $source,
            'reply' => (string) ($chat['reply'] ?? 'Minh da nhan thong tin. Ban thu bam tao lich trinh de minh len ke hoach cu the hon nhe.'),
            'resolvedInput' => [
                'prompt' => (string) ($context['resolved_input']['prompt'] ?? $input['prompt'] ?? ''),
                'location' => (string) ($context['resolved_input']['location'] ?? $input['location'] ?? ''),
                'duration' => (string) ($context['resolved_input']['duration'] ?? $input['duration'] ?? ''),
                'budgetAmount' => max(0, (int) ($context['resolved_input']['budget_amount'] ?? $input['budget_amount'] ?? 0)),
                'numPeople' => max(1, (int) ($context['resolved_input']['num_people'] ?? $input['num_people'] ?? 1)),
                'preferences' => array_values($context['resolved_input']['preferences'] ?? $input['preferences'] ?? []),
            ],
            'fieldUpdates' => [
                'location' => $this->nullableString(data_get($chat, 'fieldUpdates.location')),
                'duration' => $this->nullableString(data_get($chat, 'fieldUpdates.duration')),
                'budgetAmount' => $this->nullableInt(data_get($chat, 'fieldUpdates.budgetAmount')),
                'numPeople' => $this->nullableInt(data_get($chat, 'fieldUpdates.numPeople')),
                'prompt' => $this->nullableString(data_get($chat, 'fieldUpdates.prompt')),
            ],
            'suggestedServiceIds' => $suggestedIds,
            'suggestedServices' => collect($suggestedIds)->map(fn ($id) => $lookup[$id])->values()->all(),
            'quickActions' => collect($chat['quickActions'] ?? [])
                ->filter(fn ($value) => is_string($value) && trim($value) !== '')
                ->take(3)
                ->values()
                ->all(),
        ];
    }

    protected function rankServices(array $services, string $location, array $keywords, int $budget, int $people): array
    {
        $locationNeedle = Str::lower(Str::ascii($location));
        $budgetPerPerson = $people > 0 ? (int) floor($budget / $people) : $budget;
        $keywords = array_values(array_unique(array_filter($keywords)));

        return collect($services)
            ->map(function (array $service) use ($locationNeedle, $keywords, $budgetPerPerson) {
                $haystack = Str::lower(Str::ascii(implode(' ', array_filter([
                    $service['title'],
                    $service['provider'],
                    $service['category_name'],
                    $service['location'],
                    $service['description'],
                    implode(' ', $service['keywords']),
                ]))));

                $score = 0;

                if ($locationNeedle !== '' && Str::contains($haystack, $locationNeedle)) {
                    $score += 8;
                }

                foreach ($keywords as $keyword) {
                    if (Str::contains($haystack, $keyword)) {
                        $score += 3;
                    }
                }

                if ($budgetPerPerson > 0 && $service['price'] <= max($budgetPerPerson, 300000)) {
                    $score += 4;
                }

                if ($service['rating'] > 0) {
                    $score += (int) round($service['rating'] * 2);
                }

                $score += min(5, (int) floor($service['reviews'] / 5));
                $service['match_score'] = $score;

                return $service;
            })
            ->sortByDesc(fn (array $service) => [$service['match_score'], $service['rating'], -$service['price']])
            ->take(8)
            ->values()
            ->all();
    }

    protected function extractIntentKeywords(array $input, string $intent): array
    {
        $seed = collect([
            Str::ascii((string) ($input['prompt'] ?? '')),
            Str::ascii((string) ($input['location'] ?? '')),
            implode(' ', array_map(fn ($value) => Str::ascii((string) $value), $input['preferences'] ?? [])),
        ])->implode(' ');

        $tokens = collect(preg_split('/[^a-z0-9]+/i', Str::lower($seed)) ?: [])
            ->filter(fn ($token) => strlen($token) >= 3)
            ->take(16)
            ->values()
            ->all();

        $extra = match ($intent) {
            'service' => ['sua', 'tho', 'bao tri', 'lap dat', 've sinh', 'dieu hoa', 'may lanh', 'dien', 'nuoc'],
            default => ['du lich', 'tour', 'lich trinh', 'tham quan', 'am thuc', 'checkin', 'khach san', 'homestay'],
        };

        return array_values(array_unique(array_merge($tokens, $extra)));
    }

    protected function normalizeInput(array $input): array
    {
        $normalized = $input;
        $prompt = trim((string) ($input['prompt'] ?? ''));
        $asciiPrompt = Str::lower(Str::ascii($prompt));

        if ($prompt !== '') {
            $location = $this->extractLocationFromText($asciiPrompt);
            if ($location !== null) {
                $normalized['location'] = $location;
            }

            $duration = $this->extractDurationFromText($asciiPrompt);
            if ($duration !== null) {
                $normalized['duration'] = $duration;
            }

            $budget = $this->extractBudgetFromText($asciiPrompt);
            if ($budget !== null) {
                $normalized['budget_amount'] = $budget;
            }

            $people = $this->extractPeopleFromText($asciiPrompt);
            if ($people !== null) {
                $normalized['num_people'] = $people;
            }

            $promptPreferences = $this->extractPreferencesFromText($asciiPrompt);
            if (count($promptPreferences) > 0) {
                $current = collect($normalized['preferences'] ?? [])->filter()->values()->all();
                $normalized['preferences'] = array_values(array_unique(array_merge($current, $promptPreferences)));
            }
        }

        $normalized['prompt'] = $prompt;
        $normalized['location'] = trim((string) ($normalized['location'] ?? $input['location'] ?? ''));
        $normalized['duration'] = trim((string) ($normalized['duration'] ?? $input['duration'] ?? '1N'));
        $normalized['budget_amount'] = max(0, (int) ($normalized['budget_amount'] ?? $input['budget_amount'] ?? 0));
        $normalized['num_people'] = max(1, min(20, (int) ($normalized['num_people'] ?? $input['num_people'] ?? 1)));
        $normalized['preferences'] = collect($normalized['preferences'] ?? [])
            ->filter(fn ($value) => is_string($value) && $value !== '')
            ->values()
            ->all();

        return $normalized;
    }

    protected function detectIntent(array $input): string
    {
        $text = Str::lower(Str::ascii(
            trim((string) ($input['prompt'] ?? '')) . ' ' . trim((string) ($input['duration'] ?? ''))
        ));

        $serviceKeywords = ['sua', 'tho', 'bao tri', 'lap dat', 've sinh', 'dien', 'nuoc', 'dieu hoa', 'may lanh', 'gap'];
        $tripKeywords = ['du lich', 'tour', 'lich trinh', 'tham quan', 'nghi duong', 'khach san', 'homestay'];

        if (Str::contains($text, $serviceKeywords)) {
            return 'service';
        }

        if (Str::contains($text, $tripKeywords) || $this->parseDays((string) ($input['duration'] ?? '1N')) > 1) {
            return 'trip';
        }

        return 'trip';
    }

    protected function extractLocationFromText(string $text): ?string
    {
        foreach ([
            'da lat' => 'Đà Lạt',
            'dalat' => 'Đà Lạt',
            'ha noi' => 'Hà Nội',
            'da nang' => 'Đà Nẵng',
            'ho chi minh' => 'Hồ Chí Minh',
            'sai gon' => 'Hồ Chí Minh',
            'hcm' => 'Hồ Chí Minh',
            'phu quoc' => 'Phú Quốc',
            'vung tau' => 'Vũng Tàu',
        ] as $needle => $label) {
            if (Str::contains($text, $needle)) {
                return $label;
            }
        }

        return null;
    }

    protected function extractDurationFromText(string $text): ?string
    {
        if (preg_match('/(\d+)\s*ngay\s*(\d+)\s*dem/u', $text, $matches)) {
            $days = max(1, (int) $matches[1]);
            $nights = max(0, (int) $matches[2]);
            $calendarDays = max($days, $nights + 1);

            return $nights > 0 ? "{$calendarDays}N{$nights}Đ" : "{$calendarDays}N";
        }

        if (preg_match('/(\d+)\s*n\s*(\d+)\s*d/u', $text, $matches)) {
            return max(1, (int) $matches[1]) . 'N' . max(0, (int) $matches[2]) . 'Đ';
        }

        if (preg_match('/(\d+)\s*ngay/u', $text, $matches)) {
            return max(1, (int) $matches[1]) . 'N';
        }

        return null;
    }

    protected function extractBudgetFromText(string $text): ?int
    {
        if (preg_match('/(\d+(?:[.,]\d+)?)\s*(trieu|cu|k|nghin|ngan)/u', $text, $matches)) {
            $value = (float) str_replace(',', '.', $matches[1]);
            $unit = $matches[2];

            return match ($unit) {
                'trieu' => (int) round($value * 1000000),
                'k', 'nghin', 'ngan' => (int) round($value * 1000),
                default => (int) round($value),
            };
        }

        return null;
    }

    protected function extractPeopleFromText(string $text): ?int
    {
        if (preg_match('/(\d+)\s*(nguoi|khach|ban)/u', $text, $matches)) {
            return max(1, min(20, (int) $matches[1]));
        }

        return null;
    }

    protected function extractPreferencesFromText(string $text): array
    {
        $preferences = [];
        $map = [
            'nature' => ['thien nhien', 'doi', 'rung', 'nui', 'bien'],
            'food' => ['am thuc', 'an uong', 'mon ngon', 'dac san', 'an vat'],
            'adventure' => ['phieu luu', 'trek', 'kham pha', 'van dong'],
            'culture' => ['van hoa', 'bao tang', 'lich su', 'chua', 'dinh'],
            'relax' => ['thu gian', 'nghi duong', 'chill', 'yen tinh'],
            'photo' => ['checkin', 'song ao', 'chup anh', 'view dep'],
        ];

        foreach ($map as $pref => $needles) {
            if (Str::contains($text, $needles)) {
                $preferences[] = $pref;
            }
        }

        return $preferences;
    }

    protected function parseDays(string $duration): int
    {
        preg_match('/(\d+)/', $duration, $matches);

        return max(1, min(7, (int) ($matches[1] ?? 1)));
    }

    protected function buildBudgetBreakdown(int $budget, string $intent): array
    {
        if ($intent === 'service') {
            return $this->normalizeBudgetBreakdown([
                'accommodation' => 0,
                'transport' => (int) round($budget * 0.15),
                'sightseeing' => (int) round($budget * 0.45),
                'food' => (int) round($budget * 0.15),
                'misc' => (int) round($budget * 0.25),
            ], $budget);
        }

        return $this->normalizeBudgetBreakdown([
            'accommodation' => (int) round($budget * 0.3),
            'transport' => (int) round($budget * 0.15),
            'sightseeing' => (int) round($budget * 0.2),
            'food' => (int) round($budget * 0.25),
            'misc' => (int) round($budget * 0.1),
        ], $budget);
    }

    protected function normalizeBudgetBreakdown(array $budget, int $total): array
    {
        $keys = ['accommodation', 'transport', 'sightseeing', 'food', 'misc'];
        $normalized = [];
        $running = 0;

        foreach ($keys as $index => $key) {
            if ($index === count($keys) - 1) {
                $normalized[$key] = max(0, $total - $running);
                continue;
            }

            $value = max(0, (int) ($budget[$key] ?? 0));
            $normalized[$key] = $value;
            $running += $value;
        }

        return $normalized;
    }

    protected function buildInsights(array $input, array $context, array $recommended): array
    {
        $insights = [];
        $budgetPerPerson = max(0, (int) floor($context['budget'] / max(1, $context['people'])));

        $insights[] = 'Đã ưu tiên gợi ý dựa trên địa điểm, ngân sách và dữ liệu dịch vụ thật trên hệ thống.';
        $insights[] = sprintf(
            'Ngân sách hiện tại vào khoảng %s mỗi người.',
            number_format($budgetPerPerson, 0, ',', '.') . 'd'
        );

        if (count($recommended) > 0) {
            $best = $recommended[0];
            $insights[] = sprintf(
                'Dịch vụ nổi bật nhất hiện tại là %s từ %s.',
                $best['title'],
                $best['provider']
            );
        }

        if (! empty($context['preferences'])) {
            $insights[] = 'Sở thích đang được ưu tiên: ' . implode(', ', $context['preferences']) . '.';
        }

        return array_slice($insights, 0, 4);
    }

    protected function getCityHighlights(string $location): array
    {
        $normalized = Str::lower(Str::ascii($location));

        $presets = [
            'da lat' => [
                'landmarks' => [
                    ['name' => 'Quảng trường Lâm Viên', 'desc' => 'Bắt đầu nhẹ nhàng ở trung tâm thành phố.', 'cost' => 0],
                    ['name' => 'Thiền viện Trúc Lâm', 'desc' => 'Phù hợp cho buổi tham quan và nghỉ dưỡng.', 'cost' => 100000],
                    ['name' => 'Chợ đêm Đà Lạt', 'desc' => 'Tập trung ẩm thực và không khí địa phương.', 'cost' => 120000],
                ],
                'food' => [
                    ['name' => 'Lẩu gà lá é', 'desc' => 'Món đặc trưng hợp nhóm bạn hoặc gia đình.', 'cost' => 220000, 'icon' => 'food'],
                    ['name' => 'Cà phê view đồi thông', 'desc' => 'Phù hợp check-in và thư giãn.', 'cost' => 90000, 'icon' => 'coffee'],
                ],
                'hotel' => ['name' => 'Check-in homestay trung tâm', 'desc' => 'Chọn điểm ở để di chuyển dễ dàng.'],
            ],
            'ha noi' => [
                'landmarks' => [
                    ['name' => 'Ho Hoan Kiem', 'desc' => 'Diem khoi dau hop ly de kham pha khu trung tam.', 'cost' => 0],
                    ['name' => 'Pho co Ha Noi', 'desc' => 'Hop cho lich trinh di bo va an vat.', 'cost' => 100000],
                    ['name' => 'Hoang thanh Thang Long', 'desc' => 'Them chieu sau van hoa cho lich trinh.', 'cost' => 30000],
                ],
                'food' => [
                    ['name' => 'Pho bo Ha Noi', 'desc' => 'Lua chon an sang hoac bua chinh de dang.', 'cost' => 80000, 'icon' => 'food'],
                    ['name' => 'Ca phe trung', 'desc' => 'Mot diem dung ngan cho buoi chieu.', 'cost' => 50000, 'icon' => 'coffee'],
                ],
                'hotel' => ['name' => 'Nhan phong khu pho co', 'desc' => 'Tien cho di bo va di chuyen ngan.'],
            ],
            'da nang' => [
                'landmarks' => [
                    ['name' => 'Bien My Khe', 'desc' => 'Ly tuong cho buoi sang va van dong nhe.', 'cost' => 0],
                    ['name' => 'Ban dao Son Tra', 'desc' => 'Them canh quan thien nhien vao lich trinh.', 'cost' => 100000],
                    ['name' => 'Cau Rong ve dem', 'desc' => 'Ket thuc ngay bang trai nghiem thanh pho.', 'cost' => 0],
                ],
                'food' => [
                    ['name' => 'Mi Quang', 'desc' => 'Mon dia phuong de xep vao bua trua.', 'cost' => 70000, 'icon' => 'food'],
                    ['name' => 'Ca phe bien', 'desc' => 'Noi nghi chan hop cap doi va nhom nho.', 'cost' => 70000, 'icon' => 'coffee'],
                ],
                'hotel' => ['name' => 'Check-in khach san gan bien', 'desc' => 'Giu nhiet do lich trinh nhe va de di chuyen.'],
            ],
            'ho chi minh' => [
                'landmarks' => [
                    ['name' => 'Nha tho Duc Ba', 'desc' => 'Diem mo dau trung tam cho lich trinh thanh pho.', 'cost' => 0],
                    ['name' => 'Cho Ben Thanh', 'desc' => 'Vua tham quan vua thu am thuc dia phuong.', 'cost' => 150000],
                    ['name' => 'Pho di bo Nguyen Hue', 'desc' => 'Phu hop cho buoi toi va check-in.', 'cost' => 0],
                ],
                'food' => [
                    ['name' => 'Com tam Sai Gon', 'desc' => 'Bua an nhanh, de xep lich.', 'cost' => 80000, 'icon' => 'food'],
                    ['name' => 'Ca phe trung tam Q1', 'desc' => 'Them nhan nghi ngan o khu trung tam.', 'cost' => 70000, 'icon' => 'coffee'],
                ],
                'hotel' => ['name' => 'Nhan phong khu trung tam', 'desc' => 'Uu tien vi tri de ket noi nhieu diem.'],
            ],
            'phu quoc' => [
                'landmarks' => [
                    ['name' => 'Bai bien trung tam', 'desc' => 'Bat dau bang nhung diem bien de di chuyen.', 'cost' => 0],
                    ['name' => 'Cho dem Phu Quoc', 'desc' => 'Tap trung am thuc va mua sam buoi toi.', 'cost' => 150000],
                    ['name' => 'Diem ngam hoang hon', 'desc' => 'Hop lich trinh cap doi va gia dinh.', 'cost' => 100000],
                ],
                'food' => [
                    ['name' => 'Hai san tuoi', 'desc' => 'Nen xep cho bua toi hoac nhom dong.', 'cost' => 300000, 'icon' => 'food'],
                    ['name' => 'Ca phe view bien', 'desc' => 'Nghi nhe giua lich trinh.', 'cost' => 90000, 'icon' => 'coffee'],
                ],
                'hotel' => ['name' => 'Nhan phong gan bien', 'desc' => 'Toi uu trai nghiem nghi duong.'],
            ],
            'vung tau' => [
                'landmarks' => [
                    ['name' => 'Bai Sau', 'desc' => 'Diem khoi dong de thu gian va tam bien.', 'cost' => 0],
                    ['name' => 'Mui Nghinh Phong', 'desc' => 'Hop cho chup anh va di bo ngan.', 'cost' => 0],
                    ['name' => 'Cho hai san', 'desc' => 'Them trai nghiem am thuc dia phuong.', 'cost' => 150000],
                ],
                'food' => [
                    ['name' => 'Hai san ven bien', 'desc' => 'Hop cho nhom ban va gia dinh.', 'cost' => 250000, 'icon' => 'food'],
                    ['name' => 'Ca phe ven bien', 'desc' => 'Nghi chan nhanh buoi chieu.', 'cost' => 80000, 'icon' => 'coffee'],
                ],
                'hotel' => ['name' => 'Nhan phong gan Bai Sau', 'desc' => 'Di bo de hon trong ngay dau.'],
            ],
        ];

        foreach ($presets as $needle => $preset) {
            if (Str::contains($normalized, $needle)) {
                return $preset;
            }
        }

        return [
            'landmarks' => [
                ['name' => 'Diem noi bat khu vuc', 'desc' => 'Lua chon mot diem tham quan de bat dau nhe nhang.', 'cost' => 0],
                ['name' => 'Khu trung tam dia phuong', 'desc' => 'Phu hop de kham pha nhanh va linh hoat.', 'cost' => 100000],
            ],
            'food' => [
                ['name' => 'Mon dac san dia phuong', 'desc' => 'Nen dat cho bua an chinh.', 'cost' => 150000, 'icon' => 'food'],
                ['name' => 'Quan ca phe noi bat', 'desc' => 'Diem nghi ngan trong lich trinh.', 'cost' => 70000, 'icon' => 'coffee'],
            ],
            'hotel' => ['name' => 'Check-in khu trung tam', 'desc' => 'Uu tien diem o gan khu di chuyen chinh.'],
        ];
    }

    protected function dayTitle(int $index): string
    {
        return [
            'Kham pha nhung diem chinh',
            'Trai nghiem van hoa va am thuc',
            'Thu gian va dich vu noi bat',
            'Mua sam va ket noi dia phuong',
            'Tong hop va ket thuc hanh trinh',
        ][$index % 5];
    }

    protected function preferenceSummary(array $preferences): string
    {
        if (count($preferences) === 0) {
            return 'lich trinh can bang';
        }

        return implode(', ', array_slice($preferences, 0, 3));
    }

    protected function iconForService(array $service): string
    {
        $haystack = Str::lower(Str::ascii($service['category_name'] . ' ' . $service['title']));

        if (Str::contains($haystack, ['xe', 'car', 'di chuyen', 'tour'])) {
            return 'car';
        }

        if (Str::contains($haystack, ['khach san', 'hotel', 'homestay', 'phong'])) {
            return 'bed';
        }

        if (Str::contains($haystack, ['ca phe', 'coffee'])) {
            return 'coffee';
        }

        if (Str::contains($haystack, ['an', 'food', 'am thuc', 'nha hang'])) {
            return 'food';
        }

        return 'mappin';
    }

    protected function normalizeIcon(string $icon): string
    {
        return in_array($icon, ['bed', 'mappin', 'food', 'car', 'coffee'], true) ? $icon : 'mappin';
    }

    protected function shortLocation(string $location): string
    {
        $location = trim($location);
        if ($location === '') {
            return 'Không rõ khu vực';
        }

        $parts = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $location) ?: [])));

        return end($parts) ?: $location;
    }

    protected function nullableString(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $value = trim($value);

        return $value === '' ? null : $value;
    }

    protected function nullableInt(mixed $value): ?int
    {
        return is_numeric($value) ? (int) $value : null;
    }

    protected function isConfigured(): bool
    {
        return filled(config('services.openai.key'));
    }

    protected function configuredProvider(): string
    {
        return 'openai';
    }
}
