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
		DBAL::get()->xeq("UPDATE Product SET price = price * 2 WHERE idProduct = :id", ['id' => $id]);
	}
}
