<?php

$msg=false;
$err=false;
$log=false;
$allow=false;

require_once ('e/config/config.php');
require_once ('e/modules/mysql/mysql.php');
require_once ('e/modules/accesscontrol/ipcheck.php');
require_once ('e/modules/pages/pages.php');



?>
<!doctype html>
<html class="no-js" lang="hu">

	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="robots" content="noindex, nofollow, noarchive">
		<link rel="stylesheet" href="/styles/main.css?<?php echo date('U'); ?>">
		<?php
			$page_style = '/styles/' . $page['name'] .'.css';
			if(file_exists('.'.$page_style)) {
				echo '<link rel="stylesheet" href="' . $page_style . '?' . date('U') . '">';
			}
		?>
		<title>
			<?php
				echo 'Adattörlő kód kezelő';
				if($location['name']) {
					echo ' [' . $location['name'] . ']';
				}
				
			?>
		</title>
		<meta name="description" content="Weboldal">
	</head>


	<body>

		<div id="container">
				<?php
					if($allow) {
						include $page['file'];
					} else if(!$allow and $page['name']){
						include $page['file'];
					}
				?>
		</div>

		<footer>
				<!--a href="https://github.com/borbasmatyas" target="_blank">
					<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAABmJLR0QA/wD/AP+gvaeTAAABeklEQVRIidXUPU8VURSF4YeLgIQES0JhQSeQGPA3mChfkQgVhR1/g4QQNVJaameorCy0sLO2JRAQOoNKTEhosJJLMTO5w+YMM5eOlZzcj73Wu+ec2TncdvXU1HvxFMuYxv088xvf8Rmf8P8mzZ9gH+2adYi5bsA9WMd5A3ixzrGp/kTARhfguDbq4HMh8AgL2E7AfmAJD8L/M1XwO7LzLJv78togpvLPQTzEvbzWGzJ7pdwlLSaecqxuyxhJ5BaKYqtkXArBA9k41ulUdlxlRRbYCU/xvAG80LOQ3U6Z/gbTcBcNhkP2pCiUj6i/C2BUnP+7qQY/g6ly3BKaDb+PU6Ytl7d5hPEG8An8CtmPKWNqTP/hVQ6JmsTr3BNzL1IN+nQutzV8KQXeJvwfEuC2bLQHqrY7L7u4zjCKr/iTf0/tINVgpQpe6GVufFPjayXg7+rgdK7rNnbx7RpvGf5edp811mOdd3Jdg2MVL7WJWq7OeFmrGLop/HboAtvUneYxMJW3AAAAAElFTkSuQmCC"/>
					@borbasmatyas</a> |--><a href="https://github.com/fotoplus/adattorlo-kod/wiki" target="_blank">Segítség</a>
		</footer>
		<div id="location"><?php echo $location['name']; ?></div>
	<!-- Scriptek -->	
	
	<!-- Scriptek (vége) -->

	</body>
</html>
<?php

?>