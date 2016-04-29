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
  Ayuda
</div>
<div class="submenu">
  <a href="?">Inicio</a> 
  | <a href="#usuarios">Usuarios</a>
  <span class="level1">| <a href="#modulos_publicos">Módulos Públicos</a></span>
  <span class="level3">| <a href="#modulos_empleados">Módulos Empleados</a></span>
  <span class="level4">| <a href="#modulos_administrativos">Módulos Administrativos</a></span>
</div>
<div class="container">
M;

////////////////////////////////////////////////////////////////////////
//BODY
////////////////////////////////////////////////////////////////////////
$content.=<<<C
<p>
Encuentre aquí ayuda (guías básicas, manuales, videotutoriales) sobre
el uso de la información y servicios de $SINFIN.
</p>
<h3>Video tutoriales</h3>

<p>
A continuación encontrará guías en video al uso de la plataforma
$SINFIN. Se han organizado las guías de acuerdo al tipo de usuarios
que pueden usar cada servicio: visitantes, estudiantes y otros
usuarios registrados, empleados y profesores y administradores.
</p>

<div class="">
<a name="usuarios"></a>
<h4>Administración de Usuarios</h4>
<p>A continuación encontrará videotutoriales relacionados con la
administración de los usuarios en $SINFIN, la creación de cuentas, la
recuperación de contraseñas, etc.</p>
<center>
<iframe width="$WIDTHVID" height="$HEIGHTVID" src="https://www.youtube.com/embed/7rYOTNF-SfA" frameborder="0" allowfullscreen>
</iframe>
<iframe width="$WIDTHVID" height="$HEIGHTVID" src="https://www.youtube.com/embed/OhQmLkBX7Z0" frameborder="0" allowfullscreen>
</iframe>
</center>
</div>

<div class="level1">
<a name="modulos_publicos"></a>
<h4>Módulos Públicos</h4>
<p>Los módulos públicos son aquellos que pueden usar todos los usuarios registrados de la plataforma.</p>
<center>
<!-- MOVILIDAD -->
<iframe width="$WIDTHVID" height="$HEIGHTVID" src="https://www.youtube.com/embed/vpBmjn3pm2o" frameborder="0" allowfullscreen>
</iframe>
<!-- RECONOCIMIENTOS -->
<iframe width="$WIDTHVID" height="$HEIGHTVID"
        src="https://www.youtube.com/embed/O85cGBINggU"
        frameborder="0" allowfullscreen>
</iframe>
</center>
</div>

<div class="level2">
<a name="modulos_publicos"></a>
<h4>Módulos Empleados</h4>
<p>Los módulos de empleados corresponden a aquellos que pueden usar solamente los empleados, incluyendo profesores.</p>
<center>
$MANATWORK
</center>
</div>

<div class="level3">
<a name="modulos_publicos"></a>
<h4>Módulos Administrativos</h4>
<p>Los módulos administrativos están reservados exclusivamente para
los empleados administrativos, secretarias, coordinadores, directores
vicedecano y decano.</p>
<center>
$MANATWORK
</center>
</div>

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
