<?
# Muestra una lista de los últimos 5 archivos subidos al servidor
function mostrar_ultimos() {
	$query="select id, nombre, fecha 
				from docs 
				where (id_estado=1 or id_estado=3) 
						and id_grado<={$_SESSION['user']['grado']} 
				order by fecha desc 
				limit 0,5";
	$result=mysql_query($query);
	echo "<table>";
	while($row=mysql_fetch_assoc($result)) {
		?>
		<tr>
		<td width=250><a href='carpetas.php?file=<?=$row['id']?>'><?=$row['nombre']?></a></td>
		<td><?=date('d/m/Y',strtotime($row['fecha']))?></td>
		</tr>
		<?
	}
	echo "</table>";
}

# Muestra una lista de los 5 archivos más descargados
function mostrar_favoritos() {
	$query="select id, nombre, descargas 
				from docs 
				where (id_estado=1 or id_estado=3)
						and id_grado<={$_SESSION['user']['grado']}
				order by descargas desc
				limit 0,5";
	$result=mysql_query($query);
	echo "<table>";
	while($row=mysql_fetch_assoc($result)) {
		?>
		<tr>
		<td width=250><a href="carpetas.php?file=<?=$row['id']?>"><?=$row['nombre']?></a></td>
		<td><?=$row['descargas']?> descargas</td>
		</tr>
		<?
	}
	echo "</table>";
}

# Muestra los últimos anuncios
function mostrar_anuncios() {
	$hoy=date('Y-m-d',time());
	$query="select anuncios.id, anuncios.titulo, anuncios.texto, etiquetas.descrip as prioridad 
				from anuncios
					join etiquetas on anuncios.id_etiqueta=etiquetas.id
				where id_grado<={$_SESSION['user']['grado']} 
					and fecha_inicio<='$hoy' 
					and (fecha_fin>='$hoy' or fecha_fin='0000-00-00')
					and visible=1
				order by id_etiqueta asc, fecha_inicio desc
				limit 0,7";
	$result=mysql_query($query);
	echo "<div id='anuncios'>";
	echo "<table width=100%>";
	while($row=mysql_fetch_assoc($result)) {
		echo "<tr>";
		switch($row['prioridad']) {
			case "Urgente":
				echo "<td class='fila_urgente'>";
				break;
			case "Importante":
				echo "<td class='fila_importante'>";
				break;
			default:
				echo "<td class='fila_informativo'>";
				break;
		}
		echo "<a href='anuncios.php?id={$row['id']}'>{$row['titulo']}</a>";
		echo "</td>";
		echo "</tr>";
	}
	echo "</table>";
	echo "<div id='ver_mas'>";
	echo "<center><a href='anuncios.php'>Ver Más...</a></center>";
	echo "</div>";
	echo "</div>";
}
