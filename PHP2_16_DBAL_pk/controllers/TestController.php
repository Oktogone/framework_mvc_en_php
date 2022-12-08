<?php

declare(strict_types=1);

namespace controllers;

use peps\core\DBAL;
use peps\core\Router;
use stdClass;

/**
 * Classe 100% statique de test.
 */
final class TestController
{
	/**
	 * Constructeur privé.
	 */
	private function __construct()
	{
	}

	/**
	 * Méthode de test.
	 *
	 * GET /test/{id}
	 *
	 * @param array $params Tableau associatif des paramètres.
	 * @return void
	 */
	public static function test(array $params): void
	{
		$pk = DBAL::get()
			->xeq("INSERT INTO Product VALUES(null, 1, 'Test', 'TEST', 123.45)")
			->pk();
		var_dump($pk);
	}
}
