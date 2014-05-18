<?php
define('ROOT', dirname(realpath(__FILE__)));
include(ROOT . '/library/autoload.php');
include(ROOT . '/vendor/autoload.php');

// $event = \D3R\Event\Store\Json::read('/tmp/json/sara.errors.8afdea9a917d219b071d2bcc1bbd43b7.json');
// var_dump($event);

// exit;

$event = \D3R\Event::Factory('sara.errors')
            ->set('error', 'Foobar is not defined')
            ->set('line', 102)
            ;
$event1 = \D3R\Event::Factory('sara.errors')
            ->set('error', 'Foobar is not defined')
            ->set('line', 103)
            ;
$event2 = \D3R\Event::Factory('sara.errors')
            ->set('error', 'Foobar is not defined')
            ->set('line', 104)
            ;
$event3 = \D3R\Event::Factory('sara.errors')
            ->set('error', 'Foobar is not defined')
            ->set('line', 105)
            ;


$options = array(
    'username' => 'd3r.events',
    'password' => 'd3r.3v3nt5',
    'hostname' => '192.168.1.70',
    'database' => 'd3r.events'
);

// $options = array(
//     'directory' => '/tmp/json'
// );

$writer = \D3R\Event\Store\Base::Factory('InfluxDB', $options);
// @TODO Remove var_dump
// var_dump($writer); exit();
$writer->batchWrite($event);
$writer->batchWrite($event1);
$writer->batchWrite($event2);
$writer->batchWrite($event3);
// @TODO Remove var_dump
// var_dump($writer); exit();
$writer->commitBatch();

/*
$client = new \crodas\InfluxPHP\Client(
    '192.168.1.70',
    8086,
    'd3r.events',
    'd3r.3v3nt5'
);
$db = $client->getDatabase('d3r.events');

$data = array();
foreach (range(1,rand(1,10)) as $number) {
    $data[] = array('foobar' => 'bar' . $number);
}

if (!$ret = $db->insert('sara.errors', $data)) {
    var_dump($ret);
}

$cursor = $db->query('SELECT * FROM sara.errors');

foreach ($cursor as $row) {
    var_dump($row);
}
*/
