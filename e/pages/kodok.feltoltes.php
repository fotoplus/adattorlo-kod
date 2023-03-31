<?php

/*
if (isset($_POST['submit'])) {
    // Ellenőrizzük, hogy a fájl létezik és sikeresen be lett töltve
    if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] == 0) {
        $file = $_FILES['csv_file']['tmp_name'];

        // Nyissuk meg a fájlt olvasásra
        $handle = fopen($file, "r");

        // Olvassuk be a fájl tartalmát és szúrjuk be a MySQL adatbázisba
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $code = $mysqli->real_escape_string($data[0]); // adatbázisba szúrás előtt az escape-elt kód

            // Adatbázisba szúrás
            $stmt = $mysqli->prepare("INSERT INTO codes (code) VALUES (?)");
            $stmt->bind_param("s", $code);

            if ($stmt->execute()) {
                echo "A(z) $code sikeresen be lett szúrva az adatbázisba.";
            } else {
                echo "Hiba történt a(z) $code szúrása során: " . $mysqli->error;
            }
        }

        // Zárjuk be a fájlt
        fclose($handle);
    } else {
        echo "Hiba történt a fájl feltöltése során.";
    }
} else {
*/

if (isset($_POST['submit'])) {
	$codes=0;
	$added=0;
	$duplicated=0;
	$error=0;
	//$msg='';
	

    // Ellenőrizzük, hogy a fájl létezik és sikeresen be lett töltve
    if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] == 0) {
        $file = $_FILES['csv_file']['tmp_name'];

        // Nyissuk meg a fájlt olvasásra
        $handle = fopen($file, "r");

        // Olvassuk be a fájl tartalmát és szúrjuk be a MySQL adatbázisba
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
			$codes++;
            $code = $mysqli->real_escape_string($data[0]); // adatbázisba szúrás előtt az escape-elt kód

            // Ellenőrizzük, hogy a kód még nincs-e az adatbázisban
            $query = "SELECT COUNT(*) as count FROM codes WHERE code='$code'";
            $result = $mysqli->query($query);
            $row = $result->fetch_assoc();
            $count = $row['count'];
            if ($count == 0) {
                // Adatbázisba szúrás
                $sql = "INSERT INTO codes (code) VALUES ('$code')";
                if ($mysqli->query($sql)) {
                    $msg .= '<span class="added">A(z) <span class="bold">'.$code.'</span> sikeresen hozzáadva.</span><br>'.chr(13);
					$added++;
                } else {
                    $msg .= '<span class="error">Hiba történt a(z) <span class="bold">'.$code.'</span> hozzáadása során: ' . $mysqli->error .'</span><br>'.chr(13);
					$error++;
                }
            } else {
                $msg .= '<span class="duplicated">A(z) <span class="bold">'.$code.'</span> már létezik az adatbázisban.</span><br>'.chr(13);
				$duplicated++;
            }
        }

        // Zárjuk be a fájlt
        fclose($handle);
    } else {
        $msg .= '<span class="errp">Hiba történt a fájl feltöltése során.</span><br>'.chr(13);
		$error++;
    }

echo <<<HTML

<header>
	<h1>Új kódok feltöltése</h1>
	<p>A feltötlés az alábbi eredménnyel zárult:</p>
	<table>
		<tr>
			<td>Feldolgozott sor:</td>
			<td class="bold">{$codes}</td>
		</tr>
		<tr>
			<td>Hozzáadva:</td>
			<td class="bold">{$added}</td>
		</tr>
		<tr>
			<td>Duplikált kód:</td>
			<td class="bold">{$duplicated}</td>
		</tr>
		<tr>
			<td>Egyéb hiba:</td>
			<td class="bold">{$error}</td>
		</tr>
	</table>

	<a href="/kodok/feltoltes" class="back">Vissza</a>

	<hr>

	<h2>A részletes kimenet:</h2>

	<div id="output">{$msg}</div>


	<a href="/kodok/feltoltes" class="back">Vissza</a>


</header>

HTML;


} else {

echo <<<HTML
		<header>
			<h1>Új kódok feltöltése</h1>
			<p>Ezen a felületen a NAV-tól kapott CSV fájllal új kódok tölthetőek fel.</p>
		</header>

		<main>

			<form action="/kodok/feltoltes" method="post" enctype="multipart/form-data">
				<input type="file" name="csv_file" accept=".csv">
				<button type="submit" name="submit" value="upload">Feltöltés</button>
			</form>

		</main>
HTML;

echo <<<HTML
		<nav>
			<a href="/kodok" class="nav-back">Vissza</a>
		</nav>
HTML;

}

?>