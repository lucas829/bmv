<?
include '../include/lib.php';
include_once '../include/mysql.php';
include_once '../include/users_admin.php';
include_once '../include/funciones_mysql.php';

cabecera('Administración de usuarios',true);
if(isset($_SESSION['id_permiso']) && $_SESSION['id_permiso']==1) {	# Administrador?
	?>
<h1>Administración de usuarios</h1><hr />
<div align="center">
	<?
	if(isset($_POST['eliminar'])) {
		$user_id=filter_input(INPUT_POST,'id_user',FILTER_SANITIZE_NUMBER_INT);
		eliminar_usuario($user_id);
		listar_usuarios();
	} elseif(isset($_POST['modificar'])) {
		$user_id=filter_input(INPUT_POST,'id_user',FILTER_SANITIZE_NUMBER_INT);
		modificar_usuario($user_id);
	} elseif(isset($_POST['guardar'])) {
		guardar_usuario();
	} else {
		listar_usuarios();
	}
	?>
</div>
	<?
} else {
	echo "<br />No tiene los permisos suficientes para ver esta página.<br /><br />";
}
pie();
