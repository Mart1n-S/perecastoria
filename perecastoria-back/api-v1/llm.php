<?php
require __DIR__ . '../../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '../../');
$dotenv->load();

// if (isset($_POST['prompt'])) {
//     $movieTitle = $_POST['prompt'];
// } else {
//     header('Content-Type: application/json');
//     echo json_encode(["error" => "Titre du film manquant.", "status" => "error"]);
//     exit();
// }

// if (isset($_POST['langue'])) {
//     $lang = $_POST['langue'];
// }else {
//     header('Content-Type: application/json');
//     echo json_encode(["error" => "Langue manquante.", "status" => "error"]);
//     exit();
// }

function generateStory($movieTitle, $lang) {

    $apiKey = $_ENV['OPENAI_API_KEY'] ?? null;
    
    if (!$apiKey) {
        die("Erreur : Clé API manquante.");
    }    
    
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
    $prompt = "Illustration d'une scène de cette histoire : " . substr($story, 0, 200) . "... dans un style cinématographique épique.";
    return $prompt;
}
function getStoryData($movieTitle, $lang) {

    $response = generateStory($movieTitle, $lang);

    if (isset($response['choices'][0]['message']['content'])) {
        $story = $response['choices'][0]['message']['content'];
        $imagePrompt = generateImagePrompt($story);

        return [
            "story" => $story,
            "imagePrompt" => $imagePrompt,
            "status" => "success"
        ];
    } else {
        return [
            "error" => "Erreur lors de la génération de l'histoire",
            "status" => "error"
        ];
    }
}