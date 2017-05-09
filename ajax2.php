<?php
////////////////////////////////////////////////////////////////////////
//LOAD LIBRARY
////////////////////////////////////////////////////////////////////////
$HOST=$_SERVER["HTTP_HOST"];
$SCRIPTNAME=$_SERVER["SCRIPT_FILENAME"];
$ROOTDIR=rtrim(shell_exec("dirname $SCRIPTNAME"));
require("etc/library2.php");

////////////////////////////////////////////////////////////////////////
//ACTIONS
////////////////////////////////////////////////////////////////////////
if($action=="test"){
  $ps=parseParams($params);
  print_r($ps);
  $html.="Test";
}
else
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//LOGIN
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
if($action=="login"){
  $html=getHeaders(false);
  $ps=parseParams($params);
  $email=$ps["email"];
  $nombre=$ps["fullname"];

  //IF USER EXIST
  if(($results=mysqlCmd("select * from Usuarios where email='$email'"))){
      session_start();
      foreach(array_keys($results) as $key){
	  if(preg_match("/^\d$/",$key)){continue;}
	  $_SESSION["$key"]=$results[$key];
      }
      $urlref="$SITEURL/index$VER.php?yes";
  }
  //IF USER DOES NOT EXIST
  else{
    $html.="<div id='msg'>El usuario no ha sido creado en nuestra base de datos...</div>";
    $urlref="$SITEURL/usuarios$VER.php?mode=nuevo&nombre=$nombre&email=$email&new";
  }

$html.=<<<V

<div class="g-signin2" data-onsuccess="onVerify" data-theme="dark" data-width="0" data-height="0">
</div>
<script>
function onVerify(googleUser) {
    var profile = googleUser.getBasicProfile();
    var email=profile.getEmail();
    var \$msg=$("#msg");
    if(email!="$email"){
	\$msg.html("El usuario no es reconocido...");
	setTimeout(function(){document.location="$SITEURL/actions$VER.php?action=Cerrar";},1000);
    }else{
      //\$msg.html("Conectado como usuario $email...");
      setTimeout(function(){document.location="$urlref";},0);
    }
}
</script>
V;

}
else
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//UPDATE COURSE
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
if($action=="updatecourses"){
  $ps=parseParams($params);
  $planid=$ps["planid"];
  $cursos=updateCursos($planid);
  $html.=generateSelection($cursos,"curso","");
}
else
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//UPDATE COURSE
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
if($action=="updateinstituto"){
  $ps=parseParams($params);
  $planid=$ps["planid"];
  preg_match("/(\d+)-/",$planid,$matches);
  $programaid=$matches[1];
  $results=mysqlCmd("select instituto from Programas where programaid='$programaid'");
  $instituto=$results["instituto"];
  //$instituto=$INSTITUTOS["$instituto"];
  $html.="$instituto";
}
else
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//UPDATE STUDENT
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
if($action=="updatestudent"){
   $ps=parseParams($params);
   $documento=$ps["documento"];
   if($results=mysqlCmd("select * from Estudiantes where documento='$documento'")){
     $nombre=$results["nombre"];
     $email=$results["email"];
     $html.="{\"nombre\":\"$nombre\",\"email\":\"$email\"}";
   }else{
     $html.="0";
   }
}
else
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//FILL PROFESOR
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
if($action=="fillProfesor"){
   $ps=parseParams($params);
   $documento=$ps["documento"];
   $db=mysqli_connect("localhost","comisiones","123","Comisiones");
   if($results=mysqlCmdDB($db,"select * from Profesores where cedula='$documento'")){
     $nombre=$results["nombre"];
     $email=$results["email"];
     $html.="{\"nombre\":\"$nombre\",\"email\":\"$email\"}";
   }else{
     $html.="0";
   }
}
else{
  $html.="Option not recognized";
}

////////////////////////////////////////////////////////////////////////
//RETURN
////////////////////////////////////////////////////////////////////////
echo $html;
?>
