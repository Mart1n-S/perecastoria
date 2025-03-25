<?php
require __DIR__ . '../../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '../../');
$dotenv->load();

if (!$apiKey) {
    die("Erreur : Clé API manquante.");
}

$movieTitle = "Le seigneur des anneaux : La communauté de l'anneau";
$lang = "français";

function generateStory($movieTitle, $lang) {

    $apiKey = $_ENV['OPENAI_API_KEY'] ?? null;
    
    $url = "https://api.openai.com/v1/chat/completions";

    $prompt = "Raconte moi une histoire inspirée du film '$movieTitle' en 300 mots et en $lang. Garde un ton captivant et immersif.";

    $data = [
        "model" => "gpt-4o-mini",
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


function generateImagePrompt($story) {
    $prompt = "Illustration d'une scène de cette histoire : " . substr($story, 0, 200) . "é dans un style cinématographique épique.";
    return $prompt;
}

$response = generateStory($movieTitle,$lang, $apiKey);

if (isset($response['choices'][0]['message']['content'])) {
    echo "<h2>Histoire générée basé sur : $movieTitle et en $lang</h2>";
    echo "<p>" . nl2br(htmlspecialchars($response['choices'][0]['message']['content'])) . "</p>";
} else {
    echo "Erreur lors de la génération de l'histoire.";
}

$imagePrompt = generateImagePrompt($response['choices'][0]['message']['content']);
echo "<h2>Prompt pour DALL-E :</h2>";

if (isset($imagePrompt)) {
    echo "<p>$imagePrompt</p>";
} else {
    echo "Erreur lors de la génération du prompt pour DALL-E.";
}

