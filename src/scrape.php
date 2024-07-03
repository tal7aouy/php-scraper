<?php
require __DIR__ . '/../vendor/autoload.php';

use Nesk\Puphpeteer\Puppeteer;

use Nesk\Rialto\Data\JsFunction;



// open a new Chromium browser window

$puppeteer = new Puppeteer();

$browser = $puppeteer->launch([

    'headless' => true, // set to false while developing locally

]);


$page = $browser->newPage();

$page->goto('https://scrapingclub.com/exercise/list_infinite_scroll/');


$products = [];


$product_elements = $page->querySelectorAll('.post');


foreach ($product_elements as $product_element) {

    // select the name and price elements

    $name_element = $product_element->querySelector('h4');

    $price_element = $product_element->querySelector('h5');



    // retrieve the data of interest

    $name = $name_element->evaluate(JsFunction::createWithParameters(['node'])->body('return node.innerText;'));

    $price = $price_element->evaluate(JsFunction::createWithParameters(['node'])->body('return node.innerText;'));



    $product = ['name' => $name, 'price' => $price];

    $products[] = $product;
}


// echo "<pre>";
// var_dump($products);
// echo "</pre>";

// save as csv file
// open the output CSV file
$csvFilePath = 'products.csv';
$csvFile = fopen($csvFilePath, 'w');

// write the header row
$header = ['name', 'price'];
fputcsv($csvFile, $header);

// add each product to the CSV file
foreach ($products as $product) {
    fputcsv($csvFile, $product);
}

// close the CSV file
fclose($csvFile);

$browser->close();
