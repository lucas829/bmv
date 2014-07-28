<?

include '../include/lib.php';
include '../include/mysql.php';

if(isset($_POST['enviar'])) {
	$asunto=PROGRAM_NAME." - ";
	$asunto.=trim(filter_input(INPUT_POST,'asunto',FILTER_SANITIZE_STRING));
	$query="select concat(nombre,' ', apellido) as 'nombre' from users where id=".$_SESSION['user']['id'];
	$result=mysql_query($query);
	$row=mysql_fetch_assoc($result);
	$cuerpo="El ".date("d/m/Y")." ".$row['nombre']." escribió:\n\r\n\r";
	$cuerpo.=trim(filter_input(INPUT_POST,'mensaje',FILTER_SANITIZE_STRING));
	$para=ADMIN_EMAIL;
	$headers="From: ".PROGRAM_NAME." <".EMAIL_REPLY_TO.">";
	mail($para,$asunto,$cuerpo,$headers);
	$mensaje="<br /><span class='success'>Su mensaje ha sido enviado.</span>";
}

cabecera('Reporte de errores',true);
?>
<h1>Reportar un error o sugerencia</h1><hr />
<div id="user" style="width: 500px">
<?
if(isset($mensaje)) {
	echo $mensaje;
}
?>
<form action="" method=POST>
<label>Asunto: <input type="text" name="asunto" required autofocus /></label><br />
<label>Describa su problema o sugerencia: <br />
<textarea name="mensaje" cols=50 rows=5></textarea></label><br />
<div class="submit_acciones">
<input type="submit" name="enviar" value="Enviar" />
</div>
</form>
</div>
<?
pie();
