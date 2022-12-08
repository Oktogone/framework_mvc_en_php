<?php

declare(strict_types=1);

namespace controllers;

use controllers\ProductControllerException;
use entities\Category;
use entities\Product;
use peps\core\Cfg;
use peps\core\DBALException;
use peps\core\Router;
use peps\core\RouterException;

/**
 * Classe 100% statique de gestion des produits.
 *
 * @see Product
 */
final class ProductController
{
	/**
	 * Constructeur privé.
	 */
	private function __construct()
	{
	}

	/**
	 * Affiche la liste des produits par catégorie.
	 *
	 * GET /
	 * GET /product/list
	 *
	 * @return void
	 * @throws RouterException|DBALException
	 */
	public static function list(): void
	{
		// Récupérer toutes les catégories dans l'ordre alphabétique.
		$categories = Category::findAllBy([], ['name' => 'ASC']);
		// Rendre la vue.
		Router::render('listProducts.php', ['categories' => $categories]);
	}

	/**
	 * Affiche le détail d'un produit.
	 *
	 * GET /product/show/{idProduct}
	 *
	 * @param array $params Tableau associatif des paramètres.
	 * @return void
	 * @throws RouterException|DBALException
	 */
	public static function show(array $params): void
	{
		//TODO
	}

	/**
	 * Affiche le formulaire d'ajout d'un produit.
	 *
	 * GET /product/create/{idCategory}
	 *
	 * @param array $params Tableau associatif des paramètres.
	 * @return void
	 * @throws RouterException|DBALException
	 */
	public static function create(array $params): void
	{
		//TODO
	}

	/**
	 * Affiche le formulaire de modification d'un produit.
	 *
	 * GET /product/update/{idProduct}
	 *
	 * @param array $params Tableau associatif des paramètres.
	 * @return void
	 * @throws RouterException|DBALException
	 */
	public static function update(array $params): void
	{
		//TODO
	}

	/**
	 * Persiste le produit en ajout ou modification.
	 *
	 * POST /product/save
	 *
	 * @return void
	 * @throws DBALException|RouterException
	 */
	public static function save(): void
	{
		//TODO
	}

	/**
	 * Supprime un produit.
	 *
	 * GET /product/remove/{idProduct}
	 *
	 * @param array $params Tableau associatif des paramètres.
	 * @return void
	 * @throws DBALException
	 */
	public static function remove(array $params): void
	{
		//TODO
	}
}
