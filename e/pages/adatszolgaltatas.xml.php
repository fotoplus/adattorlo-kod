<?php

/**
 * Ez kissé logikátlanul lett összerakva.
 * 
 * 
 * 
 */


$generate_xml = false;
$download_xml = false;
$provision = false;
$sendmail=false;
$error=false;
$xml=false;


/**
 * Beállítjuk a vizgsált időszakot
 * 
 * A viszgált időszak mindíg az előző hét hétfőjétől vasárnapjáig tart.
 * 
 */
$kezdo_datum = date("Y-m-d", strtotime("monday last week"));
$zaro_datum = date("Y-m-d", strtotime("sunday last week",));

/**
 * Ellenőrizzük, hogy a vizsgált időszakról létrehoztunk-e már bejegyzést (és XML-t)
 * 
 * Ez azért szükséges, mert lehet hogy ez a script cron műveletből már lefutott, 
 * és mi most menüből nyitottuk meg az oldalt.
 * 
 */
$sql_provision = 'SELECT * 
		FROM data_provisions
		WHERE
			interval_start LIKE "' . $kezdo_datum . '"
			AND
			interval_end LIKE "' . $zaro_datum . '"
';

$result_provision = $mysqli->query($sql_provision);
if ($result_provision->num_rows == 0) {
	// Ha nincs még bejegyzés, akkor létre kell hozni, ezért a provision változót true-ra állítjuk
	$provision=true;

	#$generate_xml=true;
	#$sendmail='send_xml';

} else if($result_provision->num_rows == 1) {
	// Ha van 1 db bejegyzés, akkor nem kell generálni adatot, hanem a meglévő XML-t le lehet tölteni.
	$download_xml = true;
} else {
	// Ez elvileg nem lehetséges, de ha mégis, akkor hibát jelezünk
	$error=('Dupliáklt bejegyzés!');
}


// Ha a provision változó true, akkor le kell kérni az értékesítési adatokat (ha vannak)
if($provision and !$error) {
	$sql = 'SELECT id, date, receipt_number, code 
			FROM sales
			WHERE date BETWEEN "'.$kezdo_datum.'" AND "'.$zaro_datum.'"
		';

	$result = $mysqli->query($sql);
	if ($result->num_rows > 0) {
		// Ha a vizsgált időszakban volt értékesítés, akkor létre kell hozni az XML-t az adatszolgáltatáshoz
		$generate_xml = true;
	}
}

// Ha az előző vizsgálat szerint volt értékesítés, akkor a generate_xml true lett és megyünk tovább (ha nincs hiba)
if($generate_xml and !$error) {

	// Csoportokra bontás
	$i = 0; // Csoport számláló
	$n = 0; // Sor számláló
	$maxsor = 35;

	$torzs['adoszam'] = str_replace('-','',$torzs['adoszam']);

	$xml ='<?xml version="1.0" encoding="utf-8"?>
	<nyomtatvanyok xmlns="http://www.apeh.hu/abev/nyomtatvanyok/2005/01">

		<nyomtatvany>
			<nyomtatvanyinformacio>
				<nyomtatvanyazonosito>AATKOD</nyomtatvanyazonosito>
				<nyomtatvanyverzio>1.0</nyomtatvanyverzio>
				<adozo>
				<nev>' . $torzs['cegnev'] . '</nev>
				<adoszam>' . $torzs['adoszam'] . '</adoszam>
				</adozo>
			</nyomtatvanyinformacio>
			<mezok>
	';

	// Az előző $sql-ből lekért adatokat feldolgozzuk
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
				
			// Csoport kezdete
			if ($n % ($maxsor+1) == 0) {
				$i++;
				$lap=$i;
				$lapszam=sprintf("%04d", $lap);

				$xml .= '
				<mezo eazon="01' . $lapszam . 'B001A">' . $lap .'</mezo>
				<mezo eazon="01' . $lapszam . 'B002A">' . $torzs['cegnev'] . '</mezo>
				<mezo eazon="01' . $lapszam . 'B003A">' . $torzs['adoszam'] . '</mezo>
				';
				$n=1;
			}
			
			// Sor kiírása
			$sorszam=sprintf("%04d", $n);
			$xml_adat['bizonylatszam']=$row['receipt_number'];
			$xmal_adat['datum']= date("Ymd", strtotime($row['date']));
			$xmal_adat['kod']=str_replace('-','',$row['code']);

			$xml .= '

				<mezo eazon="01' . $lapszam . 'C' . $sorszam . 'AA">' . $xml_adat['bizonylatszam'] . '</mezo>
				<mezo eazon="01' . $lapszam . 'C' . $sorszam . 'BA">' . $xmal_adat['datum'] . '</mezo>
				<mezo eazon="01' . $lapszam . 'C' . $sorszam . 'CA">' . $xmal_adat['kod'] . '</mezo>
			';

			$n++;

	/* Ez nem tudom miért van itt, vagy miért van kikommentelve
			// Csoport vége
			if ($n % $maxsor == 0 || $n == $result->num_rows) {
			}
	*/

		}
	}

	$xml .= '
				<mezo eazon="0A0001C010A">' . $torzs['adoszam'] . '</mezo>
				<mezo eazon="0A0001C011A">' . $torzs['cegnev'] . '</mezo>
			</mezok>
		</nyomtatvany>
	</nyomtatvanyok>
	';

	/**
	 * 
	 * Ha a provision változó true, akkor az adatbázisba is be kell szúrni az XML-t,
	 * bár ebben az ágban amúgy sew lennénk benne ha nem lenne true, 
	 * mert a generate_xml ezért lett ture
	 * 
	 */
	if($provision) {
		$insert_xml = $mysqli->real_escape_string($xml);


		$sql = 'INSERT INTO `data_provisions`
			(
				`interval_start`
				,`interval_end`
				,`xml`
			)
			VALUES (
				"'. $kezdo_datum .'"
				,"'. $zaro_datum .'"
				,"'. $insert_xml .'"
			)
		';

		if(!$mysqli->query($sql)) {
			$error=('Adatbázis hiba!');
		} 
	}

} 

/**
 * 
 * Ha a provision true, azaz most kell adatot küldeni,
 * akkor a sendmail értékét aszerint állítjuk be, hogy a vizsgált időszakban volt-e értékesítés,
 * azaz generáltunk e xml-t vagy sem.
 * 
 * 
 */
if($provision and $xml and !$error) {
	$sendmail = 'send_xml';
} else if($provision and !$xml and !$error) {
	$sendmail = 'no_xml';
}

/**
 * 
 * Ha a sendmail true, azaz küldeni kell az emailt, akkor a PHPMailer-t használva küldjük el az emailt.
 * A tartalma pedig attól függ, hogy volt-e értékesítés.
 * 
 */
if($sendmail) {

	require 'e/credentials/email.php';
	require 'e/vendor/PHPMailer/src/PHPMailer.php';
	require 'e/vendor/PHPMailer/src/Exception.php';
	require 'e/vendor/PHPMailer/src/SMTP.php';


	// SMTP konfiguráció beállítása
	$mail = new PHPMailer\PHPMailer\PHPMailer(true);
	$mail->isSMTP();
	$mail->CharSet = "UTF-8";
	#$mail->isHTML(true);
	$mail->Host = $email['host'];
	$mail->SMTPAuth = true;
	$mail->Username = $email['username'];
	$mail->Password = $email['auth_password'];
	$mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
	$mail->Port = 587;

	
	// Feladó beállítása
	$mail->setFrom('noreply@fotoplus.hu', 'Adattörlő kód kezelő');

	// Címzett beállítása
	$mail->addAddress('fotoplus@fotoplus.hu', $torzs['cegnev']);

	// Másolati email címek
	$mail->addCC('borbas.matyas@fotoplus.hu', 'Borbás Mátyás');


	if($provision and $xml and !$error) {

		$mail->Subject = 'Új XML - Adatszolgáltatás adat törlő kódokról';
		$mail->Body = 'Az előző heti értékesítésekről az XML állomány az AATKOD nyomtatvány beadásához elkészült.'
					.chr(13). 'A benyújtás az ONYA vagy az ÁNYK program AATKOD űrlapjával lehetséges.'
					.chr(13). 'A benyújtás igazolását a https://aatkod.fotoplus.hu/adatszolgaltatas/igazolas oldalon kell megtenni.';
		
		// Attach the XML file
		$mail->addStringAttachment($xml, 'AATKOD_'. $kezdo_datum .'-' . $zaro_datum . '.xml');

	} else if($provision and !$xml and !$error) {
		
		$mail->Subject = 'Nincs - Adatszolgáltatás adat törlő kódokról';
		$mail->Body = 'Az előző héten nem volt értékesítés, nem szükséges az AATKOD nyomtatvány beadása.';

	} else if($error) {

		$mail->Subject = 'HIBA - Adatszolgáltatás adat törlő kódokról';
		$mail->Body = 'Nem sikerült elkészíteni az XML fájlt az előző heti adatokból.'.chr(13).'A hiba: '.$error;

	}
	
	// Send the email
	if (!$mail->send()) {
		echo 'Email could not be sent.';
		echo 'Mailer Error: ' . $mail->ErrorInfo;
	} else {
		#echo 'Email sent successfully.';
	}

}

/**
 * A download_xml akkor true, hogy ha a vizsgált időszakról már létezik XML.
 * Itt az a probléma, hogy ha ez cronbol futna le (vagy parancsosrból) akkor oda kitenné az XML-t,
 * így viszgálni kell, hoyg ez a parancsor e, ami a cli változóban van.
 * 
 * 
 */
if($download_xml and !$cli and !$error) {
	$provision=$result_provision->fetch_assoc();
	$xml=$provision['xml'];

	header('Content-disposition: attachment; filename=AATKOD_'. $kezdo_datum .'-' . $zaro_datum . '.xml');
	header('Content-type: text/xml');
	echo($xml);
}



?>
