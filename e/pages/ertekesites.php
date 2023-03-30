<?php

$code = 'W3QX-FQK2-T59N-5PW2';

if(isset($_POST['save'])) {
	print_r($_POST);
}

?>

<form method="post" action="/ertekesites" autocomplete="off">
	<input type="hidden" name="code" value="<?php echo $code; ?>">
	<fieldset>
		<legend>Új értékesítés</legend>

		<span class="label">Adattörlő kód:</span>
		<div class="code-container">
			<div id="code"><?php echo $code; ?></div>
			<div class="btn" onclick="copyCode()">Másol</div>
		</div>

		<label for="date">Dátum</label>
		<input type="date" name="date" value="<?php echo date('Y-m-d'); ?>">

		<label for="receipt_number">Értékesítési bizonylat száma</label>
		<input type="text" name="receipt_number" placeholder="Másold ide, vagy olvasd be a bizonylat számát." id="barcode">

		<button name="save" value="save">Mentés</button>
		<button name="save" value="save_and_print">Mentés és nyomtatás</button>
		<a href="/" title="Mentés nélküli visszalépés" class="space">Vissza</a>

	</fieldset>
</form>

<script type="text/javascript">
	/**
	 * How to Copy Text to the Clipboard with JavaScript
	 * https://www.freecodecamp.org/news/copy-text-to-clipboard-javascript/
	 * 
	 * Joel Olawanle - https://joelolawanle.com/
	 * 
	 * Fontos tudni, hogy a vágólap API-t csak a HTTPS-en keresztül megjelenített oldalak támogatják.
	 * A vágólapra való írás előtt ellenőriznie kell(ene) a böngésző engedélyeit is, hogy van-e írási jogosultsága. 
	 * 
	 */

	let code = document.getElementById('code').innerHTML;
	let prefix = 'Adattörlő kód';

	const copyCode = async () => {
		try {
			await navigator.clipboard.writeText(`${prefix} ${code}`);
			console.log('A kód sikeresen a vágólapra került');
		} catch (err) {
			console.error('Hiba a másolás közben: ', err);
		}
}


/**
 * Itt pedig megakadályozzuk, hogy ha vonalkódolvasóval olvassuk be a bizonylatszámot,
 * akkor az elküldje az űrlapot.
 * 
 * Bár így az enter sem fog működni, az űrlap gombjait kell használni.
 * 
 */
document.getElementById("barcode").onkeypress = function(e) {
    var key = e.charCode || e.keyCode || 0;     
    if (key == 13 ) {
      e.preventDefault();
    }
  } 
</script>


