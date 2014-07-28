<?
include '../include/lib.php';
include_once '../include/mysql.php';
include '../include/navegador.php';

cabecera('Publicaciones pendientes',true);
if(isset($_SESSION['id_permiso']) && $_SESSION['id_permiso'] == 1 ) {	# Administrador?

# Acciones
#############################
if(isset($_POST['eliminar'])) {	# Eliminar un documento
	$id_file=filter_input(INPUT_POST,'id_file',FILTER_SANITIZE_NUMBER_INT);
	if($id_file>0) {
		eliminar_archivo($id_file);
	}
} elseif(isset($_POST['aprobar'])) {	# Aprobar la publicación del documento
	$id_file=filter_input(INPUT_POST,'id_file',FILTER_SANITIZE_NUMBER_INT);
	if($id_file>0) {
		$query="update docs set id_estado=1 where id=$id_file";
		mysql_query($query);
	}
	
}

# Listado de archivos pendientes
################################
?>
<h1>Documentos pendientes de publicación</h1>
<hr /><br />
<div align="center">
<?

$query="select docs.id, docs.nombre, docs.filename, docs.id_tipo, tipo.descrip as tipo, carpetas.descrip as carpeta, grados.descrip as grado, users.username from docs 
	join tipo on docs.id_tipo=tipo.id 
	join carpetas on docs.id_carpeta=carpetas.id 
	join grados on docs.id_grado=grados.id 
	join users on docs.subido_por=users.id 
	where docs.id_estado=2 and
		docs.id_grado<={$_SESSION['user']['grado']}";
$result=mysql_query($query);
if(mysql_num_rows($result)>0) {
	# Completo la lista de documentos pendientes.
?>
<table class='tabla'>
<tr>
<th>Nombre</th>
<th>Nombre del archivo</th>
<th>Tipo</th>
<th>Carpeta</th>
<th>Visible por</th>
<th>Subido por</th>
<th>Acciones</th>
</tr>
<?
	$i=0;
	while($row=mysql_fetch_assoc($result)) {
	if(espar($i)){
                echo "<tr class='bg_par'>";
        } else {
                echo "<tr class='bg_impar'>";
        }
?>
<td><a href='download.php?file=<?=$row['id']?>' target='_blank'><?=$row['nombre']?></a></td>
<td><?=$row['filename']?></td>
<td><?=$row['tipo']?></td>
<td><?=$row['carpeta']?></td>
<td><?=$row['grado']?></td>
<td><?=$row['username']?></td>
<td class="td_submit">
	<form action="pendientes.php" method=POST>
	<input type="hidden" name="id_file" value="<?=$row['id']?>" />
	<input type="submit" class="button_mini" name="aprobar" value="aprobar" />
	<input type="submit" class="button_mini" name="eliminar" value="eliminar" />
	</form>
</td>
</tr>
	<?
	$i++;
	}
?>
</table>
</div>
<?
} else {
	echo "No hay documentos pendientes de publicación.<br /><br />";
}

} else {
	echo "<br />No tiene los permisos suficientes para ver esta página<br /><br />";
}

pie();
