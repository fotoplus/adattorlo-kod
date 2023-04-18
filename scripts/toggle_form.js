// Kiválasztjuk az összes toggle gombot
const toggleButtons = document.querySelectorAll('.toggle-button');

// Végigmegyünk az összes toggle gombon
toggleButtons.forEach(button => {
  // Hozzáadjuk az eseményfigyelőt a gombhoz
  button.addEventListener('click', () => {
    // Az adattagból kiolvassuk a céldiv azonosítóját
    const targetId = button.dataset.target;
    // Kiválasztjuk a céldivot
    const targetElement = document.getElementById(targetId);
    // A toggle függvény meghívása a céldiv-re
    toggle(targetElement);
  });
});

// A toggle függvény megvalósítása
function toggle(element) {
  // Ha a div látható, akkor elrejtjük
  if (element.style.display === 'block') {
    element.style.display = 'none';
	// Elrejtjük az aktuális gombot
	element.previousElementSibling.style.display = 'none';
  }
  // Ha nem látható, akkor megjelenítjük
  else {
    element.style.display = 'block';
    // Megjelenítjük az aktuális gombot
    element.previousElementSibling.style.display = 'none';
  }
}