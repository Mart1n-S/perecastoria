<?php
require __DIR__ . '../../vendor/autoload.php';

use Dotenv\Dotenv;

class ImageGenerator
{
    private $api_key;

    public function __construct()
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '../../');
        $dotenv->load();
        $this->api_key = $_ENV['TTI_API_KEY'] ?? null;
    }

    /**
     * Génère une image à partir d'un prompt
     * @param string $prompt La description de l'image à générer
     * @param string $format 'url'|'b64_json' - Format de réponse souhaité
     * @return array ['image' => mixed, 'error' => string|null]
     */
    public function generateImage(string $prompt, string $format = 'b64_json'): array
    {
        if (!$this->api_key) {
            return ['image' => null, 'error' => "Clé API introuvable. Vérifiez votre variable d'environnement TTI_API_KEY."];
        }

        $data = [
            "prompt" => $prompt,
            "n" => 1,
            "size" => "512x512",
            "response_format" => $format
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

        if (isset($result['data'][0][$format])) {
            return [
                'status' => 'success',
                'image' => $result['data'][0][$format],
                'error' => null
            ];
        } else {
            $error = $result['error']['message'] ?? 'Erreur inconnue lors de la génération de l\'image';
            return [
                'status' => 'error',
                'image' => null,
                'error' => $error
            ];
        }
    }
}

// Exemple d'utilisation
$generator = new ImageGenerator();
$result = $generator->generateImage("Un chat astronaut dans l'espace", 'b64_json');

// Affichage HTML avec image en base64 (recommandé pour les pages web)
if ($result['image']) {
    echo '<!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Image générée</title>
        <style>
            body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; text-align: center; }
            img { max-width: 100%; height: auto; border: 1px solid #ddd; border-radius: 4px; padding: 5px; margin-top: 20px; }
            .error { color: red; padding: 20px; border: 1px solid red; border-radius: 4px; }
            .download-btn { display: inline-block; margin-top: 15px; padding: 8px 16px; background: #4CAF50; color: white; text-decoration: none; border-radius: 4px; }
        </style>
    </head>
    <body>
        <h1>Votre image générée</h1>
        <img src="data:image/png;base64,' . htmlspecialchars($result['image']) . '" alt="Image générée par IA">
        <p>
            <a href="data:image/png;base64,' . htmlspecialchars($result['image']) . '" 
               download="image-generee.png" 
               class="download-btn">Télécharger l\'image</a>
        </p>
    </body>
    </html>';
} else {
    echo '<div class="error"><h1>Erreur</h1><p>' . htmlspecialchars($result['error']) . '</p></div>';
}
