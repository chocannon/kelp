<?php
require_once('./vendor/autoload.php');
define('APPLICATION_CONFIG', __DIR__ . '/config');

use Kelp\Utility\File;
use Kelp\Utility\Directory;
use Kelp\Utility\Config;
use Kelp\App;
use Kelp\Swoole\SwooleEvent;


$a = App::instance()->run();
var_dump($a);