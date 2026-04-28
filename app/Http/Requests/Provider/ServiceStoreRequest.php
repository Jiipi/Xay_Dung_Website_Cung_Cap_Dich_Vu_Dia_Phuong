<?php

namespace App\Http\Requests\Provider;

use Illuminate\Foundation\Http\FormRequest;

class ServiceStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Middleware đã check role
    }

    public function rules(): array
    {
        return [
            'ten_dich_vu' => 'required|string|max:255',
            'danh_muc_id' => 'required|integer|exists:danh_muc_dich_vu,id',
            'mo_ta_chi_tiet' => 'nullable|string|max:5000',
            'gia_tu' => 'nullable|numeric|min:0',
            'gia_den' => 'nullable|numeric|min:0|gte:gia_tu',
            'don_vi_gia' => 'nullable|string|max:50',
            'dia_chi_hien_thi' => 'nullable|string|max:500',
            'anh_dich_vu' => 'nullable|array|max:10',
            'anh_dich_vu.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'the_tu_khoa' => 'nullable|array',
            'the_tu_khoa.*' => 'string|max:50',
            'khu_vuc_phuc_vu' => 'nullable|array',
            'khu_vuc_phuc_vu.*' => 'string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'ten_dich_vu.required' => 'Vui lòng nhập tên dịch vụ.',
            'ten_dich_vu.max' => 'Tên dịch vụ không quá 255 ký tự.',
            'danh_muc_id.required' => 'Vui lòng chọn danh mục.',
            'danh_muc_id.exists' => 'Danh mục không hợp lệ.',
            'gia_den.gte' => 'Giá đến phải lớn hơn hoặc bằng giá từ.',
            'anh_dich_vu.max' => 'Tối đa 10 ảnh.',
            'anh_dich_vu.*.max' => 'Mỗi ảnh tối đa 2MB.',
            'anh_dich_vu.*.mimes' => 'Ảnh phải có định dạng jpg, png hoặc webp.',
        ];
    }
}
