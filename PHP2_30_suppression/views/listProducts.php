<?php

declare(strict_types=1);

namespace views;

use peps\core\Cfg;

$categories = $categories ?? [];

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
		<?php
		foreach ($categories as $category) {
		?>
			<div class="category">
				<a href="/product/create/<?= $category->idCategory ?>">
					<img class="ico" src="/assets/img/icons/create.svg" alt="Ajouter un produit dans cette catégorie" />
				</a>
				<?= $category->name ?>
			</div>
			<?php
			foreach ($category->products as $product) {
				// Ajouter dynamiquement le chemin de l'image au produit.
				$product->imagePath = "/assets/img/products/{$product->idCategory}_" . $product->idProduct % 10 . ".png";
			?>
				<div class="blockProduct">
					<a href="/product/show/<?= $product->idProduct ?>">
						<img class="thumbnail" src="<?= $product->imagePath ?>" alt="<?= $product->name ?>" />
						<div class="name"><?= $product->name ?></div>
					</a>
					<a class="ico update" href="/product/update/<?= $product->idProduct ?>">
						<img src="/assets/img/icons/update.svg" alt="Modifier" />
					</a>
					<a class="ico remove" href="/product/remove/<?= $product->idProduct ?>">
						<img src="/assets/img/icons/remove.svg" alt="Supprimer" />
					</a>
				</div>
		<?php
			}
		}
		?>
	</main>
	<footer></footer>
</body>

</html>