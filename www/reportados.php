<?
include '../include/lib.php';
include_once '../include/mysql.php';
include_once '../include/navegador.php';
include_once '../include/report.php';

cabecera('Archivos reportados',true);
if(isset($_SESSION['id_permiso']) && $_SESSION['id_permiso'] == 1 ) {	# Administrador?
	if(isset($_POST['eliminar'])) {
		# Eliminar un documento
		#######################
		$id_file=filter_input(INPUT_POST,'id_file',FILTER_SANITIZE_NUMBER_INT);
		if($id_file>0) {
			eliminar_archivo($id_file);
			# Ocultar los reportes
			$query="update reportes set visible=0 where id_doc=$id_file";
			mysql_query($query);
		}
	} elseif(isset($_POST['aprobar'])) {
		# Publicar un documento
		#######################
		$id_file=filter_input(INPUT_POST,'id_file',FILTER_SANITIZE_NUMBER_INT);
		if($id_file>0) {
			$query="update docs set id_estado=1 where id=$id_file";
			mysql_query($query);
			$query="update reportes set visible=0 where id_doc=$id_file";
			mysql_query($query);
		}
	}

	if(isset($_GET['file'])) {	# Muestro detalle de un archivo
		$file_id=filter_input(INPUT_GET,'file',FILTER_SANITIZE_NUMBER_INT);
		mostrar_reporte_archivo($file_id);
	} else {	# O muestro todos los archivos
		mostrar_reportados();
	}
} else {
	echo "<br />No tiene los permisos suficientes para ver esta página<br /><br />";
}
pie();
