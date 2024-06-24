<?php

$html = '';

if(isset($_POST['pid']) and is_numeric($_POST['pid'])) {

	$provision_id = $_POST['pid']; // numeric, int
	$submission_date = isset($_POST['submission_date']) ? $_POST['submission_date'] : false; // date
	$submission_proof = isset($_POST['submission_proof']) ? $_POST['submission_proof'] : false; // string, text
	$submitted_by = isset($_POST['submitted_by']) ? $_POST['submitted_by'] : false; // string (varchar(50))

	if($submission_date and $submission_date and $submitted_by) {

		// Ellenőrizzük, hogy a kapott $provision_id létezik-e a data_provisions táblában
		$check_provision_query = $mysqli->prepare("SELECT id FROM data_provisions WHERE id = ?");
		$check_provision_query->bind_param("i", $provision_id);
		$check_provision_query->execute();
		$result = $check_provision_query->get_result();

		if ($result->num_rows !== 1) {
			echo "Ismeretlen adatszolgáltatás-azonosító.";
			exit();
		}

		// Beszúrjuk az adatokat az adatbázisba
		$insert_query = $mysqli->prepare("INSERT INTO submissions (submission_date, submission_proof, submitted_by, provision_id) VALUES (?, ?, ?, ?)");
		$insert_query->bind_param("sssi", $submission_date, $submission_proof, $submitted_by, $provision_id);
		$insert_query->execute();

		if ($insert_query->affected_rows !== 1) {
			echo "Failed to insert submission data into database.";
			exit();
		} else {

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
			$mail->addCC('borbas.matyas@fotoplus.hu', 'Borbás Mátyás');
		
		
			$mail->Subject = 'Adatszolgáltatás igazolása';
			$mail->Body = 'Az előző heti értékesítésekről az adatszolgáltatás megtörtént.'
						.chr(13). 'Dátum: '.$submission_date
						.chr(13). 'NAV érkezési szám: '.$submission_proof
						.chr(13). 'Beküldte: '.$submitted_by;
				

			
			// Send the email
			if (!$mail->send()) {
				echo 'Email could not be sent.';
				echo 'Mailer Error: ' . $mail->ErrorInfo;
			} else {
				#echo 'Email sent successfully.';
			}
		}
	} else {
		$html .=  '<div class="error">Hiányzó adatok!</div>';
	}

}


$sql = 'SELECT 
		 p.id AS "pid"
		,p.interval_start
		,p.interval_end
		,s.id AS "sid"
		,s.submission_date
		,s.submission_proof
		,s.submitted_by
	FROM data_provisions p
	LEFT JOIN submissions s
	 	ON s.provision_id = p.id
	ORDER BY created DESC
';

$result=$mysqli->query($sql);
while($row = $result->fetch_assoc()) {
	$html .= '
		<div class="provisions">
			<div class="interval">'.$row['interval_start'].' - '.$row['interval_end'].'</div>
	';
	if($row['submission_date'] == NULL) {

		$html .= '
			<button class="toggle-button" data-target="form_'.$row['pid'].'">Igazolás</button>
			<form method="post"action="/adatszolgaltatas/igazolas" id="form_'.$row['pid'].'" style="display: none;">
				<input type="hidden" name="pid" value="'.$row['pid'].'">
				<label>Benyújtás dátuma</label>
				<input type="date" name="submission_date" value="'.date('Y-m-d').'" autocomplete="off">
				<label>NAV érkezési szám</label>
				<input type="text" name="submission_proof" value="" autocomplete="off">
				<label>A nyomtatványt beküldte</label>
				<input type="text" name="submitted_by" value="">
				<button name="new_submission" valeu="'.$row['pid'].'">Rögzítés</button>
			</form>
		';

	} else {
		$html .='
				<table>
					<tr>
						<td>Benyújtva:</td>
						<td>'.$row['submission_date'].'</td>
					</tr>
					<tr>
						<td>Benyújtotta:</td>
						<td>'.$row['submitted_by'].'</td>
					</tr>
					<tr>
						<td>NAV érkezési szám:</td>
						<td>'.$row['submission_proof'].'</td>
					</tr>
				</table>
		';
	}
	$html .='
		</div>
	';
}


echo <<<HTML
		<header>
			<h1>Nyomtatványbenyújtás igazolása</h1>
			<p>Itt megtekitnhetőek az eddigi időszakok és megadható a benyújtott nyomtatvány érkezési száma.</p>
		</header>
		<nav>
			<!--ul>
				<li><a href=""></a></li>
			</ul-->
			<a href="/" class="nav-back">Vissza</a>
		</nav>
		<main>
			{$html}
			<a href="/" class="nav-back">Vissza</a>
		</main>
		<script src="/scripts/toggle_form.js"></script>
	HTML;

?>