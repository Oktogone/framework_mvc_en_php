<?php

declare(strict_types=1);

namespace peps\core;

use Exception;

/**
 * Exceptions en lien avec Cfg.
 * Classe 100% statique.
 *
 * @see Cfg
 */
final class CfgException extends Exception
{
	// Messages d'erreur.
	public const SERVER_ADDR_UNAVAILABLE = "Server variable SERVER_ADDR unavailable.";
}
