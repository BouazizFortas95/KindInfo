<?php

return [
    'resource' => [
        'label' => 'Message de contact',
        'plural_label' => 'Messages de contact',
    ],
    'fields' => [
        'name' => 'Nom',
        'email' => 'E-mail',
        'subject' => 'Sujet',
        'message' => 'Message',
        'is_read' => 'Lu',
        'status' => 'Statut',
        'unread' => 'Non lu',
        'created_at' => 'Reçu le',
        'has_been_read' => 'A été lu',
    ],
    'filters' => [
        'unread_only' => 'Afficher uniquement les non-lus',
    ],
    'actions' => [
        'mark_as_read' => 'Marquer comme lu',
        'reply' => 'Répondre par e-mail',
    ],
];
