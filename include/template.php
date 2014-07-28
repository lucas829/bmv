<?
function cabecera($titulo=null,$nav=true) {
# Limpiamos el título
	$titulo = trim(filter_var($titulo,FILTER_SANITIZE_STRING));
	if($titulo != null) {
	    if(strlen($titulo)>0) {
	        $titulo = PROGRAM_NAME . " - " . $titulo;
	    } else {
	        $titulo = PROGRAM_NAME;
	    }
	} else {
	    $titulo = PROGRAM_NAME;
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta name="Keywords" content="keywords" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="Distribution" content="Global" />
<link rel="shortcut icon" href="img/favicon.png" />
<link rel="stylesheet" href="css/base.css" type="text/css" />
<link rel="stylesheet" href="css/navbar.css" type="text/css" />

<title><?=$titulo?></title>

</head>

<body>
<div id="logo">
<img src="../img/logo.jpg" />
</div>
<div id="header">
<? navbar($nav);?>
</div>
<div id="wrapper">
<?
}

function pie() {
?>
</div>	<?# Cierro el div "wrapper" ?>
	<div id="footer">
		<hr />
		<br/>&copy; 2013 <?=PROGRAM_NAME?> - <?=ADMIN_EMAIL?> - <a href="feedback.php">Enviar un reporte de error o sugerencia</a>
	</div>
</body>
</html>
<?
	if(DEBUG==true) {
		echo "<pre class='debug'>";
		echo '$_SESSION=';
		print_r($_SESSION);
		echo '$_POST=';
		print_r($_POST);
		echo '$_GET=';
		print_r($_GET);
		echo "</pre><br />";
	}
}
