<?php

 $sql='SELECT
	 s.id
	,s.code
	,s.receipt_number
	,s.date
	,s.added
	,l.name
FROM sales s
LEFT JOIN locations l
	ON l.id = s.lid
ORDER BY s.date DESC
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
		<td>{$row['date']}</td>
		<td>{$row['code']}</td>
		<td>{$row['receipt_number']}</td>
		<td>{$row['name']}</td>
	</tr>
	";
}

echo <<<HTML
	<header>
		<h1>Átadott kódok</h1>
		<p>Itt időrendben visszafelé haladva láthatjuk a már átadott kódokat.</p>
	</header>

	<main>
		<table>
			<thead>
				<tr>
					<td>Dátum</td>
					<td>Kód</td>
					<td>Bizonylat szám</td>
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