<?
include "../include/lib.php";
require_once "../include/mysql.php";

if(isset($_FILES['archivo'])) {
	$upload_error='';
	if ($_FILES['archivo']['error'] > 0) {
	# Verifico archivo válido
	#########################
		switch($_FILES['archivo']['error']) {
			case 1:
			case 2:
				$upload_error="El archivo es demasiado grande";
				break;
			case 3:
				$upload_error="La subida del archivo fue interrumpida. Vuelva a intentarlo.";
				break;
			case 4:
				$upload_error="El archivo no es válido";
				break;
			default:
				$upload_error="Error: ".$_FILES['archivo']['error'];
				break;
		}
	} else {
		if(DEBUG==true) {
			echo "<pre>";
			print_r($_FILES);
			echo "</pre>";
		}
		# Verifico extensiones permitidas
		#################################
		$ext_allowed=array('gif','jpg','jpeg','png','cdr','doc','docx','xls','xlsx','ppt','pps','pptx','odt','ods','pdf','odp','rar','zip','txt','wav','mp3','ogg','rtf');
		$extension=strtolower(end(explode('.',$_FILES['archivo']['name'])));
		if(!in_array($extension, $ext_allowed)) {
			$upload_error="Error: El tipo de archivo no está permitido";
		} else {
			# Verifico tamaño del archivo
			#############################
			if($_FILES['archivo']['size']/1024 > MAX_FILE_UPLOAD_SIZE) {
				$upload_error="Error: El archivo es demasiado grande";
			} else {
				# Obtengo el nombre del archivo y encripto
				###################################################
				$filename=filtrar_nombre($_FILES['archivo']['name']);
				
				$hash=sha1($filename);
				# Verifico que no exista ese nombre de archivo
				$i=0;
				while(file_exists("../docs/$hash")) {
					if($i>=99) {
						$upload_error="Ocurrió un error inesperado. Consulte al administrador del sistema.";
						break;
					}
					$hash=sha1($hash);
					$i++;
				}
				# Muevo el archivo a la ruta de uploads
				#######################################
				if($upload_error=="") {
					if(!move_uploaded_file($_FILES['archivo']['tmp_name'],DOC_PATH.$hash)) {
						$upload_error="Error: no se pudo escribir el archivo en el servidor";
					} else {
						# Archivo subido correctamente, grabo en la BBDD
						################################################
						$titulo=FILTER_INPUT(INPUT_POST,'titulo',FILTER_SANITIZE_STRING);
						$descrip=FILTER_INPUT(INPUT_POST,'descrip',FILTER_SANITIZE_STRING);
						$id_carpeta=FILTER_INPUT(INPUT_POST,'id_carpeta',FILTER_SANITIZE_NUMBER_INT);
						$id_grado=FILTER_INPUT(INPUT_POST,'id_grado',FILTER_SANITIZE_NUMBER_INT);
						$date=date("Y-m-d");
						$size=$_FILES['archivo']['size'];
						$id_tipo=tipo_archivo($extension);
						$id_estado=2;
						$subido_por=$_SESSION['user']['id'];
	
						$query="insert into docs(nombre,filename,descrip,hash,id_carpeta,id_grado,fecha,size,id_tipo,id_estado,subido_por) 
									values('$titulo','$filename','$descrip','$hash',$id_carpeta,$id_grado,'$date',$size,$id_tipo,$id_estado,$subido_por)";
						if(DEBUG==true) {echo '$query='."<br />$query<br />";}
						mysql_query($query) or die(cabecera()."Ha ocurrido un error. Por favor informe al administrador del sitio");
						# Envío mail a los administradores que correspondan 
						# según el grado del documento
						###################################################
						$row=mysql_fetch_assoc(mysql_query("select concat(nombre,' ',apellido) as nombre from users where id=$subido_por"));
						$user=$row['nombre'];
						$query="select users.nombre, users.email 
									from users 
										join permisos on users.id_permiso=permisos.id 
									where permisos.id=1 and users.id_grado>=$id_grado;";
						$result=mysql_query($query);
						while($row=mysql_fetch_assoc($result)) {
							$para=$row['email'];
							$asunto=PROGRAM_NAME." - Archivo subido al servidor";
							$program_name=PROGRAM_NAME;
							$cuerpo=<<<TEXT
{$row['nombre']},
El usuario $user ha subido un archivo al servidor.

Titulo: $titulo
Grado: $id_grado

Para previsualizar el contenido, aprobarlo o rechazarlo, ingrese en la
biblioteca virtual, menu "Administracion" -> "Documentos pendientes".

Saludos,
El equipo de $program_name.
TEXT;
							$headers="From: ".PROGRAM_NAME." <".EMAIL_REPLY_TO.">";
							mail($para,$asunto,$cuerpo,$headers);
						}
					}
				}
			}
		}
	}
}

cabecera("Subir un documento", true);

# Formulario de subida
######################
?>
<h1>Subir un documento</h1>
<hr />
<div id="user" style="width: 500px">
<form action="subir.php" method=POST enctype="multipart/form-data">
	<?
	if(isset($upload_error) && $upload_error!='') {
		echo "<div class='error'>$upload_error</div><br />";
	}
	if(isset($_FILES['archivo']) && $upload_error=="") {
		echo "<div class='success'>El archivo ha sido enviado con éxito. Un administrador deberá aprobarlo para ser publicado, por lo cual no será visible inmediatamente.</div><br />";
	}
	?>
	<label>Seleccione un archivo: <input type=file name=archivo required /></label><br />
	<center><span class="little_font">El tamaño máximo de archivo permitido es de 8 megabytes.</span></center>
	<label>Título del documento: <input type=text name=titulo maxlength=255 required /></label><br />
	Seleccione una carpeta: <select name="id_carpeta">
		<?
		$query = "select * from carpetas";
		$result = mysql_query($query);
		while($row = mysql_fetch_assoc($result)) {
			echo "<option value=\"" . $row['id'] . "\">" . $row['descrip'] . "</option>";
		}
		?>
	</select><br />
	Visible por: <select name="id_grado">
		<?		#todo Este código debería ir en lib.php
		$query = "select * from grados where id <= {$_SESSION['user']['grado']}";
		$result = mysql_query($query);
		while($row=mysql_fetch_assoc($result)) {
			echo "<option value=\"" . $row['id'] . "\">" . $row['descrip'] . "</option>";
		}
		?>
	</select><br />
	Ingrese una breve descripción del archivo:<br />
	<textarea name="descrip" maxlength="255" rows=4 cols=50 ></textarea>
	<div class='submit_acciones'>
	<input type=submit value="Subir archivo" />
	</div>
</form>
</div>
<?
pie();
