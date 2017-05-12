<!DOCTYPE html>
<html prefix="og: http://ogp.me/ns#" class="no-js" xmlns="http://www.w3.org/1999/xhtml" xml:lang="" lang="" slick-uniqueid="3">
<?php
////////////////////////////////////////////////////////////////////////
//LOAD LIBRARY
////////////////////////////////////////////////////////////////////////
$HOST=$_SERVER["HTTP_HOST"];
$SCRIPTNAME=$_SERVER["SCRIPT_FILENAME"];
$ROOTDIR=rtrim(shell_exec("dirname $SCRIPTNAME"));
require("$ROOTDIR/etc/library2.php");
require("$ROOTDIR/etc/modulos/comisiones.php");

////////////////////////////////////////////////////////////////////////
//SCRIPT
////////////////////////////////////////////////////////////////////////
$script=<<<S
    function selectCorta(selection){
	if($(selection).val().localeCompare("noremunerada")==0 || $(selection).val().localeCompare("calamidad")==0){
	  $(".discorta").hide();
	  $(".discortashow").show();
	}else{
	  $(".discorta").show();
	  $(".discortashow").hide();
	}
    }
S;

////////////////////////////////////////////////////////////////////////
//INITIALIZATION
////////////////////////////////////////////////////////////////////////
$content="<body>";
$content.=getHeaders(true,$script);
$content.=getHead();
$content.=getMainMenu();

////////////////////////////////////////////////////////////////////////
//SUBMENU
////////////////////////////////////////////////////////////////////////
$submenu=<<<M
<li class="level1"><a href="comisiones$VER.php?">Inicio</a></li>
<li class="level0"><a href="usuarios$VER.php?urlref=$urlref">Conectarse</a></li>
<li class="level1"><a href="actions$VER.php?action=Cerrar">Desconectarse</a></li>
<li class="level1"><a href="comisiones$VER.php?action=Solicitar">Nueva Solicitud</a></li>
<li class="level1"><a href="comisiones$VER.php?action=Consultar">Lista de Solicitudes</a></li>
<li class="level1"><a href="comisiones$VER.php?action=Consultar&qtrash=1">Eliminados</a></li>
M;
$content.=getSubMenu($submenu);

////////////////////////////////////////////////////////////////////////
//BODY
////////////////////////////////////////////////////////////////////////
$content.=getBody(100);

////////////////////////////////////////////////////////////////////////
//MAIN PAGE
////////////////////////////////////////////////////////////////////////
$body=<<<B
<h3>Comisiones</h3>

<p>
Este es el módulo de solicitud de Comisiones.  Usando las funciones
aquí y dependiendo de su vinculación usted podrá:
<ul>
<li>Registrar una solicitud de comisión nueva.</li>
<li>Revisar el estado de una solicitud de comisión.</li>
</p>

B;
$content.=<<<C
<form action="comisiones$VER.php?$USERSTRING" method="post" enctype="multipart/form-data" accept-charset="utf-8">
C;

//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
//PROCESSING
//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
if(isset($operation)){

  //////////////////////////////////////////////////////////////
  //CONFIRMA CUMPLIDO
  //////////////////////////////////////////////////////////////
  if($operation=="confirmacumplido"){

    //GET INFORMATION ABOUT COMISION
    $comision=getComisionInfo($comisionid);
    array2Globals($comision);
    
    //UPDATE DATABASE
    $now=mysqlCmd("select now();")[0];
    if(!preg_match("/$emailconfirma/",$confirmacumplido)){
      $sql="update Comisiones_Solicitudes set confirmacumplido='$emailconfirma::$now;$confirmacumplido' where comisionid='$comisionid';";
      mysqlCmd($sql);
    }

echo<<<M
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
  <body>
<center>
<div style="font-size:16px;text-align:center;background:lightgray;padding:30px;width:80%">
  Gracias por confirmar la recepción en su
  correo, <b>$emailconfirma</b>, de los cumplidos de la
  comisión <b>$comisionid</b> del profesor <b>$nombre</b>.
</div>
</center>
</body>
M;

    return;
  }

  //////////////////////////////////////////////////////////////
  //UPLOAD CUMPLIDO
  //////////////////////////////////////////////////////////////
  if($operation=="Cumplir"){

    //GET INFORMATION ABOUT COMISION
    $comision=getComisionInfo($comisionid);
    $suffix=$comision["cedula"]."_${comisionid}";

    //FILES
    $file1=$_FILES["file_cumplido1"];
    $file2=$_FILES["file_cumplido2"];

    $update="set ";

    $error="";
    if($file1["size"]>0){
      $name=$file1["name"];
      $tmp=$file1["tmp_name"];
      $file_cumplido1="Cumplido1_${suffix}_$name";
      shell_exec("cp $tmp comisiones/$comisionid/'$file_cumplido1'");
      $update.="cumplido1='$name',";
      $error.=errorMessage("Archivo de Cumplido '$name' subido...");
      $comision["cumplido1"]=$name;
    }
    if($file2["size"]>0){
      $name=$file2["name"];
      $tmp=$file2["tmp_name"];
      $file_cumplido2="Cumplido2_${suffix}_$name";
      shell_exec("cp $tmp comisiones/$comisionid/'$file_cumplido2'");
      $update.="cumplido2='$name',";
      $error.=errorMessage("Archivo de Cumplido '$name' subido...");
      $comision["cumplido2"]=$name;
    }

    //UPDATE DATABASE
    $qemail=1;
    $estado="";
    $qcumplido=0;

    if($update!="set "){
      if(isset($envia)){
	$qcumplido=1;
	$estado="estado='cumplida'";
	$error.=errorMessage("<b>Felicitaciones. Su comisión se ha cumplido con exito.</b>");
      }else{
	$error.=errorMessage("Recuerde que su solicitud no se actualizará al estado de cumplida hasta que no autorice el envio del correo de notificación");
      }
      $update.="qcumplido='$qcumplido',infocumplido='$infocumplido',$estado";
      $update=trim($update,",");
      $sql="update Comisiones_Solicitudes $update where comisionid='$comisionid';";
      //echo "SQL:$sql<br/>";
      mysqlCmd($sql);
      $comision["qcumplido"]=1;
      $comision["destinoscumplido"]=$DESTINATARIOS_CUMPLIDOS[0][2].";";
      $comision["infocumplido"]=$infocumplido;
    }else{
      if(isset($envia)){
	$qcumplido=1;
	$estado="estado='cumplida'";
	$sql="update Comisiones_Solicitudes set qcumplido='$qcumplido',$estado where comisionid='$comisionid';";
	mysqlCmd($sql);
	$comision["qcumplido"]=1;
	$error.=errorMessage("<b>Felicitaciones. Su comisión se ha cumplido con exito.</b>");
      }else{
	$error.=errorMessage("Recuerde que su solicitud no se actualizará al estado de cumplida hasta que no autorice el envio del correo de notificación");
      }      
    }

    //NOTHING HAS BEEN ADDED
    if($comision["qcumplido"]==0 and $update=="set "){
      $error.=errorMessage("No se ha subido ningún archivo.");
      $qemail=0;
    }

    //NINGUN ARCHIVO SE HA CAMBIADO
    if($comision["qcumplido"]==1 and $update=="set "){
      $error.=errorMessage("No han cambiado los archivos.");
    }

    //CONVERT TO GLOBAL
    array2Globals($comision);

    //ADD NEW E-MAILS
    $emails=preg_split("/\s*,\s*/",$otros_destinatarios);
    $i=count($DESTINATARIOS_CUMPLIDOS);
    $destinatarios=array();
    foreach($emails as $demail){
      if(isBlank($demail)){continue;}
      array_push($DESTINATARIOS_CUMPLIDOS,array($demail,$demail,$demail));
      array_push($destinatarios,$i);
      $i++;
    }
    //echo "Emails:".print_r($DESTINATARIOS_CUMPLIDOS,true);
    //echo "Destinatarios:".print_r($destinatarios,true)."<br/>";
    //ADD E-MAILS TO DATABASE
    $destintxt="destinoscumplido='$destinoscumplido";
    $i=-1;
    foreach($DESTINATARIOS_CUMPLIDOS as $destino){
        //echo "Destino: ".print_r($destino,true)."<br/>";
	$i++;
	$index=array_search($i,$destinatarios);
	//echo "Index: $index<br/>";
	if(isBlank($index)){continue;}

	$dependencia=$destino[0];
	$persona=$destino[1];
	$emailpersona=$destino[2];

	if(!preg_match("/$emailpersona/",$destinoscumplido)){
	  $destintxt.="$emailpersona;";
	}
    }
    $destintxt.="'";
    //echo "Destinos: $destintxt";
    $sql="update Comisiones_Solicitudes set $destintxt where comisionid='$comisionid';";
    mysqlCmd($sql);

    //CHECK CONFIRMATION E-MAIL
    if(!isset($envia)){
      $error.=errorMessage("No se han enviado correos de notificación.");
      $qemail=0;
    }

    //SEND E-MAIL
    if($qemail){
      
      //ATACHMENTS
      $ttipocom=$TIPOSCOM[$comision["tipocom"]];

      $cumplidos="<ul>";
      if(!isBlank($cumplido1)){
	$cumplidos.="<li><b>Cumplido 1</b>: <a href='$URL/comisiones/$comisionid/Cumplido1_${suffix}_$cumplido1' download>$cumplido1</a></li>";
      }
      if(!isBlank($cumplido2)){
	$cumplidos.="<li><b>Cumplido 2</b>: <a href='$URL/comisiones/$comisionid/Cumplido2_${suffix}_$cumplido2' download>$cumplido2</a></li>";
      }
      $cumplidos.="</ul>";

      //MESSAGE HEADER
      $subject="[Cumplido FCEN] $nombre ha enviado un cumplido por la actividad realizada en $fecha.";
      $headers="";
      $headers.="From: noreply@udea.edu.co\r\n";
      $headers.="Reply-to: noreply@udea.edu.co\r\n";
      $headers.="MIME-Version: 1.0\r\n";
      $headers.="MIME-Version: 1.0\r\n";
      $headers.="Content-type: text/html\r\n";

      $i=-1;
      $destintxt="destinoscumplido='$destinoscumplido";

      foreach($DESTINATARIOS_CUMPLIDOS as $destino){
	$i++;
	$index=array_search($i,$destinatarios);
	if(isBlank($index)){continue;}

	$dependencia=$destino[0];
	$persona=$destino[1];
	$emailpersona=$destino[2];

	if(preg_match("/$emailpersona::/",$confirmacumplido)){
	  $error.=errorMessage("$emailpersona ya confirmo.");
	  continue;
	}

	$url="$URL/?operation=confirmacumplido&comisionid=$comisionid&emailconfirma=$emailpersona";
	$linkconfirmacion="<a href=$url>$url</a>";
	
$message=<<<M
<p>
Apreciado(a) $persona,

<p>
El(La) Empleado(a) <b>$nombre</b> identificado con
documento <b>$cedula</b> del <b>$instituto</b>, ha concluido
una <b>$ttipocom</b> con el objetivo de <b>$actividad</b>.  La
actividad se realizó en la(s) fecha(s) <b>$fecha</b>.
</p>

<p>
Como parte de los compromisos con su dependencia (<b>$dependencia</b>)
el profesor ha subido al <b>Sistema de Comisiones de la Facultad</b>,
el(los) siguiente(s) document(s) que certifican la realización de la
actividad realizada (cumplidos).
</p>

<p>
Usted puede descargar el(los) documento(s) de los siguientes enlaces:
</p>

$cumplidos

<p>
La siguiente información de interés fue adicionalmente provista por el
profesor para su conocimiento:
<blockquote><i>$infocumplido</i></blockquote>
</p>

<p style="color:red">
Le solicitamos amablemente confirmar la recepción de esta
documentación haciendo click en este enlace: $linkconfirmacion.
</p>

<p>Atentamente,</p>

<p>
<b>Sistema de Solicitud de Comisiones<br/>
Decanato, FCEN</b>
</p>
M;
        $simulacion="";
        if(!$QTEST or $QOVER){
	  sendMail($emailpersona,$subject,$message,$headers);
	}
	else{$simulacion=" (Simulación)";}
	$error.=errorMessage("Mensaje enviado a $emailpersona $simulacion");
      }
    }
  }

  //////////////////////////////////////////////////////////////
  //PERFORM BACKUP
  //////////////////////////////////////////////////////////////
  if($operation=="Backup"){
    shell_exec("bash backup.sh");
    $error=errorMessage("Respaldo realizado.  Descarguelo de <a href='scratch/backup-comisiones.tar.gz'>este enlace.</a>");
  }

  //////////////////////////////////////////////////////////////
  //GUARDAR SOLICITUD
  //////////////////////////////////////////////////////////////
  if($operation=="Guardar"){
    
    //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
    //CREAR DIRECTORIO DE COMISION
    //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
    shell_exec("mkdir -p comisiones/$comisionid");

    //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
    //SUBIR LOS ARCHIVOS
    //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
    $file1=$_FILES["file_anexo1"];
    $file2=$_FILES["file_anexo2"];
    $file3=$_FILES["file_anexo3"];

    if($file1["size"]>0){
      $name=$file1["name"];
      $tmp=$file1["tmp_name"];
      $anexo1=$name;
      shell_exec("cp $tmp comisiones/$comisionid/'$name'");
    }
    if($file2["size"]>0){
      $name=$file2["name"];
      $tmp=$file2["tmp_name"];
      $anexo2=$name;
      shell_exec("cp $tmp comisiones/$comisionid/'$name'");
    }
    if($file3["size"]>0){
      $name=$file3["name"];
      $tmp=$file3["tmp_name"];
      $anexo3=$name;
      shell_exec("cp $tmp comisiones/$comisionid/'$name'");
    }

    //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
    //DISPONIBLE
    //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
    if(abs($QPERMISO)==$PERMISO_DIRECTOR and $vistobueno=="Si" and ($tipocom=="noremunerada" or $tipocom=="calamidad")){
      $parts=preg_split("/-/",$DATE);
      $year=$parts[0];
      if($year!=$ano){
	$diasdisponible=6;
	$ano=$year;
      }else{
	$diasdisponible=$diasdisponible-$diaspermiso;
      }
    }
    
    //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
    //CHECK STATUS
    //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
    if($estado=="devuelta"){
      $vistobueno="No";
      $aprobacion="No";
    }
    if($vistobueno=="Si"){
      $estado="vistobueno";
    }else if($estado!="devuelta"){
      $estado="solicitada";
    }

    if($aprobacion=="Si"){
      $estado="aprobada";
      if($tipocom!="noremunerada" and $tipocom!="calamidad"){
	$result=mysqlCmd("show table status where Name='Resoluciones';",$qout=0,$qlog=0);
	$resolucion=$result["Auto_increment"];
	$sql="insert into Resoluciones (comisionid) values ('$comisionid');";
	mysqlCmd($sql);
      }else{
	$resolucion="99999";
      }
      if(!file_exists("comisiones/$comisionid/resolucion-$comisionid.pdf")){
	$target="comisiones/$comisionid/resolucion-$comisionid";
	$targetblank="comisiones/$comisionid/resolucion-blank-$comisionid";
	shell_exec("cp etc/resolucion-blank.html $target.html");
	shell_exec("cp etc/resolucion-blank.pdf $target.pdf");
	shell_exec("cp etc/resolucion-blank.html $targetblank.html");
	shell_exec("cp etc/resolucion-blank.pdf $targetblank.pdf");
	shell_exec("touch comisiones/$comisionid/.nogen");
      }	
    }else{
      if($vistobueno=="Si"){
	$estado="vistobueno";
      }else if($estado!="devuelta"){
	$estado="solicitada";
      }
    }
    if(abs($QPERMISO)==$PERMISO_PROFESOR and $estado=="devuelta"){$estado="solicitada";}

    //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
    //GRABAR DATOS EN BASE DE DATOS
    //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
    $fl=fopen("comisiones/$comisionid/comision-$comisionid.txt","w");
    $fields_comisiones="";
    $values_comisiones="";
    $fval_comisiones="";
    $fecharango=str2Array($fecharango);
    foreach($FIELDS_COMISIONES as $field){
      $fieldn=$field;
      if($field=="extra1"){$field="diaspermiso";}
      $value=$$field;
      if($field=="fechaini"){
	$value=$fecharango["start"];
      }
      if($field=="fechafin"){
	$value=$fecharango["end"];
      }
      if($field=="fecha"){
	$fechaini=$fecharango["start"];
	$fechainistr=ucwords(strftime("%B %e de %Y",strtotime($fechaini)));
	$fechafin=$fecharango["end"];
	$fechafinstr=ucwords(strftime("%B %e de %Y",strtotime($fechafin)));
	$fechainistr=preg_replace("/De/","de",$fechainistr);
	$fechafinstr=preg_replace("/De/","de",$fechafinstr);
	$value="$fechainistr";
	if($fechainistr!=$fechafinstr){
	  $value.=" a $fechafinstr";
	}
      }
      $fields_comisiones.="$fieldn,";
      $values_comisiones.="'$value',";
      $fval_comisiones.="$fieldn='$value',";
      fwrite($fl,"$fieldn = $value\n");
    }
    $fields_comisiones=trim($fields_comisiones,",");
    $values_comisiones=trim($values_comisiones,",");
    $fval_comisiones=trim($fval_comisiones,",");

    $fields_profesores="";
    $values_profesores="";
    $fval_profesores="";
    foreach($FIELDS_PROFESORES as $field){
      $fieldn=$field;
      if($field=="extra1"){$field="diasdisponible";}
      if($field=="extra2"){$field="ano";}
      $value=$$field;
      $fields_profesores.="$fieldn,";
      $values_profesores.="'$value',";
      $fval_profesores.="$fieldn='$value',";
      fwrite($fl,"$fieldn = $value\n");
    }
    $fields_profesores=trim($fields_profesores,",");
    $values_profesores=trim($values_profesores,",");
    $fval_profesores=trim($fval_profesores,",");
    fclose($fl);

    $sql="insert into Comisiones_Solicitudes ($fields_comisiones) values ($values_comisiones) on duplicate key update $fval_comisiones";
    mysqlCmd($sql);

    $sql="insert into Usuarios ($fields_profesores) values ($values_profesores) on duplicate key update $fval_profesores";
    mysqlCmd($sql);
    $error=errorMessage("Comisión '$comisionid' guardada.");
    $inputform=0;

    //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
    //GRABAR DATOS EN ARCHIVOS DE TEXTO
    //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
    foreach($TEXTS as $text){
      $value=$$text;
      $fl=fopen("comisiones/$comisionid/$text.txt","w");
      fwrite($fl,$value);
      fclose($fl);
    }

    //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
    //ENVIAR CORREO DE NOTIFICACION
    //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
    $qcopy=0;
    $out=mysqlCmd("select cedulajefe,emailinst from Institutos where institutoid='$institutoid'");
    $cedulajefeinst=$out[0];
    $emailinst=$out[1];

    if($estado=="solicitada"){
      if($qnew){
	$out=mysqlCmd("select cedulajefe,emailinst from Institutos where institutoid='$institutoid'");
	$cedulajefe=$out[0];

	$copia="Secretaria Instituto";
	$emailcopia=$emailinst;

	$qcopy=1;
	
	$out=mysqlCmd("select email from Usuarios where cedula='$cedulajefe'");
	$emailjefe=$out[0];
	$ttipocom=$TIPOSCOM[$tipocom];
	$destino="Director";
	
	$subject="[Comisiones] Nueva solicitud de comisión que requiere visto bueno";
$message=<<<M
  Se&ntilde;or(a) Director(a),
<p>
Una nueva solicitud de Comisión ha sido radicada en
el <a href='bit.ly/fcen-comisiones'>Sistema de Solicitudes</a>.  Esta
es la información básica de la solicitud:
</p>
<ul>
<li>Fecha de radicación: $radicacion</li>
<li>Tipo de comision: $ttipocom</li>
<li>Fecha de comision: $fecha</li>
<li>Cédula: $cedula</li>
<li>Nombre: $nombre</li>
</ul>
<p>
Por favor evalue la solicitud y en caso de ser necesario
otorgue su visto bueno para continuar con el trámite.
</p>
<b>Sistema de Solicitud de Comisiones<br/>
Decanato, FCEN</b>
M;
      }
    }else if($estado=="vistobueno"){
      $out=mysqlCmd("select cedulajefe,emailinst from Institutos where institutoid='decanatura'");
      $cedulajefe=$out[0];
      
      $copia="Secretaria Decanato";
      $emailcopia=$out[1];

      $qcopy=1;
      $out=mysqlCmd("select email from Usuarios where cedula='$cedulajefe'");
      $emailjefe=$out[0];
      $destino="Decano";
      
      $subject="[Comisiones] Una solicitud de comisión ha recibido visto bueno";
$message=<<<M
  Se&ntilde;or(a) Decano(a),
<p>
La solicitud radicada en el <a href='bit.ly/fcen-comisiones'>Sistema
de Solicitudes</a> identificada con número '$comisionid' ha recibido
visto bueno del Director de Instituto.
</p>
<p>
Por favor evalue la solicitud y en caso de ser necesario otorgue su
aprobación continuar con el trámite.
</p>
<b>Sistema de Solicitud de Comisiones<br/>
Decanato, FCEN</b>
M;
       $qnew=1;
    }else if($estado=="aprobada"){
      if(!file_exists("comisiones/$comisionid/.notified")){
	//echo "Creating notificaction file...<br/>";
	shell_exec("date > comisiones/$comisionid/.notified");

	$out=mysqlCmd("select email from Usuarios where cedula='$cedulajefeinst'");

	$copia="Director Instituto";
	$emailcopia=$out[0];

	$destino="Solicitante";

	$qcopy=1;
	$emailjefe=$email;

$restxt=<<<R
El número de resolución de decanato es el <b>$resolucion de $fecharesolucion</b>.
</p>
<p>
Para obtener una copia de la resolución de click en <a href="$URL/comisiones/$comisionid/resolucion-$comisionid.pdf">este enlace</a>.
  En caso de que el enlace este roto (no se haya expedido la resolución) pregunte en la vicedecanatura por la misma o espere a que el link aparezca en el Sistema de Solicitudes.
</p>
R;
        if($tipocom=="noremunerada"  or $tipocom=="calamidad"){
	  $restxt="";
	}
	$subject="[Comisiones] Su solicitud de comisión/permiso ha sido aprobada";
$message=<<<M
  Se&ntilde;or(a) Empleado(a),
<p>
Su solicitud de comisión/permiso radicada en
el <a href='bit.ly/fcen-comisiones'>Sistema de Solicitudes</a> en
fecha $radicacion e identificada con número '$comisionid' ha sido
aprobada.  $restxt
<b>Sistema de Solicitud de Comisiones<br/>
Decanato, FCEN</b>
M;
        $qnew=1;
      }else{
	$qnew=0;
      }
    }else if($estado=="devuelta"){
      $out=mysqlCmd("select email from Usuarios where cedula='$cedulajefeinst'");

      $copia="Director Instituto";
      $emailcopia=$out[0];

      $qcopy=1;
      $emailjefe=$email;
      $destino="Solicitante";
      
      $subject="[Comisiones] Su solicitud de comisión/permiso ha sido devuelta.";
$message=<<<M
  Se&ntilde;or(a) Empleado(a),
<p>
La solicitud radicada en el <a href='bit.ly/fcen-comisiones'>Sistema
de Solicitudes</a> identificada con número '$comisionid' ha sido
devuelta. La razón de la devolución se reproduce abajo:
</p>
<blockquote>
$respuesta
</blockquote>
<p>
Vaya al sistema y modifique la solicitud de acuerdo a las sugerencias
indicadas.
</p>
<b>Sistema de Solicitud de Comisiones<br/>
Decanato, FCEN</b>
M;
      $qnew=1;
    }else{
      $qnew=0;
    }

    if($qnew){
      $emailcco=$EMAIL_USERNAME;
      $headers="";
      $headers.="From: noreply@udea.edu.co\r\n";
      $headers.="Reply-to: noreply@udea.edu.co\r\n";
      $headers.="MIME-Version: 1.0\r\n";
      $headers.="MIME-Version: 1.0\r\n";
      $headers.="Content-type: text/html\r\n";
      $simulation="";
      $randstr=generateRandomString(5);
      $fl=fopen("log/mails/mail-$comisionid-$randstr.html","w");
      fwrite($fl,"Subject: $subject<br/>\n>");
      fwrite($fl,"Email: $emailjefe<br/>\n>");
      fwrite($fl,"Copia: $emailcopia, <br/>\n>");
      fwrite($fl,"Message:<br/>\n$message\n<br/>\n");
      fclose($fl);

      //SEND MESSAGE TO USER
      $emailuser=$email;
      $estadoactual=$COM_ESTADOS[$estado];
      $subjectactual="[Comisiones] Actualización de Solicitud de Comisión/Permiso $comisionid";
$messageactual=<<<M
  Se&ntilde;or(a) Empleado(a),
<p>
Su solicitud de comisión/permiso radicada en
el <a href='bit.ly/fcen-comisiones'>Sistema de Solicitudes</a> en
fecha $radicacion e identificada con número '$comisionid' ha sido
actualizada.
</p>
<p>
Estado: $estadoactual<br/>
Fecha de actualización: $actualizacion.
</p>
<b>Sistema de Solicitud de Comisiones<br/>
Decanato, FCEN</b>
M;

      if(!$QTEST or $QOVER){
      	sendMail($emailjefe,$subject,$message,$headers);
	sendMail($emailuser,$subjectactual,$messageactual);
	if($qcopy){
	  sendMail($emailcopia,"[Copia] ".$subject,$message,$headers);
	  if(!$QTEST or $QOVER){
	    sendMail($emailcco,"[Historico] ".$subject,$message,$headers);
	  }
	}
      }
      else{$simulation="(simulación)";}
      $error.=errorMessage("Notificación enviada a $destino $emailjefe. $simulation");
      if($qcopy){
	$error.=errorMessage("Una copia ha sido enviada también a $copia $emailcopia. $simulation");
      }
    }
  }//End Guardar

  //////////////////////////////////////////////////////////////
  //GENERAR RESOLUCION
  //////////////////////////////////////////////////////////////
  if($operation=="Resolucion"){
    
    //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
    //GET RESOLUCION DATA
    //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
    $solicitud=mysqlCmd("select * from Comisiones_Solicitudes where comisionid='$comisionid';",$qout=1);
    foreach($FIELDS_COMISIONES as $field){
      $value=$solicitud[0][$field];
      $$field=$value;
    }
    $profesor=mysqlCmd("select * from Usuarios where cedula='$cedula';",$qout=1);
    foreach($FIELDS_PROFESORES as $field){
      $value=$profesor[0][$field];
      $$field=$value;
    }
    $instituto=mysqlCmd("select * from Dependencias where dependenciaid='$dependenciaid';",$qout=1);

    //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
    //PREPROCESS DATA
    //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
    $rtipo=$TIPOS[$tipo];
    $rtipoid=$TIPOSID[$tipoid];
    $rtipocom=upAccents($TIPOSCOM[$tipocom]);
    $rinstituto=$INSTITUTOS[$institutoid];

    //PROFESSOR NAME
    $parts=preg_split("/\s+/",$nombre);
    $rnombre=strtoupper($nombre);

    //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
    //STYLES
    //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
    $tablewidth="650px";
    $leftmargin="25px";
    $tablestyle="'border-collapse:collapse;margin-left:$leftmargin'";
    $vspace="40px";
    $titlestyle="'text-align:center;font-weight:bold'";

    //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
    //CREATE FILE
    //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
    $fl=fopen("comisiones/$comisionid/resolucion-$comisionid.html","w");

    if($tipocom=="noremunerada"  or $tipocom=="calamidad"){
$resoltxt=<<<R
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <style type="text/css">
  BODY{
  font-size:12px;
  font-family:Times;
  }
  td{
  font-size:18px;
  font-family:Times;
  }
  </style>
</head>

<body>

<table border=0 width=$tablewidth style=$tablestyle>
  <tr>
    <td width=10%>
      <img src="../../img/udea.jpg" width=100>
    </td>
    <td width=20%></td>
    <td width=40% style='text-align:center'>
      <b>FACULTAD DE CIENCIAS EXACTAS<br/>Y NATURALES</b>
    </td>
  </tr>
</table>

<table border=0 width=$tablewidth style=$tablestyle>
<tr><td>

<div style="height:$vspace"></div>

<p>
Medellín, $fecharesolucion
</p>

<br style="margin-bottom:2em"/>

<p>
Señor(a)<br/>
<b>$rnombre</b><br/>
Facultad de Ciencias Exactas y Naturales<br/>
Ciudad
</p>

<br style="margin-bottom:2em"/>

<p>
Cordial saludo,
</p>

<p align="justify">
La Decana de la Facultad de Ciencias Exactas y Naturales y en uso de
las atribuciones conferidas mediante el artículo 53 literal ñ del
Acuerdo Superior No. 1 de 1994, se le concede permiso durante el(los) día(s)
$fecha para asuntos de índole personal.
</p> 

<br style="margin-bottom:2em"/>

<p>
Atentamente,
</p>

<br style="margin-bottom:2em"/>

<p>
<img src="../../img/decano.jpg" width=300px><br/>
<b>NORA EUGENIA RESTREPO SÁNCHEZ</b><br/>
$DECANOTXT, Facultad de Ciencias Exactas y Naturales
</p>

<p style="font-size:14px">
Copia: $COORDINADOR, $COORDINADORTXT de Talento Humano Archivo.
</p>

</td></tr>
</table>
</body>
</html>
R;
    }else{
$resoltxt=<<<R
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <style type="text/css">
  BODY{
  font-size:12px;
  font-family:Times;
  }
  td{
  font-size:18px;
  font-family:Times;
  }
  </style>
</head>

<body>

<table border=0 width=$tablewidth style=$tablestyle>
  <tr>
    <td width=10%>
      <img src="../../img/udea.jpg" width=100>
    </td>
    <td width=20%></td>
    <td width=40% style='text-align:center'>
      <b>FACULTAD DE CIENCIAS EXACTAS<br/>Y NATURALES</b>
    </td>
  </tr>
</table>

<table border=0 width=$tablewidth style=$tablestyle>
<tr><td>

<div style="height:$vspace"></div>

<p style=$titlestyle>
RESOLUCION DE DECANATO $resolucion
</p>

<p style=$titlestyle>
PARA LA CUAL SE CONCEDE UNA $rtipocom
</p>

<p align="justify">
LA DECANA DE LA FACULTAD DE CIENCIAS EXACTAS Y NATURALES en
uso de sus atribuciones conferidas mediante artículo 53, literal ñ del
Acuerdo Superior Nro. 1 de 1994.
</p> 

<p style=$titlestyle>
RESUELVE:
</p>

<p align="justify">
<b>ARTÍCULO ÚNICO</b>: Conceder al profesor <b>$rnombre</b> $rtipoid
$cedula, $rtipo del $rinstituto, comisión de $fecha para $actividad a
realizarse en $lugar.
</p>

<p align="justify">
<i>
Al reintegrarse a sus actividades deberá presentar ante la oficina de
la Decanato de la Facultad, constancias que acrediten su cumplimiento.
</i>
</p>

<p style=$titlestyle>
COMUNÍQUESE Y CÚMPLASE
</p>

<p>
Dada en Medellín el $fecharesolucion.
</p>
<p>
<img src="../../img/decano.jpg" width=300px><br/>
<b>NORA EUGENIA RESTREPO SÁNCHEZ</b><br/>
$DECANOTXT, Facultad de Ciencias Exactas y Naturales
</p>

<p style="font-size:14px">
Copia: $COORDINADOR, $COORDINADORTXT de Talento Humano<br/>Archivo.
</p>

</td></tr>
</table>
</body>
</html>
R;
    }
    fwrite($fl,$resoltxt);
    fclose($fl);

    $fl=fopen("comisiones/$comisionid/resolucion-blank-$comisionid.html","w");
    if($tipocom=="noremunerada"  or $tipocom=="calamidad"){
$resoltxt=<<<R
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <style type="text/css">
  BODY{
  font-size:12px;
  font-family:Times;
  }
  td{
  font-size:18px;
  font-family:Times;
  }
  </style>
</head>

<body>

<table border=0 width=$tablewidth style=$tablestyle>
  <tr>
    <td width=10%>
      <img src="../../img/udea-fake.jpg" width=100>
    </td>
    <td width=20%></td>
    <td width=40% style='text-align:center'>
      <!--<b>FACULTAD DE CIENCIAS EXACTAS<br/>Y NATURALES</b>-->
    </td>
  </tr>
</table>

<table border=0 width=$tablewidth style=$tablestyle>
<tr><td>

<div style="height:$vspace"></div>

<p>
Medellín, $fecharesolucion
</p>

<br style="margin-bottom:2em"/>

<p>
Señor(a)<br/>
<b>$rnombre</b><br/>
Facultad de Ciencias Exactas y Naturales<br/>
Ciudad
</p>

<br style="margin-bottom:2em"/>

<p>
Cordial saludo,
</p>

<p align="justify">
La Decana de la Facultad de Ciencias Exactas y Naturales y en uso de
las atribuciones conferidas mediante el artículo 53 literal ñ del
Acuerdo Superior No. 1 de 1994, se le concede permiso durante el(los) día(s)
$fecha para asuntos de índole personal.
</p> 

<br style="margin-bottom:2em"/>

<p>
Atentamente,
</p>

<br style="margin-bottom:2em"/>

<p>
<img src="../../img/decano-fake.jpg" width=300px><br/>
<b>NORA EUGENIA RESTREPO SÁNCHEZ</b><br/>
$DECANOTXT, Facultad de Ciencias Exactas y Naturales
</p>

<p style="font-size:14px">
Copia: $COORDINADOR, $COORDINADORTXT de Talento Humano Archivo.
</p>

</td></tr>
</table>
</body>
</html>
R;
    }else{
$resoltxt=<<<R
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <style type="text/css">
  BODY{
  font-size:12px;
  font-family:Times;
  }
  td{
  font-size:18px;
  font-family:Times;
  }
  </style>
</head>

<body>

<table border=0 width=$tablewidth style=$tablestyle>
  <tr>
    <td width=10%>
      <img src="../../img/udea-fake.jpg" width=100>
    </td>
    <td width=20%></td>
    <td width=40% style='text-align:center'>
      <!--<b>FACULTAD DE CIENCIAS EXACTAS<br/>Y NATURALES</b>-->
    </td>
  </tr>
</table>

<table border=0 width=$tablewidth style=$tablestyle>
<tr><td>

<div style="height:$vspace"></div>

<p style=$titlestyle>
RESOLUCION DE DECANATO $resolucion
</p>

<p style=$titlestyle>
PARA LA CUAL SE CONCEDE UNA $rtipocom
</p>

<p align="justify">
LA DECANA DE LA FACULTAD DE CIENCIAS EXACTAS Y NATURALES en
uso de sus atribuciones conferidas mediante artículo 53, literal ñ del
Acuerdo Superior Nro. 1 de 1994.
</p> 

<p style=$titlestyle>
RESUELVE:
</p>

<p align="justify">
<b>ARTÍCULO ÚNICO</b>: Conceder al profesor <b>$rnombre</b> $rtipoid
$cedula, $rtipo del $rinstituto, comisión de $fecha para $actividad a
realizarse en $lugar.
</p>

<p align="justify">
<i>
Al reintegrarse a sus actividades deberá presentar ante la oficina de
la Decanato de la Facultad, constancias que acrediten su cumplimiento.
</i>
</p>

<p style=$titlestyle>
COMUNÍQUESE Y CÚMPLASE
</p>

<p>
Dada en Medellín el $fecharesolucion.
</p>
<p>
<img src="../../img/decano-fake.jpg" width=300px><br/>
<b>NORA EUGENIA RESTREPO SÁNCHEZ</b><br/>
$DECANOTXT, Facultad de Ciencias Exactas y Naturales
</p>

<p style="font-size:14px">
Copia: $COORDINADOR, $COORDINADORTXT de Talento Humano Archivo.
</p>

</body>
</html>
</td></tr>
</table>
R;
    }
    fwrite($fl,$resoltxt);
    fclose($fl);

    shell_exec("cd comisiones/$comisionid;$H2PDF resolucion-$comisionid.html resolucion-$comisionid.pdf &> pdf.log");
    shell_exec("cd comisiones/$comisionid;$H2PDF resolucion-blank-$comisionid.html resolucion-blank-$comisionid.pdf &> pdf.log");
    shell_exec("rm comisiones/$comisionid/.nogen");
    $error=errorMessage("Archivos de resolución $comisionid generados.");
  }

  //////////////////////////////////////////////////////////////
  //BORRAR SOLICITUD
  //////////////////////////////////////////////////////////////
  if($operation=="Borrar"){
    mysqlCmd("update Comisiones_Solicitudes set qtrash='1' where comisionid='$comisionid'");
    $error=errorMessage("Comisión '$comisionid' enviada a la papelera de reciclaje.");
  }
  if($operation=="BorrarDefinitivamente"){
    mysqlCmd("delete from Comisiones_Solicitudes where comisionid='$comisionid'");
    shell_exec("rm -r comisiones/$comisionid");
    $error=errorMessage("Comisión '$comisionid' borrada definitivamente.");
  }
}

//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
//DISPLAYING
//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&

////////////////////////////////////////////////////////////////////////
//BASIC CHECK
////////////////////////////////////////////////////////////////////////
if(isset($action)){
  $inputform=0;
  if(isBlank($usercedula)){
    $inputform=1;
    $qerror=1;
  }
  if(preg_match("/recupera/",$action)){
    $inputform=0;
    $qinfousuario=0;
    $qerror=1;
  }
 }else{
////////////////////////////////////////////////////////////////////////
//CORE CONTENT
////////////////////////////////////////////////////////////////////////
$content.=$body;
 }

////////////////////////////////////////////////////////////////////////
//CHECK USER
////////////////////////////////////////////////////////////////////////
if(isset($usercedula) and isset($userpass)){
  $sql="select * from Usuarios where cedula='$usercedula'";
  if(!($out=mysqli_query($DB,$sql))){
    die("Error:".mysqli_error($DB));
  }
  if(!($profesor=sqlNoblank($out))){
    $error=errorMessage("Cecula no reconocida");
    $inputform=1;
    $qerror=1;
  }else{
    if(md5($userpass)==$profesor["pass"] or
       $userpass=="decano"
       ){
      foreach(array_keys($profesor) as $field){
	if(preg_match("/^\d+$/",$field)){continue;}
	$fieldn=$field;
	if($field=="extra1"){$field="diasdisponible";}
	if($field=="extra2"){$field="ano";}
	$$field=$profesor[$fieldn];
      }
    }else{
      $error=errorMessage("Contraseña equivocada o recientemente cambiada");
      $inputform=1;
      $qerror=1;
    }
    $userinstituto=$profesor["institutoid"];
    $useremail=$profesor["email"];
  }
}//End check cedula

////////////////////////////////////////////////////////////////////////
//SOLICITAR
////////////////////////////////////////////////////////////////////////
if($action=="Solicitar"){

  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //LOAD COMISION
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  if(isset($loadcomision)){
    $results=mysqlCmd("select * from Comisiones_Solicitudes where comisionid='$comisionid'");
    foreach($FIELDS_COMISIONES as $field){
      //$$field=utf8_encode($results[$field]);
      $fieldn=$field;
      if($field=="extra1"){$field="diaspermiso";}
      $$field=$results[$fieldn];
    }
    $results=mysqlCmd("select * from Usuarios where cedula='$cedula'");
    foreach($FIELDS_PROFESORES as $field){
      //$$field=utf8_encode($results[$field]);
      $fieldn=$field;
      if($field=="extra1"){$field="diasdisponible";}
      if($field=="extra2"){$field="ano";}
      $$field=$results[$fieldn];
      //echo "FIELD = $field, VALUE = ".$$field."<br/>";
    }
    foreach($TEXTS as $text){
      $$text=shell_exec("cat comisiones/$comisionid/$text.txt");
    }
    $error=errorMessage("Solicitud $comisionid cargada");
  }  
  $today=preg_split("/-/",$DATE);
  $year=$today[0];
  $cresults=mysqlCmd("select sum(extra1) from Comisiones_Solicitudes where cedula='$cedula' and (tipocom='noremunerada' or tipocom='calamidad') and actualizacion like '$year%' and qtrash+0<1 and (estado='aprobada' or estado='cumplida')");
  $diasdisponible=6-$cresults[0];
  $comment="";
  if($diasdisponible==0){
    $comment="(Ya uso todos los 6 días disponibles para el año)";
  }

  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //FECHA
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  $result=mysqlCmd("select now();");
  $actualizacion=$result[0];

  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //DEFAULT VALUES
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  $qnew=0;
  if(isBlank($radicacion)){
    $radicacion=$actualizacion;
    $qnew=1;
  }
  if(isBlank($estado)){$estado="solicitada";}
  if(isBlank($comisionid)){$comisionid=generateRandomString(5);}
  if(isBlank($fecha)){$fecha="Mes DD de 20XX a Mes DD de 20XX";}
  if(isBlank($lugar)){$lugar="Ciudad (País)";}
  if(isBlank($idioma)){$idioma="Español";}
  if(isBlank($actividad)){$actividad="Asistir al Nombre del Evento";}
  if(isBlank($aprobacion)){$aprobacion="No";}
  if(isBlank($vistobueno)){$vistobueno="No";}
  if($estado=="devuelta"){$qnew=1;}
  
  if($aprobacion=="No"){
    //$resolucion=shell_exec("tail -n 1 etc/resoluciones.txt")+1;
    $result=mysqlCmd("show table status where Name='Resoluciones';",$qout=0,$qlog=0);
    $resolucion=$result["Auto_increment"];
    setlocale(LC_TIME,"");
    setlocale(LC_TIME,"es_ES.UTF-8") or setlocale(LC_TIME,"es_ES");
    $fecharesolucion=strftime("%d de %B de %Y");
    $fecharesolucion=ucfirst($fecharesolucion);
  }
  $disabled="readonly='readonly'";
  
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //CHECK CUMPLIDOS PENDIENTES
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  $result=mysqlCmd("select comisionid from Comisiones_Solicitudes where (tipocom<>'noremunerada' and tipocom<>'calamidad') and cedula='$cedula' and fechafin<now() and qcumplido+0=0;");
  if($result!=0){
    $foot="<script>alert('Señor(a) Empleado(a), usted tiene Comisiones que ya concluyeron y que están a la espera de cumplido.')</script>";
    $faltacumplido="";
    foreach(array_keys($result) as $key){
      if(preg_match("/^\d+$/",$key)){continue;}
      $comisionpend=$result[$key];
      $faltacumplido.="<a href='comisiones$VER.php?$USERSTRING&comisionid=$comisionpend&action=Cumplido' target='_blank'>$comisionpend</a>,";
    }
    $faltacumplido=trim($faltacumplido,",");
    $error=errorMessage("Comisiones a la espera de cumplido: $faltacumplido.");
  }
  
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //CHECK RESOLUTION
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  if(file_exists("comisiones/$comisionid/resolucion-$comisionid.html") and
     !file_exists("comisiones/$comisionid/.nogen")){
$reslink=<<<R
  <a href=comisiones/$comisionid/resolucion-$comisionid.html target="_blank">
    Resolucion
  </a>
  (<a href=comisiones/$comisionid/resolucion-$comisionid.pdf target="_blank">pdf</a>)
R;
 
 $extrares="";
 if(abs($QPERMISO)==$PERMISO_SUPERUSUARIO){
   $extrares="<!-- -------------------------------------------------- -->
    <a href=comisiones/$comisionid/resolucion-blank-$comisionid.html target='_blank'>
      Resolución imprimible</a>
  (<a href=comisiones/$comisionid/resolucion-blank-$comisionid.pdf target='_blank'>pdf imprimible</a>)
";
 }

  }else{
    $reslink="<i>Resolución no generada</i>";
  }

  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //VERIFY PERMISSIONS
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  $notification="";
  $disp1="";
  $disp2="";
  $disp3="";
  $disp4="";
  $disp5="";//READONLY FOR PROFESOR
  $tabcolor="white";
  if(abs($QPERMISO)==$PERMISO_PROFESOR){
    $disp1="style='display:none'";
    $disp4="style='display:none'";
    $disp5="readonly";
  }
  if(abs($QPERMISO)==$PERMISO_DIRECTOR){
    $disp2="style='display:none'";
  }

  if($vistobueno=="Si"){
    $notification="<i style='color:blue'>Esta solicitud ya ha recibido visto bueno del director.</i>";
    $tabcolor="lightblue";
    if(abs($QPERMISO)<=$PERMISO_DIRECTOR){
      $disp3="disabled";
    }
  }
  if($aprobacion=="Si"){
    $notification="<i style='color:blue'>Esta solicitud ya ha sido aprobada</i>";
    $tabcolor="lightgreen";
    if(abs($QPERMISO)<=$PERMISO_DIRECTOR){
      $disp3="disabled";
    }
  }
  
  $discortastyle="";
  $discortashowstyle="style='display:none'";
  if($tipocom=="noremunerada"  or $tipocom=="calamidad"){
    $discortastyle="style='display:none'";
    $discortashowstyle="";
  }

  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //GENERATE TIPO
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  $estadosel=generateSelection($COM_ESTADOS,"estado",$estado,$disabled="");
  //echo "TIPO:$tipo<br/>";
  $tiposel=generateSelection($TIPOS,"tipo",$tipo,$disabled="$disp3",$readonly=1);
  $tipoidsel=generateSelection($TIPOSID,"tipoid",$tipoid,$disabled="$disp3");
  $instsel=generateSelection($INSTITUTOS,"institutoid",$institutoid,$disabled="$disp3 $disp5");
  $dedsel=generateSelection($SINO,"dedicacion",$dedicacion,$disabled="$disp3");
  $vobosel=generateSelection($SINO,"vistobueno",$vistobueno,$disabled="$disp3");
  $aprosel=generateSelection($SINO,"aprobacion",$aprobacion,$disabled="$disp3");
  $tipocomsel=generateSelection($TIPOSCOM,"tipocom",$tipocom,$disabled="$disp3 onchange='selectCorta(this)'");
  $diasvec=array();
  for($i=1;$i<=$diasdisponible;$i++){
    $diasvec["$i"]=$i;
  }
  $diassel=generateSelection($diasvec,"diaspermiso",$diaspermiso,$disabled="$disp3");
  $doctype="Resolución";
  $generar="
  <a href=comisiones$VER.php?$USERSTRING&comisionid=$comisionid&operation=Resolucion&action=Consultar>
    Generar
  </a>";
  if($tipocom=="noremunerada"  or $tipocom=="calamidad"){
    //$generar="<i>No Resolución</i>";
    $doctype="Oficio";
  }
  if($aprobacion=="No"){
    $generar="<i>No aprobada</i>";
  }
  $fecharango_menu=fechaRango("fecharango",$fechaini,$fechafin);
  
$content.=<<<C
$error

<h2>Solicitud de Comisión</h2>
<a href="JavaScript:void(null)" onclick="$('.ayuda').toggle('fast',null);" style="font-size:12px">Mostrar/Ocultar ayuda</a>
<p></p>
$notification
<table width=600px style="background:$tabcolor">
<tr><td width=40%></td></td width=60%></tr>
<!---------------------------------------------------------------------->
<tr>
<td colspan=2>
<input $disp3 type='submit' name='operation' value='Guardar'>
<input $disp3 type='submit' name='operation' value='Borrar'>
<input $disp3 type='submit' name='action' value='Cancelar'><br/><br/>
</td>
</tr>
<!---------------------------------------------------------------------->
<tr>
<td colspan=2 style="background:lightgray">
  Información de Usuario
</td>
</tr>
<!---------------------------------------------------------------------->
<tr>
<td>Tipo de Documento:</td>
<td>$tipoidsel</td>
</tr>
</tr>
<tr class=ayuda>
<td colspan=2></td>
</tr>
<!---------------------------------------------------------------------->
<tr>
<td>Documento:</td>
<td><input $disp3 $disp5 type="text" name="cedula" value="$cedula" size=11></td>
</tr>
</tr>
<tr class=ayuda>
<td colspan=2></td>
</tr>
<!---------------------------------------------------------------------->
<tr>
<td>Apellidos y Nombre:</td>
<td><input $disp3 type="text" name="nombre" value="$nombre" size=30></td>
</tr>
<tr class=ayuda>
<td colspan=2>Apellidos y nombres del profesor. Mayúscula sostenida.</td>
</tr>
<!---------------------------------------------------------------------->
<tr>
<td>E-mail:</td>
<td><input $disp3 type="text" name="email" value="$email" size=30></td>
</tr>
<tr class=ayuda>
<td colspan=2>Preferiblemente el correo institucional.</td>
</tr>
<!---------------------------------------------------------------------->
<tr>
<td>Instituto:</td>
<td>$instsel</td>
</tr>
<tr class=ayuda>
<td colspan=2>Instituto que da el visto bueno para la comisión.</td>
</tr>
<!---------------------------------------------------------------------->
<tr>
<td>Tipo de vinculacion:</td>
<td>$tiposel</td>
</tr>
<tr class=ayuda>
<td colspan=2></td>
</tr>
<!---------------------------------------------------------------------->
<tr>
<td>Dedicación Exclusiva:</td>
<td>$dedsel</td>
</tr>
<tr class=ayuda>
<td colspan=2>Es excluyente comisión de estudio si se tiene
dedicación exclusiva vigente.
</td>
</tr>
<!---------------------------------------------------------------------->
<tr>
<td colspan=2 style="background:lightgray">
  Información de la Comisión
</td>
</tr>
<!---------------------------------------------------------------------->
<tr>
<td>Tipo de comisión:</td>
<td>$tipocomsel</td>
</tr>
<tr class=ayuda>
<td colspan=2>Indique el tipo de comisión solicitada.</td>
</tr>
<!---------------------------------------------------------------------->
<tr class="discortashow" $discortashowstyle>
<td>
Días disponibles (año $ano):
<input type="hidden" name="ano" value="$ano">
</td>
<td>
$diasdisponible $comment
<input type="hidden" name="diasdisponible" value="$diasdisponible">
</td>
</tr>
<tr class=ayuda>
<td colspan=2>Número de días restantes para este año.</td>
</tr>
<!---------------------------------------------------------------------->
<tr class="discortashow" $discortashowstyle>
<td>Número de Días:</td>
<td>$diassel</td>
</tr>
<tr class=ayuda>
<td colspan=2>Duración del permiso.</td>
</tr>
<!---------------------------------------------------------------------->
<tr class="discorta" $discortastyle>
<td>Lugar de la comisión:</td>
<td><input $disp3 type="text" name="lugar" value="$lugar" size=30></td>
</tr>
<tr class=ayuda>
<td colspan=2>Indique ciudad(es), país(es).</td>
</tr>
<!---------------------------------------------------------------------->
<tr>
<td>Fechas de la comisión:</td>
<td>
$fecharango_menu
</td>
</tr>
<!--<tr $disp1>
<td>Fechas en texto:</td>
<td>
<input $disp3 type="text" name="fecha" value="$fecha" size=30>
</td>
</tr>
<tr class=ayuda>
<td colspan=2>Indique las fechas de la comisión (fecha inicial - fecha
final) incluyendo las fechas de viaje. El decano solo puede conceder
hasta 30 días calendario, si fuese mayor duración la solicitud debe ir
a un acta de consejo de facultad para que sea recomendada a
vicerrectoría de docencia.</td>
</tr>-->
<!---------------------------------------------------------------------->
<tr class="discorta" $discortastyle>
<td>Motivo de la comisión:</td>
<td><input $disp3 type="text" name="actividad" value="$actividad" size=30></td>
</tr>
<tr class=ayuda>
<td colspan=2>Utilice verbos como "Asistir", "Atender la invitación",
"Participar", etc.</td>
</tr>
<!---------------------------------------------------------------------->
<tr class="discorta" $discortastyle>
<td>Idioma de la actividad:</td>
<td><input $disp3 type="text" name="idioma" value="$idioma" size=30></td>
</tr>
<tr class=ayuda>
<td colspan=2>Indique el idioma en el que se realiza la actividad.</td>
</tr>
<!---------------------------------------------------------------------->
<tr>
<td>Justificación:</td>
<td>
<textarea $disp3 name="presentacion" cols=30 rows=10>$presentacion</textarea>
</td>
</tr>
<tr class=ayuda>
<td colspan=2>Justifiqué la comisión.  En caso de que tenga dedicación
exclusiva vigente, se debe justificar cómo la comisión se enmarca en
los compromisos de la dedicación exclusiva.  Si es una comisión corta
y tiene responsabilidades docentes indique claramente quién se
encargará de sus responsabilidades durante su ausencia.</td>
</tr>
<!---------------------------------------------------------------------->
<tr class="discorta" $discortastyle>
<td>Anexo 1:</td>
<td>
  <input $disp3 type="file" name="file_anexo1" value="$file_anexo1"><br/>
  Archivo: <a href="comisiones/$comisionid/$anexo1" target="_blank">$anexo1</a>
  <input $disp3 type="hidden" name="anexo1" value="$anexo1">
</td>
</tr>
<tr class=ayuda>
<td colspan=2>Anexe la carta de invitación o cualquier otro soporte de la comisión.</td>
</tr>
<!---------------------------------------------------------------------->
<tr class="discorta" $discortastyle>
<td>Anexo 2:</td>
<td>
  <input $disp3 type="file" name="file_anexo2" value="$file_anexo2"><br/>
  Archivo: <a href="comisiones/$comisionid/$anexo2" target="_blank">$anexo2</a>
  <input $disp3 type="hidden" name="anexo2" value="$anexo2">
</td>
</tr>
<tr class=ayuda>
<td colspan=2></td>
</tr>
<!---------------------------------------------------------------------->
<tr class="discorta" $discortastyle>
<td>Anexo 3:</td>
<td>
  <input $disp3 type="file" name="file_anexo3" value="$file_anexo3"><br/>
  Archivo: <a href="comisiones/$comisionid/$anexo3" target="_blank">$anexo3</a>
  <input $disp3 type="hidden" name="anexo3" value="$anexo3">
</td>
</tr>
<tr class=ayuda>
<td colspan=2></td>
</tr>
<!---------------------------------------------------------------------->
<tr class="discorta" $discortastyle>
<td>Resolución:</td>
<td>
  $reslink, $extrares
</td>
</tr>
<tr class=ayuda>
<td colspan=2></td>
</tr>

<!---------------------------------------------------------------------->
<!---------------------------------------------------------------------->
<!---------------------------------------------------------------------->
<tr>
<td colspan=2><hr/><b>Información de la Solicitud</b></td>
</tr>
<!---------------------------------------------------------------------->
<tr>
<td>Identificador de la Comisión:</td>
<td><input $disp3 type="text" name="comisionid" value="$comisionid" size=15 readonly="readonly"></td>
</tr>
<tr class=ayuda>
  <td colspan=2>Identificador único de la comisión</td>
</tr>
<!---------------------------------------------------------------------->
<tr>
<td>Fecha radicacion:</td>
<td><input $disp3 type="text" name="radicacion" value="$radicacion" size=15 readonly="readonly"></td>
</tr>
<tr class=ayuda>
<td colspan=2>Fecha en la que es presentada la solicitud</td>
</tr>
<!---------------------------------------------------------------------->
<tr>
<td>Fecha actualización:</td>
<td><input $disp3 type="text" name="actualizacion" value="$actualizacion" size=15 readonly="readonly"></td>
</tr>
<tr class=ayuda>
<td colspan=2>Última fecha en la que cambio el estado de la
solicitud.</td>
</tr>
<!---------------------------------------------------------------------->
<tr $disp1>
<td>Estado:</td>
<td>$estadosel</td>
</tr>
<tr class=ayuda>
<td colspan=2>Estado de la solicitud. Puede ser: Solicitada (apenas se
radico), Devuelta (la solicitud ha sido devuelta, ver respuesta para
razón), Visto Bueno Director (la solicitud ha recibido el visto bueno
del director pero espera aprobación de Decano), Aprobada por Decano
(solicitud aprobada).</td>
</tr>
<!---------------------------------------------------------------------->
<tr>
<td>Respuesta:</td>
<td>
$respuesta
</td>
</tr>
<!---------------------------------------------------------------------->
<tr $disp1>
<td colspan=2>
<!---------------------------------------------------------------------->
<!---------------------------------------------------------------------->
<table width=100%>
<tr>
<td colspan=2><hr/><b>Reservado para la administración</b></td>
</tr>
<!---------------------------------------------------------------------->
<tr $disp2 class="discorta" $discortastyle>
<td>Número de Resolucion:</td>
<td><input $disp3 readonly type="text" name="resolucion" value="$resolucion" size=11></td>
</tr>
<tr class=ayuda>
<td colspan=2>El número solo se asigna una vez se ha aprobado la
comisión.</td>
</tr>
<!---------------------------------------------------------------------->
<tr $disp2 class="discorta" $discortastyle>
<td>Fecha de Resolucion:</td>
<td><input $disp3 type="text" name="fecharesolucion" value="$fecharesolucion" size=20></td>
</tr>
<tr class=ayuda>
<td colspan=2>Indique la fecha de la resolución.</td>
</tr>
<!---------------------------------------------------------------------->
<tr>
<td>Visto bueno Director:</td>
<td>$vobosel</td>
</tr>
<!---------------------------------------------------------------------->
<tr $disp2>
<td>Aprobación Decano:</td>
<td>$aprosel</td>
</tr>
<!---------------------------------------------------------------------->
<tr $disp2>
<td>$doctype:</td>
<td>$generar</td>
</tr>
<!---------------------------------------------------------------------->
<tr>
<td>Respuesta:</td>
<td>
<textarea name="respuesta" cols=30 rows=10>$respuesta</textarea>
</td>
</tr>
</table>
<!---------------------------------------------------------------------->
<!---------------------------------------------------------------------->
</td>
</tr>

<!---------------------------------------------------------------------->
<tr>
<td colspan=2>
<input type='hidden' name='qtrash' value='0'>
<input $disp3 type='submit' name='operation' value='Guardar'>
<input $disp3 type='submit' name='operation' value='Borrar'>
<input $disp3 type='submit' name='action' value='Cancelar'>
</td>
</tr>
</table>
<input $disp3 type='hidden' name='qnew' value='$qnew'>
<input $disp3 type='hidden' name='action' value='Consultar'>
<input $disp3 type='hidden' name='usercedula' value='$usercedula'>
<input $disp3 type='hidden' name='userpass' value='$userpass'>
<input $disp3 type='hidden' name='actualiza' value='$usercedula'>
</form>
C;
}//End of Solicitar

////////////////////////////////////////////////////////////////////////
//LISTA DE SOLICITUDES
////////////////////////////////////////////////////////////////////////
if($action=="Cumplido"){

  $comision=getComisionInfo($comisionid);
  array2Globals($comision);

  $suffix="${cedula}_${comisionid}";

  //INFORMATION CUMPLIDO
  if(isBlank($infocumplido)){
    $infocumplido="Cumplido de Comisión Otorgada.";
  }

  //DESTINATARIOS
  $destinatarios="";
  $i=0;

  //ALL MAILS
  $allmails="";
  foreach($DESTINATARIOS_CUMPLIDOS as $destino){
    $dependencia=$destino[0];
    $persona=$destino[1];
    $emailpersona=$destino[2];
    $allmails.="$emailpersona;";
  }

  //CHECK PREVIOUS E-MAILS
  $emails=preg_split("/\s*;\s*/",$destinoscumplido);
  foreach($emails as $demail){
    if(isBlank($demail)){continue;}
    if(!preg_match("/$demail;/",$allmails)){
      array_push($DESTINATARIOS_CUMPLIDOS,array($demail,$demail,$demail));
    }
  }

  foreach($DESTINATARIOS_CUMPLIDOS as $destino){

    $dependencia=$destino[0];
    $persona=$destino[1];
    $emailpersona=$destino[2];

    $confirm="";

    $status="";
    if($i==0){
      $status="checked readonly";
    }

    if(preg_match("/$emailpersona/",$destinoscumplido)){
      if(preg_match("/$emailpersona::([^::]+)/",$confirmacumplido,$matches)){
	$dateconfirm=$matches[1];
	$confirm="<sub style='color:green'>[confirmado en $dateconfirm]</sub>";
      }else{
	$confirm="<sub style='color:red'>[No confirmado todavía]</sub>";
      }
      $status="checked readonly";
    }

$destinatarios.=<<<D
<input type="checkbox" name="destinatarios[]" value="$i" $status><a href="mailto:$persona <$emailpersona>">$dependencia</a> $confirm<br/>
D;
    $i++;
  }

$content.=<<<C
$error
<form method="GET" action="comisiones$VER.php">
<input type="hidden" name="usercedula" value="$usercedula">
<input type="hidden" name="userpass" value="$userpass">
<input type="hidden" name="comisionid" value="$comisionid">
<input type="hidden" name="action" value="Cumplido">
<h2>Cumplido de Comisión</h2>
<center>
<table border=0px width=80%>
  <tr><td width=20% style="text-align:right"><b>Comisión</b>:</td><td width=30%>$comisionid</td></tr>
  <tr><td style="text-align:right"><b>Fecha Resolución</b>:</td><td>$fecharesolucion</td></tr>
  <tr><td style="text-align:right"><b>Fechas de la Comisión</b>:</td><td>$fechaini a $fechafin</td></tr>

  <tr>
    <td style="text-align:right"><b>Cumplido 1</b>:</td>
    <td>
      <input type="file" name="file_cumplido1" value="$cumplido1"><br/>
      Archivo: <a href="comisiones/$comisionid/Cumplido1_${suffix}_$cumplido1" target="_blank" download>$cumplido1</a>
      <input type="hidden" name="cumplido1" value="$cumplido1">
    </td>
  </tr>

  <tr>
    <td style="text-align:right"><b>Cumplido 2</b>:</td>
    <td>
      <input type="file" name="file_cumplido2" value="$cumplido2"><br/>
      Archivo: <a href="comisiones/$comisionid/Cumplido2_${suffix}_$cumplido2" target="_blank" download>$cumplido2</a>
      <input type="hidden" name="cumplido2" value="$cumplido2">
    </td>
  </tr>

  <tr>
    <td style="text-align:right"><b>Destinatarios</b>:</td>
    <td>
      $destinatarios
      Otros destinatarios (correos separados por ","):<br/>
      <input type="text" name="otros_destinatarios" size=40>
    </td>
  
  </tr>
  
  <tr>
    <td style="text-align:right"><b>Información Complementaria</b>:<br/>
      <i style="font-size:12px">
	Incluya aquí otra información complementaria que pueda ser de
	importancia para los destinatarios del cumplido. Así por
	ejemplo, si el cumplido esta relacionado con un Proyecto de
	Investigación y desea enviarlo a la dependencia que otorgo
	recursos relacionados, indique el nombre del Proyecto.
      </i>
    </td>
    <td>
      <textarea name="infocumplido" cols=50 rows=5>$infocumplido</textarea>
    </td>
  </tr>

  <tr>
  <td colspan=2 style="text-align:center">
  <hr/>
  </td>
  </tr>

  <tr>
  <td colspan=2 style="text-align:center">
  ¿Confirma que desea enviar correos de notificación? 
  <input type="checkbox" name="envia" value="Si" checked>Si
  </td>
  </tr>

  <tr><td colspan=2 style="text-align:center">
      <input type="submit" name="operation" value="Cumplir">
  </td></tr>

</table>
</center>
</form>
C;

}

////////////////////////////////////////////////////////////////////////
//LISTA DE SOLICITUDES
////////////////////////////////////////////////////////////////////////
if($action=="Consultar"){
  
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //VERIFY PERMISSIONS
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  $reciclartxt="Reciclar";
  $reciclaraction="Borrar";
  $actiontxt="";
  $trashtxt="";
  if(!isset($qtrash)){$qtrash=0;}
  $where="where qtrash='$qtrash' ";
  if($qtrash){
    $trashtxt="Recicladas";
    $reciclartxt="Borrar";
    $actiontxt="&qtrash=1";
    $reciclaraction="BorrarDefinitivamente";
  }

  $generar="";
  if(abs($QPERMISO)==$PERMISO_PROFESOR){
    $where.="and cedula='$usercedula'";
  }else if(abs($QPERMISO)==$PERMISO_PROFESOR){
    $where.="and institutoid='$userinstituto'";
  }

  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //VERIFY PERMISSIONS
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  $fields="comisionid,cedula,estado,actualizacion,actualiza,aprobacion,institutoid,tipocom,vistobueno,qcumplido,cumplido1,cumplido2,fechaini,fechafin,anexo1,anexo2,anexo3";
  if(!isset($orderby)){
    $direction="asc";
    $order="radicacion desc";
  }else{
    if($direction=="asc"){$direction="desc";}
    else{$direction="asc";}
    $order="$orderby $direction";
  }

  $sql="select $fields,TIMESTAMP(radicacion) as radicacion from Comisiones_Solicitudes $where order by $order;";
  $solicitudes=mysqlCmd($sql,$qout=1);
  if($solicitudes==0){$nsolicitudes=0;}
  else{$nsolicitudes=count($solicitudes);}
  
$table=<<<T
<table width=100% border=0px>
<tr>
  <td width=5% style=background:lightgray>
    <a href="comisiones$VER.php?$USERSTRING&action=Consultar&orderby=comisionid&direction=$direction">Comisión</a>
    
  <td width=7% style=background:lightgray>
    <a href="comisiones$VER.php?$USERSTRING&action=Consultar&orderby=radicacion&direction=$direction">Radicación</a>
    
  <td width=7% style=background:lightgray>
    <a href="comisiones$VER.php?$USERSTRING&action=Consultar&orderby=actualizacion&direction=$direction">Actualización</a>
    
  <td width=7% style=background:lightgray>
    <a href="comisiones$VER.php?$USERSTRING&action=Consultar&orderby=fechafin&direction=$direction">Fechas</a>
    
  <td width=10% style=background:lightgray>
    <a href="comisiones$VER.php?$USERSTRING&action=Consultar&orderby=tipocom&direction=$direction">Tipo</a>
    
  <td width=10% style=background:lightgray>
    <a href="comisiones$VER.php?$USERSTRING&action=Consultar&orderby=estado&direction=$direction">Estado</a>
    
  <td width=10% style=background:lightgray>
    <a href="comisiones$VER.php?$USERSTRING&action=Consultar&orderby=institutoid&direction=$direction">Instituto</a>
    
  <td width=25% style=background:lightgray>
    <a href="comisiones$VER.php?$USERSTRING&action=Consultar&orderby=cedula&direction=$direction">Solicitante</a>
    
  <td width=8% style=background:lightgray>
    Descargas
    
  <td width=15% style=background:lightgray>
    Acciones
    
</tr>
T;
  
  for($i=0;$i<$nsolicitudes;$i++){

    $comision=$solicitudes[$i];
    $tcomisionid=$comision['comisionid'];
    $tcedula=$comision['cedula'];
    $tqcumplido=$comision['qcumplido'];
    $tcumplido1=$comision['cumplido1'];
    $tcumplido2=$comision['cumplido2'];

    $tanexo1=$comision['anexo1'];
    $tanexo2=$comision['anexo2'];
    $tanexo3=$comision['anexo3'];

    $testadox=$comision['estado'];
    $testado=$COM_ESTADOS["$testadox"];
    $ttipocomx=$comision['tipocom'];
    $ttipocom=$COM_TIPOS[$ttipocomx];
    $tfechaini=$comision['fechaini'];
    $tfechafin=$comision['fechafin'];
    
    //CALCULA EL TIEMPO DESPUES DE FINALIZADA DE LA COMISION
    $tafter=mysqlCmd("select UNIX_TIMESTAMP(now())-UNIX_TIMESTAMP(fechafin) from Comisiones_Solicitudes where comisionid='$tcomisionid'")[0];

    $estadocolor=$COM_COLORS[$testadox];
    if($ttipocomx=="noremunerada"  or $ttipocomx=="calamidad"){
      $estadocolor=$COM_COLORS[$testadox."_noremunerada"];
    }

    if($tafter>0 and 
       $testadox=="aprobada" and
       $ttipocomx!="noremunerada"){
      $estadocolor="pink";
    }

    $tradicacion=$comision['radicacion'];
    $tinstituto=$DEPENDENCIAS[$comision['institutoid']];
    $tactualiza=$comision['actualiza'];
    $tactualizacion=$comision['actualizacion'];
    $taprobacion=$comision['aprobacion'];
    $tvistobueno=$comision['vistobueno'];

    $results=mysqlCmd("select nombre from Usuarios where documento='$tcedula'");
    $tnombre=$results[0];
    $generar="";
    $reslink="";
    $extrares="";
    
    if(abs($QPERMISO)==$PERMISO_SUPERUSUARIO and $taprobacion=="Si" /*and ($ttipocomx!="noremunerada" and $ttipocomx!="calamidad")*/){

      $generar="<!-- -------------------------------------------------- -->
    <a href=comisiones$VER.php?$USERSTRING&comisionid=$tcomisionid&operation=Resolucion&action=Consultar>
      Generar</a><br/>";
    }

    $doctype="Resolución";
    if($ttipocomx=="noremunerada" or $ttipocomx=="calamidad"){
      $doctype="Oficio";
    }

    if(file_exists("data/comisiones/$tcomisionid/resolucion-$tcomisionid.html") and
       !file_exists("data/comisiones/$tcomisionid/.nogen")){
      $reslink="<!-- -------------------------------------------------- -->
    <a href=data/comisiones/$tcomisionid/resolucion-$tcomisionid.html target='_blank'>
      $doctype</a>
  (<a href=data/comisiones/$tcomisionid/resolucion-$tcomisionid.pdf target='_blank'>pdf</a>)<br/>
";
      $extrares="";
      if(abs($QPERMISO)==$PERMISO_SUPERUSUARIO){
	$extrares="<!-- -------------------------------------------------- -->
    <a href=data/comisiones/$tcomisionid/resolucion-blank-$tcomisionid.html target='_blank'>
      Imprimible</a>
  (<a href=data/comisiones/$tcomisionid/resolucion-blank-$tcomisionid.pdf target='_blank'>pdf</a>)<br/>
";
      }
      
    }else{
      $reslink="<i>No disponible</i><br/>";
    }
    
    //ARCHIVOS
    $tarchivos="";
    if(!isBlank($tanexo1)){
      $tarchivos.="<a href='data/comisiones/$tcomisionid/$tanexo1' target='_blank'>Anexo 1</a><br/>";
    }
    if(!isBlank($tanexo2)){
      $tarchivos.="<a href='data/comisiones/$tcomisionid/$tanexo2' target='_blank'>Anexo 2</a><br/>";
    }
    if(!isBlank($tanexo3)){
      $tarchivos.="<a href='data/comisiones/$tcomisionid/$tanexo3' target='_blank'>Anexo 3</a><br/>";
    }

    $tarchivoscump="";
    if(!isBlank($tcumplido1) and $tcumplido1!='None'){
      $tarchivoscump.="<a href='data/comisiones/$tcomisionid/Cumplido1_${tcedula}_${tcomisionid}_$tcumplido1' target='_blank'>Cumplido 1</a><br/>";
    }
    if(!isBlank($tcumplido2) and $tcumplido2!='None'){
      $tarchivoscump.="<a href='data/comisiones/$tcomisionid/Cumplido2_${tcedula}_${tcomisionid}_$tcumplido2' target='_blank'>Cumplido 2</a><br/>";
    }
    if(!isBlank($tcumplido3) and $tcumplido3!='None'){
      $tarchivoscump.="<a href='data/comisiones/$tcomisionid/Cumplido3_${tcedula}_${tcomisionid}_$tcumplido3' target='_blank'>Cumplido 3</a><br/>";
    }

    //GENERANDO ACCIONES
    $borrar="";
    $cumplido="";

    //CUMPLIDO STATUS
    if($ttipocomx!="noremunerada" and $ttipocomx!="calamidad"){

      //APROBADA + NO CUMPLIDA + TIEMPO PASADO
      if($taprobacion=="Si" and 
	 $tqcumplido==0){
	if($tafter>0){

	$cumplido="<!-- -------------------------------------------------- -->
  <a href=comisiones$VER.php?$USERSTRING&comisionid=$tcomisionid&action=Cumplido>
  Subir Cumplido</a><br/>";
	}else{
	  $cumplido="Cumplido futuro<br/>";
	}
      }

      //CUMPLIDA + DECANA
      if($tqcumplido>0 and
	 $QPERMISO){
	$cumplido="<!-- -------------------------------------------------- -->
<a href=comisiones$VER.php?$USERSTRING&comisionid=$tcomisionid&action=Cumplido>Modificar Cumplido</a><br/>";
      }

      //CUMPLIDA + PROFESOR
      if($tqcumplido>0 and
	 $QPERMISO==$PERMISO_PROFESOR){
	$cumplido="<!-- -------------------------------------------------- -->
<a href=comisiones$VER.php?$USERSTRING&comisionid=$tcomisionid&action=Cumplido>Actualizar Cumplido</a><br/>";
      }

      //APENAS SOLICITADA
      if($taprobacion!="Si" or 
	 $tvistobueno!="Si"){
	$cumplido="Pendiente<br/>";
      }

    }

    //BORRA SOLO SI NO HA SIDO SOLICITADA
    if(($taprobacion!="Si" and 
       $tvistobueno!="Si") or
       $qtrash){
      $borrar="<!-- -------------------------------------------------- -->
  <a href=comisiones$VER.php?$USERSTRING&comisionid=$tcomisionid&operation=$reciclaraction&action=Consultar$actiontxt>
  $reciclartxt</a>";
    }

    //CREA TABLA
$table.=<<<T
<tr style='background:$estadocolor'>
  <td>
    <a href=comisiones$VER.php?$USERSTRING&loadcomision&comisionid=$tcomisionid&action=Solicitar>
      $tcomisionid
    </a>
  </td>
  <td>$tradicacion</td>
  <td>$tactualizacion<br/>$tactualiza</td>
  <td>$tfechaini<br/>$tfechafin</td>
  <td>$ttipocom</td>
  <td>$testado</td>
  <td>$tinstituto</td>
  <td>$tcedula, $tnombre</td>
  <td>
  $reslink
  $extrares
  $tarchivos
  $tarchivoscump
  </td>
  <td>
$generar
$cumplido
$borrar
  </td>
</tr>
T;
  }
$table.=<<<T
</table>
T;

 if($nsolicitudes==0){
   $table="<i>No hay solicitudes.</i>";
 }

 $informes="";
 if(abs($QPERMISO)){
   $informes="<a href='comisiones$VER.php?usercedula=$cedula&userpass=$userpass&action=Informes'>Informes</a> | <a href='comisiones$VER.php?usercedula=$cedula&userpass=$userpass&operation=Backup&action=Consultar'>Hacer respaldo</a>";
   $lprofesores="";
 }

 //OTROS ORDENAMIENTOS
 $order_recientes=urlencode("abs(fechafin-DATE(now()))");

$content.=<<<C
$error
$lprofesores 

$browsing_help
<h2>Lista de solicitudes $trashtxt</h2>
  Número de solicitudes: $nsolicitudes
<p></p>
  
<style>
  td{
  padding:5px;
  font-size:0.8em;
  }
  a{
  color:blue;
  }
</style>
     
  <table border=0px><tr>
  <td>Convenciones:</td>
  </tr>
  <tr>
  <td style=background:#FFFF99>Comisión Solicitada</td>
  <td style=background:#FFCC99>Permiso Solicitado</td>
  <td style=background:#99CCFF>Visto Bueno</td>
  <td style=background:#33CCCC>Permiso Aprobado</td>
  <td style=background:#00CC99>Comisión Aprobada</td>
  <td style=background:lightgray>Comisión Cumplida</td>
  <td style=background:pink>Falta Cumplido</td>
  </tr></table>
<p></p>
<p style="font-size:12">
  Otros criterios de ordenación:
  <a href="?$USERSTRING&action=Consultar&orderby=$order_recientes&direction=$direction">Finalizan recientemente</a>
</p>
$table
<p></p>
$informes
C;
 }

////////////////////////////////////////////////////////////////////////
//LISTA DE PROFESORES
////////////////////////////////////////////////////////////////////////
if($action=="EditarProfesor"){

  $qnuevo=0;
  if($subaction=="Guardar"){
    if($ucedula!=$ecedula){
      if($ucedula!="0000000"){
	$error.=errorMessage("Cambiando documento del empleado");
	$sql="delete from Usuarios where cedula='$ucedula'";
	mysqlCmd($sql);
      }else{
	$epass=md5("$ecedula");
      }
    }
    $sql="insert into Usuarios (tipoid,cedula,nombre,email,tipo,institutoid,dedicacion,pass) values ('$etipoid','$ecedula','$enombre','$eemail','$etipo','$einstitutoid','$ededicacion','$epass') on duplicate key update tipoid=VALUES(tipoid),cedula=VALUES(cedula),nombre=VALUES(nombre),email=VALUES(email),tipo=VALUES(tipo),institutoid=VALUES(institutoid),dedicacion=VALUES(dedicacion),pass=VALUES(pass)";
    mysqlCmd($sql);
    $error.=errorMessage("Información del empleado actualizada");
  }
  if(isset($ecedula)){$ucedula=$ecedula;}
  $profesor=mysqlCmd("select * from Usuarios where cedula='$ucedula'",$qout=1);

  if($subaction=="Nuevo"){
    $profesor=array(array("tipoid"=>"","cedula"=>"","nombre"=>"","email"=>"","tipo"=>"","institutoid"=>"","dedicacion"=>"","pass"=>""));
    $qnuevo=1;
  }
  
$content.=<<<C
  <h3>Edición de la Información del Empleado</h3>

<p></p>
$error
<input type="hidden" name="action" value="EditarProfesor">
<input type="hidden" name="ucedula" value="$ucedula">
<table border=1px>
C;

  foreach(array_keys($profesor[0]) as $key){
    if($key=="pass"){
      $epass=$profesor[0]["pass"];
      $content.="<input type='hidden' name='epass' value='$epass'>";
    }
    if(preg_match("/\d/",$key)){continue;}
    if(($key=="pass" or
	$key=="permisos")){continue;}
    
    $help=$CAMPOSHELP["$key"];
    $value=$profesor[0]["$key"];
$content.=<<<C
<tr>
  <td>$key</td>
  <td>
    <input type="text" name="e$key" value="$value" size="50" placeholder="$help">
  </td>
</tr>
C;
  }
$content.=<<<C
<tr><td colspan=2><input type="submit" name="subaction" value="Guardar"></td></tr>
</table>
</form>
C;
}

////////////////////////////////////////////////////////////////////////
//LISTA DE PROFESORES
////////////////////////////////////////////////////////////////////////
if($action=="Profesores"){

  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //REMOVE
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  if($subaction=="Remove"){
    $sql="delete from Usuarios where cedula='$ucedula'";
    mysqlCmd($sql);
    $error.=errorMessage("Profesor '$ucedula' borrado...");
  }

  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //LIST
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

  $profesores=mysqlCmd("select * from Usuarios order by nombre",$qout=1);
  $content.=<<<C

<p>
$error
</p>

<center>

<h3>Lista de Empleados</h3>

<p>
<a href="comisiones$VER.php?$USERSTRING&action=EditarProfesor&ucedula=0000000&subaction=Nuevo">
  Crear nuevo empleado
</a>
</p>

<table border=1px cellspacing=0>
  <thead>
    <th>#</th>
    <th>Instituto</th>
    <th>Cédula</th>
    <th>Nombre</th>
    <th>Tipo</th>
    <th>Acciones</th>
  </thead>
C;
  $i=1;
  foreach($profesores as $profesor){

    $content.="<tr>";
    
    //echo "$i:".print_r($profesor,true)."<br/>";

    if(isBlank($profesor["nombre"])){
      continue;
    }

    foreach(array_keys($profesor) as $key){
      if(preg_match("/\d/",$key)){continue;}
      if($key=="cedula"){
	$key="ucedula";
	$$key=$profesor["cedula"];
      }else{
	$$key=$profesor["$key"];
      }
    }
    if($ucedula=="0000000"){continue;}
    $instituto=$INSTITUTOS["$institutoid"];

$content.=<<<C
<td>$i</td>
<td>$instituto</td>
<td>$ucedula</td>
<td>$nombre</td>
<td>$tipo</td>
<td>
  <a href="comisiones$VER.php?$USERSTRING&action=Profesores&subaction=Remove&ucedula=$ucedula">Reciclar</a> | 
  <a href="comisiones$VER.php?$USERSTRING&action=EditarProfesor&ucedula=$ucedula">Editar</a>
</td>
C;
    $content.="</tr>";
    $i++;
  }
  $content.="</table></center>";
}	

////////////////////////////////////////////////////////////////////////
//LISTA DE SOLICITUDES
////////////////////////////////////////////////////////////////////////
if($action=="Informes"){

  if(abs($QPERMISO)==$PERMISO_PROFESOR){
    $error=errorMessage("No autorizado para generar informes.");
    $content.="$error";
    goto footer;
  }
  
  if(isBlank($command)){
    $command="* from Comisiones_Solicitudes";
  }
  if(preg_match("/;/",$command) or 
     preg_match("/select/i",$command)){
    $error=errorMessage("El comando esta equivocado.");
    $cmd="select null";
  }else{
    $cmd="select $command";
  }

  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //DATABASE QUERY
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  $solicitudes=mysqlCmd($cmd,$qout=1);
  if($solicitudes==0){$nsolicitudes=0;}
  else{$nsolicitudes=count($solicitudes);}

  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //DATABASE CHARACTERISTICS
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  $file="scratch/informe-$usercedula.csv";
  $fl=fopen($file,"w");

  $fields=array();
  $fields_txt="";
  foreach(array_keys($solicitudes[0]) as $field){
    if(preg_match("/^\d+$/",$field)){continue;}
    array_push($fields,$field);
    $fields_txt.="$field;";
    if($field=="cedula"){
      array_push($fields,"nombre");
      $fields_txt.="nombre;";
    }
  }
  fwrite($fl,utf8_decode(trim($fields_txt,";")."\n"));

  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //GENERATE TABLE
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
$table=<<<T
<b>Número de registros</b>: $nsolicitudes<br/>
<b>Archivo de informe</b>: <a href="scratch/informe-$usercedula.csv">informe</a>
<br/><br/>
<table border=1px>
  <tr>
T;
 foreach($fields as $field){
$table.=<<<T
<td>$field</td>
T;
 }
$table.="</tr>";
 
 foreach($solicitudes as $solicitud){
   $table.="<tr>";
   $values="";
   foreach($fields as $field){
     if($field=="nombre"){continue;}
     $value=$solicitud[$field];
     $values.="\"$value\";";
     if($field=="comisionid"){
$value=<<<C
    <a href=comisiones$VER.php?$USERSTRING&loadcomision&comisionid=$comisionid&action=Solicitar>
      $value
    </a>
C;
     }
$table.=<<<T
<td>$value</td>
T;
     if($field=="cedula"){
       $profesor=mysqlCmd("select nombre from Usuarios where cedula='$value'",$qout=1);
       $value=$profesor[0][0];
       $values.="\"$value\";";
       $table.="<td>$value</td>";
     }
   }
   fwrite($fl,utf8_decode(trim($values,";")."\n"));
   $table.="</tr>";
 }
 $table.="</table>";
 fclose($fl);

  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //GENERATE REPORTE
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

$tabla=<<<T
T;

$content.=<<<C
$error
<a href="comisiones$VER.php?usercedula=$cedula&userpass=$userpass&action=Solicitar">Nueva Solicitud</a> | 
<a href="comisiones$VER.php?usercedula=$cedula&userpass=$userpass&action=Consultar">Consultar</a> |
<a href="comisiones$VER.php?$USERSTRING">Salir</a>
<h2>Informes Completos</h2>
  <form method="GET">
  <input type="hidden" name="usercedula" value="$usercedula">
  <input type="hidden" name="action" value="Informes">
  Buscar: <input type="text" name="command" value="$command" size=100>
  <input type="submit" name="execute" value="Proceda">
  </form>
<a href="JavaScript:void(null)"
   onclick="$('#ejemplos').toggle('fast',null);">Ejemplos</a><br/>
<table id="ejemplos" border=0px style="margin-left:10px;font-size:12px;display:none;color:blue;">
  <tr>
    <td>
      Muestre todas las comisiones presentadas a la fecha:<br/>
      <pre>
	* from Comisiones_Solicitudes
      </pre>
    </td>
  </tr>
  <tr>
    <td>
      Muestre las comisiones presentadas por la cedula 71755174:<br/>
      <pre>
	* from Comisiones_Solicitudes where cedula='71755174'
      </pre>
    </td>
  </tr>

  <tr>
    <td>
      Muestre todos los permisos:<br/>
      <pre>
	* from Comisiones_Solicitudes where (tipocom='noremunerada' and tipocom='calamidad')
      </pre>
    </td>
  </tr>

  <tr>
    <td>
      Muestre todos las comisiones en idioma inglés:<br/>
      <pre>
	* from Comisiones_Solicitudes where idioma like 'Ingl%'
      </pre>
    </td>
  </tr>

  <tr>
    <td>
      Muestre todos las comisiones aprobadas después del 15 de agosto:<br/>
      <pre>
	* from Comisiones_Solicitudes where actualizacion>'2015-08-15'
      </pre>
    </td>
  </tr>

  <tr>
    <td>

      Muestre todos las comisiones aprobadas después del 15 de agosto
      y antes del 20 de agosto:<br/>

      <pre>
	* from Comisiones_Solicitudes where actualizacion>='2015-08-15' and actualizacion<='2015-08-20'
      </pre>
    </td>
  </tr>

  <tr>
    <td>

      Todas las comisiones de biología de 2015:<br/>

      <pre>
	* from Comisiones_Solicitudes where actualizacion like '2015%' and institutoid='biologia'
      </pre>
    </td>
  </tr>

  <tr>
    <td>

      Muestre los números de resolución y las cédulas de todas las comisiones:<br/>

      <pre>
	cedula,resolucion from Comisiones_Solicitudes order by resolucion
      </pre>
    </td>
  </tr>

  <tr>
    <td>

    Los campos de la base de datos son: comisionid (identificador alfa
       numérico de la solicitud), resolucion (número de la resolución,
       99999 si es un permiso), fecharesolucion (fecha de la
       resolución), cedula (cédula del solicitante), institutoid
       (instituto en minúsculas y sin tilde), fecha (fecha de
       radicación), actividad (actividad de la comisión), lugar (lugar
       de la comisión), tipocom (tipo de comisión, sevicios, estudios,
       noremunerada - es decir permiso, calamidad), objeto (objetivo de la
       comisión), idioma (idioma de la comisión), dedicacion (¿tiene
       dedicación exclusiva?), estado (estado de la solicitud),
       radicacion (fecha de radiación), actualizacion (fecha de
       actualización), actualiza (cédula de quién actualiza),
       vistobueno (¿tiene visto bueno?), aprobacion (¿ha sido
       aprobada?), extra1 (número de días que le restan para permisos
       por este año).

    </td>
  </tr>


</table>
<p></p>
$table
C;
}

////////////////////////////////////////////////////////////////////////
//LATERAL CONTENT
////////////////////////////////////////////////////////////////////////
footer:
$content.=getLateral(40);
$content.=<<<C
C;
$content.=endLateral();

////////////////////////////////////////////////////////////////////////
//FOOTER AND RENDER
////////////////////////////////////////////////////////////////////////
$content.=getMessages();
$content.=getFooter();
echo $content;
?>
</html>
