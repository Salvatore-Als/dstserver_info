<?php 

include 'lobyClass.php';

$lobby = new Lobby(ZONE_EU);
$test = $lobby->getServerByAddr('164.132.200.11', '10999');

var_dump($test);
?>
