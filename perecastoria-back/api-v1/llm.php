<?php
require __DIR__ . '/../../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$apiKey = $_ENV['OPENAI_API_KEY'] ?? null;

if (!$apiKey) {
    die("Erreur : Clé API manquante.");
}

$movieTitle = "Le seigneur des anneaux : La communauté de l'anneau";

function generateStory($movieTitle, $apiKey) {
    $url = "https://api.openai.com/v1/chat/completions";

    $prompt = "Raconte une histoire inspirée du film '$movieTitle' en 300 mots. Garde un ton captivant et immersif.";

    $data = [
        "model" => "gpt-4-turbo-mini",
        "messages" => [
            ["role" => "system", "content" => "Tu es un narrateur qui raconte des histoires captivantes."],
            ["role" => "user", "content" => $prompt]
        ],
        "max_tokens" => 500,
        "temperature" => 0.7
    ];

    $headers = [
        "Authorization: Bearer $apiKey",
        "Content-Type: application/json"
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}

$response = generateStory($movieTitle, $apiKey);

echo $response;
