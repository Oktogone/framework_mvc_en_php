<?php

declare(strict_types=1);

namespace controllers;

use entities\Product;
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
		$product = new Product();
		$product->idProduct = 3;
		var_dump($product);
		var_dump($product->hydrate());
		var_dump($product);
	}
}
