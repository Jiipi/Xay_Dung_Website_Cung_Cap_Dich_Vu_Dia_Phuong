<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\User;
use App\Models\VaiTroNguoiDung;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            ...$this->profileRules(),
            'role' => ['required', Rule::in(['customer', 'provider'])],
            'password' => $this->passwordRules(),
        ])->validate();

        $roleName =
            $input['role'] === 'provider' ? 'Nhà cung cấp' : 'Khách hàng';
        $role = VaiTroNguoiDung::firstOrCreate(
            ['ten_vai_tro' => $roleName],
            [
                'mo_ta' =>
                    $input['role'] === 'provider'
                        ? 'Người cung cấp dịch vụ'
                        : 'Người dùng tìm kiếm và đặt dịch vụ',
            ],
        );

        return User::create([
            'ho_ten' => $input['name'],
            'email' => $input['email'],
            'mat_khau_hash' => $input['password'],
            'vai_tro' => $role->id,
        ]);
    }
}
