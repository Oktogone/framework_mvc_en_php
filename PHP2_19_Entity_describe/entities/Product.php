<?php

declare(strict_types=1);

namespace entities;

use peps\core\Entity;

class Product extends Entity
{
	public ?int $idProduct = null;
	public ?int $idCategory = null;
	public ?string $name = null;
	public ?string $ref = null;
	public ?float $price = null;

	public function __construct()
	{
		var_dump(self::describe());
	}
}
