<?php

use DiDom\Document;

include_once 'include/db.php';

require 'vendor/autoload.php';

function getUrlBasePage(){
    $client = new \GuzzleHttp\Client();
    $resp = $client->get('https://addssites.com/');
    $html = $resp->getBody()->getContents();

    $document = new Document();
    $document->loadHtml($html);

    $url = $document->find('div.cid-qZjE4Jq3oJ .container .row-content .card .card-wrapper a');

    foreach ($url as $item) {
        $arr[] = $item->attr('href');
    }

    return $arr;
}

$client = new \GuzzleHttp\Client();

foreach (getUrlBasePage() as $url){
    $resp = $client->get('https://addssites.com/' . $url);
    $html = $resp->getBody()->getContents();

    $document = new Document();
    $document->loadHtml($html);

    $sites = $document->find('div.testimonials1  .container .text-box');

    foreach ($sites as $site){
        $item = [
            'title' => $site->first('h3.card-title')->text(),
            'description' => $site->first('p.text')->text(),
            'link' => $site->first('a.rd')->text()
        ];

        includeDB()->query("INSERT INTO site (`title`, `description`, `url`)
                VALUES ('{$item["title"]}', '{$item["description"]}', '{$item["link"]}')");
    }
}


?>