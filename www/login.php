<?
session_start();

require_once '../config/config.php';
include_once '../include/mysql.php';
include_once '../include/users_login.php';

$error_login = "";

if(isset($_POST['enviar_email'])) {
	$aviso_reset="";
	$email=filter_input(INPUT_POST,'email',FILTER_SANITIZE_EMAIL);
	$query="select id, username from users where email='$email'";
	$result=mysql_query($query);
	if(mysql_num_rows($result)>0) {
		# existe e-mail
		$row=mysql_fetch_assoc($result);
		$username=$row['username'];
		$id_user=$row['id'];
		$asunto=PROGRAM_NAME." - Cambio de contraseña";
		$code = sha1(time());
		$mensaje = generar_texto($code,$username);
		$headers=PROGRAM_NAME." <".EMAIL_REPLY_TO.">";
		mail($email,$asunto,$mensaje,$headers);
		# elimino solicitudes anteriores
		$query="delete from resets where id_user=$id_user";
		mysql_query($query);
		# inserto el código en la BBDD
		$query="insert into resets(id_user,code) values($id_user,'$code')";
		mysql_query($query);
		$aviso_reset="Ha sido enviado un correo a su casilla con los pasos a seguir para restablecer su contraseña.";
	} else {
		# no existe e-mail
		$error_login="El e-mail ingresado no se encuentra registrado en nuestra base de datos.";
	}
}

if(!isset($_SESSION['user']['id'])) {	# si no está iniciada la sesión, ingreso al sistema
    if(isset($_POST['user'])) {
	login();
    } elseif(isset($_GET['reset'])) {
        # Estoy reseteando la contraseña
	form_reset();
        exit();
    }
    cabecera('Login',false);
# Dibujo el formulario
?>
<script type="text/css" href="css/login.css"></script>
<div id="login">
    <br />
    <div id="form_login">
        <?
        if($error_login!="") {
            echo "<div class='error'>";
            echo $error_login;
            echo "</div>";
        }
	if(isset($aviso_reset)) {
		if($aviso_reset!="") {
			echo "<div class='success'>";
			echo $aviso_reset;
			echo "</div>";
		}
	}
        if(isset($_GET['out'])) {
            echo "<div class='notice'>";
            echo "Has sido desconectado.";
            echo "</div>";
        }
	if(isset($_GET['rp'])) {
		echo "<span class='success'>La contraseña se ha modificado con éxito.</span><br />";
	}
        ?>
        <form action="login.php" method="POST">
            <input type="text" name="user" placeholder="Nombre de usuario" required autofocus /><br />
            <input type="password" name="pass" placeholder="Contraseña" required /> <br />
            <span class="little_font">Introduzca los caracteres de la siguiente imagen:</span><br />
            <img src="../include/captcha/captcha.php" /><br />
            <input type="text" size="9" name="captcha" required /><br />
            <input type="submit" value="Entrar"/><br />
            <a id="reset_pass" href="login.php?reset=1">¿Olvidó su contraseña?</a>
        </form>
    </div>
</div>
<?
} else {
    # Ya está iniciada la sesión
    header('Location: index.php');
}
pie();
