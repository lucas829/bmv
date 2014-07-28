<?
include '../include/lib.php';
include_once '../include/mysql.php';
include_once '../include/navegador.php';

cabecera('Búsqueda',true);

?>
<h1>Buscar documentos</h1>
<hr />
<div id='buscar'>
	<form action='' method=POST >
	<div class="cuadro_busqueda">
	<?
	if(isset($_POST['busqueda'])) {
		$busqueda=trim(filter_input(INPUT_POST,'busqueda',FILTER_SANITIZE_STRING));
		echo "<input type='text' size=30 name='busqueda' value='$busqueda' required />";
	} else {
		echo "<input type='text' size=30 name='busqueda' placeholder='Buscar...' required />";
	}
	?>
	</div>
	<div class="opciones_busqueda">
	<div class="opcion_busqueda">
	Carpeta: <select name="id_carpeta">
	<option value="0">Todas</option>
	<?
	$query="select * from carpetas order by descrip asc";
	$result=mysql_query($query);
	while($row=mysql_fetch_assoc($result)) {
		echo "<option value='{$row['id']}'>{$row['descrip']}</option>";
	}
	?>
	</select>
	</div>
	<div class="opcion_busqueda">
	Grado: <select name="id_grado">
	<option value="0">Todos</option>
	<?
	$query="select * from grados where id<={$_SESSION['user']['grado']}";
	$result=mysql_query($query);
	while($row=mysql_fetch_assoc($result)){
		echo "<option value='{$row['id']}'>{$row['descrip']}</option>";
	}
	?>
	</select>
	</div>
	<div class="opcion_busqueda">
	Subido por: <select name="id_user">
	<option value="0">Cualquiera</option>
	<?
	$query="select id, concat(apellido,', ',nombre) as nombre from users order by nombre asc";
	$result=mysql_query($query);
	while($row=mysql_fetch_assoc($result)){
		echo "<option value='{$row['id']}'>{$row['nombre']}</option>";
	}
	?>
	</select>
	</div>
	<div class="clear"></div>
	<div class="submit_acciones">
	<input type="submit" value="Buscar" />
	</div>
	</div>
	</form>
</div>
<?

if(isset($_POST['busqueda'])){
	# Realizar la búsqueda
	######################
	$buscando=trim(filter_input(INPUT_POST,'busqueda',FILTER_SANITIZE_STRING));
	# Guardo el texto de búsqueda en una global
	# $_SESSION['gui']['buscando'] = $buscando;
	$query= "select docs.id, docs.nombre, docs.id_tipo, docs.filename, docs.descrip, grados.descrip as grado, docs.fecha, docs.id, users.username from docs join grados on docs.id_grado=grados.id join users on docs.subido_por=users.id where docs.nombre like '%".$buscando."%' and docs.id_estado=1";
	# filtros
	$id_carpeta=filter_input(INPUT_POST,'id_carpeta',FILTER_SANITIZE_NUMBER_INT);
	$id_grado=filter_input(INPUT_POST,'id_grado',FILTER_SANITIZE_NUMBER_INT);
	$id_user=filter_input(INPUT_POST,'id_user',FILTER_SANITIZE_NUMBER_INT);
	# carpetas
	if($id_carpeta>0) {
		$query .= " and docs.id_carpeta=$id_carpeta";
	}
	# grados
	if($id_grado>0) {
		if($id_grado<=$_SESSION['user']['grado']) {	# Corresponde buscar ese grado?
			$query.=" and docs.id_grado=$id_grado";
		} else {
			$query.=" and docs.id_grado<={$_SESSION['user']['grado']}";
		}
	} else {
		$query.=" and docs.id_grado<={$_SESSION['user']['grado']}";
	}
	# uploader
	if($id_user>0) {
		$query.=" and docs.subido_por=$id_user";
	}
	$result=mysql_query($query);
	echo "<h1>Resultados de la búsqueda:</h1>";
	if(DEBUG==true) {
		echo '<br />$query='.$query.'<br />';
	}
	if(mysql_num_rows($result)>0) {
		while($row=mysql_fetch_assoc($result)) {
		?>
<div class="archivo">

<div class="icono">
	<?=insertar_icono($row['id_tipo'])?>
</div>
<div class='filename'>
<a href="carpetas.php?file='<?=$row['id']?>'"><?=$row['nombre']?></a>
</div>
<div class='fecha_upload'>
Subido el <?echo date('d/m/Y',strtotime($row['fecha']))?> por <?=$row['username']?>
</div>
<div class='clear'></div>
</div>
</div>
		<?
		}
	} else {
		echo "<br />No se encontraron resultados. <br /><br />";
	}
}

pie();
