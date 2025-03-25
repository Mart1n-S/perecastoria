<?php

function openaiTextToSpeech($input, $apiKey) {
    $endpoint = 'https://api.openai.com/v1/audio/speech'; // The TTS endpoint - replace if different

    // Data to send in the request
    $data = [
        'model' => 'gpt-4o-mini-tts',
        'input' => $input,
        'voice' => 'coral',
    ];

    // Initialize cURL session
    $ch = curl_init($endpoint);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  // Receive the response
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $apiKey,    // Add your OpenAI API key
        'Content-Type: application/json',       // Set content type to JSON
    ]);
    curl_setopt($ch, CURLOPT_POST, true);  // Send a POST request
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));  // Encode data as JSON

    // Execute the cURL request and capture the response
    $response = curl_exec($ch);

    // Check for errors
    if(curl_errno($ch)) {
        $error_msg = curl_error($ch);
        curl_close($ch);
        return [
            'status' => 'error',
            'message' => "cURL error: " . $error_msg
        ];
    }

    // Get the response code to check if the request was successful
    $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // If the response code is 200, we assume success
    if ($responseCode === 200) {
        // file_put_contents('speech.mp3', $response);
        // Return the binary audio response
        return [
            'status' => 'success',
            'audio' => base64_encode($response),  // Encode the audio as base64 for direct use in React
        ];
    } else {
        // Return error message if something went wrong
        return [
            'status' => 'error',
            'message' => "Error: Received HTTP code $responseCode. Response: " . $response,
        ];
    }
}

/* Example usage:
$input = "Bonjour, c'est un test en français pour le texte";
echo json_encode(openaiTextToSpeech($input, $apiKey));
*/
