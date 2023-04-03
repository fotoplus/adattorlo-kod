<?php

 $sql='SELECT
	 c.id
	,c.code
	,c.locked
	,l.name
FROM codes c
LEFT JOIN sales s
	ON c.code = s.code
	LEFT JOIN locations l
	ON c.lid = l.id
WHERE c.locked IS NOT NULL
	AND s.code IS NULL
ORDER BY c.locked DESC

/*LIMIT 0,100*/
 ';

$result = $mysqli->query($sql);

echo

<<<HTML

HTML;

$html =false;
while($row = $result->fetch_assoc()) {
	$html .="
	<tr>
		<td>{$row['code']}</td>
		<td>{$row['locked']}</td>
		<td>{$row['name']}</td>
	</tr>
	";
}

echo <<<HTML
	<header>
		<h1>Zárolt kódok</h1>
		<p>Itt időrendben visszafelé haladva láthatjuk a rendszer által (ideiglenesen) zárolt, még nem feloldott kódokat, és a zárolás helyét. A zárolt kódok felszabadítása 1 nap elteltével, automatikusan az "Értékesítés" felület által történik, annak megnyitásakor.</p>
	</header>

	<main>
		<table>
			<thead>
				<tr>
					<td>Kód</td>
					<td>Zárolva</td>
					<td>Telephely</td>
				</tr>
			</thead>
			<tbody>
				{$html}
			</tbody>
		</table>
	</main>

	<a href="/kodok" title="Vissza" class="nav-back">Vissza</a>	
HTML;

?>