<?php

function createMealieRecipeViaHtmlOrJson($apiUrl, $apiToken, $recipeData) {
    $url = rtrim($apiUrl, '/') . '/api/recipes/create/html-or-json';

    $headers = [
        'Content-Type: application/json',
        "Authorization: Bearer $apiToken"
    ];

    // Generate schema.org JSON string
    $schemaJsonString = $recipeData; //convertToSchemaOrgJson($recipeData);

    $payload = json_encode([
        'includeTags' => false,
        'data' => $schemaJsonString
    ]);

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_POSTFIELDS => $payload
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 201) {
        echo "Rezept (Schema.org JSON) erfolgreich erstellt.\n";
        return json_decode($response, true);
    } else {
        echo "Fehler beim Erstellen (Schema.org JSON): HTTP $httpCode\n";
        echo $response;
        return null;
    }
}