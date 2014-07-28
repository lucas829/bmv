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
<meta name="Robots" content="index,follow" />
<link rel="shortcut icon" href="../img/favicon.png" />
<link rel="stylesheet" href="css/login.css" type="text/css" />
<link rel="stylesheet" href="css/base.css" type="text/css" />

<title><?=$titulo?></title>

</head>

<body>
<div id="header">

</div>
<div id="wrapper">
<?
}

function pie() {
?>
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

function login() {
	global $error_login;
	if(strtoupper($_REQUEST["captcha"]) == $_SESSION["captcha"]){
                $_SESSION["captcha"] = md5(rand()*time());
                $user = trim(filter_input(INPUT_POST, 'user', FILTER_SANITIZE_STRING));
                $pass = trim(filter_input(INPUT_POST, 'pass', FILTER_SANITIZE_STRING));
                if($user==''||$pass=='') {
                    # Campos sólo con espacios
                    $error_login = "Datos incorrectos";
                } else {
                    $pass = sha1($pass);
                    $query = "select * 
                        from users 
                        where username='$user' and password='$pass'";
                    $result = mysql_query($query);
                    if(mysql_num_rows($result) != 1) {
                        # No existe el usuario
                        $error_login = "Datos incorrectos";
                    } else {
                        $row = mysql_fetch_assoc($result);
                        # Llenamos la superglobal de sesión
                        $_SESSION['username'] = $row['username'];
                        $_SESSION['user']['id'] = $row['id'];
                        $_SESSION['user']['nombre'] = $row['nombre'];
                        $_SESSION['user']['apellido'] = $row['apellido'];
                        $_SESSION['user']['email'] = $row['email'];
                        $_SESSION['user']['grado'] = $row['id_grado'];
                        $_SESSION['id_permiso'] = $row['id_permiso'];
                        # Redirigimos a la página principal
                        header('location: index.php');
                    }
                }
        } else {
                $_SESSION["captcha"] = md5(rand()*time());
                $error_login='El código de seguridad ingresado no es correcto';
        }
}

function generar_texto($code,$user) {
	$mensaje=PROGRAM_NAME."\r\n";
	$subrayado = "";
	for($i=1;$i<=strlen(PROGRAM_NAME);$i++) {
		$subrayado.= "-";
	}
	$mensaje.= $subrayado."\r\n\r\n";
	$mensaje.="$user, ha solicitado un cambio de contraseña para su cuenta\r\n";
	$mensaje.="Si esto es correcto, haga clic en el siguiente link, o cópielo y péguelo en su navegador:\r\n\r\n";
	$mensaje.=PROGRAM_URL."/reset.php?code=$code\r\n\r\n";
	$mensaje.="Si usted no solicitó un cambio de contraseña, elimine este correo";
	return $mensaje;
}

function form_reset() {
	cabecera('Solicitar contraseña', false);
?>
<h1>Restaurar contraseña</h1><hr /><br />
<div id="user" style="width: 500px;">
<form action="login.php" method=POST>
<label>Ingrese su e-mail: <input type="email" name="email" required autofocus /></label><br />
<div class="submit_acciones">
<input type="submit" name="enviar_email" value="cambiar contraseña" />
</div>
</form>
</div>
<?
        pie();
}
