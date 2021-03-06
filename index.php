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
<div class="container">
M;

////////////////////////////////////////////////////////////////////////
//ACTIVE PART
////////////////////////////////////////////////////////////////////////
if(isset($action)){
  if(0){}else
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //CONFIRMAR
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  if($action=="confirmar"){
  }
}

////////////////////////////////////////////////////////////////////////
//BODY
////////////////////////////////////////////////////////////////////////
$content.=<<<C
<p>¡Bienvenido al <b>Sistema de Información Curricular Integrada (SInfIn)</b>!</p>

<p><b>SInFIn</b> es una plataforma de información sobre distintos
aspectos del Currículo de la <b>Facultad de Ciencias Exactas y
Naturales</b> (FCEN).  La plataforma además ofrece algunos servicios
de edición y consulta en línea para administradores, profesores y
estudiantes, destinados a facilitar la gestión y acceso a la
información curricular.</p>

<p>La plataforma consta de una serie de bases de datos (programas
académicos, planes de estudio, planes de asignatura, estudiantes,
reconocimientos, etc.) y de unos conjuntos de servicios específicos
(<b>Módulos</b>).</p>

<span class="level1">
<p>Los módulos y servicios disponibles en la presente versión son:</p>

<ul>

<li><a href=reconoce.php>Reconocimientos</a>.  Este módulo le brinda
información y servicios relacionados con el sistema de reconocimientos
de materias para los estudiantes nuevos, de reingreso o transferencia
en los programas de la FCEN.  Los reconocimientos permiten que una
asignatura cursada y aprobada en otro programa o institución sea
reconocida por una que debe ser vista en el programa de la FCEN en el
que se encuentra matriculado el estudiante.</li>

<li><a href=planes.php>Planes de Estudio (pensums)</a>.  Este módulo le brinda
información y servicios relacionados con los planes de estudio de los
programas de la FCEN.  Por planes de estudio nos referimos al conjunto
de asignaturas y la relación entre ellas que define un programa
académico.</li>

<li><a href=asignaturas.php>Planes de Asignatura</a>.  Este módulo le
brinda información y servicios relacionados con los planes de
asignatura de los cursos ofrecidos en los programas académicos de la
FCEN.  Los planes de asignatura contienen toda la información
relacionada con cursos específicos (objetivos, metodología, temáticas,
bibliografía, etc.).</li>

</ul>
</span>

<p>Además de los módulos de servicio mencionado <b>SInfIn</b> ofrece
otra información de interés sobre el Currículo.  Encontrara los
enlaces a todos los conjuntos de información adicionales ofrecidos por
el sistema en el Menú Principal de esta página.

<p class="level0" style="color:blue">
Para acceder debe registrarse y crear una cuenta de usuario o usar una
cuenta existente.
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
