

<?php

// Orchestrator.php

include_once 'api-v1/llm.php';     // Text generation (returns array)
include_once 'api-v1/tts.php';     // Text-to-speech
include_once 'api-v1/tti.php';     // Text-to-image prompt

use Spatie\Async\Pool;

/**
 * Main orchestrator to handle story generation workflow.
 */
class Orchestrator
{
    /**
     * Handle incoming POST request and orchestrate the generation process.
     *
     * @param string $language Language code (e.g., 'en', 'fr').
     * @param string $prompt   Prompt or movie title provided by the user.
     * @return array           Response containing generated data.
     */
    public function handleRequest(string $language, string $prompt): array
    {
        // Step 1: Generate story using LLM (returns array with multiple parts)
        $storyData = getStoryData($prompt, $language);
        if($storyData['status'] === 'success') {
            $imagePrompt = $storyData['imagePrompt'];
            $story = $storyData['story'];
        } else {
            $imagePrompt = null;
            $story = null;
            //show error message
            echo "Erreur lors de la génération de l'histoire : ";
            echo $story['message'];

        }

        $async = false;
        if(!$async) {
            // Step 2: Generate audio and image using TTS and TTI
            $audioBase64 = null;
            $imageBase64 = null;

            // Generate audio using TTS
            $audioResult = openaiTextToSpeech($story, $language);
            if ($audioResult['status'] === 'success') {
                $audioBase64 = $audioResult['audio'];
            } else {
                $audioBase64 = null;
                echo "Erreur lors de la génération de l'audio : " . $audioResult['message'];
            }

            // Generate image using TTI
            $generator = new ImageGenerator();
            $imageResult = $generator->generateImages($imagePrompt,'b64_json', 1);
            if ($imageResult['status'] === 'success') {
                $imageBase64 = $imageResult['images'][0];
            } else {
                $imageBase64 = null;
                echo "Erreur lors de la génération de l'image : " . $imageResult['error'];
            }
        } else {
            //if $async = false not asynchornous else
            // Step 2: Generate audio and image using TTS and TTI
            $audioBase64 = null;
            $imageBase64 = null;

            // Création d'une instance de Pool pour gérer les tâches asynchrones
            $pool = Pool::create();

            // Ajout de la tâche de génération d'audio au pool
            $pool->add(function () use ($story, $language) {
                return openaiTextToSpeech($story, $language);
            })->then(function ($audioResult) use (&$audioBase64) { 
                echo "audioResult";
                dd($audioResult);
                if ($audioResult['status'] === 'success') {
                    $audioBase64 = $audioResult['audio'];
                } else {
                    $audioBase64 = null;
                    echo "Erreur lors de la génération de l'audio : " . $audioResult['message'];
                }
            })->catch(function (Throwable $exception) {
                echo "Exception lors de la génération de l'audio : " . $exception->getMessage();
            });

            // // Ajout de la tâche de génération d'image au pool
            // $pool->add(function () use ($imagePrompt) {
            //     $generator = new ImageGenerator();
            //     return $generator->generateImage($imagePrompt);
            // })->then(function ($imageResult) use (&$imageBase64) {
            //     if ($imageResult['status'] === 'success') {
            //         $imageBase64 = $imageResult['image'];
            //     } else {
            //         $imageBase64 = null;
            //         echo "Erreur lors de la génération de l'image : " . $imageResult['error'];
            //     }
            // })->catch(function (Throwable $exception) {
            //     echo "Exception lors de la génération de l'image : " . $exception->getMessage();
            // });

            // Exécution des tâches asynchrones et attente de leur achèvement
            $pool->wait();
        }

        // Retour des résultats
        return [
            'story_data' => $story?? null,
            'audio_url' => $audioBase64 ?? null,
            'image_base_64' => $imageBase64 ?? null,
        ];

        
    }
}

// Handle incoming request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
     
    // Lire le contenu brut de la requête POST
    $json = file_get_contents('php://input');

    // Convertir le JSON en tableau associatif PHP
    $data = json_decode($json, true);

    // Optionnel : vérifier si le décodage a réussi
    if ($data === null) {
        http_response_code(400); // Bad Request
        echo json_encode(["error" => "Invalid JSON"]);
        exit;
    }
    $language = $data['language'] ?? 'fr';
    $prompt = $data['prompt'] ?? '';

    $orchestrator = new Orchestrator();
    $response = $orchestrator->handleRequest($language, $prompt);

    header('Content-Type: application/json');
    echo json_encode($response);
} else if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['debug']) && $_GET['debug'] === 'true') {
    
    $language ='fr';
    $prompt = 'Inception';

    $orchestrator = new Orchestrator();
    $response = $orchestrator->handleRequest($language, $prompt);
    dd($response);

}  else {
    header('HTTP/1.1 405 Method Not Allowed');
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Method Not Allowed']);
}



// DD function
function dd($data) {
    echo "<pre>";
    var_dump($data);
    echo "</pre>";
    die();
}