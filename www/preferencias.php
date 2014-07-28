<?
include '../include/lib.php';
include_once '../include/mysql.php';

if(isset($_POST['guardar'])) {
	$nombre=trim(filter_input(INPUT_POST,'nombre',FILTER_SANITIZE_STRING));
	$apellido=trim(filter_input(INPUT_POST,'apellido',FILTER_SANITIZE_STRING));
	$email=trim(filter_input(INPUT_POST,'email',FILTER_SANITIZE_STRING));
	$telcel=trim(filter_input(INPUT_POST,'telcel',FILTER_SANITIZE_STRING));
	$pass_1=trim(filter_input(INPUT_POST,'pass_1',FILTER_SANITIZE_STRING));
	if($pass_1!="") {
		$pass_1=sha1($pass_1);
	}
	$pass_2=trim(filter_input(INPUT_POST,'pass_2',FILTER_SANITIZE_STRING));
	if($pass_2!="") {
		$pass_2=sha1($pass_2);
	}
	$error="";
	if(strlen($nombre)>40) {
		$error.="El nombre debe tener menos de 40 caracteres<br />";
	}
	if(strlen($apellido)>40) {
		$error.="El apellido debe tener menos de 40 caracteres.<br />";
	}
	if(strlen($email)>50) {
		$error.="El e-mail debe tener menos de 50 caracteres.<br />";
	}
	if(strlen($telcel)!=0) {
		if(strlen($telcel)<7 or strlen($telcel)>40) {
			$error.="El teléfono introducido es incorrecto.<br />";
		}
	}
	$query="select id from users where email='$email'";
	$result=mysql_query($query);
	while($row=mysql_fetch_assoc($result)) {
		if($row['id']!=$_SESSION['user']['id']) {
			$error.="El e-mail ingresado ya está siendo utilizado por otro usuario.<br />";
		}
	}

	if($pass_1!="") {
		if($pass_1!=$pass_2) {
			$error.="Las contraseñas no coinciden.<br />";
		}
	}
	if($error=="") {
		$query="update users set nombre='$nombre', apellido='$apellido', email='$email', telcel='$telcel'";
		if($pass_1!="") {
			$query.=", password='$pass_1'";
		}
		$query.=" where id='{$_SESSION['user']['id']}'";
		if(DEBUG==true) {
			echo '<br />$query= '.$query.'<br />';
		}
		mysql_query($query);
		$mensaje="<br /><span class='success'>Se han actualizado los datos.</span>";
	} else {
		$mensaje="<br /><span class='error'>Se han producido los siguientes errores:<br />$error</span>";
	}
}

cabecera('Preferencias',true);

$query="select username, nombre, apellido, email, telcel from users where id={$_SESSION['user']['id']}";
$result=mysql_query($query);
$row=mysql_fetch_assoc($result);

?>
<h1>Preferencias</h1><hr />
<div align="center">
<?
if(isset($mensaje)) {
	echo $mensaje;
}
?>
<div id="user" style="width: 500px">
<form action="" method=POST>
	<fieldset>
	<legend>Datos personales</legend>
	<label>Nombre: <input type="text" name="nombre" value="<?=$row['nombre']?>" maxlength=40 required /></label><br />
	<label>Apellido: <input type="text" name="apellido" value="<?=$row['apellido']?>" maxlength=40 required /></label><br />
	<label>e-mail: <input type="text" name="email" value="<?=$row['email']?>" maxlength=50 required /></label><br />
	<label>Teléfono: <input type="text" name="telcel" value="<?=$row['telcel']?>" maxlength=40 /></label>
	</fieldset>
	<fieldset>
	<legend>Cambio de contraseña</legend>
	<label>Contraseña nueva: <input type="password" name="pass_1" /></label><br />
	<label>Repita su contraseña: <input type="password" name="pass_2" /></label>
	</fieldset>
	<div class="submit_acciones">
		<input type="submit" name="guardar" value="Guardar cambios" />
		<a class="btn_link" href="index.php"><input type="button" value="Cancelar" /></a>
	</div>
</form>
</div>
</div>
<?
pie();
