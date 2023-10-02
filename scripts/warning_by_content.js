
document.addEventListener("DOMContentLoaded", function() {
  // Keresd meg a receipt_number mezőt
  var receiptNumberInput = document.getElementById("receipt_number");
  
  // Keresd meg a warning_msg elemet
  var warningMsg = document.getElementById("warning_msg");

  // Adj hozzá egy eseményfigyelőt az input mezőhöz
  receiptNumberInput.addEventListener("input", function() {
    var inputValue = receiptNumberInput.value;
    
    // Ellenőrizd, hogy a bemenet "ORD-"-vel kezdődik-e
    if (inputValue.startsWith("ORD-")) {
      // Ha igen, jeleníts meg egy figyelmeztetést
      warningMsg.textContent = "Ebbe a mezőbe nem adható meg a rendelés száma!";
    } else {
      // Ha nem, töröld a figyelmeztetést
      warningMsg.textContent = "";
    }
  });
});

