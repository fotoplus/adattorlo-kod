<!doctype html>
<html class="no-js" lang="hu">

	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="robots" content="noindex, nofollow, noarchive">
		<link rel="stylesheet" href="/styles/main.css?<?php echo date('U'); ?>">
		<?php
			$page_style = '/styles/' . $page['name'] .'.css';
			if(file_exists('.'.$page_style)) {
				echo '<link rel="stylesheet" href="' . $page_style . '?' . date('U') . '">';
			}
		?>
		<title>
			<?php
				echo 'Adattörlő kód kezelő';
				if(isset($location['name'])) {
					echo ' [' . $location['name'] . ']';
				}
				
			?>
		</title>
		<meta name="description" content="Weboldal">
	</head>


	<body>

		<div id="container">