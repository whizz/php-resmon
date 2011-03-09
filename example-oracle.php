<?php


require_once('Resmon.class.php');
require_once('ResmonOracle.class.php');

$resmon = new ResmonOracle('resmon','resmon', 'MYTNSNAME', true);

$resmon->getSysStat();
$resmon->outputAsXML();


