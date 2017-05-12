<html>
<?php
////////////////////////////////////////////////////////////////////////
//LOAD LIBRARY
////////////////////////////////////////////////////////////////////////
$HOST=$_SERVER["HTTP_HOST"];
$SCRIPTNAME=$_SERVER["SCRIPT_FILENAME"];
$ROOTDIR=rtrim(shell_exec("dirname $SCRIPTNAME"));
require("$ROOTDIR/etc/library2.php");
$SESSID=$_COOKIE["PHPSESSID"];

////////////////////////////////////////////////////////////////////////
//ACTIVE PART
////////////////////////////////////////////////////////////////////////
if(isset($action)){
  if(0){}
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //CERRAR SESSION
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  if($action=="Cerrar"){

    $urlref="$SITEURL/index$VER.php";

    $content=getHeaders(false);
$content.=<<<C
<div class="g-signin2" data-theme="dark" data-width="0" data-height="0"></div>
<div style="display:table;width:100%;height:100%">
<div style="display:table-cell;vertical-align:middle;text-align:center">
  <a href="$urlref" onclick="signOut($urlref);" style="font-size:1.5em">Presione aquí para desonectar también $SINFIN de su cuenta de google</a>
</div>
</div>
C;
    echo $content;
    session_unset();
    header("Refresh:3;url=$urlref");
  }  
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //RESPONDER PREGUNTAS RÁPIDAS
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  if($action=="quickans"){
    $ps=parseParams($params);

    $sessionid=$SESSID;
    $pregunta=$ps["pregunta"];

    if($results=mysqlCmd("select * from RespuestasRapidas where sessionid='$sessionid' and pregunta='$pregunta'")){
      echo "<center style='font-size:2em'>Ya habíamos registrado antes una respuesta suya a esta pregunta.</center>";
    }else{
      $tipo=$ps["tipo"];
      $opciones=$ps["opciones"];
      $respuesta=$respuesta;
      $fecha=$DATE;
      /*
      echo "Fecha:".$fecha."<br/>";
      echo "Sesión:".$sessionid."<br/>";
      echo "Pregunta:".$pregunta."<br/>";
      echo "Tipo:".$tipo."<br/>";
      echo "Opciones:".$opciones."<br/>";
      echo "Respuesta:".$respuesta."<br/>";
      */
      $results=mysqlCmd("select * from RespuestasRapidas");
      
      insertSql("RespuestasRapidas",array("sessionid"=>"",
					  "respuesta"=>"",
					  "pregunta"=>"",
					  "tipo"=>"",
					  "opciones"=>"",
					  "fecha"=>""));
      echo "<center style='font-size:2em'>Hemos recibido su respuesta.  Muchas gracias.</center>";
    }
    echo "<center style='font-size:1em;color:gray'>Esta ventana se cerrará automáticamente en 3 segundos</center>";
    echo "<script type='text/javascript'>setTimeout(function(){window.close();},3000);</script>";
    /*
      http://astronomia-udea.co/principal/Sinfin/actions$VER.php?action=quickans&respuesta=Si&params=pregunta:¿tuviste+problemas+con+la+matr%C3%ADcula+2017-1%3F;tipo:sino;opciones:Si,No
     */
  }
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //FINAL
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  else{}
 endaction:
}else{}
?>
