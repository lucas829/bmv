<?
mysql_connect(DB_HOST,DB_USER,DB_PASS) 
	or die('Error: No se pudo conectar al servidor. Por favor revise la configuración en el archivo config/config.php');
mysql_select_db(DB_NAME)
	or die('Error: no se pudo conectar a la base de datos. Por favor revise la configuración en el archivo config/config.php');
mysql_query("SET NAMES 'utf8'");
