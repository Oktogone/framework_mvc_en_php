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
		$id = (int)$params['id'];
		$obj = new stdClass();
		var_dump($obj);
		DBAL::get()
			->xeq("SELECT * FROM Product WHERE idProduct = :id", ['id' => $id])
			->into($obj);
		var_dump($obj);
	}
}
