<?
# Mostrar el detalle de un archivo reportado
############################################
function mostrar_reporte_archivo($file_id) {
	$query="select docs.id, docs.nombre, docs.filename, docs.descrip, docs.id_carpeta, grados.descrip as 'grado', docs.fecha, docs.size, docs.id_tipo, tipo.descrip as tipo_archivo, users.username from docs join grados on docs.id_grado=grados.id join users on docs.subido_por=users.id join tipo on docs.id_tipo=tipo.id where docs.id='$file_id'";
	$result=mysql_query($query);
	if(mysql_num_rows($result)>0) {
		$row=mysql_fetch_assoc($result);
		?>
<a href="reportados.php"><img src="../img/back.png" width=15px /> Volver a los archivos reportados</a>
<div class="detalle_fichero">
	<div class="detalle_titulo">
		<div class="detalle_icono">
			<?=insertar_icono($row['id_tipo'])?>
		</div>
		<div class="detalle_nombre">
			<a class="titulo" href="../docs/<?=$row['filename']?>" target="_blank">
			<?=$row['nombre']?>
			</a>
		</div>
		<div class="clear"></div>
	</div>
	<div class="detalle_extendido">
		<div class="detalle_datos">
			<strong>Nombre del archivo:</strong> <?=$row['filename']?><br />
			<strong>Tipo de archivo: </strong><?=$row['tipo_archivo']?><br />
			<strong>Tamaño: </strong><?echo round($row['size']/1024,0)?> Kb<br />
			<strong>Subido por: </strong><?=$row['username']?><br />
			<strong>Visible por: </strong><?=$row['grado']?><br />
			<strong>Descripción del archivo: </strong>
			<?
			if($row['descrip']!="") {
				echo $row ['descrip'];
			} else {
				echo "No hay información disponible";
			}
			?>
		</div>
		<div class="clear"></div>
	</div>
</div>
		<?
		mostrar_descrip_reportes($file_id);
		?>
		<div class="reporte_acciones">
		<form action="reportados.php" method=POST>
                <input type="hidden" name="id_file" value="<?=$file_id?>" />
                <input type="submit" class="button_mini" name="aprobar" value="aprobar" />
                <input type="submit" class="button_mini" name="eliminar" value="eliminar" />
                </form>
		</div>
		<?
	} else {
		echo "Ha ocurrido un error. El archivo solicitado no existe o está dañado.";
	}
}

# Mostrar la lista de archivos reportados
#########################################
function mostrar_reportados() {
	?>
	<h1>Documentos reportados</h1>
	<hr /><br />
	<div align="center">
	<?
	$query="select docs.id, docs.nombre, docs.filename, tipo.descrip as tipo, carpetas.descrip as carpeta, grados.descrip as grado, count(*) as cantidad 
		from reportes join docs on reportes.id_doc=docs.id join tipo on docs.id_tipo=tipo.id join carpetas on docs.id_carpeta=carpetas.id join grados on docs.id_grado=grados.id 
		where reportes.visible=1 and docs.id_estado=3 
		group by id_doc 
		order by cantidad desc";
	$result=mysql_query($query);
	if(mysql_num_rows($result)>0) {
		# Completo la lista de documentos reportados.
	?>
	<table class='tabla'>
	<tr>
	<th>Nombre</th>
	<th>Nombre del archivo</th>
	<th>Tipo</th>
	<th>Carpeta</th>
	<th>Visible por</th>
	<th>Reportes</th>
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
	<td><a href='reportados.php?file=<?=$row['id']?>'><?=$row['nombre']?></a></td>
	<td><?=$row['filename']?></td>
	<td><?=$row['tipo']?></td>
	<td><?=$row['carpeta']?></td>
	<td><?=$row['grado']?></td>
	<td><?=$row['cantidad']?></td>
	<td class="td_submit">
		<form action="reportados.php" method=POST>
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
		echo "No hay documentos reportados.<br /><br />";
	}
}

# Mostrar los reportes enviados al archivo
##########################################
function mostrar_descrip_reportes($file_id) {
	echo "<h2>Reportes enviados por los usuarios:</h2>";
	$query="select concat(users.apellido,', ',users.nombre) as nombre, reportes.descrip from reportes join users on reportes.id_user=users.id where reportes.id_doc=$file_id and reportes.visible=1";
	$result=mysql_query($query);
	while($row=mysql_fetch_assoc($result)) {
		echo "<div class='reporte'>";
		echo "<div class='reporte_nombre'>{$row['nombre']}<hr /></div>";
		echo "<div class='reporte_descrip'>{$row['descrip']}</div>";
		echo "</div>";
	}
}
