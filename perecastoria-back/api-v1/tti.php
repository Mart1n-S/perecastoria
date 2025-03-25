<?php
require __DIR__ . '../../vendor/autoload.php';

use Dotenv\Dotenv;

class ImageGenerator
{
    private $api_key;

    public function __construct()
    {
        // Charge les variables d'environnement
        $dotenv = Dotenv::createImmutable(__DIR__ . '../../');
        $dotenv->load();
        $this->api_key = $_ENV['TTI_API_KEY'] ?? null;
    }

    /**
     * Génère une image à partir d'un prompt
     * @param string $prompt La description de l'image à générer
     * @return array ['url' => string|null, 'error' => string|null]
     */
    public function generateImage(string $prompt): array
    {
        if (!$this->api_key) {
            return ['url' => null, 'error' => "Clé API introuvable. Vérifiez votre variable d'environnement TTI_API_KEY."];
        }

        $data = [
            "prompt" => $prompt,
            "n" => 1,
            "size" => "512x512"
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/images/generations');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->api_key
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);

        if (isset($result['data'][0]['url'])) {
            return ['url' => $result['data'][0]['url'], 'error' => null];
        } else {
            $error = $result['error']['message'] ?? 'Erreur inconnue lors de la génération de l\'image';
            return ['url' => null, 'error' => $error];
        }
    }
}

// Exemple d'utilisation
$generator = new ImageGenerator();
$result = $generator->generateImage("Un chat astronaut dans l'espace");

// Génération du HTML
echo '<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Image générée</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            text-align: center;
        }
        img {
            max-width: 100%;
            height: auto;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 5px;
            margin-top: 20px;
        }
        .error {
            color: red;
            padding: 20px;
            border: 1px solid red;
            border-radius: 4px;
        }
    </style>
</head>
<body>';

if ($result['url']) {
    echo '<h1>Votre image générée</h1>
          <img src="' . htmlspecialchars($result['url']) . '" alt="Image générée par IA">
          <p><a href="' . htmlspecialchars($result['url']) . '" target="_blank">Ouvrir l\'image dans un nouvel onglet</a></p>';
} else {
    echo '<div class="error">
            <h1>Erreur</h1>
            <p>' . htmlspecialchars($result['error']) . '</p>
          </div>';
}

echo '</body>
</html>';
