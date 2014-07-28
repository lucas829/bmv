<?
# Formulario de envío de e-mail
###############################
function form_email() {
	if(isset($_POST['id_user'])) {
		$id_user=filter_input(INPUT_POST,'id_user',FILTER_SANITIZE_NUMBER_INT);
		?>
<h1>Enviar e-mail</h1><hr />
<div id="user" style="width: 600px">
		<?
		$query="select concat(nombre,' ', apellido) as nombre, email
					from users
					where id=$id_user";
		$result=mysql_query($query);
		$row=mysql_fetch_assoc($result);
		?>
	<form action="" method="POST">
		Para:  <input type="text" value="<?=$row['nombre']?>" disabled /><br />
		<label>Asunto: <input type="text" name="asunto" size=50 required autofocus /></label><br />
		<label>Texto:<br />
			<textarea name="texto" cols=60 rows=10 required ></textarea>
		</label>
		<div class="submit_acciones">
		<input type="hidden" name="email" value="<?=$row['email']?>" />
		<input type="submit" name="enviar_email" value="Enviar" />
		<a class="btn_link" href="secretaria.php">
			<input type="button" value="Cancelar"></input>
		</a>
		</div>
	</form>
</div>
		<?
	}
}

# Mostrar el cuadro lógico
##########################
function mostrar_cuadro() {
	?>
<h1>Cuadro Lógico</h1><hr />
<div align="center">
<table class="tabla">
<tr>
<th>Apellido</th>
<th>Nombres</th>
<th>e-mail</th>
<th>Teléfono</th>
<th>Grado</th>
<th>Cargo</th>
<th>Acciones</th>
</tr>
	<?
	$query="select users.id, users.apellido, users.nombre, users.email, users.telcel, grados.descrip as grado, cargos.descrip as cargo
			from users
				join grados on users.id_grado=grados.id 
				join cargos on users.id_cargo=cargos.id 
			order by apellido asc";
	$result=mysql_query($query);
	$i=0;
	while($row=mysql_fetch_assoc($result)) {
		if(espar($i)) {
			echo "<tr class='bg_par'>";
		} else {
			echo "<tr class='bg_impar'>";
		}
		?>
<td><?=$row['apellido']?></td>
<td><?=$row['nombre']?></td>
<td><?=$row['email']?></td>
<td><?=$row['telcel']?></td>
<td><?=$row['grado']?></td>
<td><?=$row['cargo']?></td>
<td class="td_submit">
	<form action="" method=POST>
		<input type="hidden" name="id_user" value="<?=$row['id']?>" />
		<input type="submit" class="button_mini" name="form_email" value="Enviar e-mail" />
	</form>
</td>
		<?
		$i++;
	}
	?>
</table>
</div>
<?
}

# Envío de mails del secretario
function enviar_email($para,$asunto,$texto) {
	$asunto=PROGRAM_NAME." - ".$asunto;
	$query="select concat(nombre,' ', apellido) as nombre, email
				from users
				where id={$_SESSION['user']['id']}";
	$result=mysql_query($query);
	$row=mysql_fetch_assoc($result);
	$texto=$texto."\r\n\r\n{$row['nombre']}\r\nSecretario";
	$headers="From: ".$row['nombre']." <".$row['email'].">\r\n";
	$headers.="Reply-to: {$row['email']}";

	mail($para,$asunto,$texto,$headers);
	header("location: secretaria.php");
}
