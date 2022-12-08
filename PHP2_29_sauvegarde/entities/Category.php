<?php

declare(strict_types=1);

namespace entities;

use peps\core\DBALException;
use peps\core\Entity;

/**
 * Entité Category.
 * Toutes les propriétés à null par défaut pour d'éventuels formulaires de saisie.
 *
 * @see Entity
 */
class Category extends Entity
{
	/**
	 * @var int|null PK.
	 */
	public ?int $idCategory = null;

	/**
	 * @var string|null Nom.
	 */
	public ?string $name = null;

	/**
	 * Collection des produits de cette catégorie.
	 *
	 * @var Product[] | null
	 */
	protected ?array $products = null;

	/**
	 * Constructeur.
	 *
	 * @param int|null $idCategory PK.
	 */
	public function __construct(?int $idCategory = null)
	{
		$this->idCategory = $idCategory;
	}

	/**
	 * Retourne un tableau des produits (triés par nom) de cette catégorie, lazy loading.
	 *
	 * @return Product[] Tableau des produits.
	 * @throws DBALException
	 */
	protected function getProducts(): array
	{
		// Si propriété non renseignée, requêter la DB.
		if (!$this->products)
			$this->products = Product::findAllBy(['idCategory' => $this->idCategory], ['name' => 'ASC']);
		return $this->products;
	}
}
