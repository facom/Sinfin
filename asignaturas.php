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
//SUBMENU
////////////////////////////////////////////////////////////////////////
$content.=<<<M
<div class="moduletitle">
  Modulo de Planes de Asignatura
</div>
<div class="submenu">
  <a href="?">Inicio</a> 
  <span class="level5">| <a href="?mode=mode">Modo</a></span>
</div>
<div class="container">
M;

////////////////////////////////////////////////////////////////////////
//BODY
////////////////////////////////////////////////////////////////////////
$content.=<<<C
<p>
En este módulo tendrá acceso a la información completa sobre los
Planes de Asignatura de los cursos ofrecidos en los programas de la FCEN.
</p>

<p>Aquí encontrará:</p>

<ul>

<li>Lista completa de las asignaturas ofrecidas en cada programa.</li>

<li>Contenidos completos y actualizados de las asignaturas.</li>

</ul>
$MANATWORK

<p>
Mientras implementamos este módulo puede ver los Planes de Estudio en
este enlace: <a href=http://bit.ly/fcen-planes-asignatura>http://bit.ly/fcen-planes-asignatura</a>
</p>
C;

////////////////////////////////////////////////////////////////////////
//FOOTER AND RENDER
////////////////////////////////////////////////////////////////////////
$content.="</div>";
$content.=getMessages();
$content.=getFooter();
echo $content;
?>
</html>
