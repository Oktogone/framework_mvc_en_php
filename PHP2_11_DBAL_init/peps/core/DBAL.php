<?php

declare(strict_types=1);

namespace peps\core;

use Error;
use PDO;
use PDOException;
use PDOStatement;

/**
 * DBAL (via PDO).
 * Design Pattern Singleton.
 */
final class DBAL
{
	/**
	 * Options de connexion communes à toutes les DB :
	 *   - Gestion des erreurs basée sur des exceptions.
	 *   - Typage des colonnes respecté.
	 *   - Requêtes réellement préparées plutôt que simplement simulées.
	 */
	private const OPTIONS = [
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_STRINGIFY_FETCHES => false,
		PDO::ATTR_EMULATE_PREPARES => false
	];

	/**
	 * Instance Singleton.
	 *
	 * @var self|null
	 */
	private static ?self $instance = null;

	/**
	 * Instance de PDO.
	 *
	 * @var PDO|null
	 */
	private ?PDO $db = null;

	/**
	 * Instance de PDOStatement.
	 *
	 * @var PDOStatement|null
	 */
	private ?PDOStatement $stmt = null;

	/**
	 * Nombre d'enregistrements retrouvés (SELECT) ou affectés (NON SELECT) par la dernière requête.
	 *
	 * @var int|null
	 */
	private ?int $nb = null;

	/**
	 * Constructeur privé.
	 */
	private function __construct()
	{
	}

	/**
	 * Crée l'instance Singleton et l'instance PDO encapsulée.
	 *
	 * @param string $driver Driver DB.
	 * @param string $host Hôte DB.
	 * @param integer $port Port de l'hôte DB.
	 * @param string $dbName Nom de la base de données.
	 * @param string $log Identifiant de l'utilisateur DB.
	 * @param string $pwd Mot de passe de l'utilisateur DB.
	 * @param string $charset Jeu de caractères.
	 * @return void
	 * @throws DBALException Si la connexion PDO échoue.
	 */
	public static function init(
		string $driver,
		string $host,
		int    $port,
		string $dbName,
		string $log,
		string $pwd,
		string $charset
	): void {
		// Si déjà initialisée, ne rien faire.
		if (self::$instance)
			return;
		// Créer la chaîne DSN.
		$dsn = "{$driver}:host={$host};port={$port};dbname={$dbName};charset={$charset}";
		// Créer l'instance Singleton.
		self::$instance = new self();
		// Créer l'instance PDO.
		try {
			self::$instance->db = new PDO($dsn, $log, $pwd, self::OPTIONS);
		} catch (PDOException) {
			throw new DBALException(DBALException::DB_CONNECTION_FAILED);
		}
	}

	/**
	 * Retourne l'instance Singleton.
	 * La méthode init() DEVRAIT avoir été appelée au préalable.
	 *
	 * @return self|null Instance Singleton ou null si init() pas encore appelée.
	 */
	public static function get(): ?static
	{
		return self::$instance;
	}

	/**
	 * Exécute une requête SQL.
	 * Par sécurité, si requête NON-SELECT non préparée, l'instance de PDOStatement est remise à null.
	 * Retourne $this pour chaînage.
	 *
	 * @param string $q Requête SQL.
	 * @param array|null $params Tableau associatifs des paramètres (optionnel).
	 * @return self $this pour chaînage.
	 * @throws DBALException Si requête ou paramètres SQL incorrects.
	 */
	public function xeq(string $q, ?array $params = null): static
	{
		// TODO
		// Retourner $this.
		return $this;
	}

	/**
	 * Retourne le nombre d'enregistrements retrouvés (SELECT) ou affectés (NON-SELECT) par la dernière requête exécutée.
	 *
	 * @return int|null Le nombre d'enregistrements.
	 */
	public function getNb(): ?int
	{
		return $this->nb;
	}

	/**
	 * Retourne un tableau d'instances d'une classe donnée en exploitant le dernier jeu d'enregistrements.
	 * Une requête SELECT DEVRAIT avoir été exécutée préalablement.
	 *
	 * @param string $className La classe donnée.
	 * @return array Tableau d'instances de la classe donnée.
	 * @throws DBALException Si classe inexistante.
	 */
	public function findAll(string $className = 'stdClass'): array
	{
		// TODO
		return [];// TEMP
	}

	/**
	 * Retourne une instance d'une classe donnée en exploitant le premier des enregistrements du dernier jeu.
	 * Une requête SELECT (typiquement retrouvant au maximum un enregistrement) DEVRAIT avoir été exécutée préalablement.
	 * Retourne null si aucun recordset ou recordset vide.
	 *
	 * @param string $className La classe donnée.
	 * @return object|null L'instance de la classe donnée ou null.
	 * @throws DBALException Si classe inexistante.
	 */
	public function findOne(string $className = 'stdClass'): ?object
	{
		// TODO
		return null; // TEMP
	}

	/**
	 * Hydrate une instance donnée en exploitant le premier enregistrement du dernier jeu.
	 * Une requête SELECT (typiquement retrouvant au maximum un enregistrement) DEVRAIT avoir été exécutée préalablement.
	 *
	 * @param object $obj Instance donnée à hydrater.
	 * @return boolean True si l'hydratation a réussi.
	 */
	public function into(object $obj): bool
	{
		// TODO
		return true; // TEMP
	}

	/**
	 * Retourne la dernière PK auto-incrémentée.
	 * Retourne 0 si aucune PK.
	 *
	 * @return integer PK
	 */
	public function pk(): int
	{
		// TODO
		return 0; // TEMP
	}

	/**
	 * Démarre une transaction.
	 * Retourne $this pour chaînage.
	 *
	 * @return self $this pour chaînage.
	 */
	public function start(): static
	{
		// TODO
		// Retourner $this.
		return $this;
	}

	/**
	 * Définit un point de restauration dans la transaction en cours.
	 * Retourne $this pour chaînage.
	 *
	 * @param string $label Nom du point de restauration.
	 * @return self $this pour chaînage.
	 * @throws DBALException
	 */
	public function savepoint(string $label): static
	{
		// TODO
		// Retourner $this.
		return $this;
	}

	/**
	 * Effectue un rollback au point de restauration donné ou au départ si absent.
	 * Retourne $this pour chaînage.
	 *
	 * @param string|null $label Nom du point de restauration (optionnel).
	 * @return self $this pour chaînage.
	 * @throws DBALException
	 */
	public function rollback(?string $label = null): static
	{
		// TODO
		// Retourner $this.
		return $this;
	}

	/**
	 * Valide la transaction en cours.
	 * Retourne $this pour chaînage.
	 *
	 * @return self $this pour chaînage.
	 */
	public function commit(): static
	{
		// TODO
		// Retourner $this.
		return $this;
	}
}
