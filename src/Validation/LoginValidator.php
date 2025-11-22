<?php
declare(strict_types=1);

namespace App\Validation;

use Rakit\Validation\Validator;

class LoginValidator
{
    public function __construct(private Validator $validator) {}

    public function validate(array $data): array
    {
        $validation = $this->validator->make($data, [
            'name'   => 'required|min:2',
            'password' => 'required|min:6',
        ]);
        $validation->validate();

        if ($validation->fails()) {
            return $validation->errors()->all();
        }

        return [];
    }
}