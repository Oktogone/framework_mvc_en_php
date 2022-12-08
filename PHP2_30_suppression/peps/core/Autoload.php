<?php

declare(strict_types=1);

namespace peps\core;

/**
 * Classe 100% statique d'autoload.
 */
final class Autoload
{
	/**
	 * Constructeur privé.
	 */
	private function __construct()
	{
	}

	/**
	 * Initialise l'autoload.
	 * DOIT être appelée depuis le contrôleur frontal EN TOUT PREMIER.
	 * Utilise le chemin ABSOLU des classes depuis la RACINE DU SERVEUR pour fonctionnement correct de SessionDB.write() dans extension session.
	 *
	 * @throws AutoloadException Si variable serveur DOCUMENT_ROOT indéfinie.
	 */
	public static function init(): void
	{
		// Récupération du chemin absolu du contrôleur frontal (index.php) depuis la racine du serveur.
		// Double tentative nécessaire pour fonctionnement sur tous serveurs.
		$documentRoot = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: filter_var($_SERVER['DOCUMENT_ROOT'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
		//var_dump($documentRoot);
		// Si introuvable, déclencher une exception.
		if (!$documentRoot) {
			throw new AutoloadException(AutoloadException::DOCUMENT_ROOT_UNAVAILABLE);
		}
		// Inscrire la fonction d'autolad dans la pile d'autoload.
		spl_autoload_register(function (string $className) use ($documentRoot): void {
			// Construire le chemin absolu de la classe (tout en inversant les antislashes du namespace pour fonctionnement sur tous serveurs).
			$classPath = strtr("{$documentRoot}/{$className}.php", '\\', '/');
			//var_dump($classPath);
			@require $classPath; // @ pour éviter les warnings.
		});
	}
}
