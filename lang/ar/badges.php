<?php

return [
    'resource' => [
        'label' => 'شارة',
        'plural_label' => 'شارات',
    ],
    'fields' => [
        'icon_path' => 'أيقونة الشارة',
        'name' => 'الاسم',
        'description' => 'الوصف',
        'type' => 'النوع',
        'points_required' => 'النقاط المطلوبة',
        'is_active' => 'نشط',
        'created_at' => 'تم الإنشاء في',
    ],
    'types' => [
        'course_completion' => 'إكمال الدورة',
        'points_achievement' => 'إنجاز النقاط',
        'special_achievement' => 'إنجاز خاص',
    ],
    'filters' => [
        'type' => 'النوع',
        'is_active' => 'نشط',
    ],
    'earned_badges' => 'الشارات المكتسبة',
];