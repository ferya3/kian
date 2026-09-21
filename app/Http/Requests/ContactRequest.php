<?php

namespace App\Http\Requests;

use App\Support\Digits;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', Rule::in(['general', 'quote', 'technical', 'distributor'])],
            'name' => ['required', 'string', 'max:120'],
            'company' => ['nullable', 'string', 'max:160'],
            'email' => ['nullable', 'email:rfc', 'max:180'],
            'phone' => ['required', 'string', 'regex:/^0?9\d{9}$|^0\d{2,3}\d{8}$/'],
            'city' => ['nullable', 'string', 'max:80'],
            'subject' => ['nullable', 'string', 'max:180'],
            'product_id' => ['nullable', 'exists:products,id'],
            'message' => ['required', 'string', 'min:10', 'max:4000'],
            // تله‌ی ربات — باید همیشه خالی بماند
            'website' => ['nullable', 'size:0'],
        ];
    }

    /**
     * نامِ فیلدها در پیام خطا — همان برچسبی که کاربر روی فرم دیده.
     *
     * از پرونده‌ی زبان می‌آید و نه از اینجا: خطایی که فیلد را به نامی
     * صدا بزند که روی فرم ننوشته، کاربر را دنبال چیزی می‌فرستد که نیست.
     */
    public function attributes(): array
    {
        return collect(['type', 'name', 'company', 'email', 'phone', 'city', 'subject', 'message'])
            ->mapWithKeys(fn (string $field) => [$field => __("site.contact.attribute.{$field}")])
            ->all();
    }

    public function messages(): array
    {
        return [
            'phone.regex' => __('site.contact.phone_format'),
            'website.size' => __('site.contact.invalid'),
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'phone' => Digits::digitsOnly((string) $this->input('phone')),
        ]);
    }
}
