<?php
declare(strict_types=1);

namespace App\Validation;

use Rakit\Validation\Validator;

class PostValidator
{
    public function __construct(private Validator $validator) {}

    public function validate(array $data): array
    {
        $validation = $this->validator->make($data, [
            'title'   => 'required|min:3',
            'content' => 'required',
            'file' => 'uploaded_file:0,5M,png,jpeg,jpg,gif,webp,avif,heic',

            // === Categories ===
            'categories'      => 'array',
            'categories.*'    => 'integer|min:1',

            // === Tags ===
            'tags'            => 'array',
            'tags.*'          => 'integer|min:1',
        ]);
        $validation->validate();

        if ($validation->fails()) {
            return $validation->errors()->all();
        }

        return [];
    }
}