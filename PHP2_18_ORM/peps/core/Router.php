<?php

declare(strict_types=1);

namespace peps\core;

use Error;

/**
 * Classe 100% statique de routage.
 * Réalise le routage initial et offre 5 méthodes de routage:
 *   render(): rendre une vue.
 *   text(): envoyer du texte brut (text/plain).
 *   json(): envoyer du JSON (application/json).
 *   download(): envoyer un fichier en flux binaire.
 *   redirect(): rediriger côté client.
 * Toutes ces méthodes ARRETENT l'exécution.
 */
final class Router
{
	/**
	 * Constructeur privé.
	 */
	private function __construct()
	{
	}

	/**
	 * Réalise le routage initial.
	 * Analyse la requête du client, détermine la route et invoque la méthode appropriée du contrôleur approprié.
	 *
	 * @throws RouterException Si fichier des routes introuvable ou invalide, ou si la méthode demandée est introuvable.
	 */
	public static function route(): void
	{
		// Récupérer le verbe HTTP et l'URI de la requête client.
		$verb = filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: filter_var($_SERVER['REQUEST_METHOD'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
		$uri = filter_input(INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
		// Si pas de verbe ou pas d'URI, erreur 404.
		if (!$verb || !$uri)
			self::render(Cfg::get('ERROR_404_VIEW'));
		// Charger la table de routage JSON.
		$routesJSON = file_get_contents(Cfg::get('ROUTES_FILE'));
		// Si fichier introuvable, déclencher une exception.
		if (!$routesJSON)
			throw new RouterException(RouterException::JSON_ROUTES_FILE_UNAVAILABLE);
		// Décoder le JSON.
		$routes = json_decode($routesJSON);
		// Si fichier JSON invalide, déclencher une exception.
		if (!$routes)
			throw new RouterException(RouterException::JSON_ROUTES_FILE_CORRUPTED);
		// Parcourir la table de routage.
		foreach ($routes as $route) {
			// Utiliser l'expression régulière de l'URI avec un slash final optionnel. Délimiteur @ au lieu de /.
			$regexp = "@^{$route->uri}/?$@";
			// Si une route correspondante est trouvée...
			if (!strcasecmp($route->verb, $verb) && preg_match($regexp, $uri, $matches)) {
				// Supprimer le premier élément du tableau (expression elle-même).
				array_shift($matches);
				// Si paramètres capturés mais pas de paramètres prévus ou nombre différent, exception.
				if ($matches && (!isset($route->params) || count($matches) !== count($route->params)))
					throw new RouterException(RouterException::WRONG_PARAMS_ARRAY);
				// Si paramètres, combiner les noms des paramètres avec les valeurs de l'URL pour obtenir un tableau associatif.
				$assocParams = $matches ? array_combine($route->params, $matches) : [];
				// Séparer le nom du contrôleur du nom de la méthode.
				[$controller, $method] = explode('.', $route->method);
				// Préfixer le nom du contrôleur avec son namespace (pas de "use"). Antislash initial obligatoire ici.
				$controller = '\\' . Cfg::get('CONTROLLERS_NAMESPACE') . '\\' . $controller;
				// Invoquer la méthode du contrôleur en lui passant le tableau des paramètres.
				try {
					$controller::$method($assocParams);
				} catch (Error) {
					throw new RouterException(RouterException::CONTROLLER_METHOD_FAILED . " Method: {$controller}.{$method}");
				}
				// Retourner pour sortir de la boucle.
				return;
			}
		}
		// Si aucune route trouvée, erreur 404.
		self::render(Cfg::get('ERROR_404_VIEW'));
	}

	/**
	 * Rend une vue et arrête l'exécution.
	 *
	 * @param string $view Nom de la vue (ex: 'test.php').
	 * @param array $params Tableau associatif des paramètres à transmettre à la vue.
	 * @throws RouterException Si $params comporte une clé 'view' ou 'params', ou si la vue demandée est introuvable.
	 */
	public static function render(string $view, array $params = []): never
	{
		// Transformer chaque clé du tableau associatif en variable.
		if (extract($params, EXTR_SKIP) < count($params))
			throw new RouterException(RouterException::PARAMS_ARRAY_CONTAINS_INVALID_KEY);
		// Insérer la vue.
		try {
			@require Cfg::get('VIEWS_DIR') . "/{$view}"; // @ pour éviter les warnings.
		} catch (Error) {
			throw new RouterException(RouterException::VIEW_NOT_FOUND);
		}
		// Arrêter l'exécution.
		exit;
	}

	/**
	 * Envoie au client une chaîne en texte brut et arrête l'exécution.
	 *
	 * @param string $text Chaîne de texte.
	 */
	public static function text(string $text): never
	{
		// Paramétrer l'entête HTTP du texte.
		header('Content-Type:text/plain');
		// Envoyer la chaîne texte au client puis arrêter l'exécution.
		exit($text);
	}

	/**
	 * Envoie au client une chaîne JSON et arrête l'exécution.
	 *
	 * @param string $json Chaîne JSON.
	 */
	public static function json(string $json): never
	{
		// Paramétrer l'entête HTTP du JSON.
		header('Content-Type: application/json');
		// Envoyer la chaîne JSON au client puis arrêter l'exécution.
		exit($json);
	}

	/**
	 * Envoie au client un fichier pour download (ou intégration comme par exemple une image) et arrête l'exécution.
	 *
	 * @param string $filePath Chemin complet du fichier depuis la racine de l'application.
	 * @param string $mimeType Type MIME du fichier.
	 * @param string $fileName Nom du fichier proposé au client.
	 * @throws RouterException Si fichier inexistant.
	 */
	public static function download(string $filePath, string $mimeType, string $fileName = 'File'): never
	{
		// Si le fichier n'existe pas, déclencher une exception.
		if (!file_exists($filePath))
			throw new RouterException(RouterException::FILE_NOT_FOUND);
		// Paramétrer l'entête HTTP.
		header("Content-Type: {$mimeType}");
		header('Content-Transfer-Encoding: binary');
		header('Content-Length: ' . @filesize($filePath));
		header("Content-Disposition: attachment; filename={$fileName}");
		// Lire le fichier et l'envoyer vers le client.
		readfile($filePath);
		// Arrêter l'exécution.
		exit;
	}

	/**
	 * Redirige côté client.
	 * Envoie la requête vers le client pour demander une redirection vers une URI puis arrête l'exécution.
	 *
	 * @param string $uri URI
	 */
	public static function redirect(string $uri): never
	{
		// Envoyer la demande de redirection vers le client.
		header("Location: {$uri}");
		// Arrêter l'exécution.
		exit;
	}
}
