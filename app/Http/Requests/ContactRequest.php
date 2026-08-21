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

    public function attributes(): array
    {
        return [
            'type' => 'موضوع درخواست',
            'name' => 'نام و نام خانوادگی',
            'company' => 'شرکت',
            'email' => 'ایمیل',
            'phone' => 'شماره تماس',
            'city' => 'شهر',
            'subject' => 'عنوان',
            'message' => 'متن پیام',
        ];
    }

    public function messages(): array
    {
        return [
            'phone.regex' => 'شماره تماس را به شکل ۰۹۱۲۱۲۳۴۵۶۷ یا ۰۲۱۸۸۱۲۳۴۵۶ وارد کنید.',
            'website.size' => 'درخواست نامعتبر است.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'phone' => Digits::digitsOnly((string) $this->input('phone')),
        ]);
    }
}
