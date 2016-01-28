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
if($action=="updatecourses"){
  $ps=parseParams($params);
  $planid=$ps["planid"];
  $cursos=updateCursos($planid);
  $html.=generateSelection($cursos,"curso","");
}
else
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
else{
  $html.="Option not recognized";
}

////////////////////////////////////////////////////////////////////////
//RETURN
////////////////////////////////////////////////////////////////////////
echo $html;
?>
