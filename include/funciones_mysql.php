<?
# Llenar un select con los datos de una tabla
#############################################
/*
Parámetros:
$tabla = recibe el nombre de la tabla sobre la cual se ejecutará la consulta
$seleccionado = En caso que se esté llenando un formulario de modificación
	de datos, es útil dejar seleccionado el item que ya estaba ingresado en
	la base de datos. El valor del argumento es el id o llave primaria de
	la tabla.
$campo = En caso de que la tabla no tenga un campo "descrip", se puede
	indicar otro.
$tope = Se usa cuando la lista de opciones depende del rango del usuario.
	El valor de $tope es el valor del rango del usuario ($_SESSION['user']['grado']).
*/
#############################################
function llenar_select($tabla,$seleccionado=null,$campo='descrip',$tope=null) {
   $seleccionado=filter_var($seleccionado,FILTER_SANITIZE_NUMBER_INT);
   $tabla=filter_var($tabla,FILTER_SANITIZE_STRING);
	$campo=filter_var($campo,FILTER_SANITIZE_STRING);
	if($campo=='') {
		$campo="descrip";
	}
	$tope=filter_var($tope,FILTER_SANITIZE_NUMBER_INT);
   $query="select id, $campo from $tabla";
	if($tope!=null) {
		$query.=" where id<=$tope";
	}
   $result=mysql_query($query);
   while($row=mysql_fetch_assoc($result)) {
      $string="";
      $string="<option value=".$row['id'];
      if($row['id']==$seleccionado) {
         $string.=" selected>";
      } else {
         $string.=" >";
      }
      $string.=$row[$campo]."</option>\n";
      echo $string;
   }
}

# Obtener nombre de un archivo
##############################
/*
Obtiene el nombre de un documento de acuerdo al id.
Parámetros:
$file_id = el ID del documento.
*/
##############################
function get_name($file_id) {
   $query="select nombre from docs where id='$file_id'";
   $result=mysql_query($query);
   $row=mysql_fetch_assoc($result);
   $file_name=$row['nombre'];
   return $file_name;
}
