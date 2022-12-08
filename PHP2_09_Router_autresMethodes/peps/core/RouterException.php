<?php

declare(strict_types=1);

namespace peps\core;

use Exception;

/**
 * Exceptions en lien avec Router.
 * Classe 100% statique.
 * 
 * @see Router
 */
final class RouterException extends Exception
{
	// Messages d'erreur.
	public const JSON_ROUTES_FILE_UNAVAILABLE = "JSON routes file unavailable.";
	public const JSON_ROUTES_FILE_CORRUPTED = "JSON routes file absent or not JSON.";
	public const WRONG_PARAMS_ARRAY = "Wrong params array.";
	public const CONTROLLER_METHOD_FAILED = "Controller method failed.";
	public const PARAMS_ARRAY_CONTAINS_INVALID_KEY = "Params array contains invalid key 'view' or 'params'.";
	public const VIEW_NOT_FOUND = "View not found.";
	public const FILE_NOT_FOUND = "File not found.";
}
