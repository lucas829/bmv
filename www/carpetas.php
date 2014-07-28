<?
include '../include/lib.php';
include_once '../include/mysql.php';
include '../include/navegador.php';

cabecera('Explorar',true);

# 'report' es una palabra clave para indicar que acabo de reportar un archivo
if(isset($_GET['report'])){
	echo "<div class='success'>Se ha enviado el reporte del documento.</div>";
}

# Filtro de "ordenar por" (por defecto: nombre)
if(isset($_GET['order'])) {
	$order=filter_input(INPUT_GET,'order',FILTER_SANITIZE_STRING);
} else {
	$order="name";
}

# Filtro por grado, por defecto todos a los que el usuario tenga acceso
if(isset($_GET['deg'])) {
	$deg=filter_input(INPUT_GET,'deg',FILTER_SANITIZE_NUMBER_INT);
	# Filtro mayor al grado que pertenece?
	if($deg>$_SESSION['user']['grado']) {
		$deg=$_SESSION['user']['grado'];
	}
} else {
	$deg=0;
}

if(isset($_GET['fid'])) {
	# Muestro la lista de archivos en una carpeta
	#############################################
	$folder=filter_input(INPUT_GET,'fid',FILTER_SANITIZE_NUMBER_INT);
	$query="select * from carpetas where id=$folder";
	$result=mysql_query($query);
	if(mysql_num_rows($result)>0) {	# Existe la carpeta
		if(isset($_GET['page'])) {	# Página?
			$page=filter_input(INPUT_GET,'page',FILTER_SANITIZE_NUMBER_INT);
			if($page=="" || $page<1) {	# Si el número de página no es válido, página = 1
				$page=1;
			}
		} else {	# No se especificó qué página
			$page=1;
		}
		mostrar_archivos($folder, $page, $order,$deg);
	} else {
		mostrar_carpetas();
	}
} elseif(isset($_GET['file'])) {
	# Muestro la descripción de un archivo
	######################################
	$file_id=filter_input(INPUT_GET,'file',FILTER_SANITIZE_NUMBER_INT);
	$query = "select id from docs where id='$file_id'";
	$result=mysql_query($query);
	if(mysql_num_rows($result)>0) {
		$row=mysql_fetch_assoc($result);
		detalles_archivo($row['id']);
	} else {
		mostrar_carpetas();
	}
} else {
	# Muestro las carpetas
	######################
	mostrar_carpetas();
}
pie();
