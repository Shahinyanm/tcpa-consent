<?php

namespace App\DTO;

use App\Enums\Language;

class ConsentData
{
    public function __construct(
        public string $firstName,
        public string $lastName,
        public string $phoneNumber,
        public Language $language,
        public string $companyHash
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            $data['first_name'],
            $data['last_name'],
            $data['phone_number'],
            Language::from($data['language']),
            $data['company_hash']
        );
    }
}
