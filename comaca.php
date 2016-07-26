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
//ROUTINES
////////////////////////////////////////////////////////////////////////

////////////////////////////////////////////////////////////////////////
//SUBMENU
////////////////////////////////////////////////////////////////////////
$content.=<<<M
<div class="moduletitle">
  Modulo de Comunidad Académica
</div>
<div class="submenu">
  <a href="?">Inicio</a> 
  | <a href="#agenda">Agenda</a>
  <span class="level1">
    | <a href="?mode=registrar">Registrar asistencia</a>
    | <a href="?mode=consultar">Consultar asistencia</a>
  </span>
  <span class="level2">
    | <a href="?mode=agregar">Agregar</a>
  </span>
</div>
<div class="container">
M;

////////////////////////////////////////////////////////////////////////
//DEBUGGING
////////////////////////////////////////////////////////////////////////
if(0){
  if($results=mysqlCmd("select * from Estudiantes")){
    print_r($results);
  }
}

////////////////////////////////////////////////////////////////////////
//ACTIVE PART
////////////////////////////////////////////////////////////////////////
if(isset($action)){

  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //SALIR DE LA EDICION
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  if($action=="Salir"){
    $mode="lista";
    goto endaction;
  }

  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //CONFIRMAR APOYO
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  if($action=="apoyo"){
    $movildir="data/movilidad/$movilid";
    $codsave=rtrim(shell_exec("cat $movildir/.codigo"));

    if($movil=mysqlCmd("select * from Movilidad where movilid='$movilid'")){
      foreach(array_keys($MOVILIDAD_FIELDS) as $field) $$field=$movil["$field"];
      //CHECK CODIGO
      if($codsave!=$codigo){
	errorMsg("El código de la solicitud no coincide.");
	goto endaction;
      }
      //VISTO BUENO
      if($resultado=="apoyo"){
	cambiaEstado($movilid,"pendiente_aprobacion");
	mysqlCmd("update Movilidad set respuesta='1' where movilid='$movilid'");
      }
      //DEVOLUCION DEL PROFESOR
      if($resultado=="devol"){
	cambiaEstado($movilid,"devuelta");
	mysqlCmd("update Movilidad set respuesta='0' where movilid='$movilid'");
      }
$content.=<<<C
<p style="font-size:2em;text-align:center;">
Hemos registrado su información. Gracias.
</p>
C;
      $mode="empty";
      header("Refresh:5;url=$SITEURL");
    }else{
      errorMsg("La solicitud $movilid ya no existe.");
      goto endaction;
    }
  }

  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //CARGAR SOLICITUD
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  if($action=="Cargar"){

    //&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
    //UPLOAD FILE
    //&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
    $movilfile=$_FILES["movilfile"];
    if($movilfile["size"]>0){
      $movilidnew=$movilid;
      $movildir="data/movilidad/$movilid";
      shell_exec("mkdir -p $movildir");
      $name=$movilfile["name"];
      $tmp=$movilfile["tmp_name"];
      shell_exec("cp $tmp $movildir/$name");
      shell_exec("cd $movildir;unzip $name");
      include("$movildir/movilidad.php");
      $movilid=$movilidnew;
      $blankfields=array("nombre","cumplido","compromiso","fechapresenta","historia","apoyo","monto","respuesta","observacionesadmin","acto","estado");
      foreach($blankfields as $field){unset($$field);}
      statusMsg("Solicitud cargada.");
    }else{
      errorMsg("Ningún archivo fue provisto.");
    }
    $mode="editar";
  }

  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //DESCARGAR
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  if($action=="descargar"){

    if($movil=mysqlCmd("select * from Movilidad where movilid='$movilid'")){
      $movildir="data/movilidad/$movilid";

      //&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
      //CREATE DOWNLOADABLE FILE
      //&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
      $fl=fopen("$movildir/movilidad.php","w");
      fwrite($fl,"<?php\n");
      foreach(array_keys($MOVILIDAD_FIELDS) as $field){
	fwrite($fl,"\$$field='".$movil["$field"]."';\n");
      }
      fwrite($fl,"?>\n");
      fclose($fl);
      //&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
      //ZIP FILES
      //&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
      shell_exec("cd $movildir;zip -r ../../../tmp/movilidad_$movilid.zip *.*");
      header("Refresh:0;url=tmp/movilidad_$movilid.zip");
      $mode="lista";
      statusMsg("Solicitud descargada.");
      goto endaction;
    }else{
      errorMsg("La solicitud no existe");
      $mode="lista";
      goto endaction;
    }
  }

  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //ENVIAR
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  if($action=="Guardar" or $action=="Enviar" or $action=="Cumplir"){

    $movildir="data/movilidad/$movilid";
    //FECHAS
    $fecharango=str2Array($fecharango);
    $fechaini=$fecharango["start"];
    $fechafin=$fecharango["end"];

    //&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
    //VALIDATE PROVIDED INFORMATION
    //&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
    if(!preg_match("/udea\.edu\.co/",$email)){
      errorMsg("Para hacer una solicitud de movilidad debes estar registrado con tu correo institucional");
      $mode="editar";
      unset($fechapresenta);
      unset($loadmovil);
      goto endaction;
    }
    if(isBlank($lugar)){
      errorMsg("No se ha provisto un lugar");
      $mode="editar";
      unset($fechapresenta);
      unset($loadmovil);
      goto endaction;
    }
    if(isBlank($idioma)){
      errorMsg("No se ha provisto un idioma");
      $mode="editar";
      unset($fechapresenta);
      unset($loadmovil);
      goto endaction;
    }
    if(isBlank($evento)){
      errorMsg("No se ha provisto un evento");
      $mode="editar";
      unset($fechapresenta);
      unset($loadmovil);
      goto endaction;
    }
    $anticipacion=(strtotime($fechaini)-strtotime($DATE))/86400.0;
    if($anticipacion<30.0){
      errorMsg("Las solicitudes deben presentarse con mas de 30 días de anticipación");
      /*
      $mode="editar";
      unset($fechapresenta);
      unset($loadmovil);
      goto endaction;
      */
    }
    if(isBlank($documento_profesor)){
      errorMsg("No se ha provisto un profesor de apoyo");
      $mode="editar";
      unset($fechapresenta);
      unset($loadmovil);
      goto endaction;
    }
    if(isBlank($profesor)){
      errorMsg("No se ha provisto un profesor de apoyo");
      $mode="editar";
      unset($fechapresenta);
      unset($loadmovil);
      goto endaction;
    }
    if(isBlank($item1) or isBlank($value1) or isBlank($fuente1)){
      errorMsg("El presupuesto debe tener al menos 1 item");
      $mode="editar";
      unset($fechapresenta);
      unset($loadmovil);
      goto endaction;
    }
    if(isBlank($total) or preg_replace("/[$,\.]+/","",$total)<1000){
      errorMsg("El total debe ser mayor a $1,000 pesos");
      $mode="editar";
      unset($fechapresenta);
      unset($loadmovil);
      goto endaction;
    }
    if(isBlank($valor) or preg_replace("/[$,\.]+/","",$valor)<1000){
      errorMsg("El valor solicitado debe ser mayor a $1,000 pesos");
      $mode="editar";
      unset($fechapresenta);
      unset($loadmovil);
      goto endaction;
    }
    if($estado=="nueva"){
      $file_historia=$_FILES["historia"];
      if($file_historia["size"]==0){
	errorMsg("No se ha provisto un archivo de historia académica");
	$mode="editar";
	unset($fechapresenta);
	unset($loadmovil);
	goto endaction;
      }
      $file_carta=$_FILES["carta"];
      if($file_carta["size"]==0){
	errorMsg("No se ha provisto una carta de invitación");
	$mode="editar";
	unset($fechapresenta);
	unset($loadmovil);
	goto endaction;
      }
    }

    //&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
    //PREPARE PROVIDED INFO
    //&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
    shell_exec("mkdir -p $movildir");
    $suffix=$documento."_${movilid}";

    //ARCHIVOS
    if($file_historia["size"]>0){
      $name=$file_historia["name"];
      $tmp=$file_historia["tmp_name"];
      $filename="Historia_${suffix}_$name";
      shell_exec("cp $tmp $movildir/'$filename'");
      $historia=$filename;
    }
    if($file_carta["size"]>0){
      $name=$file_carta["name"];
      $tmp=$file_carta["tmp_name"];
      $filename="Carta_${suffix}_$name";
      shell_exec("cp $tmp $movildir/'$filename'");
      $carta=$filename;
    }
    $file_compromiso=$_FILES["compromiso"];
    if($file_compromiso["size"]>0){
      $name=$file__compromiso["name"];
      $tmp=$file__compromiso["tmp_name"];
      $filename="Compromiso_${suffix}_$name";
      shell_exec("cp $tmp $movildir/'$filename'");
      $compromiso=$filename;
      if($estado=="aprobada" or $estado=="cumplida"){
	$estado="terminada";
      }
    }
    $file_cumplido=$_FILES["cumplido"];
    if($file_cumplido["size"]>0){
      $name=$file_cumplido["name"];
      $tmp=$file_cumplido["tmp_name"];
      $filename="Cumplido_${suffix}_$name";
      shell_exec("cp $tmp $movildir/'$filename'");
      $cumplido=$filename;
      if($estado=="aprobada"){
	$estado="cumplida";
      }
    }

    //&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
    //READ PREVIOUS FIELDS
    //&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
    $movil=mysqlCmd("select * from Movilidad where movilid='$movilid'");
    
    //&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
    //CÓDIGO ÚNICO
    //&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
    $codigo=generateRandomString(20);
    shell_exec("echo $codigo > $movildir/.codigo");

    //&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
    //CHECK STATUS TO SEND MAILS
    //&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
    if($estado=="nueva" and $action="Guardar"){
      $estado="guardada";
    }
    if(($estado=="nueva" or $estado=="guardada") and $action=="Enviar"){
      $estado=cambiaEstado($movilid,"pendiente_apoyo");
    }
    if($estado=="aprobada" and $movil){
      if($movil["estado"]!="aprobada"){
	$estado=cambiaEstado($movilid,"aprobada");
      }
    }
    if($estado=="devuelta" and $movil){
      if($movil["estado"]!="devuelta"){
	$estado=cambiaEstado($movilid,"devuelta");
      }
    }
    if($estado=="rechazada" and $movil){
      if($movil["estado"]!="rechazada"){
	$estado=cambiaEstado($movilid,"rechazada");
      }
    }
    if($estado=="terminada"){
      if($movil["estado"]!="terminada"){
	$estado=cambiaEstado($movilid,"terminada");
      }
    }
    if($estado=="cumplida"){
      if($movil["estado"]!="cumplida"){
	$estado=cambiaEstado($movilid,"cumplida");
      }
    }

    //&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
    //STORE INFORMATION IN DATABASE
    //&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
    insertSql("Movilidad",$MOVILIDAD_FIELDS);
    insertSql("Usuarios",array("email"=>"","programa"=>""));

    statusMsg("Solicitud guardada.");
    //errorMsg("Cargado");
    //goto endaction;
  }

  if($action=="Borrar"){
    mysqlCmd("delete from Movilidad where movilid='$movilid'");
    $movildir="data/movilidad/$movilid";
    if(is_dir($movildir)){
      shell_exec("rm -r $movildir");
      statusMsg("Solicitud '$movilid' borrada.");
    }else{
      errorMsg("La solicitud '$movilid' no existe");
    }
    $mode="lista";
  }

  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //LOAD DATA
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  if($action=="loadmovil"){

    if($result=mysqlCmd("select * from Movilidad where movilid='$movilid'")){
      foreach(array_keys($MOVILIDAD_FIELDS) as $field) $$field=$result["$field"];
      statusMsg("Cargado");
    }else{
      $mode="lista";
      errorMsg("La solicitud no existe");
    }
    goto endaction;
  }
  
 endaction:
}else{}

////////////////////////////////////////////////////////////////////////
//MODOS
////////////////////////////////////////////////////////////////////////
if(!isset($mode)){

  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //PRINCIPAL
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
$content.=<<<C
<p>

La <b>Comunidad Académica</b> alrededor de los programas de pregrado y
posgrado de la Facultad de Ciencias Exactas y Naturales es una de las
fuentes más ricas y menos convencionales de <b>formación
científica</b> para los estudiantes de la Facultad.  

</p>

<p>

La participación de todos en las actividades organizadas en el seno de
esta comunidad (seminarios, ciclos de conferencias, actividades
divulgativas, etc.)  es parte integral del currículo.

</p>

<p>

Este módulo ofrece información sobre la agenda de actividades de la
comunidad académica y acceso a los mecanismos utilizados en algunos
programas para hacer seguimiento a la participación en dichas
actividades.

</p>

C;

}else{

  if(0){}

  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //AGREGAR ACTIVIDAD
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  else if($mode=="agregar"){
    if(!isset($actid)){$actid=generateRandomString(5);}
    else{
      
    }

$content.=<<<C
<h4>Registro de actividad $actid</h4>
<form action="comaca.php?loadact" method="post" enctype="multipart/form-data" accept-charset="utf-8">
<table border=1px width=60% cellspacing=0px>
<tr class="field">
  <td class="campo">Nombre del solicitante:</td>
  <td class="form">
    $nombre
    <input type="hidden" name="nombre" value="$nombre">
  </td>
</tr>
</table>
C;

  }

  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //REGISTRAR ACTIVIDAD
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  else if($mode=="registrar"){
    goto end;
  }

  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //CONSULTAR ASISTENCIA
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  else if($mode=="consultar"){
    goto end;
  }

  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //REFRESH (EXPERIMENTAL)
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  else if($mode=="refresh"){
    $content.="<script>window.location.href='$FILENAME';</script>";
    goto end;
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
