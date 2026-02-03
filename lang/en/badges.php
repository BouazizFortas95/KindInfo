<?php

return [
    'resource' => [
        'label' => 'Badge',
        'plural_label' => 'Badges',
    ],
    'fields' => [
        'icon_path' => 'Badge Icon',
        'name' => 'Name',
        'description' => 'Description',
        'type' => 'Type',
        'points_required' => 'Points Required',
        'is_active' => 'Active',
        'created_at' => 'Created At',
    ],
    'types' => [
        'course_completion' => 'Course Completion',
        'points_achievement' => 'Points Achievement',
        'special_achievement' => 'Special Achievement',
    ],
    'filters' => [
        'type' => 'Type',
        'is_active' => 'Active',
    ],
    'earned_badges' => 'Earned Badges',
];