

document.getElementById("print").addEventListener("click", function() {

	let printCode = document.getElementById('printCode').innerHTML;
	let printReceipt_number = document.getElementById('printReceipt_number').innerHTML;
	let printDate = document.getElementById('printDate').innerHTML;

		let html = `

		<!doctype html>
		<html class="no-js" lang="hu">
		  <head>
			<meta charset="utf-8">
			<title>Adattörlő kód</title>
			<style>
				header {
					width: 500px;
					margin:0 auto;
					font-family: sans-serif;
					font-size:1.2rem;
					font-weight: bold;
				}
				main {
					width: 500px;
					margin:0 auto;
					font-family: sans-serif;
				}
				
				div.print-code {
					font-family:monospace;
					font-size: 2rem;
					background-color: #cccccc;
					text-align: center;
					margin: 20px a 20px 0;
					padding: 20px 10px 20px 10px;
					-webkit-border-radius: 7px;
					-moz-border-radius: 7px;
					border-radius: 7px;
					border-color: 1px solid black;
				}
				
				.bold {
					font-weight: bold;
				}
				
				footer {
					width: 500px;
					margin:50px auto;
					font-family: sans-serif;
					border-top: 1px solid gray;
					font-size: 0.8rem;
					color: gray
				}
			</style>
		  </head>
		  <body>
			<header>
			  <h1>Adattörlő kód</h1>
			</header>
			<main>
			  <p>
			  	Személyes adataink védelmében a Nemzeti Média- és Hírközlési Hatóság (NMHH) – nem hatósági hatáskörében – ingyenes adattörlési szolgáltatást nyújt a fogyasztóknak.
			  </p>
			  <p>
				Az adattörlő alkalmazás a tartós adathordozó eszközök széles körénél teszi lehetővé az eszközön tárolt adatok biztonságos és visszavonhatatlan törlését.
			  </p>
			  <p>
			  	Az adattölrő alkalmazás használatához adattörlő kódra van szüksége, melyet a megvásárolt tartós adathordozóhoz adunk.
			  </p>
			  <p class="bold">
			  	Az Ön adattölrő kódja a következő:
			  </p>
			  <div class="print-code">
				${printCode}
			  </div>
			  <p>
			  	További információkért és az alkalmazás letöltéséhez látogasson el a <span class="bold">veglegestorles.hu</span> weboldalra.
			  </p>
			  <p>
				Köszönjük, hogy nálunk vásárolt.<br>
				<span class="bold">FOTOPLUS Kft</span>
			  </p>
			</main>
			<footer>
			  <p>
				Kapcsolódó értékesítési bizonylat száma: ${printReceipt_number}<br>
				Vásárlás időpontja: ${printDate}
			  </p>
		
			</footer>
		  </body>
		</html>
		`;

		// Nyomtatás

		var printWindow = window.open();
		printWindow.document.write(html);
		printWindow.print();
		printWindow.close();

}); 
