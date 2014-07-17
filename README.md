bmv
===
Lucas Ricardo Grana
<lucasrgrana@gmail.com>

Contenido
----------
* Documentación
* Requisitos
* Instalación
* Bugs
* Licencia


Documentación
--------------
A la fecha no hay documentación del sistema.

Requisitos
------------

Para instalar este sistema se requiere de un servidor web (apache, nginx, etc.)
capaz de correr scripts PHP (apache-mod-php, php5-fpm, php5-FCGI, php5-cgi, etc)
y con un servidor MySQL.


Instalación
------------

* Descomprima el archivo en un directorio, por ejemplo ejemplo /var/www/

* Configure su servidor web para que sirva el sitio publicado en /var/www/

* Cree una base de datos MySQL, cree un usuario con permisos sobre ella y configure
  esos datos en /var/www/config/config.php (dispone de un archivo de ejemplo en
  config/config.inc.php).

* Importe la base de datos a partir del esquema preferido en el directrio sql/
  Por ejemplo:
	mysql -u user -p dbbiblioteca < sql/db.sql

* Importe los datos iniciales (usuario, cargos, etc.)
  Por ejemplo:
	mysql -u user -p dbbiblioteca < sql/datos_iniciales.sql

* Acceda a su nuevo sistema; por defecto se crea un usuario 'admin' con contraseña 'admin'


Bugs:
-----

Este sistema está en estado beta, por lo que los bugs que se encuentren pueden estar
actualmente en proceso de corrección.
Si cree que encontró un error, envíenos un correo a <lrgrana at gmail.com>


Licencia
---------

El presente sistema se distribuye bajo licencia GNU Public License v3
Los terminos de la misma se encuentran en el archivo LICENSE
