<?php

return [
    'resource' => [
        'label' => 'رسالة اتصال',
        'plural_label' => 'رسائل الاتصال',
    ],
    'fields' => [
        'name' => 'الاسم',
        'email' => 'البريد الإلكتروني',
        'subject' => 'الموضوع',
        'message' => 'الرسالة',
        'is_read' => 'تمت القراءة',
        'status' => 'الحالة',
        'unread' => 'غير مقروء',
        'created_at' => 'تم الاستلام في',
        'has_been_read' => 'تمت قراءته',
    ],
    'filters' => [
        'unread_only' => 'إظهار غير المقروءة فقط',
    ],
    'actions' => [
        'mark_as_read' => 'تحديد كمقروء',
        'reply' => 'الرد عبر البريد الإلكتروني',
    ],
];
