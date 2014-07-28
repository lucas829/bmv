<?
include "../include/lib.php";
include "../include/mysql.php";
include "../include/funciones_secretaria.php";

# Es Secretario?
$query="select id_cargo 
			from users
			where id={$_SESSION['user']['id']}";
$result=mysql_query($query);
$row=mysql_fetch_assoc($result);
if($row['id_cargo']!=6) {
	header("location: index.php");
}

cabecera("Secretaría",true);

if(isset($_POST['form_email'])) {
	form_email();
} elseif(isset($_POST['enviar_email'])) {
	$para=trim(filter_input(INPUT_POST,'email',FILTER_SANITIZE_EMAIL));
	$asunto=trim(filter_input(INPUT_POST,'asunto',FILTER_SANITIZE_STRING));
	$texto=trim(filter_input(INPUT_POST,'texto',FILTER_SANITIZE_STRING));
	enviar_email($para,$asunto,$texto);
} else {
	mostrar_cuadro();
}

pie();
