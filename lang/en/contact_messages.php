<?php

return [
    'resource' => [
        'label' => 'Contact Message',
        'plural_label' => 'Contact Messages',
    ],
    'fields' => [
        'name' => 'Name',
        'email' => 'Email',
        'subject' => 'Subject',
        'message' => 'Message',
        'is_read' => 'Read',
        'status' => 'Status',
        'unread' => 'Unread',
        'created_at' => 'Received at',
        'has_been_read' => 'Has been read',
    ],
    'filters' => [
        'unread_only' => 'Show Unread Only',
    ],
    'actions' => [
        'mark_as_read' => 'Mark as Read',
        'reply' => 'Reply via Email',
    ],
];
