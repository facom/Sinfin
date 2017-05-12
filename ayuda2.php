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
$content="";
$content.=getHeaders();
$content.=getHead();
$content.=getMainMenu();

////////////////////////////////////////////////////////////////////////
//SUBMENU
////////////////////////////////////////////////////////////////////////
$submenu.=<<<M
<li class="level0"><a href="usuarios$VER.php?urlref=$urlref">Conectarse</a></li>
<li class="level1"><a href="actions$VER.php?action=Cerrar">Desconectarse</a></li>
M;
$content.=getSubMenu($submenu);

////////////////////////////////////////////////////////////////////////
//BODY
////////////////////////////////////////////////////////////////////////
$content.=getBody(100);
$content.=<<<C
<h3>Ayuda</h3>

<p>
Encuentre aquí ayuda (guías básicas, manuales, videotutoriales) sobre
el uso de la información y servicios de $SINFIN.
</p>
<h4>Video tutoriales</h4>

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
<!-- COMACA: REGISTRO DE ASISTENCIA -->
<iframe width="$WIDTHVID" height="$HEIGHTVID"
        src="https://www.youtube.com/embed/-f5j4er-484"
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
$content.=endBody();

////////////////////////////////////////////////////////////////////////
//LATERAL CONTENT
////////////////////////////////////////////////////////////////////////
$content.=getLateral(40);
$content.=<<<C
C;
$content.=endLateral();

////////////////////////////////////////////////////////////////////////
//FOOTER AND RENDER
////////////////////////////////////////////////////////////////////////
end:
$content.=getMessages();
$content.=getFooter();
echo $content;
?>
</html>
