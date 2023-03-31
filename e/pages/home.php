<?php

echo <<<HTML
	<header>
		<h1>Adattörlő kód kezelő</h1>
		<p>
			Az alábbi lehetőségek közül választhatsz
		</p>
	</header>
	<nav>
		<ul>
			<li><a href="/ertekesites">Értékesítés</a></li>
			<li><a href="/adatszolgaltatas">Adatszolgáltatás</a></li>
			<li><a href="/kodok">Kódok kezelése</a></li>
		</ul>
	</nav>

	<article>
		<h2>Kapcsolódó</h2>
		<p><a class="rounded-main border-main" href="https://net.jogtar.hu/jogszabaly?docid=A2000726.KOR" target="_blank">726/2020. (XII. 31.) Korm. rendelet az adatok végleges hozzáférhetetlenné tételét lehetővé tevő alkalmazás biztosításával kapcsolatos eljárási szabályok meghatározásáról</a></p>
		<p><a class="rounded-main border-main" href="https://veglegestorles.hu" target="_blank">Véglegestörlés weboldal</a></p>
	</article>

HTML;

?>