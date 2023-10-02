
/**
* Itt pedig megakadályozzuk, hogy ha vonalkódolvasóval olvassuk be a bizonylatszámot,
* akkor az elküldje az űrlapot.
* 
* Bár így az enter sem fog működni, az űrlap gombjait kell használni.
* 
*/
document.getElementById("receipt_number").addEventListener("keydown", function(e) {
	if (e.key === "Enter") {
		e.preventDefault();
	}
});

document.getElementById("order_id").addEventListener("keydown", function(e) {
	if (e.key === "Enter") {
		e.preventDefault();
	}
});
  

