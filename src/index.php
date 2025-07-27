<?php

require_once('GrocyApi.php');
require_once('MealieApi.php');


$grocyURL = getenv('GROCYURL');
$grocyApiKey = getenv('GROCYAPIKEY');

$mealieURL = getenv('MEALIEURL');
$mealieApiKey = getenv('MEALIEAPIKEY');

$grocy = new GrocyApi($grocyURL, $grocyApiKey);
$recipies = $grocy->getRecipies();


foreach ($recipies as $r){
    $pos = $grocy->getRecipiesPos($r->id);
    echo "Title: ". $r->name ." (".$r->id.") .\n";
    echo "Zubereitung: ". $r->description . " (".count($pos)." Zutaten) \n";

    $ingr = [];
    foreach ($pos as $p) {
        $ingr[] = $grocy->getProductEntity($p->product_id)->name;
    }

    // Crate Recipe Array 
    $recipe = [
        'name' => $r->name,
        'description' => '',
        'yield' => '2 Portionen',
        'ingredients' => $ingr,
        'instructions' => !empty(parseInstructionsFromHtml($r->description)) ? parseInstructionsFromHtml($r->description) : []
    ];
    // Create recipe JSON from array
    $recipeJson = convertToSchemaOrgJson($recipe);
    // Create Mealie Recipe
    createMealieRecipeViaHtmlOrJson($mealieURL, $mealieApiKey,$recipeJson);
    echo "------- \n\n";
}

/* Helper Functions */

function parseInstructionsFromHtml($htmlinput) {
    if (empty($htmlinput)) {
        return [];
    }

    $html = <<<HTML
<!DOCTYPE html>
<html><head><meta charset="UTF-8"></head><body>
$htmlinput
</body></html>
HTML;

    $dom = new DOMDocument();
    libxml_use_internal_errors(true); 
    $dom->loadHTML($html);

    $lis = $dom->getElementsByTagName('li');
    $instr = [];
    // include <li> content 
    foreach ($lis as $li) {
        $instr[] = trim($li->textContent);
    }

    // include <p> content if not empty
    $ps = $dom->getElementsByTagName('p');
    foreach ($ps as $p) {
        $text = trim($p->textContent);
        if (!empty($text)) {
            $instr[] = $text;
        }
    }

    return $instr;
}


function convertToSchemaOrgJson(array $recipe): string {
    $schema = [
        "@context" => "https://schema.org",
        "@type" => "Recipe",
        "name" => $recipe['name'],
        "description" => $recipe['description'] ?: "Rezept ohne Beschreibung",
        "recipeYield" => $recipe['yield'],
        "recipeIngredient" => $recipe['ingredients'],
        "recipeInstructions" => array_map(function($step) {
            return [
                "@type" => "HowToStep",
                "text" => $step
            ];
        }, $recipe['instructions']),
        "prepTime" => "PT10M",
        "cookTime" => "PT20M",
        "totalTime" => "PT30M",
        "recipeCuisine" => "Italienisch",
        "recipeCategory" => "Hauptgericht",
        "keywords" => "Spaghetti, Fleischbällchen, Tomatensauce, Pasta"
    ];

    return json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}
