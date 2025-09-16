<?php

date_default_timezone_set('UTC');

require '../vendor/autoload.php';

$f3 = \Base::instance();

$f3->config('../backend/config/config.ini');
$f3->config('../backend/config/routes.ini');

$f3->run();