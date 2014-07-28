<?
# Contar la cantidad de anuncios
################################
function contar_anuncios($id_grado) {
	$query="select count(*) as total from anuncios where id_grado<=$id_grado";
	$result=mysql_query($query);
	$row=mysql_fetch_assoc($result);
	return $row['total'];
};

# Mostrar todos los anuncios
############################
function mostrar_anuncios($page=1) {
	?>
<h1>Anuncios</h1><hr />
<div align="center">

   <?##### Menú de navegación #####
   $total=contar_anuncios($_SESSION['user']['grado']);
	$total_pages=ceil($total/15);
	if($page>$total_pages) {
		$page=1;		# Si no existe la página, muestro la primera
	}
	$indice=$page*15-15; # Página 1: limit 0, 15; Página 2: limit 15, 15; etc.
	$hoy=date('Y-m-d',time());
   $query="select anuncios.id, anuncios.titulo, anuncios.texto, etiquetas.descrip as prioridad, concat(users.apellido,', ',users.nombre) as nombre
				from anuncios
					join etiquetas on anuncios.id_etiqueta=etiquetas.id
					join users on anuncios.id_user=users.id
				where anuncios.id_grado<={$_SESSION['user']['grado']} 
					and fecha_inicio<='$hoy' 
					and (fecha_fin>='$hoy' or fecha_fin='0000-00-00')
					and visible=1
				order by id_etiqueta asc, fecha_inicio desc
				limit $indice, 15";
	$result=mysql_query($query);
	?>
<table class="tabla" width="800px">
	<tr>
		<th>Título</th>
		<th>Publicado por</th>
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
<td>
		<?
		switch($row['prioridad']) {
         case "Urgente":
            echo "<span class='urgente'>";
            break;
         case "Importante":
            echo "<span class='importante'>";
            break;
         default:
            echo "<span class='informativo'>";
            break;
      }
		?>
<a href="anuncios.php?id=<?=$row['id']?>"><?=$row['titulo']?></a>
</span>
</td>
<td><?=$row['nombre']?></td>
</tr>
		<?
		$i++;
	}
	?>
</table>
</div>
<div id="nav">
<br />
   <?
	# Menú de navegación entre las páginas
	echo "Página: ";
	for($i=1;$i<=$total_pages;$i++) {
		if($i==$page) {   # página actual
			echo "<span class='nav_actual'>$i</span>";
		} else { # otra página
			echo "<a href='anuncios.php?page=$i'>$i</a>";
		}
	}
	?>
</div>
<br />
	<?
}

# Mostrar el detalle de un anuncio
##################################
function detalle_anuncio($id_anuncio) {
	$query="select anuncios.id, anuncios.titulo, anuncios.texto, anuncios.id_user, concat(users.apellido, ', ',users.nombre) as nombre, grados.descrip as grado
				from anuncios join users on anuncios.id_user=users.id
					join grados on anuncios.id_grado=grados.id
				where anuncios.id=$id_anuncio";
	$result=mysql_query($query);
	$row=mysql_fetch_assoc($result);
	# Acomodamos los saltos de línea del texto.
	$texto=str_replace("\r\n","<br />",$row['texto']);
	cabecera();
	?>
<a href="anuncios.php"><img width="15px" src="../img/back.png"></img> Volver a la lista</a>
<div id="user" style="width: 700px">
	<div id="anuncio_titulo"><?=$row['titulo']?></div>
	<div id="anuncio_enviado_por">Enviado por: <?=$row['nombre']?></div>
	<div id="anuncio_visible_por">Visible por: <?=$row['grado']?></div>
	<div id="anuncio_cuerpo">
		<?=$texto?>
	</div>
	<div class="submit_acciones">
		<form action="" method="POST">
		<?
		if($row['id_user']==$_SESSION['user']['id']) {	# Si el usuario es quien subió el anuncio, puede modificarlo
		?>
		<input type="hidden" name="id_anuncio" value="<?=$row['id']?>" />
		<input type="submit" name="accion" value="Eliminar" />
		<input type="submit" name="accion" value="Modificar" />
		<?
		}
		?>
		</form>
	</div>
</div>
	<?
	pie();
}

# Crear un anuncio nuevo
################################
include_once "../include/funciones_mysql.php";
function form_crear_anuncio() {
	cabecera();
	?>
<link href="css/jquery.datepick.css" rel="stylesheet">
<script src="../js/jquery.min.js"></script>
<script src="../js/jquery.plugin.js"></script>
<script src="../js/jquery.datepick.min.js"></script>
<script src="../js/jquery.datepick-es-AR.js"></script>
<script>
$(function() {
	$('#fechaInicio').datepick({dateFormat: 'dd/mm/yyyy'});
	$('#fechaFin').datepick({dateFormat: 'dd/mm/yyyy'});
});
</script>

<h1>Publicar un anuncio</h1><hr />
<div id="user" style="width: 700px">
	<form action="" method="POST">
		<label>Título: <input type="text" name="titulo" size=50 maxlength=255 required autofocus /></label><br />
		<label>Visible por: 
			<select name="id_grado"><?llenar_select("grados",1,'',$_SESSION['user']['grado'])?></select>
		</label><br />
		Fecha de inicio de la publicación: <input name="fecha_inicio" id="fechaInicio" value="<?=date('d/m/Y',time())?>"></input> <br />
		Fecha de fin de publicación: <input name="fecha_fin" id="fechaFin" value="Nunca"></input><br />
		<label>Prioridad: <select name="id_etiqueta"><?llenar_select("etiquetas",3)?></select></label><br />
		<label>Texto del anuncio:<br /><textarea name="texto" cols=80 rows=10 maxlength=16777214 required></textarea></label>
		<div class="submit_acciones"><input type="submit" value="Publicar" /></div>
	</form>
</div>
	<?
	pie();
}

# Formulario de eliminar un anuncio
###################################
function form_eliminar_anuncio($id_anuncio) {
	cabecera();
	?>
<h1>Eliminar</h1>
<div id="user" style="width: 500px">
	<form action="" method=POST>
		<h2>¿Confirma la eliminación del anuncio?</h2>
		<div class="submit_acciones">
			<input type="hidden" name="id_anuncio" value="<?=$id_anuncio?>" />
			<input type="hidden" name="accion" value="confirma_eliminar" />
			<input type="submit" value="Sí, eliminar" />
			<a class="btn_link" href="anuncios.php?id=<?=$id_anuncio?>"><input type="button" value="Cancelar" /></input></a>
		</div>
	</form>
</div>
	<?
	pie();
}

# Eliminar un anuncio
################################
function eliminar_anuncio($id_anuncio) {
	$query="update anuncios set visible=0 where id=$id_anuncio";
	mysql_query($query);
	header("location: anuncios.php");
}

# Formulario de edición de un anuncio
#####################################
function form_editar_anuncio($id_anuncio) {
	$query="select id_grado, titulo, texto, fecha_inicio, fecha_fin, id_etiqueta 
				from anuncios 
				where id=$id_anuncio";
	$result=mysql_query($query);
	$row=mysql_fetch_assoc($result);
	cabecera();
	?>
<link href="css/jquery.datepick.css" rel="stylesheet">
<script src="../js/jquery.min.js"></script>
<script src="../js/jquery.plugin.js"></script>
<script src="../js/jquery.datepick.min.js"></script>
<script src="../js/jquery.datepick-es-AR.js"></script>
<script>
$(function() {
   $('#fechaInicio').datepick({dateFormat: 'dd/mm/yyyy'});
   $('#fechaFin').datepick({dateFormat: 'dd/mm/yyyy'});
});
</script>
<h1>Editar anuncio</h1>
<div id="user" style="width: 700px">
   <form action="" method="POST">
      <label>Título: <input type="text" name="titulo" value="<?=$row['titulo']?>" size=50 maxlength=255 required autofocus /></label><br />
      <label>Visible por:
         <select name="id_grado"><?llenar_select("grados",$row['id_grado'],'',$_SESSION['user']['grado'])?></select>
      </label><br />
      Fecha de inicio de la publicación: <input name="fecha_inicio" id="fechaInicio" value="<?=fecha_humana($row['fecha_inicio'])?>"></input> <br />
      Fecha de fin de publicación: <input name="fecha_fin" id="fechaFin" value="<?=fecha_humana($row['fecha_fin'])?>"></input><br />
      <label>Prioridad: <select name="id_etiqueta"><?llenar_select("etiquetas",$row['id_etiqueta'])?></select></label><br />
      <label>Texto del anuncio:<br /><textarea id="texto" name="texto" cols=80 rows=10 maxlength=16777214 required><?=$row['texto']?></textarea></label>
      <div class="submit_acciones">
			<input type="hidden" name="id_anuncio" value="<?=$id_anuncio?>" />
			<input type="hidden" name="accion" value="confirma_modificar" />
			<input type="submit" value="Guardar cambios" />
			<a class="btn_link" href="anuncios.php?id=<?=$id_anuncio?>"><input type="button" value="Cancelar" /></input></a>
		</div>
   </form>
</div>
	<?
	pie();
}

# Guardar un anuncio en la BBDD
###############################
function guardar_anuncio($id_anuncio=null) {
	$titulo=filter_input(INPUT_POST,'titulo',FILTER_SANITIZE_STRING);
	$id_grado=filter_input(INPUT_POST,'id_grado',FILTER_SANITIZE_NUMBER_INT);
	$fecha_inicio=filter_input(INPUT_POST,'fecha_inicio',FILTER_SANITIZE_STRING);
	$fecha_fin=filter_input(INPUT_POST,'fecha_fin',FILTER_SANITIZE_STRING);
	$id_etiqueta=filter_input(INPUT_POST,'id_etiqueta',FILTER_SANITIZE_NUMBER_INT);
	$texto=filter_input(INPUT_POST,'texto',FILTER_SANITIZE_STRING);
		#todo verificar que la longitud máxima sea de 16777214 caracteres
	$fecha_inicio=crear_fecha($fecha_inicio);
	if($fecha_fin=="Nunca") {
		$fecha_fin="0";
	} else {
		$fecha_fin=crear_fecha($fecha_fin);
	}
	if($id_anuncio===null) {
		$query="insert into anuncios(id_grado,titulo,texto,id_user,fecha_inicio,fecha_fin,id_etiqueta,visible) 
 					values($id_grado,'$titulo','$texto',{$_SESSION['user']['id']},'$fecha_inicio','$fecha_fin',$id_etiqueta,1)";
		
		mysql_query($query);
		# Enviar mail a los usuarios
		############################
		$query="select nombre, email 
					from users 
					where id_grado >= $id_grado";
		$result=mysql_query($query);
		while($row=mysql_fetch_assoc($result)) {
			$para=$row['email'];
			$program_name=PROGRAM_NAME;
			$program_url=PROGRAM_URL;
			$asunto="$program_name - Nuevo anuncio";
			$cuerpo= <<<TEXT
{$row['nombre']},
Un nuevo anuncio ha sido subido al servidor.

Para leerlo ingrese en la Biblioteca Virtual
$program_url

Saludos,
El equipo de $program_name.
TEXT;
		$from=EMAIL_FROM;
		$reply_to=EMAIL_REPLY_TO;
		$headers="From: $program_name <$from>\r\n";
		$headers="Reply-to: <$reply_to>";
		mail($para,$asunto,$cuerpo,$headers);
		}
	} else {
		$query="update anuncios set 
						id_grado=$id_grado, 
						titulo='$titulo', 
						texto='$texto',
						fecha_inicio='$fecha_inicio', 
						fecha_fin='$fecha_fin', 
						id_etiqueta=$id_etiqueta
					where id=$id_anuncio";
	}
	mysql_query($query);
	cabecera();
	mostrar_anuncios();
	pie();
}
