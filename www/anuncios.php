<?
include "../include/lib.php";
include "../include/mysql.php";
include "../include/funciones_anuncios.php";

if(isset($_POST['accion'])) {		# Acciones sobre el anuncio
	$accion=filter_input(INPUT_POST,'accion',FILTER_SANITIZE_STRING);
	$id_anuncio=filter_input(INPUT_POST,'id_anuncio',FILTER_SANITIZE_NUMBER_INT);
	switch($accion) {
		case "Eliminar":
			form_eliminar_anuncio($id_anuncio);
			break;
		case "confirma_eliminar":
			eliminar_anuncio($id_anuncio);
			break;
		case "Modificar":
			form_editar_anuncio($id_anuncio);
			break;
		case "confirma_modificar":
			guardar_anuncio($id_anuncio);
			break;
	}
} elseif(isset($_POST['titulo'])) { 
	# Guardar un anuncio
	####################
	guardar_anuncio();
} elseif(isset($_GET['id'])) {
	# Ver en detalle un anuncio
	###########################
	$id=filter_input(INPUT_GET,'id',FILTER_SANITIZE_NUMBER_INT);
	detalle_anuncio($id);
} elseif(isset($_GET['accion'])) {
	# Formulario para publicar un anuncio
	#####################################
	$accion=filter_input(INPUT_GET,'accion',FILTER_SANITIZE_STRING);
	switch($accion) {
		case "publicar":
			$query="select id_cargo from users where id={$_SESSION['user']['id']}";
			$result=mysql_query($query);
			$row=mysql_fetch_assoc($result);
			if($row['id_cargo']>1) {
				form_crear_anuncio();
			} else {
				header('location: anuncios.php');
			}
	}
} else {
	# Ver todos los anuncios
	########################
	cabecera();
	if(isset($_GET['page'])) {
		$page=filter_input(INPUT_GET,'page',FILTER_SANITIZE_NUMBER_INT);
		if($page=="" || $page<1) {
			$page=1;
		}
	} else {
		$page=1;
	}
	mostrar_anuncios($page);
	pie();
}
