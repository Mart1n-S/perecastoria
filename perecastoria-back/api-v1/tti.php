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
     * Génère plusieurs images à partir d'un prompt
     * @param string $prompt La description de l'image à générer
     * @param string $format 'url'|'b64_json' - Format de réponse souhaité
     * @param int $count Nombre d'images à générer (1-10)
     * @return array ['status' => string, 'images' => array|null, 'error' => string|null]
     */
    public function generateImages(string $prompt, string $format = 'b64_json', int $count = 3): array
    {
        if (!$this->api_key) {
            return [
                'status' => 'error',
                'images' => null,
                'error' => "Clé API introuvable. Vérifiez votre variable d'environnement TTI_API_KEY."
            ];
        }

        // Validation du nombre d'images
        $count = max(1, min(10, $count)); // Limité à 10 images max

        $data = [
            "prompt" => $prompt,
            "n" => $count,
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

        if (isset($result['data'])) {
            $images = array_column($result['data'], $format);
            return [
                'status' => 'success',
                'images' => $images,
                'error' => null
            ];
        } else {
            $error = $result['error']['message'] ?? 'Erreur inconnue lors de la génération des images';
            return [
                'status' => 'error',
                'images' => null,
                'error' => $error
            ];
        }
    }
}

// Exemple d'utilisation
// $generator = new ImageGenerator();
// $result = $generator->generateImages("Un chat astronaut dans l'espace", 'b64_json', 3);

// // Affichage HTML avec les 3 images
// if ($result['status'] === 'success' && !empty($result['images'])) {
//     echo '<!DOCTYPE html>
//     <html lang="fr">
//     <head>
//         <meta charset="UTF-8">
//         <title>Images générées</title>
//         <style>
//             body { font-family: Arial, sans-serif; max-width: 1200px; margin: 0 auto; padding: 20px; text-align: center; }
//             .gallery { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-top: 30px; }
//             .image-card { border: 1px solid #ddd; border-radius: 8px; padding: 15px; }
//             img { max-width: 100%; height: auto; border-radius: 4px; }
//             .download-btn { display: inline-block; margin-top: 10px; padding: 8px 16px; background: #4CAF50; color: white; text-decoration: none; border-radius: 4px; }
//             .error { color: red; padding: 20px; border: 1px solid red; border-radius: 4px; margin: 20px auto; max-width: 600px; }
//         </style>
//     </head>
//     <body>
//         <h1>Vos images générées</h1>
//         <div class="gallery">';
//     print_r($result);
//     foreach ($result['images'] as $index => $imageData) {
//         echo '<div class="image-card">
//                 <img src="data:image/png;base64,' . htmlspecialchars($imageData) . '" alt="Image générée ' . ($index + 1) . '">
//                 <p>
//                     <a href="data:image/png;base64,' . htmlspecialchars($imageData) . '" 
//                        download="image-' . ($index + 1) . '.png" 
//                        class="download-btn">Télécharger</a>
//                 </p>
//               </div>';
//     }

//     echo '</div></body></html>';
// } else {
//     echo '<div class="error"><h1>Erreur</h1><p>' . htmlspecialchars($result['error']) . '</p></div>';
// }
