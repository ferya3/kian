<?php

/*
|--------------------------------------------------------------------------
| رسائل التحقّق
|--------------------------------------------------------------------------
|
| مفاتيح القواعد التي يستخدمها هذا الموقع فعلًا. وما لم يُذكَر هنا يعود إلى
| الفارسية (APP_FALLBACK_LOCALE)، وهو أمر لا يحدث إلّا لقاعدة لم تُستخدَم بعد.
|
*/

return [

    'accepted' => 'يجب قبول :attribute.',
    'after' => 'يجب أن يكون :attribute تاريخًا بعد :date.',
    'alpha_dash' => 'لا يجوز أن يحتوي :attribute إلّا على حروف وأرقام وشرطات وشرطات سفلية.',
    'alpha_num' => 'لا يجوز أن يحتوي :attribute إلّا على حروف وأرقام.',
    'array' => 'يجب أن يكون :attribute مصفوفة.',
    'before' => 'يجب أن يكون :attribute تاريخًا قبل :date.',
    'boolean' => 'يجب أن يكون :attribute صحيحًا أو خطأً.',
    'confirmed' => 'تأكيد :attribute غير مطابق.',
    'date' => 'يجب أن يكون :attribute تاريخًا صحيحًا.',
    'different' => 'يجب أن يختلف :attribute عن :other.',
    'email' => 'يجب أن يكون :attribute بريدًا إلكترونيًّا صحيحًا.',
    'exists' => ':attribute المحدَّد غير صحيح.',
    'file' => 'يجب أن يكون :attribute ملفًّا.',
    'image' => 'يجب أن يكون :attribute صورة.',
    'in' => ':attribute المحدَّد غير صحيح.',
    'integer' => 'يجب أن يكون :attribute عددًا صحيحًا.',
    'max' => [
        'array' => 'لا يجوز أن يحتوي :attribute على أكثر من :max عنصرًا.',
        'file' => 'لا يجوز أن يتجاوز :attribute حجم :max كيلوبايت.',
        'numeric' => 'لا يجوز أن يكون :attribute أكبر من :max.',
        'string' => 'لا يجوز أن يتجاوز :attribute :max حرفًا.',
    ],
    'mimes' => 'يجب أن يكون :attribute ملفًّا من نوع: :values.',
    'min' => [
        'array' => 'يجب أن يحتوي :attribute على :min عنصرًا على الأقلّ.',
        'file' => 'يجب ألّا يقلّ حجم :attribute عن :min كيلوبايت.',
        'numeric' => 'يجب ألّا يقلّ :attribute عن :min.',
        'string' => 'يجب ألّا يقلّ :attribute عن :min حرفًا.',
    ],
    'numeric' => 'يجب أن يكون :attribute رقمًا.',
    'regex' => 'صيغة :attribute غير صحيحة.',
    'required' => 'حقل :attribute مطلوب.',
    'same' => 'يجب أن يتطابق :attribute مع :other.',
    'size' => [
        'array' => 'يجب أن يحتوي :attribute على :size عنصرًا.',
        'file' => 'يجب أن يكون حجم :attribute :size كيلوبايت.',
        'numeric' => 'يجب أن يكون :attribute مساويًا :size.',
        'string' => 'يجب أن يكون :attribute :size حرفًا.',
    ],
    'string' => 'يجب أن يكون :attribute نصًّا.',
    'unique' => ':attribute مستخدَم من قبل.',
    'uploaded' => 'تعذّر رفع :attribute.',
    'url' => 'يجب أن يكون :attribute عنوانًا صحيحًا.',

    'attributes' => [],

];
