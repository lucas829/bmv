<?
# Mostrar la tabla de usuarios
##############################
function listar_usuarios() {
	?>
<table class="tabla">
<tr>
<th>Id</th>
<th>Apellido</th>
<th>Nombre</th>
<th>Usuario</th>
<th>e-mail</th>
<th>teléfono</th>
<th>Grado</th>
<th>Cargo</th>
<th>Permiso</th>
<th>Acciones</th>
</tr>
	<?
	$query="select users.id, users.username, users.nombre, users.apellido, users.email, users.telcel, users.id_permiso, permisos.descrip as permisos, users.id_grado, grados.descrip as grado, cargos.descrip as cargo 
				from users join permisos on users.id_permiso=permisos.id 
					join grados on users.id_grado=grados.id 
					join cargos on users.id_cargo=cargos.id
				order by apellido asc";
	$result=mysql_query($query);
	$i=0;
	while($row=mysql_fetch_assoc($result)) {
	if(espar($i)){
                echo "<tr class='bg_par'>";
        } else {
                echo "<tr class='bg_impar'>";
        }		
	?>
<td><?=$row['id']?></td>
<td><?=$row['apellido']?></td>
<td><?=$row['nombre']?></td>
<td><?=$row['username']?></td>
<td><?=$row['email']?></td>
<td><?=$row['telcel']?></td>
<td><?=$row['grado']?></td>
<td><?=$row['cargo']?></td>
<td><?=$row['permisos']?></td>
<td class="td_submit">
        <form action="" method=POST>
        <input type="hidden" name="id_user" value="<?=$row['id']?>" />
        <input type="submit" class="button_mini" name="modificar" value="modificar" />
        <input type="submit" class="button_mini" name="eliminar" value="eliminar" />
        </form>
</td>
</tr>
	<?
	$i++;
	}
?>
</table>
<div class="submit_acciones">
	<a class="btn_link" href="alta_usuario.php"><input type="button" value="Nuevo usuario"></a>
</div>
<?
}

# Eliminar un usuario
##############################
function eliminar_usuario($user_id) {
	$user_id=filter_var($user_id,FILTER_SANITIZE_NUMBER_INT);
	$query="delete from users where id=$user_id";
	mysql_query($query);
	echo "<br /><span class='success'>Se ha eliminado el usuario.</span><br />";
}

# Modificar un usuario
##############################
function modificar_usuario($user_id) {
	$user_id=filter_var($user_id,FILTER_SANITIZE_NUMBER_INT);
	$query="select id, username, nombre, apellido, email, telcel, id_permiso, id_grado from users where users.id=$user_id";
	$result=mysql_query($query);
	$row=mysql_fetch_assoc($result);
	?>
<br />
<form action="" method=POST>
<div id="user" style="width: 500px">
	<fieldset>
	<legend>Datos de usuario</legend>
	<input type="hidden" name="id_user" value="<?=$row['id']?>" />
	id: <strong><?=$row['id']?></strong><br />
	Nombre de usuario: <strong><?=$row['username']?></strong><br />
	</fieldset>
	<fieldset>
	<legend>Datos personales</legend>
	<label>Nombre: <input type="text" name="nombre" maxlength=40 value="<?=$row['nombre']?>" required /></label><br />
	<label>Apellido: <input type="text" name="apellido" maxlength=40 value="<?=$row['apellido']?>" required /></label><br />
	<label>e-mail: <input type="email" name="email" maxlength=50 value="<?=$row['email']?>" required /></label><br />
	<label>Teléfono: <input type="text" name="telcel" maxlength=40 value="<?=$row['telcel']?>" /></label><br />
	</fieldset>
	<fieldset>
	<legend>Cambiar contraseña</legend>
	<label>Contraseña nueva: <input type="password" name="pass_1" /></label><br />
	<label>Repita la contraseña: <input type="password" name="pass_2" /></label><br />
	</fieldset>
	<fieldset>
	<legend>Grupos</legend>
	<label>Grado: 
	<select name="id_grado" >
		<?llenar_select('grados',$row['id_grado'])?>
	</select>
	</label><br />
	<label>Permisos: 
	<select name="id_permiso">
		<?llenar_select('permisos',$row['id_permiso'])?>
	</select>
	</label><br />
	<label>Cargo:
	<select name="id_cargo">
		<?llenar_select('cargos')?>
	</select>
	</label>
	</fieldset>
	<div class="submit_acciones">
	<input type="submit" name="guardar" value="Guardar cambios" /> 
	<a class="btn_link" href="users.php"><input type="button" value="Cancelar" /></a>
	</div>
</div>
</form>
	<?
}

# Guardar los datos del usuario
###############################
function guardar_usuario() {
	$id=filter_input(INPUT_POST,'id_user',FILTER_SANITIZE_NUMBER_INT);
	$nombre=trim(filter_input(INPUT_POST,'nombre',FILTER_SANITIZE_STRING));
	$apellido=trim(filter_input(INPUT_POST,'apellido',FILTER_SANITIZE_STRING));
	$email=trim(filter_input(INPUT_POST,'email',FILTER_SANITIZE_EMAIL));
	$telcel=trim(filter_input(INPUT_POST,'telcel',FILTER_SANITIZE_STRING));
	$pass_1=trim(filter_input(INPUT_POST,'pass_1',FILTER_SANITIZE_STRING));
	if($pass_1!="") {
		$pass_1=sha1($pass_1);
	}
	$pass_2=trim(filter_input(INPUT_POST,'pass_2',FILTER_SANITIZE_STRING));
	if($pass_2!="") {
		$pass_2=sha1($pass_2);
	}
	$id_grado=filter_input(INPUT_POST,'id_grado',FILTER_SANITIZE_NUMBER_INT);
	$id_cargo=filter_input(INPUT_POST,'id_cargo',FILTER_SANITIZE_NUMBER_INT);
	$id_permiso=filter_input(INPUT_POST,'id_permiso',FILTER_SANITIZE_NUMBER_INT);
	$error="";
	if(strlen($nombre)<4 or strlen($nombre)>40) {
		$error="El nombre debe tener entre 4 y 40 caracteres.<br />";
	}
	if(strlen($apellido)<4 or strlen($apellido)>40) {
		$error.="El apellido debe tener entre 4 y 40 caracteres.<br />";
	}
	if(strlen($email)>50) {
		$error.="El e-mail es demasiado largo.<br />";
	}
	if(strlen($telcel)!=0) {
		if(strlen($telcel)<7 or strlen($telcel)>40) {
			$error.="El número de teléfono no es válido.<br />";
		}
	}
	if($pass_1!="") {
		if($pass_1!=$pass_2) {
			$error.="Las contraseñas no coinciden";
		}
	}
	if($error=="") {
		$query="update users set 
					nombre='$nombre', 
					apellido='$apellido', 
					email='$email',
					telcel='$telcel',
					id_permiso=$id_permiso, 
					id_grado=$id_grado, 
					id_cargo=$id_cargo";
		if($pass_1!="") {
			$query.=", password='$pass_1'";
		}
		$query.=" where id=$id";
		mysql_query($query);
		echo "<br /><span class='success'>Se han guardado los cambios.</span><br />";
		listar_usuarios();
	} else {
		echo "<br /><span class='error'>Se han producido los siguientes errores:<br />$error</span>";
		modificar_usuario($id);
	}
}
