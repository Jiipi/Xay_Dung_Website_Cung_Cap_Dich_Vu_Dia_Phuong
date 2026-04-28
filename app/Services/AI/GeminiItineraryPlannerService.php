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

class GeminiItineraryPlannerService
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
                $plan = $this->requestPlanFromGemini($input, $context, $user);

                return $this->normalizePlan($plan, $input, $context, 'gemini');
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
                ? 'Gemini tam thoi ban, he thong da chuyen sang goi y noi bo.'
                : 'Gemini chua duoc cau hinh, he thong dang dung goi y noi bo.'
        );
    }

    public function chat(array $input, ?User $user = null): array
    {
        $input = $this->normalizeInput($input);
        $context = $this->buildPlannerContext($input);

        if ($this->isConfigured()) {
            try {
                $chat = $this->requestChatFromGemini($input, $context, $user);

                return $this->normalizeChat($chat, $input, $context, 'gemini');
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
                ? 'Gemini tam thoi ban nen minh tra loi bang bo quy tac noi bo.'
                : 'Gemini chua duoc cau hinh nen minh tra loi bang bo quy tac noi bo.'
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

    protected function requestPlanFromGemini(array $input, array $context, ?User $user = null): array
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

        $system = "Ban la tro ly AI du lich va goi y dich vu dia phuong cho nguoi dung Viet Nam.\n"
            . "Hay tra ve JSON hop le, khong them markdown, khong them giai thich ngoai schema.\n"
            . "Neu nhu cau nghieng ve sua chua/goi tho/yeu cau gap, hay tao action plan 1 ngay de xu ly cong viec.\n"
            . "Chi duoc de xuat serviceId nam trong danh sach da cung cap.\n"
            . "Van phong cach ro rang, thuc te, toi uu ngan sach.";

        $prompt = "Thong tin nguoi dung:\n"
            . "- Ten: " . ($user?->ho_ten ?? 'Khach') . "\n"
            . "- Nhu cau cua khach hang (QUAN TRONG NHAT): " . trim((string) ($input['prompt'] ?? '')) . "\n";

        if (!empty($input['location'])) {
            $prompt .= "- Dia diem dang chon: " . $input['location'] . "\n";
        }
        if (!empty($input['duration'])) {
            $prompt .= "- Thoi gian dang chon: " . $input['duration'] . " (" . $context['days'] . " ngay)\n";
        }
        if (!empty($context['budget']) && $context['budget'] > 0) {
            $prompt .= "- Ngan sach tong: " . number_format((int) $context['budget'], 0, ',', '.') . " VND\n";
        }

        $prompt .= "- So nguoi: " . $context['people'] . "\n";
        
        if (!empty($context['preferences'])) {
            $prompt .= "- So thich: " . implode(', ', $context['preferences']) . "\n";
        }
            
        $prompt .= "- Kieu nhu cau: " . $context['intent'] . "\n\n"
            . "Service co san uu tien de xuat:\n"
            . ($serviceLines !== '' ? $serviceLines : "- Chua co service phu hop ro rang") . "\n\n"
            . "YEU CAU TUYET DOI QUAN TRONG: Ban phai DOC VA BAM SAT VAO PHAN 'Nhu cau cua khach hang' (prompt) de hieu ho dang muon gi va muon xu ly van de o dau. Cac thong tin nhu Dia diem/Thoi gian/Ngan sach chi mang tinh chat BO TRO neu ho co nhap, neu ho de trong thi ban duoc tu quyet dinh dua vao nhu cau thuc te. Tao lich trinh hoac goi y thong minh nhat dua vao do.";

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

        return $this->callGemini($payload);
    }

    protected function requestChatFromGemini(array $input, array $context, ?User $user = null): array
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

        $system = "Ban la tro ly hoi dap cho AI Planner. Tra ve JSON hop le, gon, than thien va bang tieng Viet.\n"
            . "Neu nguoi dung de cap dia diem/ngan sach/thoi gian/so nguoi moi thi dua vao fieldUpdates.\n"
            . "Chi de xuat serviceId nam trong danh sach cho san.\n"
            . "Neu can, huong nguoi dung bam nut tao lich trinh.";

        $prompt = "Trang thai hien tai:\n"
            . "- Nguoi dung: " . ($user?->ho_ten ?? 'Khach') . "\n"
            . "- Nhu cau chinh cua ho truoc do: " . ($input['prompt'] ?? '') . "\n";

        if (!empty($input['location'])) {
            $prompt .= "- Dia diem: " . $input['location'] . "\n";
        }
        if (!empty($input['duration'])) {
            $prompt .= "- Thoi gian: " . $input['duration'] . "\n";
        }
        if (!empty($context['budget']) && $context['budget'] > 0) {
            $prompt .= "- Ngan sach: " . number_format((int) $context['budget'], 0, ',', '.') . " VND\n";
        }

        $prompt .= "- So nguoi: " . $context['people'] . "\n";
        if (!empty($context['preferences'])) {
            $prompt .= "- So thich: " . implode(', ', $context['preferences']) . "\n";
        }

        $prompt .= "- Danh sach service co the de xuat:\n"
            . ($serviceLines !== '' ? $serviceLines : "- Chua co service noi bat") . "\n\n"
            . "YEU CAU: Ban phai bám sát vào tin nhắn mới nhất và Nhu cầu chính của khách. Các field khác chỉ là phụ.\n\n"
            . "Tin nhan moi cua nguoi dung: " . trim((string) ($input['message'] ?? ''));

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

        return $this->callGemini($payload);
    }

    protected function callGemini(array $payload): array
    {
        $model = config('services.gemini.model', 'gemini-2.5-flash');
        $key = config('services.gemini.key');
        $timeout = (int) config('services.gemini.timeout', 20);
        $url = sprintf(
            'https://generativelanguage.googleapis.com/v1beta/models/%s:generateContent?key=%s',
            $model,
            $key
        );

        $response = Http::timeout($timeout)
            ->acceptJson()
            ->asJson()
            ->post($url, $payload);

        if (! $response->successful()) {
            throw new \RuntimeException('Gemini request failed with status ' . $response->status());
        }

        $text = data_get($response->json(), 'candidates.0.content.parts.0.text');

        if (! is_string($text) || trim($text) === '') {
            throw new \RuntimeException('Gemini response was empty.');
        }

        $text = trim($text);
        $text = preg_replace('/^```json\s*|\s*```$/', '', $text) ?? $text;
        $decoded = json_decode($text, true);

        if (! is_array($decoded)) {
            throw new \RuntimeException('Gemini JSON could not be decoded.');
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
                'title' => 'Xu ly nhu cau uu tien',
                'activities' => array_values(array_filter([
                    [
                        'time' => '08:00',
                        'name' => 'Xac nhan nhu cau',
                        'desc' => 'Chot van de can xu ly, muc do gap va dia chi thuc hien.',
                        'cost' => 0,
                        'icon' => 'mappin',
                        'serviceId' => null,
                    ],
                    $primary ? [
                        'time' => '09:00',
                        'name' => $primary['title'],
                        'desc' => sprintf(
                            '%s - %s, danh gia %.1f sao. Phu hop de lien he dat lich nhanh.',
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
                        'name' => 'Xac nhan bao gia',
                        'desc' => 'So sanh pham vi cong viec, thoi gian den va chi phi phat sinh neu co.',
                        'cost' => max(100000, (int) round($budget * 0.08)),
                        'icon' => 'car',
                        'serviceId' => null,
                    ],
                    [
                        'time' => '14:00',
                        'name' => 'Theo doi va nghiem thu',
                        'desc' => 'Kiem tra ket qua, luu thong tin nha cung cap uy tin cho lan sau.',
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
                            '%s - %s. Match nhu cau "%s" va uu tien khu vuc %s.',
                            $service['provider'],
                            $service['category_name'],
                            trim((string) ($input['prompt'] ?? 'nhu cau hien tai')),
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
                                '%s - %s, rating %.1f sao. Goi y bo sung de toi uu hanh trinh.',
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
                    'title' => $service ? 'Dich vu that + trai nghiem dia phuong' : $this->dayTitle($index),
                    'activities' => $activities,
                ];
            }
        }

        $plan = [
            'title' => sprintf('Lich trinh %s tai %s', $input['duration'], $input['location']),
            'subtitle' => sprintf(
                'Danh cho %d nguoi - uu tien %s',
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

        $insights[] = 'Da uu tien goi y dua tren dia diem, ngan sach va du lieu dich vu that tren he thong.';
        $insights[] = sprintf(
            'Ngan sach hien tai vao khoang %s moi nguoi.',
            number_format($budgetPerPerson, 0, ',', '.') . 'd'
        );

        if (count($recommended) > 0) {
            $best = $recommended[0];
            $insights[] = sprintf(
                'Dich vu noi bat nhat hien tai la %s tu %s.',
                $best['title'],
                $best['provider']
            );
        }

        if (! empty($context['preferences'])) {
            $insights[] = 'So thich dang duoc uu tien: ' . implode(', ', $context['preferences']) . '.';
        }

        return array_slice($insights, 0, 4);
    }

    protected function getCityHighlights(string $location): array
    {
        $normalized = Str::lower(Str::ascii($location));

        $presets = [
            'da lat' => [
                'landmarks' => [
                    ['name' => 'Quang truong Lam Vien', 'desc' => 'Bat dau nhe nhang o trung tam thanh pho.', 'cost' => 0],
                    ['name' => 'Thien vien Truc Lam', 'desc' => 'Phu hop cho buoi tham quan va nghi duong.', 'cost' => 100000],
                    ['name' => 'Cho dem Da Lat', 'desc' => 'Tap trung am thuc va khong khi dia phuong.', 'cost' => 120000],
                ],
                'food' => [
                    ['name' => 'Lau ga la e', 'desc' => 'Mon dac trung hop nhom ban hoac gia dinh.', 'cost' => 220000, 'icon' => 'food'],
                    ['name' => 'Ca phe view doi thong', 'desc' => 'Phu hop check-in va thu gian.', 'cost' => 90000, 'icon' => 'coffee'],
                ],
                'hotel' => ['name' => 'Check-in homestay trung tam', 'desc' => 'Chon diem o de di chuyen de dang.'],
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
            return 'Khong ro khu vuc';
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
        return filled(config('services.gemini.key'));
    }
}
