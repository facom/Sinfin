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
  Modulo de Planes de Estudio
</div>
<div class="submenu">
  <a href="?">Inicio</a> 
  | <a href="?mode=mode">Modo</a>
</div>
<div class="container">
M;

////////////////////////////////////////////////////////////////////////
//BODY
////////////////////////////////////////////////////////////////////////
$content.=<<<C
<p>
En este módulo tendrá acceso a la información completa sobre los
Planes de Estudio de los programas de pregrado ofrecidos por la
Facultad de Ciencias Exactas y Naturales (FCEN).
</p>

<p>Aquí encontrará:</p>

<ul>

<li>Lista de los programas de pregrado ofrecidos por la FCEN.</li>

<li>Lista de las distintas versiones de los planes de estudio.</i>

<li>Planes de estudio detallados de los programas.</li>

<li>Diagramas de flujo curricular mostrando la relación entre los
cursos de un determinado programa.</li>

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
