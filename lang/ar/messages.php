<?php

declare(strict_types=1);

return [
    'auth' => [
        'unauthorized' => 'غير مصرح.',
    ],
    'impersonation' => [
        'forbidden' => 'ليست لديك صلاحية لانتحال مستخدم آخر.',
        'self' => 'لا يمكنك انتحال حسابك الخاص.',
        'admin_forbidden' => 'لا يمكنك انتحال حساب المسؤول.',
        'logged_in_as' => 'تم تسجيل الدخول الآن باسم :email',
        'session_inactive' => 'وضع الانتحال غير نشط.',
        'session_missing' => 'لم يتم العثور على الجلسة الأصلية، يرجى تسجيل الدخول مجدداً.',
        'returned_to_admin' => 'لقد عدت إلى حساب المسؤول.',
    ],
    'users' => [
        'roles' => [
            'admin' => 'مسؤول',
            'admin_description' => 'مسؤول',
            'user' => 'مستخدم',
            'user_description' => 'مستخدم عادي',
        ],
        'created' => 'تم إنشاء المستخدم',
        'updated' => 'تم تحديث المستخدم',
        'cannot_change_own_role' => 'لا يمكنك تغيير دورك الشخصي.',
    ],
    'invoices' => [
        'created' => 'تم إنشاء الفاتورة',
        'updated' => 'تم تحديث الفاتورة',
        'create_failed' => 'فشل إنشاء الفاتورة.',
        'update_failed' => 'فشل تحديث الفاتورة.',
        'already_finalized' => 'تم إنهاء الفاتورة بالفعل.',
        'already_expired' => 'الفاتورة منتهية بالفعل.',
        'amount_below_min' => 'المبلغ أقل من الحد الأدنى المسموح.',
        'amount_above_max' => 'المبلغ يتجاوز الحد الأقصى المسموح.',
    ],
    'addresses' => [
        'added' => 'تمت إضافة العنوان',
        'updated' => 'تم تحديث العنوان',
        'add_failed' => 'فشل إضافة العنوان.',
        'errors' => [
            'not_found_blockchain' => 'العنوان المحدد غير موجود على البلوكتشين.',
            'duplicate' => 'العنوان موجود بالفعل للشبكة والعملة المختارة.',
            'unsupported_currency' => 'عملة غير مدعومة.',
            'unsupported_currency_value' => 'عملة غير مدعومة: :currency',
            'unsupported_network' => 'شبكة غير مدعومة.',
            'unsupported_network_value' => 'شبكة غير مدعومة: :network',
            'currency_mismatch' => 'هذه العملة غير متاحة على الشبكة المختارة.',
            'currency_mismatch_value' => 'العملة :currency غير متاحة على شبكة :network.',
            'not_owner' => 'العنوان لا يخص المستخدم الحالي.',
            'not_exist_blockchain' => 'العنوان غير موجود على البلوكتشين للعملة/الشبكة المحددة.',
            'no_available' => 'لا يوجد عنوان متاح للمبلغ المطلوب حالياً.',
            'invalid_balance' => 'يجب أن يكون الرصيد قيمة عشرية غير سالبة.',
        ],
    ],
    'merchants' => [
        'created' => 'تم إنشاء التاجر',
        'updated' => 'تم تحديث التاجر',
        'create_failed' => 'فشل إنشاء التاجر.',
        'update_failed' => 'فشل تحديث التاجر.',
        'errors' => [
            'logo_invalid' => 'تعذر قراءة ملف الشعار.',
            'logo_not_square' => 'يجب أن يكون الشعار مربعاً.',
            'logo_too_large' => 'يجب ألا يتجاوز الشعار 500x500 بكسل.',
            'not_owner' => 'التاجر لا يخص المستخدم الحالي.',
        ],
    ],
    'settings' => [
        'max_limit_exceeded' => 'لا يمكن أن يتجاوز الحد الأقصى :amount USDT.',
    ],
    'blockchain' => [
        'only_tron' => 'شبكة TRON فقط مدعومة لهذه العملية.',
        'only_usdt' => 'فقط USDT (TRC20) مدعوم لهذه العملية.',
        'malformed_trongrid_data' => 'استجابة TronGrid غير صحيحة: البيانات ليست مصفوفة.',
        'malformed_trongrid_events' => 'استجابة أحداث TronGrid غير صحيحة: البيانات ليست مصفوفة.',
        'trc20_transfer_missing' => 'حدث تحويل TRC20 غير موجود للمعاملة المحددة.',
        'malformed_wallet_nowblock' => 'استجابة nowblock لمحفظة Tron غير صحيحة: ليست كائناً.',
        'malformed_trongrid_account' => 'استجابة حساب TronGrid غير صحيحة: ليست كائناً.',
        'account_not_found' => 'بيانات الحساب غير موجودة.',
        'malformed_trongrid_trc20' => 'استجابة حساب TronGrid غير صحيحة: trc20 ليست مصفوفة.',
    ],
    'ip_geolocation' => [
        'quota_exceeded' => 'تم تجاوز الحصة.',
        'request_failed' => 'فشل الطلب بالحالة :status.',
    ],
    'api' => [
        'callback_failed' => 'فشل طلب الـ callback بالحالة :status.',
    ],
    'money' => [
        'empty_string' => 'قيمة المبلغ فارغة.',
        'currency_mismatch' => 'يجب أن تتطابق العملات.',
    ],
];

