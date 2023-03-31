<?php

$sql = 'SELECT id FROM codes';
$result = $mysqli->query($sql);
$num_codes = $result->num_rows;

$sql = 'SELECT id FROM sales';
$result = $mysqli->query($sql);
$num_sales = $result->num_rows;

$sql = 'SELECT c.id FROM codes c LEFT JOIN sales s ON c.code = s.code WHERE s.code IS NULL AND c.locked IS NOT NULL';
$result = $mysqli->query($sql);
$num_locked = $result->num_rows;


echo <<<HTML
	<header>
		<h1>Statisztika</h1>
	</header>

	<main>

		<table>
			<tr>
				<td>Kódok összesen</td>
				<td>{$num_codes}</td>
			</tr>
			<tr>
				<td>Átadott kódok</td>
				<td>{$num_sales}</td>
			</tr>
			<tr>
				<td>Zárolt kódok</td>
				<td>{$num_locked}</td>
			</tr>
		</table>

		<a href="/kodok" title="Vissza" class="nav-back">Vissza</a>
	<main>

HTML;
?>