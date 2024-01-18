#!/usr/bin/env php
<?php

$lookup =  array_map('str_getcsv', file('lookup.csv'));
$vips = [
    'collector1' => '192.168.1.5',
    'collector2' => '192.168.1.5',
    'collector3' => '192.168.1.5',
];

$server = new Swoole\Server('0.0.0.0', 9162, SWOOLE_BASE, SWOOLE_SOCK_UDP);

$server->set([
    'worker_num' => 4,
]);

$server->on('packet', function ($server, $data, $clientInfo) use ($lookup, $vips) {
    $knownHost = array_search($clientInfo['address'], array_column($lookup, 0));
    $collector = $knownHost ? $lookup[$knownHost][1] : 'collector3';
    $server->sendTo($vips[$collector], 162, $data);
});

$server->start();