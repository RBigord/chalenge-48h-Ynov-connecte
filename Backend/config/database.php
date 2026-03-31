<?php
// Backward-compatibility shim for legacy includes.
require_once __DIR__ . '/../../config/Database.php';

try {
    $bdd = Database::getInstance();
} catch (Throwable $e) {
    error_log('Erreur de connexion BD: ' . $e->getMessage());
    http_response_code(500);
    exit('Service indisponible. Reessayez plus tard.');
}
?>