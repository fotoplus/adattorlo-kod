<?php

// A mysqli hibakijelzése
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// A kapcsolat karakterkódolása (megegyezik az adatbáziséval)
$db_charset = 'utf8mb4_unicode_ci';

// Adatbázis tábláinak előtagja (ha van ilyen)
$tbl_prefix = 'fp_';


?>