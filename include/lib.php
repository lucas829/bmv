<?
session_start();

#Chequeo la instalación
if(!file_exists("../config/config.php")) {
	echo "<h1>Error</h1><hr />";
	echo "No se encuentra al archivo de configuración.<br />";
	echo "<em>(Es probable que el archivo no exista por tratarse de una nueva instalación.)</em><br /><br />";
	echo "Por favor cree un archivo <strong>config.php</strong> dentro de la carpeta <strong>config/</strong><br />";
	echo "En dicha carpeta dispone de un archivo de ejemplo <strong>config.inc.php</strong>";
	die();
}
include "../config/config.php";
include "template.php";
include "navbar.php";
include_once "mysql.php";

if(!isset($_SESSION['user'])) {
	header('location: login.php');
	die();
}

##### Menú y submenús #####
$menu[0]=array('Inicio','index.php');

$menu[1]=array('Biblioteca',
	   array('Explorar documentos','carpetas.php'));
$menu[1][]=array('Buscar documento','buscar.php');
$menu[1][]=array('Subir documento','subir.php');

$menu[2]=array('Preferencias','preferencias.php');

$menu[3]=array('Secretaría',
		array('Anuncios','anuncios.php'));
# Ocupa algún cargo?
$query="select id_cargo
			from users
			where id={$_SESSION['user']['id']}";
$result=mysql_query($query);
$row=mysql_fetch_assoc($result);
if($row['id_cargo']>1) {
	$menu[3][]=array('Publicar anuncio','anuncios.php?accion=publicar');
	if($row['id_cargo']==6) {		# Secretario?
		$menu[3][]=array('Cuadro lógico','secretaria.php');
	}
}
$menu[4]=array('Salir','logout.php');
if($_SESSION['id_permiso']==1) {
	$menu[5]=array('Administración',
		   array('Documentos pendientes','pendientes.php'));
	$menu[5][]=array('Documentos reportados','reportados.php');
	$menu[5][]=array('Administrar usuarios','users.php');
	$menu[5][]=array('Alta usuario','alta_usuario.php');
}

function filtrar_nombre($filename) {
	$filename=trim(strtolower($filename));
	$chars=array('*',' ','%20','/','$','%','&','\'','\\','¿','?','!','¡',')','(');
	$filename=str_replace($chars,'_',$filename);
	return $filename;
};

function tipo_archivo($extension) {
	switch($extension) {
		case "gif":
			$id_tipo= 9;
			break;
		case "bmp":
			$id_tipo= 10;
			break;
		case "tif":
		case "tiff":
			$id_tipo= 11;
		case "jpe":
		case "jpg":
		case "jpeg":
			$id_tipo= 8;
			break;
		case "png":
			$id_tipo= 7;
			break;
		case "cdr":
			$id_tipo= 14;
			break;
		case "doc":
		case "docx":
			$id_tipo= 2;
			break;
		case "xls":
		case "xlsx":
			$id_tipo= 3;
			break;
		case "ppt":
		case "pps":
		case "pptx":
			$id_tipo= 4;
			break;
		case "pdf":
			$id_tipo= 5;
			break;
		case "txt":
			$id_tipo= 6;
			break;
		case "odp":
			$id_tipo= 17;
			break;
		case "ods":
			$id_tipo= 16;
			break;
		case "odt":
			$id_tipo= 15;
			break;
		case "rar":
			$id_tipo= 13;
			break;
		case "zip":
			$id_tipo= 12;
			break;
		case "wav":
			$id_tipo= 20;
			break;
		case "mp3":
			$id_tipo= 18;
			break;
		case "ogg":
			$id_tipo= 19;
			break;
		case "rtf":
			$id_tipo=21;
			break;
		default:
			$id_tipo= 1;
			break;
	}
	return $id_tipo;
}

function duplicar_nombre_archivo($filename) {
	$extension = end(explode(".",$filename));
	$nombre = substr($filename, 0, -(strlen($extension)+1));
	$nombre .= "(2)";
	$nombre_final = $nombre.".".$extension;
	return $nombre_final;
}

function espar($valor) {
        if($valor%2==0) {
                return true;
        } else {
                return false;
        }
}

function eliminar_archivo($id_file) {
	$query="select hash from docs where id=$id_file";
        $result=mysql_query($query);
        $row=mysql_fetch_assoc($result);
        $file="../docs/".$row['hash'];
	if(unlink($file)) {
		$query="update docs set id_estado=4 where id=$id_file";
		mysql_query($query);
	}
}

function crear_fecha($fecha) {
	$signos=array("/","+","-","e","E");
	$fecha=filter_var(str_replace($signos,"",$fecha),FILTER_SANITIZE_NUMBER_INT);
	if(strlen($fecha)!=8) {
		return false;
	} else {
		$d=substr($fecha,0,2);
		$m=substr($fecha,2,2);
		$y=substr($fecha, 4);
		return "$y-$m-$d";
	}
}

function fecha_humana($fecha) {
	if($fecha!="0000-00-00") {
		$fecha=substr($fecha,8,2)."/".substr($fecha,5,2)."/".substr($fecha,0,4);
	} else {
		$fecha="Nunca";
	}
	return $fecha;
}
