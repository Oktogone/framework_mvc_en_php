<?php

declare(strict_types=1);

var_dump($_SERVER);

use peps\core\Autoload;
use peps\core\AutoloadException;

// Initialiser l'autoload (à faire EN PREMIER).
require 'peps/core/Autoload.php';
try {
	Autoload::init();
} catch (AutoloadException $e) {
	exit($e->getMessage());
}

new Truc();
