<?php
define('ROOT', dirname(realpath(__FILE__)));
include(ROOT . '/library/autoload.php');
include(ROOT . '/vendor/autoload.php');

function out($message) {
    echo " > ${message}\n";
}

function err($message) {
    echo " ! ${message}\n";
}

function usage() {
    global $argv;
    err('Usage : ' . $argv[0] . ' <username> <password> [<database>] [<hostname>]');
}

$validArgs = array('username', 'password', 'database', 'hostname');
$requiredArgs = array('username', 'password', 'database');

$args = $argv;
array_shift($args);
foreach ($validArgs as $arg) {
    $$arg = array_shift($args);
    if (empty($$arg) && in_array($arg, $requiredArgs)) {
        err('Missing argument "' . $arg . '"');
        usage();
        exit(1);
    }
    out($arg . ' : ' . $$arg);
}

if (!isset($hostname)) {
    $hostname = 'localhost';
}


$options = array(
    'username' => $username,
    'password' => $password,
    'hostname' => $hostname,
    'database' => $database
);

// $options = array(
//     'directory' => '/tmp/json'
// );

// @TODO Remove var_dump
var_dump($options);

$event = \D3R\Event::Factory('sara.errors')
            ->tag('mysite.errors')
            ->set('error', 'Foobar is not defined')
            ->set('line', rand(1,100))
            ;
// @TODO Remove var_dump
var_dump($event);

// $event1 = \D3R\Event::Factory('sara.errors')
//             ->set('error', 'Foobar is not defined')
//             ->set('line', 103)
//             ;
// $event2 = \D3R\Event::Factory('sara.errors')
//             ->set('error', 'Foobar is not defined')
//             ->set('line', 104)
//             ;
// $event3 = \D3R\Event::Factory('sara.errors')
//             ->set('error', 'Foobar is not defined')
//             ->set('line', 105)
//             ;

try {
    $writer = \D3R\Event\Store\Base::Factory('InfluxDB', $options);
    // $writer = \D3R\Event\Store\Base::Factory('Json', $options);
    // @TODO Remove var_dump
    // var_dump($writer); exit();
    $writer->write($event);
    // $writer->batchWrite($event);
    // $writer->batchWrite($event1);
    // $writer->batchWrite($event2);
    // $writer->batchWrite($event3);
    // @TODO Remove var_dump
    // var_dump($writer); exit();
    // $writer->commitBatch();
}
catch (Exception $ex) {
    // @TODO Remove var_dump
    echo('ERROR : ' . $ex->getMessage() . "\n");
}

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
