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
		// Récupérer idProduct.
		$idProduct = (int)$params['idProduct'];
		// Créer le produit.
		$product = new Product($idProduct);
		// Hydrater et si produit inexistant, erreur 404.
		if (!$product->hydrate())
			Router::render(Cfg::get('ERROR_404_VIEW'));
		// Ajouter dynamiquement le chemin de l'image au produit.
		$product->imagePath = "/assets/img/products/{$product->idCategory}_" . $product->idProduct % 10 . ".png";
		// Ajouter dynamiquement la propriété formattedPrice.
		$product->formattedPrice = Cfg::get('appLocale2dec')->format($product->price) . ' ' . Cfg::get('appCurrency');
		// Rendre la vue.
		Router::render('showProduct.php', ['product' => $product]);
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
