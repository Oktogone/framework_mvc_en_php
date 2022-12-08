<?php

declare(strict_types=1);

use peps\core\Autoload;
use peps\core\AutoloadException;
use peps\core\Cfg;
use peps\core\CfgException;
use peps\core\Router;
use peps\core\RouterException;

// Initialiser l'autoload (à faire EN PREMIER).
require 'peps/core/Autoload.php';
try {
	Autoload::init();
} catch (AutoloadException $e) {
	exit($e->getMessage());
}

// Initialiser la configuration en fonction de l'IP du serveur (à faire EN DEUXIEME).
$serverIP = filter_input(INPUT_SERVER, 'SERVER_ADDR', FILTER_VALIDATE_IP) ?: filter_var($_SERVER['SERVER_ADDR'], FILTER_VALIDATE_IP);
if (!$serverIP)
	exit(CfgException::SERVER_ADDR_UNAVAILABLE);
// ICI VOS CLASSES DE CONFIGURATION EN FONCTION DES ADRESSES IP DE VOS SERVEURS (antislash initial obligatoire ici).
(match ($serverIP) {
	'127.0.0.1', '::1' => \cfg\CfgLocal::class, // Local server (IPv4 ou IPv6)
	'46.255.164.7' => \cfg\CfgNuxit::class, // Remote server (IP v4)
})::init();

// Router la requête du client (à faire EN DERNIER).
try {
	Router::route();
} catch (RouterException $e) {
	exit($e->getMessage());
}
