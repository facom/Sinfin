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
//ACTIVE PART
////////////////////////////////////////////////////////////////////////
if(isset($action)){
  if(0){}
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //CERRAR SESSION
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  if($action=="Cerrar"){
    $urlref="$SITEURL/index.php";

    $content=getHeaders(false);
$content.=<<<C
<div class="g-signin2" data-theme="dark" data-width="0" data-height="0">
</div>

<div style="display:table;width:100%;height:100%">
<div style="display:table-cell;vertical-align:middle;text-align:center">
  <a href="#" onclick="signOut();" style="font-size:1.5em">Presione aquí para desonectar también $SINFIN de su cuenta de google</a>
</div>
</div>

C;
    echo $content;
    session_unset();
    header("Refresh:3;url=$urlref");
  }  
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //FINAL
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  else{}
 endaction:
}else{}
?>
