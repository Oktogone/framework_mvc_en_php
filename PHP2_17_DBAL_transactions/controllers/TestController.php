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
		DBAL::get()
			->start()
			->xeq("INSERT INTO Product VALUES(null, 1, 'Test1', 'TEST1', 123.45)")
			->savepoint('sp1')
			->xeq("INSERT INTO Product VALUES(null, 2, 'Test2', 'TEST2', 567.89)")
			->rollback('sp1')
			->commit();
	}
}
