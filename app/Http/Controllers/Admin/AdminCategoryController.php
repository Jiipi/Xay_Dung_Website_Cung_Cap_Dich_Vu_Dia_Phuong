<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DanhMucDichVu;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Illuminate\Support\Str;

class AdminCategoryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = DanhMucDichVu::withCount('dichVu')->with('parent');

        if ($search) {
            $query->where('ten_danh_muc', 'ilike', "%{$search}%");
        }

        $categories = $query->orderBy('thu_tu_hien_thi')->paginate(15)->through(
            fn($c) => [
                'id' => $c->id,
                'ten_danh_muc' => $c->ten_danh_muc,
                'slug' => $c->slug,
                'mo_ta' => $c->mo_ta,
                'icon' => $c->icon,
                'hinh_anh' => $c->anh_dai_dien,
                'danh_muc_cha_id' => $c->parent_id,
                'danh_muc_cha_ten' => $c->parent?->ten_danh_muc,
                'thu_tu' => $c->thu_tu_hien_thi,
                'trang_thai' => $c->trang_thai,
                'so_luong_dich_vu' => $c->dich_vu_count,
            ],
        );

        $parentCategories = DanhMucDichVu::whereNull('parent_id')->get([
            'id',
            'ten_danh_muc',
        ]);

        return Inertia::render('admin/Categories', [
            'categories' => $categories,
            'parentCategories' => $parentCategories,
            'filters' => $request->only('search'),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ten_danh_muc' => [
                'required',
                'string',
                'max:255',
                Rule::unique('danh_muc_dich_vu', 'ten_danh_muc'),
            ],
            'mo_ta' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'hinh_anh' => 'nullable|string|max:255',
            'danh_muc_cha_id' => 'nullable|exists:danh_muc_dich_vu,id',
            'thu_tu' => 'nullable|integer',
            'trang_thai' => 'required|in:hoat_dong,tam_an',
        ]);

        $validated['slug'] = Str::slug($validated['ten_danh_muc']);

        DanhMucDichVu::create($this->toModelAttributes($validated));

        return back()->with('success', 'Thêm danh mục thành công.');
    }

    public function update(Request $request, $id)
    {
        $category = DanhMucDichVu::findOrFail($id);

        $validated = $request->validate([
            'ten_danh_muc' => [
                'required',
                'string',
                'max:255',
                Rule::unique('danh_muc_dich_vu', 'ten_danh_muc')->ignore(
                    $category->id,
                ),
            ],
            'mo_ta' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'hinh_anh' => 'nullable|string|max:255',
            'danh_muc_cha_id' =>
                'nullable|exists:danh_muc_dich_vu,id|not_in:' . $id,
            'thu_tu' => 'nullable|integer',
            'trang_thai' => 'required|in:hoat_dong,tam_an',
        ]);

        $validated['slug'] = Str::slug($validated['ten_danh_muc']);

        $parentId = $validated['danh_muc_cha_id'] ?? null;
        if (
            $parentId &&
            $this->wouldCreateCycle($category->id, (int) $parentId)
        ) {
            throw ValidationException::withMessages([
                'danh_muc_cha_id' =>
                    'Không thể chọn một danh mục con làm danh mục cha.',
            ]);
        }

        $category->update($this->toModelAttributes($validated));

        return back()->with('success', 'Cập nhật danh mục thành công.');
    }

    public function destroy($id)
    {
        $category = DanhMucDichVu::withCount([
            'dichVu',
            'children',
        ])->findOrFail($id);

        if ($category->dich_vu_count > 0) {
            return back()->with(
                'error',
                'Không thể xóa danh mục đang có dịch vụ. Vui lòng chuyển các dịch vụ sang danh mục khác trước.',
            );
        }

        if ($category->children_count > 0) {
            return back()->with(
                'error',
                'Không thể xóa danh mục cha đang có danh mục con.',
            );
        }

        $category->delete();

        return back()->with('success', 'Xóa danh mục thành công.');
    }

    /**
     * Keep the existing Vue form contract while writing the real schema names.
     *
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function toModelAttributes(array $validated): array
    {
        return [
            'ten_danh_muc' => $validated['ten_danh_muc'],
            'slug' => $validated['slug'],
            'mo_ta' => $validated['mo_ta'] ?? null,
            'icon' => $validated['icon'] ?? null,
            'anh_dai_dien' => $validated['hinh_anh'] ?? null,
            'parent_id' => $validated['danh_muc_cha_id'] ?? null,
            'thu_tu_hien_thi' => $validated['thu_tu'] ?? 0,
            'trang_thai' => $validated['trang_thai'],
        ];
    }

    private function wouldCreateCycle(int $categoryId, int $parentId): bool
    {
        $visited = [];

        while ($parentId) {
            if ($parentId === $categoryId || isset($visited[$parentId])) {
                return true;
            }

            $visited[$parentId] = true;
            $parentId =
                (int) (DanhMucDichVu::whereKey($parentId)->value('parent_id') ??
                    0);
        }

        return false;
    }
}
