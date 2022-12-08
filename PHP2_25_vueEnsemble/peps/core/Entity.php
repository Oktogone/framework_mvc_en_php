<?php

declare(strict_types=1);

namespace peps\core;

use ReflectionClass;
use ReflectionProperty;

/**
 * Implémentation de la persistance ORM pour les classes entités via DBAL.
 * Les classes entités DEVRAIENT étendre cette classe.
 * Trois règles à respecter pour profiter de cette implémentation par défaut.
 * Sinon, redéfinir la méthode describe() dans les classes entités.
 * -1- Classes et tables portent le même nom selon cet exemple: classe 'TrucChose', table 'TrucChose'.
 * -2- PK auto-incrémentée nommée selon cet exemple: table 'TrucChose', PK 'idTrucChose'.
 * -3- Chaque colonne correspond à une propriété PUBLIC du même nom. Les autres propriétés NE sont PAS PUBLIC.
 *
 * @see ORM
 */
class Entity extends ORM
{
	/**
	 * Constructeur privé.
	 */
	private function __construct()
	{
	}

	/**
	 * Retourne un tableau associatif décrivant la correspondance ORM d'une entité comme suit :
	 * ['table' => {nomTable}, 'pk' => {nomPK}, 'propertiesAndColumns' => {[{nomPropriete} => {nomColonne}, ...]}]
	 * Repose sur les trois règles ci-dessus. Redéfinir dans les classes entités si nécessaire.
	 *
	 * @return array Tableau associatif.
	 */
	protected static function describe(): array
	{
		// La classe effectivement utilisée ne doit pas être Entity elle-même.
		if (static::class === self::class)
			throw new EntityException(EntityException::WRONG_USAGE_OF_ENTITY_CLASS_ITSELF);
		// Récupérer le nom court (pas pleinement qualifié) de la classe (effectivement utilisée) donc de la table correspondante.
		$rc = new ReflectionClass(static::class);
		$tableName = $rc->getShortName();
		// Construire le nom de la PK à partir du nom de la table.
		$pkName = "id{$tableName}";
		// Récupérer le tableau des propriétés publiques de la classe.
		$properties = $rc->getProperties(ReflectionProperty::IS_PUBLIC);
		// Initialiser le tableau associatif des noms de propriétés et de colonnes.
		$propertiesAndColumnsNames = [];
		// Pour chaque propriété, compléter le tableau.
		foreach ($properties as $property)
			$propertiesAndColumnsNames[$property->getName()] = $property->getName();
		return ['table' => $tableName, 'pk' => $pkName, 'propertiesAndColumns' => $propertiesAndColumnsNames];
	}

	/**
	 * {@inheritDoc}
	 * @throws DBALException
	 */
	public function hydrate(): bool
	{
		// Récupérer la description ORM nécessaire.
		['table' => $tableName, 'pk' => $pkName] = static::describe();
		// Si PK non renseignée, retourner false.
		if (!$this->$pkName)
			return false;
		// Construire la requête SELECT.
		$q = "SELECT * FROM {$tableName} WHERE {$pkName} = :{$pkName}";
		$params = [":{$pkName}" => $this->$pkName];
		// Exécuter la requête et hydrater $this.
		return DBAL::get()->xeq($q, $params)->into($this);
	}

	/**
	 * {@inheritDoc}
	 * @throws DBALException
	 */
	public function persist(): bool
	{
		// Récupérer la description ORM nécessaire.
		['table' => $tableName, 'pk' => $pkName, 'propertiesAndColumns' => $propertiesAndColumnsNames] = static::describe();
		// Initialiser les éléments de requêtes et le tableau des paramètres.
		$strInsertColumns = $strInsertValues = $strUpdate = '';
		$params = [];
		// Pour chaque champ ORM, construire les éléments des requêtes SQL et compléter les paramètres.
		foreach ($propertiesAndColumnsNames as $propertyName => $columnName) {
			$strInsertColumns .= "{$columnName},";
			$strInsertValues .= ":{$columnName},";
			$strUpdate .= "{$columnName}=:{$columnName},";
			$params[":{$columnName}"] = $this->$propertyName;
		}
		// Supprimer la dernière virgule de chaque élément de requête.
		$strInsertColumns = rtrim($strInsertColumns, ',');
		$strInsertValues = rtrim($strInsertValues, ',');
		$strUpdate = rtrim($strUpdate, ',');
		// Créer les requêtes et les paramètres SQL.
		$strInsert = "INSERT INTO {$tableName} ({$strInsertColumns}) VALUES({$strInsertValues})";
		$strUpdate = "UPDATE {$tableName} SET {$strUpdate} WHERE {$pkName} = :__ID__"; // Symbole :{$pkName} déjà dans $strUpdate.
		$paramsInsert = $paramsUpdate = $params;
		$paramsUpdate[':__ID__'] = $this->$pkName;
		// Exécuter la requête INSERT ou UPDATE et, si INSERT, récupérer la PK auto-incrémentée.
		$dbal = DBAL::get();
		$this->$pkName ? $dbal->xeq($strUpdate, $paramsUpdate) : $this->$pkName = $dbal->xeq($strInsert, $paramsInsert)->pk();
		// Retourner true systématiquement.
		return true;
	}

	/**
	 * {@inheritDoc}
	 * @throws DBALException
	 */
	public function remove(): bool
	{
		// Récupérer la description ORM nécessaire.
		['table' => $tableName, 'pk' => $pkName] = static::describe();
		// Si PK non renseignée, retourner false.
		if (!$this->$pkName)
			return false;
		// Créer la requête, l'exécuter et retourner la conclusion.
		$q = "DELETE FROM {$tableName} WHERE {$pkName} = :{$pkName}";
		$params = [":{$pkName}" => $this->$pkName];
		return (bool)DBAL::get()->xeq($q, $params)->getNb();
	}

	/**
	 * {@inheritDoc}
	 * @throws DBALException
	 */
	public static function findAllBy(array $filters = [], array $sortKeys = [], string $limit = ''): array
	{
		// Récupérer la description ORM nécessaire.
		['table' => $tableName] = static::describe();
		// Initialiser la requête SQL et ses paramètres.
		$q = "SELECT * FROM {$tableName}";
		$params = [];
		// Si filtres présents...
		if ($filters) {
			// Construire la clause WHERE.
			$q .= " WHERE";
			foreach ($filters as $col => $val) {
				$q .= " {$col} = :{$col} AND";
				$params[":{$col}"] = $val;
			}
			// Supprimer le dernier ' AND'.
			$q = rtrim($q, ' AND');
		}
		// Si clés de tri présents...
		if ($sortKeys) {
			// Construire la clause ORDER BY.
			$q .= " ORDER BY";
			foreach ($sortKeys as $col => $sortOrder)
				$q .= " {$col} {$sortOrder},";
			// Supprimer la dernière virgule.
			$q = rtrim($q, ',');
		}
		// Si limite, ajouter la clause LIMIT.
		if ($limit)
			$q .= " LIMIT {$limit}";
		// Exécuter la requête et retourner le tableau.
		return DBAL::get()->xeq($q, $params)->findAll(static::class);
	}

	/**
	 * {@inheritDoc}
	 * @throws DBALException
	 */
	public static function findOneBy(array $filters = []): ?static
	{
		return static::findAllBy($filters, [], '1')[0] ?? null;
	}
}
