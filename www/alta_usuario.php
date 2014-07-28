<?
include '../include/lib.php';
include_once '../include/mysql.php';
include_once '../include/users_admin.php';
include_once '../include/funciones_mysql.php';

if(isset($_POST['alta'])) {
	$username=trim(filter_input(INPUT_POST,'username',FILTER_SANITIZE_STRING));
	$nombre=trim(filter_input(INPUT_POST,'nombre',FILTER_SANITIZE_STRING));
	$apellido=trim(filter_input(INPUT_POST,'apellido',FILTER_SANITIZE_STRING));
	$email=trim(filter_input(INPUT_POST,'email',FILTER_SANITIZE_EMAIL));
	$telcel=trim(filter_input(INPUT_POST,'telcel',FILTER_SANITIZE_EMAIL));
	$pass_1=sha1(trim(filter_input(INPUT_POST,'pass_1',FILTER_SANITIZE_STRING)));
	$pass_2=sha1(trim(filter_input(INPUT_POST,'pass_2',FILTER_SANITIZE_STRING)));
	$id_grado=filter_input(INPUT_POST,'id_grado',FILTER_SANITIZE_NUMBER_INT);
	$id_permiso=filter_input(INPUT_POST,'id_permiso',FILTER_SANITIZE_NUMBER_INT);
	$id_cargo=filter_input(INPUT_POST,'id_cargo',FILTER_SANITIZE_NUMBER_INT);
	$error="";
	# existe el username?
	$query="select id from users where username='$username'";
	$result=mysql_query($query);
	if(mysql_num_rows($result)>0) {
		$error.="El nombre de usuario ya está en uso.<br />";
	}
	# existe el e-mail?
	$query="select id, username, nombre, apellido from users where email='$email'";
	$result=mysql_query($query);
	if(mysql_num_rows($result)>0) {
		$row=mysql_fetch_assoc($result);
		$error.="El e-mail <strong>$email</strong> está siendo usado por <strong>{$row['username']}</strong>({$row['nombre']} {$row['apellido']}).<br />";
	}

	if(strlen($username)<4 or strlen($username)>40) {
		$error.="El nombre de usuario debe tener entre 4 y 40 caracteres.<br />";
	}
	if(strlen($nombre)>40) {
		$errori.="El nombre debe ser menor de 40 caracteres.<br />";
	}
	if(strlen($apellido)>40) {
		$error.="El apellido debe ser menor de 40 caracteres.<br />";
	}
	if(strlen($email)>50) {
		$error.="El e-mail es demasiado largo.<br />";
	}
	if(strlen($telcel)!=0) {
		if(strlen($telcel)<7 or strlen($telcel)>40) {
			$error.="El número de teléfono es incorrecto";
		}
	}
	if($pass_1!=$pass_2) {
		$error.="Las contraseñas no coinciden";
	}
	if($error=="") {
		$query="insert into users(username,password,nombre,apellido,email,telcel,id_permiso,id_grado,id_cargo) 
					VALUES('$username','$pass_1','$nombre','$apellido','$email','$telcel',$id_permiso,$id_grado,$id_cargo)";
		mysql_query($query);
		$mensaje="<br /><span class='success'>Se ha registrado con éxito al usuario <strong>$username</strong> en la base de datos.</span>";
	} else {
		$mensaje="<br /><span class='error'>Se han producido los siguientes errores:<br />$error</span>";
	}
}

cabecera('Alta de nuevo usuario',true);
if(isset($_SESSION['id_permiso']) && $_SESSION['id_permiso']==1) {	# Administrador?
	?>
<h1>Alta de nuevo usuario</h1><hr />
<div align="center">
	<?
	if(isset($mensaje)) {
		echo $mensaje;
	}
	?>
<form action="" method=POST>
<div id="user" style="width: 500px">
	<fieldset>
		<legend>Datos de usuario</legend>
		<label>Nombre de usuario: <input type="text" name="username" maxlength=20 required autofocus></label><br />
	</fieldset>
	<fieldset>
		<legend>Datos personales</legend>
		<label>Nombre: <input type="text" name="nombre" maxlength=40 required /></label><br />
		<label>Apellido: <input type="text" name="apellido" maxlength=40 required /></label><br />
		<label>e-mail: <input type="email" name="email" maxlength=50 required /></label><br />
		<label>Teléfono: <input type="text" name="telcel" maxlength=40 /></label>
	</fieldset>
	<fieldset>
		<legend>Contraseña</legend>
		<label>Contraseña: <input type="password" name="pass_1" required /></label><br />
		<label>Repita la contraseña: <input type="password" name="pass_2" required /></label><br />
	</fieldset>
	<fieldset>
		<legend>Grupos</legend>
		<label>Cargo:
		<select name="id_cargo">
			<?llenar_select('cargos')?>
		</select>
		</label><br />
		<label>Grado:
		<select name="id_grado" >
			<?llenar_select('grados')?>
		</select>
		</label><br />
		<label>Permisos:
		<select name="id_permiso">
			<?llenar_select('permisos')?>
		</select>
		</label>
	</fieldset>
	<div class="submit_acciones">
	<input type="hidden" name="alta" value="usuario" />
	<input type="submit" value="Crear usuario" />
 	<a class="btn_link" href="users.php"><input type="button" value="Cancelar" /></a>
	</div>
</div>
</form>
</div>
	<?

} else {
	echo "<br />No tiene los permisos suficientes para ver esta página.<br /><br />";
}
pie();
