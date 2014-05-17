<?php
define('ROOT', dirname(realpath(__FILE__)));
include(ROOT . '/vendor/autoload.php');

$client = new \crodas\InfluxPHP\Client('localhost', 8086, 'test', 'testing');
$db = $client->getDatabase('testing');
// $db->createUser('test', 'testing');

$cursor = $db->query('SELECT * FROM sara.errors');

foreach ($cursor as $row) {
    var_dump($row);
}
