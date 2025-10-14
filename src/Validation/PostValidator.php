<?php
declare(strict_types=1);

namespace App\Validation;

use Rakit\Validation\Validator;

class PostValidator
{
    public function validate(array $data): array
    {
        $validator = new Validator();
        $validation = $validator->make($data, [
            'title'   => 'required|min:3',
            'content' => 'required',
            'file' => 'uploaded_file:0,5M,png,jpeg,jpg,gif,webp,avif,heic',
        ]);
        $validation->validate();

        if ($validation->fails()) {
            return $validation->errors()->all();
        }

        return [];
    }
}