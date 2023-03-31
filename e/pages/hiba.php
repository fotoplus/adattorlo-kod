<?php
$kod = isset($segments[1]) ? $segments[1] : 'Hiba';

switch($kod) {
	default:
		$title_en = '';
		$text_hu = 'Ismeretlen hiba.';
	break;
	case '400':
		$title_en = 'Bad request';
		$text_hu = '';
	break;
	case '401':
		$title_en = 'Unauthorized';
		$text_hu = '';
	break;
	case '403':
		$title_en = 'Forbidden';
		$text_hu = 'Nincs jogosultságod az oldal megtekintéséhez.';
	break;

	case '404':
		$title_en = 'Not found';
		$text_hu = 'A keresett oldal vagy fájl nem található.';
	break;
	case '500':
		$title_en = 'Internal Server Error';
		$text_hu = 'Valamilyen szerverhiba történt.';
	break;
}

echo <<<HTML
	<h1><span>{$kod}</span> {$title_en}</h1>
	<p>{$text_hu}</p>
HTML;

?>