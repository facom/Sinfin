<!DOCTYPE html>
<html prefix="og: http://ogp.me/ns#" class="no-js" xmlns="http://www.w3.org/1999/xhtml" xml:lang="" lang="" slick-uniqueid="3">
<?php
////////////////////////////////////////////////////////////////////////
//LOAD LIBRARY
////////////////////////////////////////////////////////////////////////
$HOST=$_SERVER["HTTP_HOST"];
$SCRIPTNAME=$_SERVER["SCRIPT_FILENAME"];
$ROOTDIR=rtrim(shell_exec("dirname $SCRIPTNAME"));
require("$ROOTDIR/etc/library2.php");

////////////////////////////////////////////////////////////////////////
//INITIALIZATION
////////////////////////////////////////////////////////////////////////
$content="<body>";
$content.=getHeaders();
$content.=getHead();
$content.=getMainMenu();

////////////////////////////////////////////////////////////////////////
//SUBMENU
////////////////////////////////////////////////////////////////////////
$submenu=<<<M
<li class="level0"><a href="usuarios$VER.php?urlref=$urlref">Conectarse</a></li>
<li class="level1"><a href="usuarios$VER.php?action=Cerrar">Desconectarse</a></li>
M;
$content.=getSubMenu($submenu);

////////////////////////////////////////////////////////////////////////
//BODY
////////////////////////////////////////////////////////////////////////
$content.=getBody(60);
$content.=<<<C

<h3>Bienvenidos a SInfIn</h3>

<p>
El <b>Sistema de Información Curricular Integrada (SInfIn)</b> es una
plataforma de información y servicios de la <b>Facultad de
Ciencias Exactas y Naturales</b> (FCEN) desarrollada con el propósito
de complementar los sistemas de información y aplicaciones de la
Universidad.
</p>

<p>
La información más importante que reposa en $SINFIN tiene que ver con
los programas académicos que ofrece la FCEN, planes de estudio, planes
de asignatura, programación académica, entre otras.
</p>

<p>
Adicionalmente $SINFIN ofrece acceso a información y aplicaciones de
especialmente dirigidos a los estudiantes, profesores y empleados de
la Facultad.
</p>

<p class="level0" style="color:blue">
Para acceder debe registrarse y crear una cuenta de usuario o usar una
cuenta existente.
</p>
C;
$content.=endBody();

////////////////////////////////////////////////////////////////////////
//LATERAL CONTENT
////////////////////////////////////////////////////////////////////////
$content.=getLateral(40);
$content.=<<<C
<div style="width:100%;height:40vh;">
<iframe width="100%" height="315" src="https://www.youtube.com/embed/zBa1tM4j7MI" frameborder="0" allowfullscreen></iframe>
</div>
C;
$content.=endLateral();

////////////////////////////////////////////////////////////////////////
//FOOTER AND RENDER
////////////////////////////////////////////////////////////////////////
$content.=getMessages();
$content.=getFooter();
echo $content;
?>
</html>
