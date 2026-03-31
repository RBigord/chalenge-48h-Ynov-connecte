<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../Backend/models/Auth.php';

Auth::startSecureSession();
Auth::logout();

header('Location: index.php');
exit;
