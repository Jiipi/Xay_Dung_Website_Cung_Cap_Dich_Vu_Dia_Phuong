<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\User;
use App\Models\VaiTroNguoiDung;
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
            'password' => $this->passwordRules(),
        ])->validate();

        $customerRole = VaiTroNguoiDung::where('ten_vai_tro', 'Khách hàng')->first();

        return User::create([
            'ho_ten' => $input['name'],
            'email' => $input['email'],
            'mat_khau_hash' => $input['password'],
            'vai_tro' => $customerRole->id,
        ]);
    }
}
