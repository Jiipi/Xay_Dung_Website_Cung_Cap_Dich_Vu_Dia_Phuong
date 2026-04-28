<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class CreateBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'dich_vu_id'         => 'required|exists:dich_vu,id',
            'thoi_gian_thuc_hien'=> 'required|date|after_or_equal:today',
            'khung_gio'          => 'nullable|string|max:20',
            'so_luong'           => 'required|numeric|min:1|max:100',
            'dia_diem_thuc_hien' => 'nullable|string|max:500',
            'ghi_chu'            => 'nullable|string|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'dich_vu_id.required'           => 'Vui lòng chọn dịch vụ.',
            'dich_vu_id.exists'             => 'Dịch vụ không tồn tại.',
            'thoi_gian_thuc_hien.required'  => 'Vui lòng chọn ngày thực hiện.',
            'thoi_gian_thuc_hien.after_or_equal' => 'Ngày thực hiện phải từ hôm nay trở đi.',
            'so_luong.required'             => 'Vui lòng nhập số lượng.',
            'so_luong.min'                  => 'Số lượng tối thiểu là 1.',
        ];
    }
}
