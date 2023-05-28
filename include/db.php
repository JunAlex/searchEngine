<?php
function includeDB(){
    $username = 'root';
    $password = '';
    $db = 'searchEngine';
    $host = '127.0.0.1';

    $dsn = 'mysql:host=' . $host . ';dbname=' . $db . ';charset=utf8;';

    return $database = new PDO($dsn, $username, $password);
}

