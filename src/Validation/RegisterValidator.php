<?php
declare(strict_types=1);

namespace App\Validation;

use Rakit\Validation\Validator;

class RegisterValidator
{
    public function __construct(private Validator $validator) {}

    public function validate(array $data): array
    {
        $validation = $this->validator->make($data, [
            'name'   => 'required|min:2',
            'email' => 'required|email',
            'password' => 'required|min:6',
            'password_confirmation' => 'required|same:password',
        ]);
        $validation->validate();

        if ($validation->fails()) {
            return $validation->errors()->all();
        }

        return [];
    }
}