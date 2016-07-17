<?php
////////////////////////////////////////////////////////////////////////
//LOAD LIBRARY
////////////////////////////////////////////////////////////////////////
require("etc/library.php");
$html="";
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
