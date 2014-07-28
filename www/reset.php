<?
session_start();

require '../config/config.php';
include '../include/template.php';
include '../include/navbar.php';
include '../include/mysql.php';

if(isset($_POST['pass_1'])) {
	$error_reset="";
	$pass_1=trim(filter_input(INPUT_POST,'pass_1',FILTER_SANITIZE_STRING));
	$pass_2=trim(filter_input(INPUT_POST,'pass_2',FILTER_SANITIZE_STRING));
	$id=filter_input(INPUT_POST,'id',FILTER_SANITIZE_NUMBER_INT);
	if(strlen($pass_1)>0) {
		$pass_1=sha1($pass_1);
		$pass_2=sha1($pass_2);
		if($pass_1==$pass_2) {
			# grabo la contraseña nueva
			$query="update users set password='$pass_1' where id=$id";
			mysql_query($query);
			# elimino el registro de cambio
			$query="delete from resets where id_user=$id";
			mysql_query($query);
			header('location: login.php?rp=1');
		} else {
			$error_reset="<span class='error'>Las contraseñas no coinciden</span><br />";
		}
	} else {
		$error_reset="<span class='error'>La contraseña no es válida</span><br />";
	}
}

if(isset($_GET['code'])) {
	cabecera('Reseteando la contraseña',false);
	$code=trim(filter_input(INPUT_GET,'code',FILTER_SANITIZE_STRING));
	$query="select id_user from resets where code='$code'";
	$result=mysql_query($query);
	$row=mysql_fetch_assoc($result);
	$id=$row['id_user'];
?>
<h1>Cambiar contraseña</h1><hr />
<div id="user" style="width: 500px">
<form action="" method=POST>
	<input type='hidden' name='id' value=<?=$id?> /><br />
	<label>Contraseña nueva: <input type='password' name='pass_1' required autofocus /></label><br />
	<label>Repita la contraseña: <input type='password' name='pass_2' required /></label>
	<div class='submit_acciones'><input type='submit' value='Cambiar' /></div>
</form>
</div>
<?
	pie();
} else {
	header('location: index.php');
}
