<?php
declare(strict_types=1);

namespace App\Validation;

use Rakit\Validation\Validator;

class CommentValidator
{
    public function __construct(private Validator $validator) {}

    public function validate(array $data): array
    {
        $validation = $this->validator->make($data, [
            'post_id'      => 'required|integer|min:1',
            'user_id'      => 'integer|min:1',
            'content'      => 'required|min:3|max:2000',
        ]);
        $validation->validate();

        if ($validation->fails()) {
            return $validation->errors()->all();
        }

        return [];
    }
}