<?php

declare(strict_types=1);

namespace views;

use peps\core\Cfg;

$errors = $errors ?? [];
$categories = $categories ?? [];
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
			<a href="/product/list">Produits</a> &gt; Editer
		</div>
		<div class="error"><?= implode('<br/>', $errors) ?></div>
		<form name="form1" action="/product/save" method="POST">
			<input type="hidden" name="idProduct" value="<?= $product?->idProduct ?>" />
			<div class="item">
				<label>Catégorie</label>
				<select name="idCategory">
					<?php
					foreach ($categories as $category) {
						$selected = $category->idCategory === $product?->idCategory ? 'selected' : '';
					?>
						<option value="<?= $category->idCategory ?>" <?= $selected ?>>
							<?= $category->name ?>
						</option>
					<?php
					}
					?>
				</select>
			</div>
			<div class="item">
				<label>Nom</label>
				<input name="name" value="<?= $product?->name ?>" size="20" maxlength="50" />
			</div>
			<div class="item">
				<label>Référence</label>
				<input name="ref" value="<?= $product?->ref ?>" size="10" maxlength="10" />
			</div>
			<div class="item">
				<label>Prix</label>
				<input type="number" name="price" value="<?= $product?->price ?>" step=".01" />
			</div>
			<div class="item">
				<label></label>
				<a href="/product/list"><input type="button" value="Annuler" /></a>
				<input type="submit" name="submit" value="Valider" />
			</div>
		</form>
	</main>
	<footer></footer>
</body>

</html>