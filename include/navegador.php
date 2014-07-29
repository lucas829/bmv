<?
# Mostrar las carpetas del servidor
###################################
function mostrar_carpetas() {
   ?>   
<h1>Explorar carpetas</h1>
<hr />
   <?   
   #Obtener listado de carpetas
   $query = "select * from carpetas order by descrip asc";
   $result = mysql_query($query);
   while($row = mysql_fetch_assoc($result)) {
      echo "<div class='carpeta'>";
      echo "<div class='nombre_carpeta'><a href='carpetas.php?fid={$row['id']}'>{$row['descrip']}</a></div>";
      $total=contar_archivos($row['id'],$_SESSION['user']['grado']);
      echo "<div class='total_archivos'>Archivos: $total</div>";
      echo "</div>";
   }    
}

# Mostrar los archivos de una carpeta
#####################################
function mostrar_archivos($id_folder, $page, $order, $deg) {	
	$query="select descrip from carpetas where id=$id_folder";
	$result=mysql_query($query);
	$row=mysql_fetch_assoc($result);
	?>
	<a href="carpetas.php"><img src="../img/back.png" width=15px /> Volver</a>
	<div id="carpeta_header">
		<div id="carpeta_titulo">
			<h1><?=$row['descrip']?></h1>
		</div>
		<div id="carpeta_opciones">
			<form action="carpetas.php" method="GET">
				<input type="hidden" name="fid" value="<?=$id_folder?>" />
				<input type="hidden" name="page" value="<?=$page?>" />
				Ordenar por: 
				<select name="order">
					<option <?valor_orden("name")?>>Nombre del documento</option>
					<option <?valor_orden("date_asc")?>>Fecha (Más antiguos primero)</option>
					<option <?valor_orden("date_desc")?>>Fecha (Más recientes primero)</option>
					<option <?valor_orden("username")?>>Subido por</option>
				</select>
				Grado:
				<select name="deg">
					<option <?valor_grado(0)?>>Todos</option>
					<option <?valor_grado(1)?>>Aprendices</option>
					<option <?valor_grado(2)?>>Compañeros</option>
					<option <?valor_grado(3)?>>Maestros</option>
				</select>
				<input type="submit" value="Ordenar" />
			</form>
		</div>
	</div>
	<hr />
	<div class='archivos'>
	<?

	$query="select count(*) 
				from docs 
				where ";
	if($deg==0) {
		$query.="docs.id_grado<={$_SESSION['user']['grado']} ";
	} else {
		$query.="docs.id_grado=$deg ";
	}
	$query.="and docs.id_carpeta=$id_folder and (docs.id_estado=1 or docs.id_estado=3)";
	$result=mysql_query($query);
	$row=mysql_fetch_row($result);
	$total=$row[0];
	if($total>0) {
		$total_pages=ceil($total/10);
		if($page>$total_pages) {	# Si no existe la página, muestro la primera
			$page=1;
		}
		$indice=$page*10-10;	# Página 1: limit 0, 10; Página 2: limit 10, 10; etc.
		$query="select docs.id, docs.nombre, docs.filename, docs.fecha, docs.id_tipo, docs.id_estado, docs.subido_por, users.username 
					from docs join users on docs.subido_por=users.id 
					where ";
		# Grado?
	if($deg==0) {
		$query.="docs.id_grado<={$_SESSION['user']['grado']} and ";
	} else {
		$query.="docs.id_grado=$deg and ";
	}
		$query.="docs.id_carpeta=$id_folder and 
						(docs.id_estado=1 or docs.id_estado=3) 
					order by ";
		# Ordenamiento
		switch($order) {
			case "name":
				$query.="docs.nombre asc ";
				break;
			case "date_asc":
				$query.="docs.fecha asc ";
				break;
			case "date_desc":
				$query.="docs.fecha desc ";
				break;
			case "username";
				$query.="docs.subido_por asc ";
				break;
			default:
				$query.="docs.nombre asc ";
				break;
		}
		$query.= "limit $indice, 10";

		$result=mysql_query($query);
		while($row=mysql_fetch_assoc($result)) {
			echo "<div class='archivo'>\n";
			echo "\t<div class='icono'>\n\t";
			echo insertar_icono($row['id_tipo']);
			echo "\t</div>\n";
			echo "\t<div class='filename'>\n";
			echo "\t<a href='carpetas.php?file=".$row['id']."'>".$row['nombre'];
			if($row['id_estado']==3) {	# Reportado
				echo "<span class='error'> * </span>";
			}
			echo "</a>\n";
			echo "\t</div>\n";
			echo "\t<div class='fecha_upload'>\n";
			echo "\tSubido el ".date('d/m/Y',strtotime($row['fecha']))." por ".$row['username'];
			echo "\t</div>\n";
			# Acciones disponibles para el archivo
			echo "\t<div class='acciones_archivos'>\n";
			echo "\t<form action='dopt.php' method=POST>\n";
			echo "\t\t<input type='hidden' name='file_id' value={$row['id']} />\n";
			echo "\t\t<input type='submit' class='opciones_lista' name='accion' value='reportar' />\n";
			if($row['subido_por']==$_SESSION['user']['id'] || $_SESSION['id_permiso']==1) {	# Administrador?
				echo "\t\t<input type='submit' class='opciones_lista' name='accion' value='editar' />\n";
			}
			if($row['subido_por']==$_SESSION['user']['id'] || $_SESSION['id_permiso']==1) {	# Administrador?
				echo "\t\t<input type='submit' class='opciones_lista' name='accion' value='eliminar' />\n";
			}
			echo "\t</form>\n";
			echo "\t</div>\n";
			echo "\t<div class='clear'></div>\n";
			echo "</div>\n";
		}
		?>
		</div>
		<div id="nav">
		<br />
		<?
		# Menú de navegación
		####################
		echo "Página: ";
		for($i=1;$i<=$total_pages;$i++) {
			if($i==$page) {	# página actual
				echo "<span class='nav_actual'>$i</span>";
			} else {	# otra página
				echo "<a href='carpetas.php?fid=$id_folder&page=$i&order=$order'>$i</a>";
			}
		}
		?>
		</div>
		<br />&nbsp;Los archivos marcados con un <span class='error'>*</span> han sido reportados y aguardan la moderación de un administrador.<br /><br />
		<?
	} else {	# No hay archivos
		echo "<div id='user'>";
		echo "No existen archivos en esta categoría.";
		echo "</div>";
	}
}

# Mostrar el detalle de un archivo
##################################
function detalles_archivo($file_id) {
	$query="select docs.id, docs.nombre, docs.filename, docs.descrip, docs.id_carpeta, grados.descrip as 'grado', docs.fecha, docs.size, docs.id_tipo, tipo.descrip as tipo_archivo, users.username from docs join grados on docs.id_grado=grados.id join users on docs.subido_por=users.id join tipo on docs.id_tipo=tipo.id where docs.id='$file_id'";
	$result=mysql_query($query);
	if(mysql_num_rows($result)>0) {
		$row=mysql_fetch_assoc($result);
		?>
<a href="carpetas.php?fid='<?=$row['id_carpeta']?>'"><img src="../img/back.png" width=15px /> Volver a la carpeta</a>
<div class="detalle_fichero">
	<div class="detalle_titulo">
		<div class="detalle_icono">
			<?=insertar_icono($row['id_tipo'])?>
		</div>
		<div class="detalle_nombre">
			<?=$row['nombre']?>
		</div>
		<div class="clear"></div>
	</div>
	<div class="detalle_extendido">
		<div class="detalle_descargar">
			<a href="download.php?file=<?=$row['id']?>" target="_blank">
			<img src="../img/descargar.png" /><br />
			DESCARGAR
			</a>
		</div>
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
	} else {
		echo "Ha ocurrido un error. El archivo solicitado no existe o está dañado.";
	}
}

# Insertar el icono que corresponde al tipo de archivo
######################################################
function insertar_icono($tipo=1) {
	switch($tipo) {
		case 1:
			$imagen="ico_otro.png";
			break;
		case 2:
			$imagen="ico_word.png";
			break;
		case 3:
			$imagen="ico_excel.png";
			break;
		case 4: 
			$imagen="ico_ppt.png";
			break;
		case 5: 
			$imagen="ico_pdf.png";
			break;
		case 6:
			$imagen="ico_txt.png";
			break;
		case 7:
		case 8:
		case 9:
		case 10:
		case 11:
			$imagen="ico_imagen.png";
			break;
		case 12:
		case 13:
			$imagen="ico_comprimido.png";
			break;
		case 14:
			$imagen="ico_cdr.png";
			break;
		case 15:
		case 16:
		case 17:
			$imagen="ico_odf.png";
			break;
		case 18:
		case 19:
		case 20:
			$imagen="ico_audio.png";
			break;
		case 21:
			$imagen="ico_word.png";
			break;
		default:
			$imagen="ico_otro.png";
			break;
	}
	$code_icon="<img src='../img/".$imagen."' width='32' />\n";
	return $code_icon;
}

function contar_archivos($id_carpeta, $id_grado) {
        $query="select * from docs where id_carpeta=$id_carpeta and id_grado<=$id_grado and (id_estado=1 or id_estado=3)";
        $result=mysql_query($query);
        $total=mysql_num_rows($result);
        if($total > 0) {
                return $total;
        } else {
                return 0;
        }
};

function valor_orden($valor) {
	global $order;
	if($valor==$order) {
		echo "value=\"$valor\" selected";
	} else {
		echo "value=\"$valor\"";
	}
}
function valor_grado($valor) {
	global $deg;
	if($valor==$deg) {
		echo "value=\"$valor\" selected";
	} else {
		echo "value=\"$valor\"";
	}
}
