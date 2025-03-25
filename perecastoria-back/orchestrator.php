

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
        // Création d'une instance de Pool pour gérer les tâches asynchrones
        $pool = Pool::create();

        // Ajout de la tâche de génération d'audio au pool
        $pool->add(function () use ($story, $language) {
            return openaiTextToSpeech($story, $language);
        })->then(function ($audioResult) use (&$audioUrl) {
            if ($audioResult['status'] === 'success') {
                $audioUrl = $audioResult['audio_url'];
            } else {
                $audioUrl = null;
                echo "Erreur lors de la génération de l'audio : " . $audioResult['message'];
            }
        })->catch(function (Throwable $exception) {
            echo "Exception lors de la génération de l'audio : " . $exception->getMessage();
        });

        // Ajout de la tâche de génération d'image au pool
        $pool->add(function () use ($imagePrompt) {
            $generator = new ImageGenerator();
            return $generator->generateImage($imagePrompt);
        })->then(function ($imageResult) use (&$imageBase64) {
            if ($imageResult['status'] === 'success') {
                $imageBase64 = $imageResult['image'];
            } else {
                $imageBase64 = null;
                echo "Erreur lors de la génération de l'image : " . $imageResult['error'];
            }
        })->catch(function (Throwable $exception) {
            echo "Exception lors de la génération de l'image : " . $exception->getMessage();
        });

        // Exécution des tâches asynchrones et attente de leur achèvement
        $pool->wait();

        // Retour des résultats
        return [
            'story_data' => $storyData,
            'audio_url' => $audioUrl ?? null,
            'image_base_64' => $imageBase64 ?? null,
        ];

        
    }
}

// Handle incoming request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $language = $_POST['language'] ?? 'fr';
    $prompt = $_POST['prompt'] ?? '';

    $orchestrator = new Orchestrator();
    $response = $orchestrator->handleRequest($language, $prompt);

    header('Content-Type: application/json');
    echo json_encode($response);
} else if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['debug']) && $_GET['debug'] === 'true') {
    $language ='fr';
    $prompt = 'Inception';

    $orchestrator = new Orchestrator();
    $response = $orchestrator->handleRequest($language, $prompt);
    // stilized vardump
    echo "<pre>";
        var_dump($response);
    echo "</pre>";

}  else {
    header('HTTP/1.1 405 Method Not Allowed');
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Method Not Allowed']);
}
