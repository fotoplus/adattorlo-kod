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

//if (navigator.clipboard && navigator.clipboard.writeText) {
	// clipboard API használható
	let code = document.getElementById('code').innerHTML;
	let prefix = 'Adattörlő kód';

//	if (code.trim() !== '') {
		const copyCode = async () => {
			try {
				await navigator.clipboard.writeText(`${prefix} ${code}`);
				console.log('A kód sikeresen a vágólapra került');
			} catch (err) {
				console.error('Hiba a másolás közben: ', err);
			}
		}
		copyCode();
//	}

//} else {
	// clipboard API nem támogatott
//	console.log('A "navigator.clipboard" nem témogatott.');
//}

