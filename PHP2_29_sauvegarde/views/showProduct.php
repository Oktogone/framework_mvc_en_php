<?php

declare(strict_types=1);

namespace views;

use peps\core\Cfg;

$product = $product ?? null;

?>
<!DOCTYPE html>
<html lang="fr">

<head>
	<meta charset="UTF-8" />
	<title><?= Cfg::get('appTitle') ?></title>
	<link rel="stylesheet" href="/assets/css/acme.css" />
</head>

<body>
	<header></header>
	<main>
		<div class="category">
			<a href="/product/list">Produits</a> &gt; <?= $product?->name ?>
		</div>
		<div id="detailProduct">
			<img src="<?= $product?->imagePath ?>" alt="<?= $product?->name ?>" />
			<div>
				<div class="price"><?= $product?->formattedPrice ?></div>
				<div class="category">catégorie<br />
					<?= $product?->category->name ?>
				</div>
				<div class="ref">référence<br />
					<?= $product?->ref ?>
				</div>
			</div>
		</div>
	</main>
	<footer></footer>
</body>

</html>