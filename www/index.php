<?
include "../include/lib.php";
include "../include/mysql.php";
include "../include/inicio.php";

if(!isset($_SESSION['user'])) {
	header('location: login.php');
}

cabecera('',true);
$row=mysql_fetch_assoc(mysql_query("select nombre from users where id={$_SESSION['user']['id']}"));
$nombre=$row['nombre'];
?>
<h1>Inicio</h1><hr />
<?###################### Bienvenida ######################?>
<div id="left">
<h3>Bienvenido a la biblioteca másonica virtual</h3>
Q:.H:. <?=$nombre?>:<br />
&nbsp; En este sitio podrás consultar material de interés del taller correspondiente a tu grado, como así también podrás subir documentos tales como planchas, libros, etc. para compartir con los demás HH:.<br />
&nbsp;Te recordamos que tu nombre de usuario y contraseña son datos estrictamente confidenciales y no debés compartirlos con nadie. De ello depende la privacidad de los documentos publicados en el sitio.

</div>
<?################## Tablero de anuncios #################?>
<div id="middle">
<h3>Tablero de Anuncios</h3>
	<?mostrar_anuncios();?>
</div>
<?###################### Favoritos #######################?>
<div id="right">
	<div id="reciente">
		<h3>Documentos recientes</h3>
		<?mostrar_ultimos();?>
	</div>
	<br />
	<div id="favoritos">
		<h3>Los más leídos</h3>
		<?mostrar_favoritos();?>
	</div>
</div>
<div class="clear"></div>
<?
########################## Pie ###########################
pie();
