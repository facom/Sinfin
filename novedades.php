<html>
<?php
////////////////////////////////////////////////////////////////////////
//LOAD LIBRARY
////////////////////////////////////////////////////////////////////////
$HOST=$_SERVER["HTTP_HOST"];
$SCRIPTNAME=$_SERVER["SCRIPT_FILENAME"];
$ROOTDIR=rtrim(shell_exec("dirname $SCRIPTNAME"));
require("$ROOTDIR/etc/library.php");

////////////////////////////////////////////////////////////////////////
//INITIALIZATION
////////////////////////////////////////////////////////////////////////
$content="";
$content.=getHeaders();
$content.=getHead();
$content.=getMainMenu();

////////////////////////////////////////////////////////////////////////
//CONTENIDO
////////////////////////////////////////////////////////////////////////
$content.=<<<C
<h3>Novedades en $SINFIN</h3>
<p>
Estas son algunas novedades en la última versión de $SINFIN ($VERSION):
</p>

<ul>

<li>Se puede usar ahora la cuenta de usuario de Google (incluyendo la
cuenta institucional de la Universidad de Antioquia) para conectarse
en la plataforma.  Esto evita el manejo de un número excesivo de
contraseñas.</li>

<li>Se ha agregado un nuevo módulo, el módulo de Comunidad Académica,
que permite programar, registrar y consultar la asistencia a
actividades de la Comunidad académica de la Facultad.
</li>

</ul>

C;

////////////////////////////////////////////////////////////////////////
//FOOTER AND RENDER
////////////////////////////////////////////////////////////////////////
end:
$content.="</div>";
$content.=getMessages();
$content.=getFooter();
echo $content;
?>
</html>
