<?php

declare(strict_types=1);

namespace controllers;

use entities\Category;
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
		$id = (int)$params['id'];
		$product = new Product($id);
		$product->hydrate();
		var_dump($product); // Produit hydraté sauf sa categorie.
		$product->category;
		var_dump($product); // Produit hydraté avec sa catégorie.

		$category = new Category(1);
		$category->hydrate();
		var_dump($category); // Catégorie hydratée sauf ses produits.
		$category->products;
		var_dump($category); // Catégorie hydratée avec ses produits.
	}
}
