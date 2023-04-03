<?php


$generate_xml = false;
$download_xml = false;
$provision = false;
$sendmail=false;
$error=false;
$xml=false;

$kezdo_datum = date("Y-m-d", strtotime("last Monday"));
$zaro_datum = date("Y-m-d", strtotime("last Sunday",));


$sql_provision = 'SELECT * 
		FROM data_provisions
		WHERE
			interval_start LIKE "' . $kezdo_datum . '"
			AND
			interval_end LIKE "' . $zaro_datum . '"
';

$result_provision = $mysqli->query($sql_provision);
if ($result_provision->num_rows == 0) {
	#$generate_xml=true;
	#$sendmail='send_xml';
	$provision=true;
} else if($result_provision->num_rows == 1) {
	$download_xml = true;
} else {
	$error=('Dupliáklt bejegyzés!');
}


$sql = 'SELECT id, date, receipt_number, code 
		FROM sales
		WHERE date BETWEEN "'.$kezdo_datum.'" AND "'.$zaro_datum.'"
	';

$result = $mysqli->query($sql);
if ($result->num_rows > 0) {
	$generate_xml = true;
}

if($generate_xml and !$error) {

	/*
	// Query a sorok kiválasztására
	$sql = 'SELECT id, date, receipt_number, code 
			FROM sales
			WHERE date BETWEEN "'.$kezdo_datum.'" AND "'.$zaro_datum.'"
		';

	$result = $mysqli->query($sql);
	*/
	// Csoportokra bontás és kiírás
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
	/*
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


if($provision and $xml and !$error) {
	$sendmail = 'send_xml';
} else if($provision and !$xml and !$error) {
	$sendmail = 'no_xml';
}


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

	
	// Set the email details
	$mail->setFrom('noreply@fotoplus.hu', 'Adattörlő kód kezelő');
	$mail->addAddress('fotoplus@fotoplus.hu', $torzs['cegnev']);

	if($provision and $xml and !$error) {

		$mail->Subject = 'Új XML - Adatszolgáltatás adat törlő kódokról';
		$mail->Body = 'Az előző heti értékesítésekről az XML állomány az AATKOD nyomtatvány beadásához elkészült.';
		
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



if($download_xml) {
	$provision=$result_provision->fetch_assoc();
	$xml=$provision['xml'];

	header('Content-disposition: attachment; filename=AATKOD_'. $kezdo_datum .'-' . $zaro_datum . '.xml');
	header('Content-type: text/xml');
	echo($xml);
}



?>