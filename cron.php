<?php
////////////////////////////////////////////////////////////////////////
//LOAD LIBRARY
////////////////////////////////////////////////////////////////////////
$HOST=$_SERVER["HTTP_HOST"];
$SCRIPTNAME=$_SERVER["SCRIPT_FILENAME"];
$ROOTDIR=rtrim(shell_exec("dirname $SCRIPTNAME"));
require("etc/library.php");
$html="";

if(0){}
////////////////////////////////////////////////////////////////////////
//ACTIONS
////////////////////////////////////////////////////////////////////////
else
if($action=="comaca"){

  $actividades=mysqlCmd("select * from Actividades where fechaini=curdate()",$qout=1);
  $suscritos=mysqlCmd("select email from Suscripciones where suscripcion='comaca' and confirma+0>0",$qout=1);
  $subject="[ComAca] Actividades de la Comunidad Académica para hoy";

$message=<<<M
<p>
  Señor(a) suscriptor,
</p>
<p>
  Estas son las actividades de la Comunidad Académica de la Facultad
  de Ciencias Exactas y Naturales que se desarrollaran durante el
  transcurso del día de hoy.
</p>
M;

  $nact=0;
  foreach($actividades as $actividad){
    foreach(array_keys($actividad) as $key){
      if(preg_match("^\d+$",$key)){continue;}
      $$key=$actividad["$key"];
    }
    $Tipo=$TIPOS_ACTIVIDAD["$tipo"];

$message.=<<<M
<hr/>
<p>
  <i>$Tipo</i><br/>
  <h3>$nombre</h3>
  <p>$encargado</p>
  <p>Lugar: $lugar, $fechaini, $horaini</p>
  <h3>Resumen</h3>
  <blockquote>$resumen</blockquote>
</p>
M;
    $nact++;
  }

$message.=<<<M
<hr/>
<p>
  <b>Comité de Currículo</b><br/>FCEN
</p>
M;

  print_r($message);
  echo "<br/>";
  if($nact>0){
    foreach($suscritos as $suscrito){
      $email=$suscrito["email"];
      echo "$email<br/>";
      sendMail($email,$subject,$message,$EHEADERS);
    }
  }
  
  $html.="0";
}

////////////////////////////////////////////////////////////////////////
//OUTPUT
////////////////////////////////////////////////////////////////////////
echo $html;
?>
