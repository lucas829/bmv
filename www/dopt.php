<?
include '../include/lib.php';
include '../include/mysql.php';
include_once '../include/funciones_mysql.php';

cabecera();

if(isset($_POST['accion'])) {
	$accion=trim(filter_input(INPUT_POST,'accion',FILTER_SANITIZE_STRING));
	$file_id=filter_input(INPUT_POST,'file_id',FILTER_SANITIZE_NUMBER_INT);
		switch($accion) {
      	case "reportar":
         	reportar($file_id);
            break;
			case "eliminar":
				eliminar($file_id);
				break;
			case "editar":
				editar($file_id);
				break;
		}
}

# Acciones 
#####################
if(isset($_POST['reportar'])) {
	$file_id=filter_input(INPUT_POST,'file_id',FILTER_SANITIZE_NUMBER_INT);
	$descrip=filter_input(INPUT_POST,'descrip',FILTER_SANITIZE_STRING);
	# Inserto el registro en la BBDD
	$query="insert into reportes(id_doc,id_user,descrip) values($file_id,{$_SESSION['user']['id']},'$descrip')";
	mysql_query($query);
	# Actualizo el estado del documento a reportado
	$query="update docs set id_estado=3 where id=$file_id";
	mysql_query($query);
	# Vuelvo a la carpeta
	$query="select id_carpeta from docs where id=$file_id";
	$result = mysql_query($query);
	$row=mysql_fetch_assoc($result);
	$location="location: carpetas.php?fid=".$row['id_carpeta']."&report=1";
	header($location);
}
if(isset($_POST['modificar'])) {
	# Limpiamos un poco
	$file_id=filter_input(INPUT_POST,'file_id',FILTER_SANITIZE_NUMBER_INT);
	$nombre=substr(filter_input(INPUT_POST,'nombre',FILTER_SANITIZE_STRING),0,255);
	$id_carpeta=filter_input(INPUT_POST,'id_carpeta',FILTER_SANITIZE_NUMBER_INT);
	$id_grado=filter_input(INPUT_POST,'id_grado',FILTER_SANITIZE_NUMBER_INT);
	$subido_por=filter_input(INPUT_POST,'subido_por',FILTER_SANITIZE_NUMBER_INT);
	$descrip=substr(filter_input(INPUT_POST,'descrip',FILTER_SANITIZE_STRING),0,255);
	# Actualizo el registro
	$query="update docs set 
			nombre='$nombre', 
			descrip='$descrip',
			id_carpeta=$id_carpeta, 
			id_grado=$id_grado, 
			subido_por=$subido_por 
		where id=$file_id";
	mysql_query($query);
	$url="location: carpetas.php?file=".$file_id;
	header($url);
}
if(isset($_POST['eliminar'])) {
	$file_id=filter_input(INPUT_POST,'file_id',FILTER_SANITIZE_NUMBER_INT);
	$query="select id_carpeta from docs where id=$file_id";
	$result=mysql_query($query);
	$row=mysql_fetch_assoc($result);
	eliminar_archivo($file_id);
	$url="location: carpetas.php?fid=".$row['id_carpeta'];
	header($url);
}

pie();

# Eliminar un archivo
#####################
function eliminar($file_id) {
	$query="select id_carpeta from docs where id=$file_id";
	$result=mysql_query($query);
	$row=mysql_fetch_assoc($result);
	$folder_id=$row['id_carpeta'];
	?>
<h1>Eliminar un documento</h1><hr />
<div id="user" style="width: 500px">
<form action='' method=POST>
	¿Confirma la eliminación del archivo <strong><?=get_name($file_id)?></strong>?<br />
	<div class='submit_acciones'>
	<input type='hidden' name='file_id' value=<?=$file_id?> />
	<input type='hidden' name='eliminar' value='1' />
	<input type='submit' value='Eliminar' />
	<a class='btn_link' href="carpetas.php?fid=<?=$folder_id?>" /><input type="button" value="Cancelar" /></a>
	</div>
</form>
</div>
	<?
}

# Reportar un archivo
#####################
function reportar($file_id) {
	$query="select id_carpeta from docs where id=$file_id";
	$result=mysql_query($query);
	$row=mysql_fetch_assoc($result);
	$folder_id=$row['id_carpeta'];
	?>
<h1>Reportar un documento</h1><hr />
<div id="user" style="width: 500px">
<form action='' method=POST>
	Ha elegido reportar el archivo <strong><?=get_name($file_id)?></strong>.<br />
	<label>Por favor explique el motivo por el cual desea reportar el documento:<br />
	<textarea name='descrip' cols=50 rows=4 maxlength=255 required></textarea></label>
	<div class='submit_acciones'>
	<input type='hidden' name='file_id' value=<?=$file_id?> />
	<input type='hidden' name='reportar' value='1' />
	<input type='submit' value='Enviar reporte' />
	<a class='btn_link' href="carpetas.php?fid=<?=$folder_id?>" /><input type="button" value="Cancelar" /></a>
	</div>
</form>
</div>
	<?
}

# Editar un archivo
#####################
function editar($file_id) {
	$query="select id, nombre, descrip, id_carpeta, id_grado, subido_por from docs where id=$file_id";
	$result=mysql_query($query);
	$row=mysql_fetch_assoc($result);
	$folder_id=$row['id_carpeta'];
	?>
<h1>Modificar documento</h1><hr />
<div id="user" style="width: 500px">
<form action='' method=POST>
	<label>Nombre del documento: <input type='text' name='nombre' maxlength=255 size=35 value='<?=$row['nombre']?>' required /></label><br />
	<label>Carpeta: 
	<select name='id_carpeta'>
		<?llenar_select('carpetas',$row['id_carpeta'])?>
	</select>
	</label><br />
	<label>Visible por: 
	<select name='id_grado'>
		<?llenar_select('grados',$row['id_grado'],'',$_SESSION['user']['grado'])?>
	</select>
	</label><br />
	<?
	if($_SESSION['id_permiso']==1) {		# Sólo administrador puede cambiar al propietario del archivo
		?>
		<label>Subido por:
		<select name='subido_por'>
			<?llenar_select('users',$row['subido_por'],'username')?>
		</select>
		</label><br />
		<?
	}
	?>
	<label>Descripción: <br />
	<textarea name='descrip' maxlength=255 rows=4 cols=50><?=$row['descrip']?></textarea>
	</label>
	<div class='submit_acciones'>
	<input type='hidden' name='file_id' value=<?=$file_id?> />
	<input type='hidden' name='modificar' value='1' />
	<input type='submit' value='Guardar cambios' />
	<a class='btn_link' href="carpetas.php?fid=<?=$folder_id?>" /><input type="button" value="Cancelar" /></a>
	</div>
</form>
</div>
	<?
}
