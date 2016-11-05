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
function cambiaEstado($movilid,$newestado)
{
  global $MOVILIDAD_FIELDS,$SITEURL,$ESTADOS,$EMAIL_ADMIN,$SINFIN;

  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //OBTIENE VARIABLES EXTERNAS
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  foreach(array_keys($MOVILIDAD_FIELDS) as $var){
    $$var=$GLOBALS["$var"];
  }
  $codigo=$GLOBALS["codigo"];

  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //CAMBIA EL ESTADO
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  $sql="update Movilidad set estado='$newestado' where movilid='$movilid'";
  mysqlCmd($sql);

  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //ENVIA MENSAJE AL ESTUDIANTE
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  $viejoestado=$ESTADOS["$estado"];
  $nuevoestado=$ESTADOS["$newestado"];
  $subject="[SInfIn] Actualización de su solicitud de movilidad estudiantil $movilid";
$message.=<<<M
<p>
  Señor(a) estudiante,
</p>
<p>
  Su solicitud de movilidad estudiantil ha sido actualizada en
  $SINFIN.  Ha pasado al estado <b>$nuevoestado</b>.
</p>
<p>
  Para ver los detalles conéctese con su cuenta de usuario en el
  sistema y
  use <a href="$SITEURL/movilidad.php?mode=editar&movilid=$movilid&action=loadmovil">el
  siguiente enlace para ver directamente la solicitud</a>.
</p>
<p>
  <b>Comité de Currículo</b><br/>FCEN
</p>
M;
  sendMail($email,$subject,$message,$EHEADERS);
  statusMsg("Actualización de estado enviada a $email");

  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //Envia mensaje de acuerdo al cambio de estado
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  if(0){
  }
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //NUEVA SOLICITUD
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  else if($newestado=="pendiente_apoyo"){
    //Send mail to profesor 
    $urlapoyo="$SITEURL/movilidad.php?action=apoyo&resultado=apoyo&movilid=$movilid&codigo=$codigo";
    $urldevol="$SITEURL/movilidad.php?action=apoyo&resultado=devol&movilid=$movilid&codigo=$codigo";
    $urlmovil="$SITEURL/movilidad.php?mode=ver&movilid=$movilid";
    
    $subject="[SInfIn] La solicitud de movilidad estudiantil $movilid requiere su visto bueno";
$message=<<<M
<p>
  Señor(a) Profesor(a),
</p>
<p>
  Una solicitud de movilidad fue presentada por el estudiante del
  programa de <b>$programa</b>, <b>$nombre</b> identificado con
  documento <b>$documento</b> .  El estudiante lo eligió a usted como
  profesor de apoyo.
</p>
<p>
  Para continuar con el trámite es necesario que usted de visto bueno
  a la solicitud.  Para ello lo único que tiene que hacer es dar click
  en el siguiente enlace:
</p>
<center>
  <a href="$urlapoyo" style="font-size:1.5em" target="_blank">
    De click en este enlace para apoyar la solicitud
  </a>
</center>
<p>
  Si quiere conocer más a fondo la solicitud antes de dar su visto
  bueno use <a href="$urlmovil">este enlace para ver los detalles</a>. Si
  después de conocer la solicitud usted decide apoyarla vuelva a este
  correo y de click en el enlace arriba.
</p>
<p>
  Si no conoce al estudiante o la solicitud tiene un inconveniente de
  click en el siguiente enlace:
</p>
<center>
  <a href="$urldevol" style="font-size:1.5em" target="_blank">
    De click en este enlace para devolver la solicitud.
  </a>
</center>
<p>
  En este último caso comuníquese con el estudiante en el
  correo <a href="mailto:$email" target="_blank">$email</a> para
  sugerirle cambios a la solicitud o para informarle de su decisión de
  no apoyarla.
</p>
<p>Atentamente,</p>
<p>
  <b>Comité de Currículo</b><br/>FCEN
</p>
<p>
  C.C. Vicedecanato FCEN
</p>
M;
     sendMail($email_profesor,$subject,$message,$EHEADERS);
     sendMail($EMAIL_ADMIN,"[Copia]".$subject,$message,$EHEADERS);
     statusMsg("Mensaje enviado al profesor $email_profesor");
     return $newestado;
  }
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //SOLICITUD CON VISTO BUENO
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  else if($newestado=="pendiente_aprobacion"){
    $subject="[SInfIn] La solicitud de movilidad estudiantil $movilid ha recibido visto bueno";
$message=<<<M
<p>
  Señores Comité de Currículo,
</p>
<p>
  La solicitud de movilidad <b>$movilid</b> presentada por el estudiante del
  programa de <b>$programa</b>, <b>$nombre</b>, identificado con
  documento <b>$documento</b> ha recibido visto bueno del profesor.
</p>
<p>
  Una vez conectado a $SINFIN puede editar la solicitud
  usando <a href="$SITEURL/movilidad.php?mode=editar&movilid=$movilid&action=loadmovil">este
  enlace</a>.
</p>
<p>Atentamente,</p>
<p>
  $SINFIN
</p>
M;
    sendMail($EMAIL_ADMIN,$subject,$message,$EHEADERS);
    statusMsg("Mensaje enviado al administrador $EMAIL_ADMIN");
    return $newestado;
  }
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //DEVUELTA
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  else if($newestado=="devuelta"){
     //PENDIENTE
    $subject="[SInfIn] La solicitud de movilidad estudiantil $movilid ha sido devuelta";
$message=<<<M
<p>
  Señores Comité de Currículo,
</p>
<p>
  La solicitud de movilidad <b>$movilid</b> presentada por el estudiante del
  programa de <b>$programa</b>, <b>$nombre</b>, identificado con
  documento <b>$documento</b>, ha sido devuelta.
</p>
<p>
  Puede verificar el estado de la solicitud y sus detalles conectándose a $SINFIN.
  Una vez allí puede editar la solicitud
  usando <a href="$SITEURL/movilidad.php?mode=editar&movilid=$movilid&action=loadmovil">este
  enlace</a>.
</p>
<p>Atentamente,</p>
<p>
  $SINFIN
</p>
M;
    sendMail($EMAIL_ADMIN,$subject,$message,$EHEADERS);
    statusMsg("Mensaje enviado al administrador $EMAIL_ADMIN");
    return $newestado;
  }  
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //RECHAZADA
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  else if($newestado=="aprobada"){
    //PENDIENTE
    $subject="[SInfIn] Solicitud de movilidad estudiantil $movilid aprobada";
$message=<<<M
<p>
  Apreciado(a) $nombre,
</p>
<p>
  El Comité de Currículo se complace en informarle que su solicitud de
  movilidad <b>$movilid</b> ha sido aprobada en el acto
  administrativo <b>$acto</b>.
</p>
<p>
  Después de estudiar su solicitud y teniendo en cuenta los topes
  definidos por el Consejo de Facultad, el comité de currículo aprobó
  un monto total de <b>$monto</b>.
</p>
<p>
  <b>Para proceder con el desembolso del dinero deberá dirigirse al
  Centro de Extensión de la Facultad, Oficina 6-111. Es su
  responsabilidad realizar este trámite. <i style=color:red>La
  Universidad no desembolsa dinero después de iniciada la fecha del
  evento o pasantía</i>.</b>
</p>
<p>
  Le recordamos al terminar la actividad, cumplir con
  las <b>obligaciones adquiridas</b> al recibir este apoyo en un plazo
  no mayor a un mes. Estas obligaciones deberán ser legalizadas usando
  la plataforma $SINFIN, tal y como se explica en los tutoriales de la
  misma.
</p>
<p>
  Para conocer otros detalles de su solicitud conéctese a $SINFIN.
  Una vez allí puede editar la solicitud
  usando <a href="$SITEURL/movilidad.php?mode=editar&movilid=$movilid&action=loadmovil">este
  enlace</a>.
</p>
<p>Atentamente,</p>
<p>
  <b>Comité de Currículo</b><br/>FCEN
</p>
<p>
  C.C. Profesor de Apoyo, Comité de Currículo.
</p>
M;
    sendMail($email,$subject,$message,$EHEADERS);
    sendMail($email_profesor,"[Copia]".$subject,$message,$EHEADERS);
    sendMail($EMAIL_ADMIN,"[Copia]".$subject,$message,$EHEADERS);
    statusMsg("Mensaje enviado al estudiante $email, al profesor de apoyo $email_profesor y al administrador $EMAIL_ADMIN");
    return $newestado;
  }
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //RECHAZADA
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  else if($newestado=="rechazada"){
    //PENDIENTE
    $subject="[SInfIn] Solicitud de movilidad estudiantil $movilid rechazada";
$message=<<<M
<p>
  Señor(a) estudiante,
</p>
<p>
  El Comité de Currículo lamenta informarle que su solicitud de
  movilidad <b>$movilid</b> ha sido rechazada en el acto
  administrativo <b>$acto</b>.
</p>
<p>
  Las razones expresadas por el Comité fueron:
  <blockquote style="font-style:italic">
    $observacionesadmin
  </blockquote>
</p>
<p>
  Para conocer otros detalles de su solicitud conéctese a $SINFIN.
  Una vez allí puede editar la solicitud
  usando <a href="$SITEURL/movilidad.php?mode=editar&movilid=$movilid&action=loadmovil">este
  enlace</a>.
</p>
<p>Atentamente,</p>
<p>
  <b>Comité de Currículo</b><br/>FCEN
</p>
<p>
  C.C. Profesor de Apoyo, Comité de Currículo.
</p>
M;
    sendMail($email,$subject,$message,$EHEADERS);
    sendMail($email_profesor,"[Copia]".$subject,$message,$EHEADERS);
    sendMail($EMAIL_ADMIN,"[Copia]".$subject,$message,$EHEADERS);
    statusMsg("Mensaje enviado al estudiante $email, al profesor de apoyo $email_profesor y al administrador $EMAIL_ADMIN");
    return $newestado;
  }
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //CUMPLIDA
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  else if($newestado=="cumplida"){
    //PENDIENTE
    $subject="[SInfIn] Solicitud de movilidad estudiantil $movilid cumplida";
$message=<<<M
<p>
  Señores Comité de Currículo,
</p>
<p>
  La solicitud de movilidad <b>$movilid</b> presentada por el estudiante del
  programa de <b>$programa</b>, <b>$nombre</b>, identificado con
  documento <b>$documento</b>, ha sido cumplida exitosamente.
</p>
<p>
  Puede verificar los cumplidos conéctese a $SINFIN.
  Una vez allí puede editar la solicitud
  usando <a href="$SITEURL/movilidad.php?mode=editar&movilid=$movilid&action=loadmovil">este
  enlace</a>.
</p>
<p>Atentamente,</p>
<p>
  $SINFIN
</p>
M;
    sendMail($EMAIL_ADMIN,$subject,$message,$EHEADERS);
    statusMsg("Mensaje enviado al administrador $EMAIL_ADMIN");
    return $newestado;
  }
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //TERMINADA
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  else if($newestado=="terminada"){
    //PENDIENTE
    $subject="[SInfIn] Solicitud de movilidad estudiantil $movilid terminada";
$message=<<<M
<p>
  Señores Comité de Currículo,
</p>
<p>
  La solicitud de movilidad <b>$movilid</b> presentada por el
  estudiante del programa de <b>$programa</b>, <b>$nombre</b>,
  identificado con documento <b>$documento</b>, ha sido terminada
  exitosamente. Se han entregado el cumplido y el compromiso
  obligatorio.
</p>
<p>
  Puede verificar los documentos respectivos conéctese a $SINFIN.  Una
  vez allí puede editar la solicitud
  usando <a href="$SITEURL/movilidad.php?mode=editar&movilid=$movilid&action=loadmovil">este
  enlace</a>.
</p>
<p>Atentamente,</p>
<p>
  $SINFIN
</p>
<p>
C.C. Estudiante.
</p>
M;
    sendMail($EMAIL_ADMIN,$subject,$message,$EHEADERS);
    sendMail($email,"[Copia]".$subject,$message,$EHEADERS);
    statusMsg("Mensaje enviado al administrador $EMAIL_ADMIN");
    return $newestado;
  }
}

////////////////////////////////////////////////////////////////////////
//SUBMENU
////////////////////////////////////////////////////////////////////////
$content.=<<<M
<div class="moduletitle">
  Modulo de Bolsa de Movilidad
</div>
<div class="submenu">
  <a href="?">Inicio</a> 
  | <a href="#terminos">Términos</a>
  | <a href="#videotutorial">Videotutorial</a>
  <span class="level1">
    | <a href="?mode=editar">Nueva Solicitud</a>
    | <a href="?mode=lista">Seguimiento a Solicitudes</a>
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
  //INFORME
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  if($action=="Informe"){
    $mode="lista";

    $infecharango=str2Array($infecharango);
    $infechaini=$infecharango["start"];
    $infechafin=$infecharango["end"];

    //TOTALES
    $rangofechap="fechapresenta>='$infechaini' and fechapresenta<='$infechafin'";
    $sql="select count(movilid) from Movilidad where $rangofechap";
    $numtotal=mysqlCmd($sql)[0];
    $sql="select count(movilid) from Movilidad where $rangofechap and estado='rechazada'";
    $numrechazadas=mysqlCmd($sql)[0];
    $sql="select count(movilid) from Movilidad where $rangofechap and estado='devuelta'";
    $numdevueltas=mysqlCmd($sql)[0];
    $sql="select count(movilid) from Movilidad where $rangofechap and estado='aprobada'";
    $numaprobadas=mysqlCmd($sql)[0];

    $rangofecha="fechaini>='$infechaini' and fechafin<='$infechafin'";
    $sql="select count(movilid) from Movilidad where $rangofecha and estado='realizada'";
    $numrealizadas=mysqlCmd($sql)[0];
    $sql="select count(movilid) from Movilidad where $rangofecha and estado='terminada'";
    $numterminadas=mysqlCmd($sql)[0];

    //TOTALES DINERO
    $totalvalor=0;

    $sql="select sum(replace(replace(monto,'$',''),',','')) from Movilidad where $rangofecha and (estado='realizada' or estado='terminada')";
    $valortotal=mysqlCmd($sql)[0];
    $totalvalor+=$valortotal;
    $valortotal="$".number_format($valortotal);

    $sql="select sum(replace(replace(monto,'$',''),',','')) from Movilidad where $rangofechap and estado='aprobada'";
    $valorpend=mysqlCmd($sql)[0];
    $totalvalor+=$valorpend;
    $valorpend="$".number_format($valorpend);

    $sql="select sum(replace(replace(valor,'$',''),',','')) from Movilidad where $rangofechap and (estado='realizada' or estado='terminada' or estado='aprobada')";
    $valorsol=mysqlCmd($sql)[0];
    $valorsol="$".number_format($valorsol);

    $totalvalor="$".number_format($totalvalor);

    $file="scratch/informe-movilidad.csv";
    $fl=fopen($file,"w");
    $solicitudes=mysqlCmd("select * from Movilidad where $rangofechap",$qout=1);
    $fields=array();
    $fields_txt="";
    foreach(array_keys($solicitudes[0]) as $field){
      if(preg_match("/^\d+$/",$field)){continue;}
      if($field=="observaciones" or $field=="observacionesadmin"){continue;}
      array_push($fields,$field);
      $fields_txt.="$field;";
    }
    fwrite($fl,utf8_decode(trim($fields_txt,";")."\n"));
    $values="";
    foreach($solicitudes as $solicitud){
      $values="";
      foreach($fields as $field){
	$value=$solicitud[$field];
	$values.="\"$value\";";
      }
      fwrite($fl,utf8_decode(trim($values,";")."\n"));
    }
    fclose($file);

$resultados=<<<R
<h3>Resultados</h3>
  Tabla completa: <a href=$file>$file</a>
<ul>
  <li>Solicitudes presentadas: $numtotal</li>
  <li>Solicitudes rechazadas: $numrechazadas<br/>
  <li>Solicitudes aprobadas sin finalizar: $numaprobadas<br/>
  <li>Solicitudes devueltas: $numdevueltas<br/>
  <li>Realizadas: $numrealizadas<br/>
  <li>Cumplidas: $numterminadas<br/>
  <li style=color:red>Total solicitado: $valorsol<br/>
  <li>Total entregado: $valortotal<br/>
  <li>Pendiente de ser entregado: $valorpend<br/>
  <li style=color:blue>Total aprobado: $totalvalor<br/>
</ul>
R;

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
    if($anticipacion<=30.0 and 
       !preg_match("/realizada/",$estado) and 
       !preg_match("/devuelta/",$estado) and 
       $QPERMISO<3){
      errorMsg("Su solicitud es presentada con menos de 30 días de anticipación. Esta condición puede producir el rechazo de la solicitud o el no cumplimiento de los plazos adminsitrativos necesarios para el desembolso. Se recibe la solicitud pero no se garantiza un resultado positivo.");
    }
    if($anticipacion<=15.0 and 
       !preg_match("/realizada/",$estado) and 
       !preg_match("/devuelta/",$estado) and 
       $QPERMISO<3){
      //*
      errorMsg("Las solicitudes deben presentarse con mas de 15 días de anticipación");
      $mode="editar";
      unset($fechapresenta);
      unset($loadmovil);
      goto endaction;
      //*/
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
    //echo "Archivos:".print_r($_FILES,true)."<br/>";
    $file_historia=$_FILES["historia"];
    if($file_historia["size"]==0){
      if($estado=="nueva"){
	errorMsg("No se ha provisto un archivo de historia académica");
	$mode="editar";
	unset($fechapresenta);
	unset($loadmovil);
	goto endaction;
      }
    }
    $file_carta=$_FILES["carta"];
    if($file_carta["size"]==0){
      if($estado=="nueva"){
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
      $name=$file_compromiso["name"];
      $tmp=$file_compromiso["tmp_name"];
      $filename="Compromiso_${suffix}_$name";
      shell_exec("cp $tmp $movildir/'$filename'");
      $compromiso=$filename;
      if($estado=="aprobada" or $estado=="cumplida" or $estado=="realizada"){
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
      }else{
	$estado=cambiaEstado($movilid,"pendiente_aprobacion");
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
El Consejo de la Facultad de Ciencias Exactas y Naturales en
su <b>Acta 01 del 20 de enero de 2016</b>, aprobó la creación de una
<b>Bolsa de Apoyo para la Movilidad de los estudiantes de pregrado</b>.  En
este módulo lo estudiantes podrán presentar solicitudes de apoyo y
hacer seguimiento a las solicitudes presentadas.
</p>

<a name="terminos"></a>
<h3>Términos de la bolsa</h3>
  <p  style=" margin: 12px auto 6px auto; font-family: Helvetica,Arial,Sans-serif; font-style: normal; font-variant: normal; font-weight: normal; font-size: 14px; line-height: normal; font-size-adjust: none; font-stretch: normal; -x-system-font: none; display: block;">   <a title="View Terminos Bolsa Movilidad on Scribd" href="https://es.scribd.com/doc/300665984/Terminos-Bolsa-Movilidad"  style="text-decoration: underline;" >Terminos Bolsa Movilidad</a> by <a title="View CienciasExactas's profile on Scribd" href="https://www.scribd.com/user/263978519/CienciasExactas"  style="text-decoration: underline;" >CienciasExactas</a></p><iframe class="scribd_iframe_embed" src="https://www.scribd.com/embeds/322568452/content?start_page=1&view_mode=scroll&access_key=key-8fciFithk7iPfeOmRapd&show_recommendations=true" data-auto-height="false" data-aspect-ratio="0.7729220222793488" scrolling="no" id="doc_59161" width="100%" height="600" frameborder="0"></iframe>

<a name="videotutorial"></a>
<h3>Videotutorial</h3>
<center>
<!-- MOVILIDAD -->
<iframe width="$WIDTHVID2" height="$HEIGHTVID2" src="https://www.youtube.com/embed/vpBmjn3pm2o" frameborder="0" allowfullscreen>
</iframe>
</center>

C;

}else{
  if(0){}

  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //VER DETALLES DE LA SOLICITUD DE MOVILIDAD
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  else if($mode=="ver"){
    //Leer información de la solicitud
    if($movil=mysqlCmd("select * from Movilidad where movilid='$movilid'")){
      $movildir="data/movilidad/$movilid";
$content.=<<<C
<center>
<h4>Solicitud de movilidad <b>$movilid</b></h4>
<table border=1px width=60% cellspacing=0px>
<tr>
  <td width=20% style="padding:5px;background:lightgray"><b>Item</b></td>
  <td width=80% style="padding:5px;background:lightgray"><b>Valor</b></td>
</tr>
C;
      foreach(array_keys($MOVILIDAD_FIELDS) as $field){
	$value=$movil["$field"];
	if(isBlank($value)){continue;}
	if(file_exists("$movildir/$value")){
	  $value="<a href='$movildir/$value' target=_blank>$value</a>";
	}
	if(isBlank($value)){continue;}
	$content.="<tr><td style='background:lightgray;padding:10px'><b>$field</b>:</td><td>$value</td></tr>";
      }
$content.=<<<C
</table>
</center>
C;
    }

  }

  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //LISTA DE SOLICITUDES
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  else if($mode=="lista"){

    ////////////////////////////////////////////////////
    //SEARCHING CRITERIA
    ////////////////////////////////////////////////////
    if(!isset($sort)){$sort="TIMESTAMP(fechaestado)";}
    if(!isset($order)){$order="desc";}
    if(!isset($search)){$search="where movilid<>'' ";}
    if($QPERMISO<=3){$search.="and email='$EMAIL' ";}

    $table="";
    ////////////////////////////////////////////////////
    //INFORMES
    ////////////////////////////////////////////////////
    if($QPERMISO>=3){

      if(!isset($resultados)){$resultados="";}
      if(!isset($infechaini)){
	$infechafin=$DATE;
	$infechaini=addDays($infechafin,"-180");
      }
      $infecharango=fechaRango("infecharango",$infechaini,$infechafin);

$table.=<<<T
<div style="background:lightgray;padding:10px;margin:auto;width:90%">
<form>
<input type="hidden" name="mode" value="lista">
<h3>Informes</h3>
<p style="font-size:12px">
  Rango de fechas: $infecharango<br/>
  <input type="submit" name="action" value="Informe">
</p>
$resultados
</form>
</div>
T;
    }

    if(!isset($action)){
    if($QPERMISO>=3){
      //$sql="update Movilidad set estado='realizada' where estado='aprobada' and fechafin<'$DATE'";
      $sql="select count(email) from Movilidad where estado='aprobada' and fechafin<'$DATE'";
      $nrealizada=mysqlCmd($sql)[0];
      if($nrealizada>0){
	statusMsg("Lista de solicitudes realizadas actualizada...");
	$sql="update Movilidad set estado='realizada' where estado='aprobada' and fechafin<'$DATE'";
	mysqlCmd($sql);
      }else{
	statusMsg("No hay solicitudes realizadas para actualizar...");
      }

    }
    ////////////////////////////////////////////////////
    //RECOVER INFO
    ////////////////////////////////////////////////////
    $sql="select * from Movilidad $search order by $sort $order";
    //echo "SQL: $sql<br/>";
    if(!($results=mysqlCmd($sql,$qout=1))){
      $content.="<i>No hay solicitudes con el criterio de búsqueda provisto.</i>";
      goto end;
    }
    if($order=="asc"){$order="desc";}
    else{$order="asc";}

    ////////////////////////////////////////////////////
    //COLORES
    ////////////////////////////////////////////////////
    $table.="<center><table border=0px style='font-size:0.8em' cellspacing:0px><caption>Posibles estados de las solicitudes</caption><tr>";
    foreach(array_keys($ESTADOS_COLOR) as $estado){
      $table.="<td style='background:".$ESTADOS_COLOR["$estado"]."'>".$ESTADOS["$estado"]."</td>";
    }
    $table.="</tr></table></center><p></p>";
    
    ////////////////////////////////////////////////////
    //CREATE TABLE
    ////////////////////////////////////////////////////
$table.=<<<T
<center>
<table border=0px cellspacing=2px>
<thead style='background:lightgray'>
<th class="header"><a href=movilidad.php?mode=lista&sort=movilid&order=$order>ID</a></th>
<th class="header"><a href=movilidad.php?mode=lista&sort=estado&order=$order>Estado</a></th>
<th class="header"><a href=movilidad.php?mode=lista&sort=TIMESTAMP(fechapresenta)&order=$order>Fecha estado</a></th>
<th colspan=2 class="header"><a href=movilidad.php?mode=lista&sort=TIMESTAMP(fechaini)&order=$order>Fechas evento</a></th>
<th class="header"><a href=movilidad.php?mode=lista&sort=documento&order=$order>Documento</a></th>
<th class="header"><a href=movilidad.php?mode=lista&sort=movilid&order=$order>Nombre</a></th>
<th class="header">Descargas</th>
</thead>
T;

    foreach($results as $movil){
      foreach(array_keys($MOVILIDAD_FIELDS) as $field) $$field=$movil["$field"];

      //Color
      $color=$ESTADOS_COLOR["$estado"];

      $table.="<tr style='background:$color;'>";
      $movildir="data/movilidad/$movilid";

      //ID
      $table.="<td class='listacampo'><a href=movilidad.php?mode=editar&movilid=$movilid&action=loadmovil>";
      $table.=$movilid;
      $table.="</a></td>";

      //Estado
      $table.="<td class='listacampo'>";
      $table.=$ESTADOS["$estado"];
      $table.="<br/>Solicitado:$valor<br/>Aprobado: $monto</td>";

      //Fecha
      $table.="<td class='listacampo'>";
      $table.=$fechapresenta;
      $table.="</td>";

      //Fechas evento
      $table.="<td class='listacampo'>";
      $table.=$fechaini;
      $table.="</td>";
      $table.="<td class='listacampo'>";
      $table.=$fechafin;
      $table.="</td>";

      //Documento
      $searchurl=urlencode("where email='$email'");
      $table.="<td class='listacampo'><a href=movilidad.php?mode=lista&search=$searchurl>";
      $table.=$documento;
      $table.="</a></td>";

      //Nombre
      $table.="<td class='listacampo'>";
      $table.=$nombre;
      $table.="</td>";
      
      //Archivos
      $table.="<td class='listacampo'>";
      if(!isBlank($historia)){
	$historiatxt="<a target=_blank href='$movildir/$historia'>Historia</a>";
      }else{$historiatxt="<i>No historia</i>";}
      if(!isBlank($carta)){
	$cartatxt="<a target=_blank href='$movildir/$carta'>Carta</a>";
      }else{$cartatxt="<i>No carta</i>";}
      if(!isBlank($cumplido)){
	$cumplidotxt="<a target=_blank href='$movildir/$cumplido'>Cumplido</a>";
      }else{$cumplidotxt="<i>No cumplido</i>";}
      if(!isBlank($compromiso)){
	$compromisotxt="<a target=_blank href='$movildir/$compromiso'>Compromiso</a>";
      }else{$compromisotxt="<i>No compromiso</i>";}
      $descargatxt="<a href='movilidad.php?action=descargar&movilid=$movilid'>Descargar</a>";

      $table.="$historiatxt<br/>$cartatxt<br/>$cumplidotxt<br/>$compromisotxt<br/>$descargatxt";
      $table.="</td>";
      

      $table.="</tr>";
    }
    $table.="</table></center>";

    $content.=$table;
    
    //CHECK STUDENTS ALREADY REALIZADA
    if($QPERMISO>=3){
      $sql="select GROUP_CONCAT(email) from Movilidad where fechafin<'$DATE' and estado='realizada'";
      $out=mysqlCmd($sql)[0];

$content.=<<<CON
<h2>Estudiantes</h2>
<p>
  Estudiantes pendientes: <pre style='width:100%'>$out</pre>
</p>
CON;
    }
    }else{
      $content.=$table;
    }
  }

  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //NUEVA SOLICITUD
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  else if($mode=="editar"){

    ////////////////////////////////////////////////////
    //LOAD DATA
    ////////////////////////////////////////////////////
    if(isset($loadmovil)){
      $duracion=abs(strtotime($fechafin)-strtotime($fechaini))/86400+1;
    }

    ////////////////////////////////////////////////////
    //DEFAULT VALUES
    ////////////////////////////////////////////////////
    if(!isset($tipoevento)){$tipo="evento";}
    if(!isset($lugar)){$lugar="";}
    if(isset($historia) and !isBlank($historia)){
      $filename=substr($historia,0,30)."...";
      $historia_archivo="<a href='data/movilidad/$movilid/$historia' target='_blank'>$filename</a><input type='hidden' name='historia' value='$historia'>";
    }else{
      $historia_archivo="<i>Ningún archivo subido todavía</i>";
    }
    if(isset($carta) and !isBlank($carta)){
      $filename=substr($carta,0,30)."...";
      $carta_archivo="<a href='data/movilidad/$movilid/$carta' target='_blank'>$filename</a><input type='hidden' name='carta' value='$carta'>";
    }else{
      $carta_archivo="<i>Ningún archivo subido todavía</i>";
    }
    if(isset($cumplido) and !isBlank($cumplido)){
      $filename=substr($cumplido,0,30)."...";
      $cumplido_archivo="<a href='data/movilidad/$movilid/$cumplido' target='_blank'>$filename</a><input type='hidden' name='cumplido' value='$cumplido'>";
    }else{
      $cumplido_archivo="<i>Ningún archivo subido todavía</i>";
    }
    if(isset($compromiso) and !isBlank($compromiso)){
      $filename=substr($compromiso,0,30)."...";
      $compromiso_archivo="<a href='data/movilidad/$movilid/$compromiso' target='_blank'>$filename</a><input type='hidden' name='compromiso' value='$compromiso'>";
    }else{
      $compromiso_archivo="<i>Ningún archivo subido todavía</i>";
    }
    if(!isset($movilid)){$movilid=generateRandomString(5);}
    if(!isset($estado)){
      $estado="nueva";
    }
    if(!isset($fechapresenta)){
      $fechapresenta=$DATE;
      $fechaestado=$DATE;
      $fechafin=$DATE;
    }
    if(!isset($respuesta)){$respuesta=0;}
    if(!isset($monto) or $monto==""){
      $montotxt="<i>No aprobado aún</i>";
      $actotxt="<i>No aprobado aún</i>";
    }else{
      $montotxt=$monto;
      $actotxt=$acto;
    }
    if(!isset($nombre)){
      $nombre=$NOMBRE;
      $email=$EMAIL;
      $documento=$DOCUMENTO;
    }
    $fecharango_menu=fechaRango("fecharango",$fechaini,$fechafin);

    ////////////////////////////////////////////////////
    //BLOQUEO DEPENDIENDO DE PERMISOS Y ESTADO
    ////////////////////////////////////////////////////
    $perm1="";
    $perm2="";
    $restricciones="Restricciones:<br/><ul>";
    if($QPERMISO<=3){
      if($estado=="nueva" or
	 $estado=="guardada" or
	 $estado=="devuelta"){
	$perm1="";
      }else{
	$restricciones.="<li>La solicitud esta en un estado en el que el estudiante no puede realizar cambios</li>";
	$perm1="readonly";
	$bperm1="disabled";
      }
    }
    //CHECK TIME FOR CUMPLIDO
    $fecha=$DATE;
    //$fecha="2016-07-01 00:00:00";
    $diferencia=(strtotime($fecha)-strtotime($fechafin))/86400.0;
    //echo "Fecha: $fecha, Fecha fin: $fechafin, Diferencia:$diferencia<br/>";
    if($diferencia<1.0){
      $perm2="readonly";
      $bperm2="disabled";
      $restricciones.="<li>No se ha cumplido el tiempo de la actividad. No puede todavía subir el cumplido o el compromiso.</li>";
      $backcumplido="";
      $botoncumplir="";
    }else{
      $perm2="";
      $backcumplido="background:pink;";
      $botoncumplir="<input type='submit' name='action' value='Cumplir' class='boton'>";
    }
    if($QPERMISO<=3){
      if($estado=="terminada" or $estado=="rechazada"){
	$perm1="readonly";
	$bperm1="disabled";
	$perm2="readonly";
	$bperm2="disabled";
	$backcumplido="";
	$botoncumplir="";
      }
    }
    if($restricciones=="Restricciones:<br/><ul>"){$restricciones="";}

    ////////////////////////////////////////////////////
    //COLOR
    ////////////////////////////////////////////////////
    $color=$ESTADOS_COLOR["$estado"];

    ////////////////////////////////////////////////////
    //SELECCION
    ////////////////////////////////////////////////////
    $estadotxt=$ESTADOS["$estado"];
    $respuestatxt=$BOOLEAN["$respuesta"];
    $helpicon="<a href='JavaScript:void(null)' onclick='toggleHelp(this)'><img src='img/help.png' width='15em'></a>";

    $readonly=0;
    if(!isBlank($perm1)){$readonly=1;}
    $tiposel=generateSelection($TIPO_EVENTO,"tipoevento","$tipoevento","",$readonly);
    $programasel=generateSelection($PROGRAMAS_FCEN,"programa","$programa","",$readonly);
    $apoyosel=generateSelection($APOYOS,"apoyo","$apoyo","",$readonly);
    $estadosel=generateSelection($ESTADOS,"estado","$estado","",$readonly);

    ////////////////////////////////////////////////////
    //DISPLAY
    ////////////////////////////////////////////////////
    if($estado=="nueva"){
$content.=<<<FORM
<style>
.escondida{
 display:none;
}
</style>      
<style>
.nonueva{
 display:none;
}
</style>      
FORM;
    }

    ////////////////////////////////////////////////////
    //BOTONES
    ////////////////////////////////////////////////////
    ////////////////////////////////////////////////////
    //BOTON DE ENVIAR
    ////////////////////////////////////////////////////
    if($estado=="guardada" or $estado=="nueva"){
      $enviar="<input $bperm1 type='submit' name='action' value='Enviar' class='boton nonueva'>";
    }

$botones=<<<B
<tr class="field">
  <td colspan=2 class="botones">
    $botoncumplir
    <input $bperm1 type="submit" name="action" value="Guardar" class="boton">
    $enviar
    <input $bperm1 type="submit" name="action" value="Salir" class="boton">
    <input $bperm1 type="submit" name="action" value="Borrar" class="boton nonueva">
  </td>
</tr>
B;
    if($estado=="nueva"){
$cargar=<<<C
<div class="escondida">
<form action="movilidad.php" method="post" enctype="multipart/form-data" accept-charset="utf-8">
  <a href="JavaScript:void(null)" onclick="$('#cargamovil').toggle()" style="font-size:0.8em";>Cargar desde un archivo</a>
  <div id="cargamovil">
    <input $bperm1 type="file" name="movilfile">
    <input type="submit" name="action" value="Cargar">
    <input type="hidden" name="movilid" value="$movilid">
  </div>
</form>
</div>
C;
    }else{$cargar="";}

    ////////////////////////////////////////////////////
    //FORMULARIO
    ////////////////////////////////////////////////////

$content.=<<<FORM
<h3>Solicitud Bolsa de Movilidad Estudiantil FCEN</h3>

<p>
Complete o modifique el formulario a continuación para presentar una
solicitud a la bolsa de movilidad
estudiantil.  <i style="color:red">Si esta es su primera vez
presentando una solicitud vea primero
el <a href="https://www.youtube.com/watch?v=vpBmjn3pm2o"
target="_blank">videotutorial asociado</a></i>.
</p>

<center>
$cargar
<form action="movilidad.php?loadmovil" method="post" enctype="multipart/form-data" accept-charset="utf-8">
<input type="hidden" name="mode" value="editar">
<table width=60% cellspacing=10px style="background:$color">
<tr><td width=20%></td></td width=60%></tr>

<!---------------------------------------------------------------------->
<tr class="nonueva"><td colspan=2 style="font-size:0.7em;font-style:italic;">
  $restricciones
</td></tr>
<!---------------------------------------------------------------------->
$botones
<!---------------------------------------------------------------------->
<tr><td colspan=2 class="header nonueva"><b>Información Solicitud</b></td></tr>
<!---------------------------------------------------------------------->
<tr class="field nonueva">
  <td class="campo">Número:</td>
  <td class="form">
    $movilid
    <input type="hidden" name="movilid" value="$movilid">
  </td>
</tr>
<tr class="field nonueva">
  <td class="campo">Fecha presentación:</td>
  <td class="form">
    $fechapresenta
    <input type="hidden" name="fechapresenta" value="$fechapresenta">
  </td>
</tr>
<tr class="field nonueva">
  <td class="campo">Fecha actualización:</td>
  <td class="form">
    $fechaestado
    <input type="hidden" name="fechaestado" value="$fechaestado">
  </td>
</tr>
<tr class="field nonueva">
  <td class="campo">Estado solicitud:</td>
  <td class="form">
    $estadotxt
  </td>
</tr>
<tr class="field nonueva">
  <td class="campo">Respuesta profesor:</td>
  <td class="form">
    $respuestatxt
    <input type="hidden" name="respuesta" value="$respuesta">
  </td>
</tr>
<tr class="field nonueva">
  <td class="campo">Monto aprobado:</td>
  <td class="form">
    $montotxt
  </td>
</tr>
<tr class="field nonueva">
  <td class="campo">Acto administrativo:</td>
  <td class="form">
    $actotxt
  </td>
</tr>
<tr class="field nonueva">
  <td class="campo">Observaciones administrativas:</td>
  <td class="form" style="border:solid black 1px;padding:10px;">
    <pre>$observacionesadmin</pre>
  </td>
</tr>
<!---------------------------------------------------------------------->
<tr><td colspan=2 class="header"><b>Información Solicitante</b></td></tr>
<!---------------------------------------------------------------------->
<tr class="field">
  <td class="campo">Nombre del solicitante:</td>
  <td class="form">
    $nombre
    <input type="hidden" name="nombre" value="$nombre">
  </td>
</tr>
<!---------------------------------------------------------------------->
<tr class="field">
  <td class="campo">E-mail:</td>
  <td class="form">
    $email
    <input type="hidden" name="email" value="$email">
  </td>
</tr>
<!---------------------------------------------------------------------->
<tr class="field">
  <td class="campo">Documento de identidad:</td>
  <td class="form">
    $documento
    <input type="hidden" name="documento" value="$documento">
  </td>
</tr>
<!---------------------------------------------------------------------->
<tr class="field">
  <td class="campo">Programa de pregrado:</td>
  <td class="form">
    $programasel
  </td>
</tr>
<!---------------------------------------------------------------------->
<tr ><td colspan=2 class="header" style="$backcumplido"><b>Cumplido</b></td></tr>
<!---------------------------------------------------------------------->
<tr class="field">
  <td class="campo" id="cumplido" style="$backcumplido">Cumplido$helpicon:</td>
  <td class="form" style="$backcumplido">
    <input $bperm2 type="file" name="cumplido" value="$cumplido"><br/>
    <span class="archivo">Archivo: $cumplido_archivo</span>
  </td>
</tr>
<tr class="ayuda" id="cumplido_help" >
  <td colspan=2>Suba aquí el documento que certifique su participación en el evento.</td>
</tr>
<tr class="field">
  <td class="campo" id="compromiso" style="$backcumplido">Compromiso$helpicon:</td>
  <td class="form"  style="$backcumplido">
    <input $bperm2 type="file" name="compromiso" value="$compromiso"><br/>
    <span class="archivo">Archivo: $compromiso_archivo</span>
  </td>
</tr>
<tr class="ayuda" id="compromiso_help">
  <td colspan=2>Suba aquí un documento que certifique que cumplió con el compromiso del apoyo (presentación de un seminario).  Puede ser una carta, foto o el aviso original del seminario.</td>
</tr>

<!---------------------------------------------------------------------->
<tr><td colspan=2 class="header"><b>Información Solicitud</b></td></tr>
<!---------------------------------------------------------------------->
<tr class="field">
  <td class="campo" id="tipo">Tipo de evento$helpicon:</td>
  <td class="form">
    $tiposel
  </td>
</tr>
<tr class="ayuda" id="tipo_help">
  <td colspan=2>Evento académico: congreso, simposio, workshop,
    taller.  Pasantía: estadía corta o larga en otra institución.</td>
</tr>
<!---------------------------------------------------------------------->
<tr class="field">
  <td class="campo" id="lugar">Lugar$helpicon:</td>
  <td class="form">
    <input $perm1 type="text" size=30 name="lugar" placeholder="Ciudad (País)" value="$lugar">
  </td>
</tr>
<tr class="ayuda" id="lugar_help">
  <td colspan=2>Indique la ciudad y el país de la actividad.</td>
</tr>
<!---------------------------------------------------------------------->
<tr class="field">
  <td class="campo" id="idioma">Idioma$helpicon:</td>
  <td class="form">
    <input $perm1 type="text" size=30 name="idioma" placeholder="Inglés" value="$idioma">
  </td>
</tr>
<tr class="ayuda" id="idioma_help">
  <td colspan=2>Idioma en el que se desarrolla el evento o la pasantía.</td>
</tr>
<!---------------------------------------------------------------------->
<tr class="field">
  <td class="campo" id="evento">Evento o Institución$helpicon:</td>
  <td class="form">
    <input $perm1 type="text" size=30 name="evento" placeholder="Nombre del Evento" value="$evento">
  </td>
</tr>
<tr class="ayuda" id="evento_help">
  <td colspan=2>Indique el nombre completo del evento o de la
  institución anfitriona.</td>
</tr>
<!---------------------------------------------------------------------->
<tr class="field">
  <td class="campo" id="fechas">Fehas$helpicon:</td>
  <td class="form">
    $fecharango_menu
  </td>
</tr>
<tr class="ayuda" id="fechas_help">
  <td colspan=2>Indique el rango de fechas del evento. Si es solo un día primero presione "Hoy" y luego busque la fecha respectiva</td>
</tr>
<!---------------------------------------------------------------------->
<tr class="field">
  <td class="campo" id="documento">Documento profesor$helpicon:</td>
  <td class="form">
    <input $perm1 type="text" size=30 name="documento_profesor" placeholder="Documento de dentidad" value="$documento_profesor" onchange="fillProfesor(this)">
  </td>
</tr>
<tr class="ayuda" id="documento_help">
  <td colspan=2>Indique el documento de identidad del profesor que respalda su aplicación.</td>
</tr>
<!---------------------------------------------------------------------->
<tr class="field">
  <td class="campo" id="profesor">Nombre profesor$helpicon:</td>
  <td class="form">
    <input $perm1 type="text" size=30 name="profesor" placeholder="Se autocompletara" value="$profesor" readonly>
  </td>
</tr>
<tr class="ayuda" id="profesor_help">
  <td colspan=2>Indique el nombre del profesor que respalda su aplicación.</td>
</tr>
<!---------------------------------------------------------------------->
<tr class="field">
  <td class="campo" id="email_profesor">E-mail del profesor$helpicon:</td>
  <td class="form">
    <input $perm1 type="text" size=30 name="email_profesor" placeholder="Se autocompletara" value="$email_profesor" readonly>
  </td>
</tr>
<tr class="ayuda" id="email_profesor_help">
  <td colspan=2>Indique el correo electrónico del profesor.  Es importante ingresar el correo correcto puesto que el profesor deberá dar el visto bueno.</td>
</tr>
<!---------------------------------------------------------------------->
<tr class="field">
  <td class="campo" colspan=2 id="presupuesto">Presupuesto$helpicon:</td>
</tr>
<tr>
  <td class="form" colspan=2>
    <table cellspacing=0 border=0px width="100%" style="border:solid black 1px;padding:20px;">
      <tr>
	<td>Elemento</td>
	<td>Valor estimado<br/><i style="font-size:0.8em">En pesos</i></td>
	<td>Fuente de financiación<br/><i style="font-size:0.8em">Si lo tiene</i></td>
      </tr>
      <tr>
	<td><input $perm1 type="text" size=30 name="item1" placeholder="Transporte" value="$item1"></td>
	<td><input $perm1 type="text" size=30 name="value1" placeholder="50,000" value="$value1" onchange="calcularTotal(this)"></td>
	<td><input $perm1 type="text" size=30 name="fuente1" placeholder="Grupo de Investigación" value="$fuente1"></td>
      </tr>
      <tr>
	<td><input $perm1 type="text" size=30 name="item2" placeholder="" value="$item2"></td>
	<td><input $perm1 type="text" size=30 name="value2" placeholder="" value="$value2" onchange="calcularTotal(this)"></td>
	<td><input $perm1 type="text" size=30 name="fuente2" placeholder="" value="$fuente2"></td>
      </tr>
      <tr>
	<td><input $perm1 type="text" size=30 name="item3" placeholder="" value="$item3"></td>
	<td><input $perm1 type="text" size=30 name="value3" placeholder="" value="$value3" onchange="calcularTotal(this)"></td>
	<td><input $perm1 type="text" size=30 name="fuente3" placeholder="" value="$fuente3"></td>
      </tr>
      <tr>
	<td><input $perm1 type="text" size=30 name="item4" placeholder="" value="$item4"></td>
	<td><input $perm1 type="text" size=30 name="value4" placeholder="" value="$value4" onchange="calcularTotal(this)"></td>
	<td><input $perm1 type="text" size=30 name="fuente4" placeholder="" value="$fuente4"></td>
      </tr>
      <tr>
	<td><input $perm1 type="text" size=30 name="item5" placeholder="" value="$item5"></td>
	<td><input $perm1 type="text" size=30 name="value5" placeholder="" value="$value5" onchange="calcularTotal(this)"></td>
	<td><input $perm1 type="text" size=30 name="fuente5" placeholder="" value="$fuente5"></td>
      </tr>
      <tr><td colspan=3>
	  Total: <span id="total">$total</span>
	  <input id="totalfield" type="hidden" name="total" value="$total">
      </td></tr>
    </table>
  </td>
</tr>
<tr class="ayuda" id="presupuesto_help">
  <td colspan=2>Complete la tabla.  Si hay más de 5 ítems en el presupuesto, resúmalos en solo 5.</td>
</tr>
<!---------------------------------------------------------------------->
<tr class="field">
  <td class="campo" id="valor">Valor solicitado$helpicon:</td>
  <td class="form">
    <input $perm1 type="text" size=30 name="valor" placeholder="50000" value="$valor" onchange="plainNumber($(this))">
  </td>
</tr>
<tr class="ayuda" id="valor_help">
  <td colspan=2>Indique el valor total solicitado a la bolsa.</td>
</tr>
<!---------------------------------------------------------------------->
<tr class="field">
  <td class="campo" id="historia">Historia académica$helpicon:</td>
  <td class="form">
    <input $bperm1 type="file" name="historia" value="$historia"><br/>
    <span class="archivo">Archivo: $historia_archivo</span>
  </td>
</tr>
<tr class="ayuda" id="historia_help">
  <td colspan=2>Suba aquí su historia académica o un certificado de
  estudio en el que conste: promedio acumulado y número total de
  créditos cursados en el programa.</td>
</tr>
<!---------------------------------------------------------------------->
<tr class="field">
  <td class="campo" id="carta">Soporte de invitación o aceptación$helpicon:</td>
  <td class="form">
    <input $bperm1 type="file" name="carta" value="$carta"><br/>
    <span class="archivo">Archivo: $carta_archivo</span>
  </td>
</tr>
<tr class="ayuda" id="carta_help">
  <td colspan=2>Soporte (carta, pantallazo o correo) indicando
  aceptación en el evento o en la pasantía. Si es evento el soporte
  debe indicar explícitamente que el estudiante participara con
  ponencia oral o poster.</td>
</tr>
<!---------------------------------------------------------------------->
<tr class="field">
  <td class="campo" colspan=2 id="observaciones">Observaciones adicionales$helpicon:</td>
</tr>
<tr class="field">
  <td class="form" colspan=2>
    <textarea $perm1 name="observaciones" cols=80 rows=10>$observaciones</textarea>
  </td>
</tr>
<tr class="ayuda" id="observaciones_help">
  <td colspan=2>Puede indicar aquí aclaraciones sobre el presupuesto,
  si viaja en un grupo, entre otros detalles no contemplados en el
  formulario.</td>
</tr>
<!---------------------------------------------------------------------->
$botones
<!---------------------------------------------------------------------->
</table>

<table class="level4" width=60% cellspacing=10px>
<tr><td width=20%></td></td width=60%></tr>
<!---------------------------------------------------------------------->
<tr><td colspan=2 class="header"><b>Reservado para la Administración</b></td></tr>
<!---------------------------------------------------------------------->
<tr class="field">
  <td class="campo">Duración:</td>
  <td class="form">
     $duracion
     <input type="hidden" name="duracion" value="$duracion">
  </td>
</tr>
<!---------------------------------------------------------------------->
<tr class="field">
  <td class="campo" id="apoyo">Tipo de apoyo$helpicon:</td>
  <td class="form">
     $apoyosel
  </td>
</tr>
<tr id="apoyo_help" class="ayuda">
  <td colspan=2>Escoja aquí el tipo de apoyo que se le brindará al estudiante.</td>
</tr>
<!---------------------------------------------------------------------->
<tr class="field">
  <td class="campo" id="monto">Monto aprobado$helpicon:</td>
  <td class="form">
    <input $perm1 type="text" size=30 name="monto" placeholder="Valor" value="$monto" onchange="plainNumber($(this))">
  </td>
</tr>
<tr id="monto_help" class="ayuda">
  <td colspan=2>Defina aquí el monto aprobado para esta solicitud.</td>
</tr>
<!---------------------------------------------------------------------->
<tr class="field">
  <td class="campo" id="acto">Acto Administrativo$helpicon:</td>
  <td class="form">
    <input $perm1 type="text" size=30 name="acto" placeholder="Número de acta, fecha" value="$acto">
  </td>
</tr>
<tr id="acto_help" class="ayuda">
  <td colspan=2>Escribar aquí el número de acta y fecha en la que se aprobó este apoyo.</td>
</tr>
<!---------------------------------------------------------------------->
<tr class="field">
  <td class="campo" colspan=2 id="observacionesadmin">Observaciones administrativas$helpicon:</td>
</tr>
<tr class="field">
  <td class="form" colspan=2>
    <textarea $perm1 name="observacionesadmin" cols=80 rows=10>$observacionesadmin</textarea>
  </td>
</tr>
<tr id="observacionesadmin_help" class="ayuda">
  <td colspan=2>Puede ingresar aquí observaciones a la solicitud que podrá leer el solicitante.</td>
</tr>
<!---------------------------------------------------------------------->
<tr class="field">
  <td class="campo" id="estado">Estado$helpicon:</td>
  <td class="form">
    $estadosel
  </td>
</tr>
<tr id="estado_help" class="ayuda">
  <td colspan=2>Puede cambiar el estado de la solicitud.  Maneje con cuidado esta opción.</td>
</tr>
<!---------------------------------------------------------------------->
$botones
<!---------------------------------------------------------------------->
</table>
</form>
</center>
FORM;

    goto end;
  }

  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //EMPTY
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  else if($mode=="refresh"){
    goto end;
  }

  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //REFRESH (EXPERIMENTAL)
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  else if($mode=="refresh"){
    $content.="<script>window.location.href='movilidad.php';</script>";
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
