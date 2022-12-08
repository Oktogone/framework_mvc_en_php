<?php

declare(strict_types=1);

namespace entities;

use peps\core\DBALException;
use peps\core\Entity;

/**
 * Entité Product.
 * Toutes les propriétés à null par défaut pour les éventuels formulaires de saisie.
 *
 * @see Entity
 */
class Product extends Entity
{
	/**
	 * @var int|null PK.
	 */
	public ?int $idProduct = null;

	/**
	 * @var int|null FK de la catégorie.
	 */
	public ?int $idCategory = null;

	/**
	 * @var string|null Nom.
	 */
	public ?string $name = null;

	/**
	 * @var string|null Référence.
	 */
	public ?string $ref = null;

	/**
	 * @var float|null Prix.
	 */
	public ?float $price = null;

	/**
	 * @var Category|null Catégorie de ce produit.
	 */
	protected ?Category $category = null;

	/**
	 * Constructeur.
	 *
	 * @param int|null $idProduct PK.
	 */
	public function __construct(?int $idProduct = null)
	{
		$this->idProduct = $idProduct;
	}

	/**
	 * Retourne la catégorie de ce produit, lazy loading.
	 *
	 * @return Category|null La catégorie.
	 * @throws DBALException
	 */
	protected function getCategory(): ?Category
	{
		// Si propriété non renseignée, requêter la DB.
		if (!$this->category)
			$this->category = Category::findOneBy(['idCategory' => $this->idCategory]);
		return $this->category;
	}
}
