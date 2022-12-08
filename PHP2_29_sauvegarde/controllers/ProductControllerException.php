<?php

declare(strict_types=1);

namespace controllers;

use Exception;

/**
 * Exceptions en lien avec ProductController.
 * Classe 100% statique.
 *
 * @see ProductController
 */
final class ProductControllerException extends Exception
{
	// Messages d'erreur.
	public const WRONG_NAME = "Nom absent ou invalide.";
	public const WRONG_REF = "Référence absente ou invalide.";
	public const WRONG_PRICE = "Prix absent ou invalide.";
	public const PERSISTANCE_ERROR = "Catégorie absente ou inexistante ou référence déjà existante.";
}
