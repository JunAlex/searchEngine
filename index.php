<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="css/style.css">
    <title>Search Engine</title>
</head>
<body>
<div class="container">
    <h1><a class="to__main" href="index.php">Search Engine</a></h1>
    <form action="index.php" method="GET" autocomplete="off">
        <input type="text" name="search" placeholder="Search...">
        <button type="submit">Search</button>
    </form>
</div>
<?php

use Elastic\Elasticsearch\ClientBuilder;

include_once 'include/db.php';
include_once 'parser/parser.php';

require __DIR__ . '/vendor/autoload.php';

$query = includeDB()->prepare('SELECT * FROM site');
$query->execute();
$results = $query->fetchAll(PDO::FETCH_ASSOC);

$es = ClientBuilder::create()
    ->setHosts(['es:9200'])
    ->build();

$params = [];

foreach ($results as $result) {
    $params['body'][] = [
        'index' => [
            '_id' => $result['id']
        ]
    ];

    $params['body'][] = [
        'title' => $result['title'],
        'description' => $result['description'],
        'url' => $result['url']
    ];
}

//$response = $es->bulk($params);

if (isset($_GET['search'])) {
    $params = [
        'index' => 'my_index',
        'body' => [
            'query' => [
                'multi_match' => [
                    'query' => $_GET['search'],
                    'fields' => ['title', 'description', 'url']
                ]
            ]
        ]
    ];

    $response = $es->search($params);

    if ($response['hits']['hits']) {
        foreach ($response['hits']['hits'] as $hit) {
            echo '<div class="result">';
            echo '<h2>' . $hit['_source']['title'] . '</h2>';
            echo '<p>' . $hit['_source']['description'] . '</p>';
            echo '<a href="' . $hit['_source']['url'] . '">' . $hit['_source']['url'] . '</a>';
            echo '</div>';
        }
    } else {
        echo '<p>No results found.</p>';
    }
}

?>
</body>
</html>
