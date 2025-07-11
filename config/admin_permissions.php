<?php

return [
    'permissions' => [
        'book' => ['create', 'read', 'update', 'delete'],
        'author' => ['create', 'read', 'update', 'delete'],
        'badge' => ['create', 'read', 'update', 'delete'],
        'country' => ['create', 'read', 'update', 'delete'],
        'category' => ['create', 'read', 'update', 'delete'],
        'size_category' => ['create', 'read', 'update', 'delete'],
        'challenge' => ['create', 'read', 'update', 'delete'],
        'complaint' => ['read', 'delete'],
        'book_suggestion' => ['read', 'delete', 'accept'],
    ],
];

