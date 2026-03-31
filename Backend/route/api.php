<?php
// Fichier : Backend/route/api.php

return [
    'POST' => [
        'login' => 'AuthController@login',
        'register' => 'AuthController@register',
        'logout' => 'AuthController@logout',
        'ai_resume' => 'AiController@resumeNews',
        'ai_job' => 'AiController@aiJob',
        'create_post' => 'PostController@createPost',
        'toggle_like' => 'PostController@toggleLike',
        'add_comment' => 'CommentController@addComment',
        'send_message' => 'MessageController@sendMessage',
    ],
    'GET' => [
        'posts' => 'PostController@getPosts',
        'profile' => 'UserController@getProfile',
        'user' => 'UserController@getUserById',
        'friends' => 'UserController@getFriends',
        'comments' => 'CommentController@getComments',
        'messages' => 'MessageController@getMessages',
        'contacts' => 'MessageController@getContacts',
    ]
];
