<?php

$subpage['name'] = isset($segments[1]) ? $segments[1] : false;
$subpage['file'] = $pages_dir . 'adatszolgaltatas.'. $subpage['name'] .'.php';


if($subpage and file_exists($subpage['file'])) {
	include($subpage['file']);
} else {
	echo <<<HTML
		<header>
			<h1>Adatszolgáltatás</h1>
			<p>Az adatszolgáltatáshoz az XML álomány generálás alapvetően automatizáltan történik és e-mailben küldi ki a rendszer. Itt az XML gomb megnyomásával kézzel is elkészíthető a fájl.. </p>
		</header>
		<nav>
			<ul>
				<li><a href="/adatszolgaltatas/xml">XML</a></li>
			</ul>
			<a href="/" class="nav-back">Vissza</a>
		</nav>
	HTML;
}

?>