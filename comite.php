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
  Comite de Curriculo
</div>
<div class="submenu">
  <a href="?">Inicio</a>
  | <a href="#circulares">Circulares</a> 
  | <a href="#agenda">Agenda</a>
  <span class="level3">| <a href="#actas">Actas</a></span>
</div>
<div class="container">
M;

////////////////////////////////////////////////////////////////////////
//ACTIVE PART
////////////////////////////////////////////////////////////////////////
if(isset($action)){
}

////////////////////////////////////////////////////////////////////////
//MODOS
////////////////////////////////////////////////////////////////////////
if(!isset($mode)){
$content.=<<<C
<p>
Esta pagina presenta informacion de interes sobre el Comite de
Curriculo de la Facultad de Ciencias Exactas y Naturales.
</p>
  <h4><a name="circulares">Circulares</a></h4>
<p>
  Todas las circulares publicadas por el comite de curriculo estan disponibles en este enlace: <a href=http://bit.ly/fcen-comcur-circulares>http://bit.ly/fcen-comcur-circulares</a>.
</p>
  <h4><a name="agenda">Agenda</a></h4>
<p>
  A continuacion encontrara la agenda de actividades el Comite de Curriculo.  Alli encontrara las fechas de reuniones, los plazos definidos para la entrega de solicitudes especiales al comite, entre otras actividades relacionadas con el.
</p>

<center>

<iframe src="https://calendar.google.com/calendar/embed?showTitle=0&amp;showDate=0&amp;showPrint=0&amp;showCalendars=0&amp;showTz=0&amp;mode=AGENDA&amp;height=1200&amp;wkst=2&amp;hl=es_419&amp;bgcolor=%23FFFFFF&amp;src=udea.edu.co_jfdls7dhs5191hf0md5r51lqqg%40group.calendar.google.com&amp;color=%232952A3&amp;src=es.co%23holiday%40group.v.calendar.google.com&amp;color=%23125A12&amp;ctz=America%2FBogota" style="border-width:0" width="800" height="1200" frameborder="0" scrolling="no"></iframe>

</center>

<div class="level3">
  <h4><a name="actas">Actas</a></h4>
<p>
  Todas las actas del comite de curriculo estan disponibles en este enlace: <a href=http://bit.ly/fcen-comcur-actas>http://bit.ly/fcen-comcur-actas</a>.
</p>
</div>
C;
}
else{

  if(0){}
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //REFRESH (EXPERIMENTAL)
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  else if($mode=="refresh"){
$content.=<<<C

C;
  }

}

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
