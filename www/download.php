<?
session_start();
include_once '../config/config.php';
include_once '../include/mysql.php';

if(!isset($_SESSION['user']['id'])) {
	error();
}

if(isset($_GET['file'])) {
	$file_id=filter_input(INPUT_GET,'file',FILTER_SANITIZE_NUMBER_INT);
	$query="select docs.filename, docs.hash, docs.id_grado, docs.descargas, tipo.mime 
				from docs join tipo on docs.id_tipo=tipo.id
				where docs.id=$file_id";
	$result=mysql_query($query);
	$row=mysql_fetch_assoc($result);
	if(mysql_num_rows($result)==0 || $row['id_grado']>$_SESSION['user']['grado']) {		# Si no existe o no corresponde al grado...
		error();
	}
	# Aumento un número al índice de descargas del documento
	$descargas=$row['descargas']+1;
	$query="update docs set descargas=$descargas where id=$file_id";
	mysql_query($query);
	
	# Genero las cabeceras
	$content_type="Content-type: ".$row['mime'];
	$content_disposition="Content-disposition: attachment; filename=".$row['filename'];
	$file=DOC_PATH.$row['hash'];

	# Descargo el archivo
	#####################
	header($content_type);
	header($content_disposition);
	readfile($file);
	exit();
} else {
	error();
}

function error() {
	echo "<h1>Error</h1>";
	echo "No tiene los permisos suficientes para acceder a esta sección.";
	die();
}
