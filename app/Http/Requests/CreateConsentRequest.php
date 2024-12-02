<?php

namespace App\Http\Requests;


use App\DTO\ConsentData;
use App\Enums\Language;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class CreateConsentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'first_name'   => ['required', 'string', 'max:255'],
            'last_name'    => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'regex:/^\+?[1-9]\d{1,14}$/'],
            'language'     => ['required', new Enum(Language::class)],
            'company_hash' => ['required', 'string', 'exists:companies,hash'],
        ];
    }

    public function getConsentData(): ConsentData
    {
        return ConsentData::fromRequest($this->validated());
    }
}
