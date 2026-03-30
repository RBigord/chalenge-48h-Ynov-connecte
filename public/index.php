<?php

require_once '../src/AiManager.php';

$ai = new AiManager();
$testMessage = $ai->improvePost("Salut, je cherche des gens pour reviser le challenge 48h.");

echo "<h1>Test IA :</h1>";
echo "<p>Message original : Salut, je cherche des gens pour reviser le challenge 48h.</p>";
echo "<p><strong>Version IA : </strong>" . $testMessage . "</p>";
die(); // On arrête l'affichage ici pour le moment
