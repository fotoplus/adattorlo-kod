<?php

$subpage['name'] = isset($segments[1]) ? $segments[1] : false;
$subpage['file'] = $pages_dir . 'kodok.'. $subpage['name'] .'.php';

if($subpage and file_exists($subpage['file'])) {
	include($subpage['file']);
} else {
	echo <<<HTML
		<header>
			<h1>Kódok</h1>
			<p>Ezen a felületene a kódokhoz kapcsolódó műveletek végezhetők el.</p>
		</header>
		<nav>
			<ul>
				<li><a href="/kodok/zarolt">Zárolt kódok</a></li>
				<li><a href="/kodok/atadott">Átadott kódok</a></li>
				<li><a href="/kodok/statisztika">Statisztika</a></li>
				<li><a href="/kodok/feltoltes">Feltöltés</a></li>
			</ul>
			<a href="/" class="nav-back">Vissza</a>
		</nav>
	HTML;
}

?>