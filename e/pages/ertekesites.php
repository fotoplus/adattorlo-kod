<?php



$error = false;
$msg = false;

if(isset($_POST['save'])) {

	$replace_from = array("ö", "Ö", "NZ","SY");
	$replace_to = array("0", "0", "NY","SZ");

	$code = (isset($_POST['code']) and !empty($_POST['code'])) ? $_POST['code'] : false;
	$date = (isset($_POST['date']) and !empty($_POST['date'])) ? $_POST['date'] : false;
	$receipt_number = (isset($_POST['receipt_number']) and !empty($_POST['receipt_number'])) ? str_replace($replace_from, $replace_to, $_POST['receipt_number']) : false;
	
	if($code) {
		$sql = "SELECT 
				codes.code 
			FROM codes 
			LEFT JOIN sales
				ON sales.code = codes.code
			WHERE
				codes.code LIKE '$code'
				AND sales.code IS NOT NULL
		";

		$result = $mysqli->query($sql);
		
		if ($result->num_rows === 0) {
			if(!$date) {
				$error .= '<span class="error">Nem érkezett dátum, jelezd a hibát és próbáld meg újra.</span><br>';
			}

			if(!$receipt_number) {
				$error .= '<span class="error">Nem érkezett bizonylat szám, jelezd a hibát és próbáld meg újra.</span><br>';
			}
		} else {
			$error .= '<span class="error">Ütközés a(z) <span class="bold">'.$code.'</span> kód használatánál, jelezd a hibát és rögzíts a fogyasztónak egy új kódot.</span><br>';
		}

	} else {
		$error .= '<span class="error">Nem érkezett kód, jelezd a hibát és próbáld meg újra.</span><br>';
	}

	if($error) {

		echo 
<<<HTML
	<header>
		<h1>Hiba</a>
	</header>
	<main>

		<div class="error">{$error}</div>

		<a href="/ertekesites" title="Visszalépés" class="nav-back">Vissza</a>

	</main>
HTML;

	} else {

		try {


			$code = $mysqli->real_escape_string($code);
			$receipt_number = $mysqli->real_escape_string($receipt_number);
			$date = $mysqli->real_escape_string($date);
			$lid = $mysqli->real_escape_string($location['lid']);

			$sql = "INSERT INTO `sales` (`code`, `receipt_number`, `date`,`lid`) VALUES ('" . $code . "','" . $receipt_number . "','" . $date . "','".$lid."')";

			if ($mysqli->query($sql) === TRUE) {
				$msg = "<p>A kódot és az értékesítési adatokat sikeresen rögzítettük.</p>";
			} else {
				$error .=  '<span class="error">Adatbázishiba a rögzítéskor. (SOR:'.__LINE__.')<br>Az adatbázis válasza: ' . $mysqli->error . '</span><br>';
			}

			
		} catch (Exception $e) {
			// Ha valami hiba történt, visszavonjuk a tranzakciót
			$mysqli->rollback();
			$error .= '<span class="error">Adatbázis hiba a(z) <span class="bold">'.$code.'</span> kód rögzítésekor. (SOR:'.__LINE__.') jelezd a hibát és rögzíts a fogyasztónak egy új kódot.<br>
				Az adatbázis válasza:'. $e->getMessage().'</span>';
		}


		echo 
<<<HTML
	<header>
		<h1>A kód nyomtatása</h1>
		{$error}
		{$msg}
		<p>A kódot kinyomtatott formában tudjuk a szükséges tájékoztatással átadni a fogyasztónak.</p>
		<p>Ha a lenti adatok rendben vannak, kattints a nyomtatás gombra.</p>
	</header>
	<main>
		<div class="printableDatas">
			<p>
				Adattörlő kód:<br>
				<span id="printCode" class="bold">{$code}</span>
			<p>
			<p>
				Vásárlás ideje:<br>
				<span id="printDate" class="bold">{$date}</span>
			</p>
			<p>
				Értékesítési bizonylat száma:<br>
				<span id="printReceipt_number" class="bold">{$receipt_number}</span>
			</p>
		</div>
		<button class="btn"  id="print">Nyomtatás</button>
		<a href="/" title="Mentés nélküli visszalépés" class="space">Vissza</a>
	</main>

	<script src="/scripts/print.js"></script>
HTML;

	}

} else {

	/**
	 * Kód felszabadítás
	 * 
	 * A 20 percnél régebben zérolt kódokat felszabadítjuk.
	 * 
	 */
	$sql = 'UPDATE codes c
		LEFT JOIN sales s
			ON c.code = s.code
		SET c.locked=NULL, c.lid=NULL
		WHERE c.locked IS NOT NULL
			AND s.code IS NULL
			AND c.locked < NOW() - INTERVAL 1 DAY ;
	';

	$result = $mysqli->query($sql);
	if(!$result) {
		echo ('<div class="top-error">Hiba! A kód-felszabadító háttérművelet meghiusult. Jelezd a problémát.</div>');
	}

	// Tranzakció kezdése
	$mysqli->autocommit(FALSE);
	$mysqli->begin_transaction();

	try {

		$sql = "SELECT codes.code FROM codes 
			LEFT JOIN sales
				ON sales.code = codes.code
			WHERE
				locked IS NULL
				AND sales.code IS NULL
			LIMIT 1 FOR UPDATE
		";
		$result = $mysqli->query($sql);

		if ($result->num_rows === 0) {
			// Nincs több szabad kód, visszaállítjuk a tranzakciót és kilépünk
			$mysqli->rollback();
			die("Nincs több szabad kód, az adathordozó nem értékesíthető!");
		} else {

			// Az első találatot vesszük, zároljuk a sort, majd frissítjük a locked mezőt
			$row = $result->fetch_assoc();
			$code = $row['code'];
			$lid = $mysqli->real_escape_string($location['lid']);
			$sql = "UPDATE codes SET locked=NOW(), lid='".$lid."' WHERE code='$code'";
			$result = $mysqli->query($sql);

			if (!$result) {
				// Hiba történt, visszaállítjuk a tranzakciót és kilépünk
				$mysqli->rollback();
				die("Hiba a kód zárolása során: " . $mysqli->error);
			} else {
				// Tranzakció véglegesítése
				$mysqli->commit();
			}

		}

	} catch (Exception $e) {
		// Ha valami hiba történt, visszavonjuk a tranzakciót
		$mysqli->rollback();
		echo "Hiba történt: " . $e->getMessage();
	}

	$date = date('Y-m-d H:i:s');

	echo 
<<<HTML

		<main>
			<form method="post" action="/ertekesites" autocomplete="off">
				<input type="hidden" name="code" value="{$code}">
				<fieldset>
					<legend>Új értékesítés</legend>

					<span class="label">Adattörlő kód:</span>
					<div class="code-container">
						<div id="code">{$code}</div>
						<div class="btn" onclick="copyCode()">Másol</div>
					</div>

					<label for="date">Dátum</label>
					<input type="datetime-local" name="date" value="{$date}" required>

					<label for="receipt_number">Értékesítési bizonylat száma</label>
					<input type="text" name="receipt_number" placeholder="Add meg a számla vagy nyugta sorszámát" id="receipt_number" required>
					<div id="receipt_number_validation" class="validation"></div>

					<button name="save" value="save">Mentés</button>
					<a href="/" title="Mentés nélküli visszalépés" class="space">Vissza</a>

				</fieldset>
			</form>
		</main>
		
		<script src="/scripts/barcode_prevent_enter.js"></script>
		<script src="/scripts/code_copy.js"></script>
		<script src="/scripts/receipt_number_validation.js"></script>


HTML;

}




?>



