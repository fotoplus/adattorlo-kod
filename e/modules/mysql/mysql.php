<?php

require_once ('e/credentials/mysql.php'); // Kapcsolódási adatok
require_once ('e/config/mysql.php'); // További beállítások

/**
 * Kísérlet az adatbáziskapcsolat létrehozására
 */
try {

  $mysqli = new mysqli(
    $mysql['host'], // Az adatbázis címe, jellemzően "localhost"
    $mysql['user'], // Felhasználóinév
    $mysql['password'], // Jelszó
    $mysql['db'] // A hasznáűlni kívánt adatbáis
  );
  
  $mysqli->set_charset($db_charset);

} catch(Exception $e) {

  exit( 'Nem sikerült létrehozni a kapcsolatot az adatbázissal, az adatbázis válasza: ' . $e->getMessage() ); 

}


?>