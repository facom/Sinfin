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
  | <a href="?mode=mode">Modo</a>
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
