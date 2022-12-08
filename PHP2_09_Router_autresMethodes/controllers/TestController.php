<?php

declare(strict_types=1);

namespace controllers;

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
		// Tester text().
		$id = $params['id'];
		Router::text($id);

		// Tester json().
		// $id = (int)$params['id'];
		// $obj = new stdClass();
		// $obj->id = $id;
		// Router::json(json_encode($obj));

		// Tester download().
		// Router::download('C:\videoform\php\acmepeps\assets\img\logo.png', 'image/png', 'ACME_logo.png');

		// Tester redirect().
		// Router::redirect('/nimportequoi');
	}
}
