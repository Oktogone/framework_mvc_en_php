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
		// Récupérer idCategory pour caler le menu déroulant.
		$idCategory = (int)$params['idCategory'];
		// Créer un produit.
		$product = new Product();
		// Définir son idCategory pour caler le menu déroulant.
		$product->idCategory = $idCategory;
		// Récupérer les catégories pour peupler le menu déroulant.
		$categories = Category::findAllBy([], ['name' => 'ASC']);
		// Rendre la vue.
		Router::render('editProduct.php', ['categories' => $categories, 'product' => $product]);
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
		// Récupérer idProduct.
		$idProduct = (int)$params['idProduct'];
		// Créer le produit correspondant.
		$product = new Product($idProduct);
		// Hydrater le produit. Si produit inexistant, erreur 404.
		if (!$product->hydrate())
			Router::render(Cfg::get('ERROR_404_VIEW'));
		// Récupérer les catégories pour peupler le menu déroulant.
		$categories = Category::findAllBy([], ['name' => 'ASC']);
		// Rendre la vue.
		Router::render('editProduct.php', ['categories' => $categories, 'product' => $product]);
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
		// Attraper les exceptions...
		try {
			// Créer le produit.
			$product = new Product();
			// Initialiser le tableau des messages d'erreur.
			$errors = [];
			// Récupérer les données POST.
			$product->idProduct = filter_input(INPUT_POST, 'idProduct', FILTER_VALIDATE_INT) ?: null;
			$product->idCategory = filter_input(INPUT_POST, 'idCategory', FILTER_VALIDATE_INT) ?: null;
			$product->name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS) ?: null;
			$product->ref = filter_input(INPUT_POST, 'ref', FILTER_SANITIZE_SPECIAL_CHARS) ?: null;
			$product->price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT) ?: null;
			// Vérifier le nom (obligatoire et max 50 caractères).
			if (!$product->name || mb_strlen($product->name) > 50)
				throw new ProductControllerException(ProductControllerException::WRONG_NAME);
			// Vérifier la référence (obligatoire et max 10 caractères).
			if (!$product->ref || mb_strlen($product->ref) > 10)
				throw new ProductControllerException(ProductControllerException::WRONG_REF);
			// Vérifier le prix (obligatoire et >0 et <10000).
			if (!$product->price || $product->price <= 0 || $product->price >= 10000)
				throw new ProductControllerException(ProductControllerException::WRONG_PRICE);
			// Tenter de persister le produit.
			$product->persist();
		} catch (ProductControllerException $e) {
			$errors[] = $e->getMessage();
		} catch (DBALException) {
			$errors[] = ProductControllerException::PERSISTANCE_ERROR;
		}
		// Si erreurs, rendre le formulaire avec les erreurs.
		if ($errors) {
			// Récupérer les catégories pour peupler le menu déroulant.
			$categories = Category::findAllBy([], ['name' => 'ASC']);
			// Rendre la vue.
			Router::render('editProduct.php', ['categories' => $categories, 'product' => $product, 'errors' => $errors]);
		}
		// Produit persisté, rediriger vers la liste des produits.
		Router::redirect('/product/list');
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
		// Récupérer idProduct.
		$idProduct = (int)$params['idProduct'];
		// Supprimer le produit.
		(new Product($idProduct))->remove();
		// Rediriger vers la liste des produits.
		Router::redirect('/product/list');
	}
}
