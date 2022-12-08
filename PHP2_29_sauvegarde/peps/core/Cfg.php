<?php

declare(strict_types=1);

namespace peps\core;

/**
 * Classe 100% statique de configuration par défaut de l'application.
 * DOIT être étendue dans l'application par une classe ou plusieurs sous-classe de configuration.
 * Typiquement, une classe de configuration générale et des sous-classes par serveur.
 */
class Cfg
{
	/**
	 * Tableau associatif des constantes de configuration.
	 *
	 * @var array
	 */
	private static array $constants = [];

	/**
	 * Constucteur privé.
	 */
	private function __construct()
	{
	}

	/**
	 * Inscrit les constantes par défaut sous la forme de paires clé/valeur.
	 * DOIT être redéfinie dans chaque sous-classe pour y inscrire les constantes de l'application en invoquant parent::init() en première instruction.
	 * Cette méthode DOIT être PUBLIC dans la sous-classe sur laquelle elle sera appelée par le contrôleur frontal.
	 * Les clés en SNAKE_CASE inscrites ici sont LES SEULES accessibles aux classes Peps.
	 * Les clés ajoutées par les sous-classes DEVRAIENT être en camelCase.
	 *
	 * @return void
	 */
	protected static function init(): void
	{
		// Chemin du fichier JSON des routes depuis la racine de l'application.
		self::register('ROUTES_FILE', 'cfg/routes.json');

		// Namespace des contrôleurs (sans antislash initial).
		self::register('CONTROLLERS_NAMESPACE', 'controllers');

		// Chemin du répertoire des vues depuis la racine de l'application.
		self::register('VIEWS_DIR', 'views');

		// Nom de la vue affichant l'erreur 404.
		self::register('ERROR_404_VIEW', 'error404.php');
	}

	/**
	 * Inscrit une constante (paire clé/valeur) dans le tableau des constantes.
	 *
	 * @param string $key Clé.
	 * @param mixed $val Valeur.
	 * @return void
	 */
	protected final static function register(string $key, mixed $val): void
	{
		self::$constants[$key] = $val;
	}

	/**
	 * Retourne la valeur de la constante à partir de sa clé.
	 * Retourne null si clé inexistante.
	 *
	 * @param string $key Clé.
	 * @return mixed Valeur.
	 */
	public final static function get(string $key): mixed
	{
		return self::$constants[$key] ?? null;
	}
}
