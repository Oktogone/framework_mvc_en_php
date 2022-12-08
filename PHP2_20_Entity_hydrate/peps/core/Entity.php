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
		// TODO
		// Retourner true systématiquement.
		return true;
	}

	/**
	 * {@inheritDoc}
	 * @throws DBALException
	 */
	public function remove(): bool
	{
		// TODO
		return true; // TEMP
	}

	/**
	 * {@inheritDoc}
	 * @throws DBALException
	 */
	public static function findAllBy(array $filters = [], array $sortKeys = [], string $limit = ''): array
	{
		// TODO
		return []; // TEMP
	}

	/**
	 * {@inheritDoc}
	 * @throws DBALException
	 */
	public static function findOneBy(array $filters = []): ?static
	{
		// TODO
		return null; // TEMP
	}
}
