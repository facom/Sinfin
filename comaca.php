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
//GLOBAL VARIABLES
////////////////////////////////////////////////////////////////////////
$TIPOS_ACTIVIDAD=array(
   "seminario"=>"Seminario",
   "divulgacion"=>"Actividad divulgativa",
   "reunion"=>"Reunión comunidad",
   "clubrevistas"=>"Club de Revistas"
		       );

$UMBRALES_ACTIVIDAD=array(
   "seminario"=>3,
   "divulgacion"=>3,
   "reunion"=>1,
   "clubrevistas"=>3
);

$HELPICON="<a href='JavaScript:void(null)' onclick='toggleHelp(this)'><img src='img/help.png' width='15em'></a>";
$PLAZO=48; //PLAZO EN HORAS PARA REGISTRAR UNA BOLETA

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
  | <a href="?mode=agenda">Agenda</a>
  <span class="level1">
    | <a href="?mode=registrar">Registrar asistencia</a>
    | <a href="?mode=consultar">Consultar asistencia</a>
  </span>
  <span class="level2">
    | <a href="?mode=agregar">Agregar actividad</a>
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
  if($action=="Suscribirse" or 
     $action=="Desuscribirse"){
    $mode="agenda";

    if($subaction=="confirmar"){
      $confirma="1";
      $fecha=$DATE;
      if($action=="Suscribirse"){
	insertSql("Suscripciones",array("email"=>"emailsubscribe",
					"confirma"=>"",
					"fecha"=>""));
	statusMsg("Su suscripción ha sido confirmada.");	
	goto endaction;
      }
      else{
	mysqlCmd("delete from Suscripciones where email='$emailsubscribe'");
	statusMsg("Su suscripción ha sido cancelada.");
	goto endaction;
      }
      goto endaction;
    }

    if(!isset($emailsubscribe)){
      errorMsg("Debe proveer un correo valido.");
      goto endaction;
    }
    if(!preg_match("/@/",$emailsubscribe) or
       !preg_match("/\./",$emailsubscribe) or
       preg_match("/\,/",$emailsubscribe)
       ){
      errorMsg("El correo electrónico provisto esta malo");
      goto endaction;
    }
    $qmsg=0;
    if($action=="Suscribirse"){
      $email=$emailsubscribe;
      if(!($result=mysqlCmd("select * from Suscripciones where email='$email'"))){
	$suscripcion="comaca";
	$confirma="0";
	$fecha=$DATE;
	insertSql("Suscripciones",
		  array("email"=>"",
			"suscripcion"=>"",
			"confirma"=>"",
			"fecha"=>"")
		  );
	$qmsg=1;
      }else{
	if($result["confirma"]==0){
	  errorMsg("Su correo ya ha sido suscrito pero no ha sido confirmado.");
	  $qmsg=1;
	}else{
	  errorMsg("Su correo ya ha sido suscrito y esta confirmado.");
	}
      }

      if($qmsg){
	$subject="[SInfIn] Suscripción a la lista de correos de Comunidad Académica";
$message.=<<<M
<p>
  Señor(a) usuario,
</p>
<p>
  Usted ha solicitado la suscripción de su correo electrónico a la
  lista de anuncios de las actividades de la Comunidad Académica de la
  Facultad de Ciencias Exactas y Naturales.
</p>
<p>
  Si esta de acuerdo confirme su deseo 
  haciendo click en el siguiente enlace:
  <center>
  <a href="$SITEURL/comaca.php?mode=agenda&action=Suscribirse&emailsubscribe=$email&subaction=confirmar">Suscribirme a la lista de anuncios</a>.
  </center>
</p>
<p>
  <b>Comité de Currículo</b><br/>FCEN
</p>
M;
        sendMail($email,$subject,$message,$EHEADERS);
	statusMsg("Hemos enviado un correo a su dirección para que confirme la suscripción");
      }

      goto endaction;
    }

    if($action=="Desuscribirse"){
      $email=$emailsubscribe;
      if($result=mysqlCmd("select * from Suscripciones where email='$email'")){
	$subject="[SInfIn] Desuscripción a la lista de correos de Comunidad Académica";
$message.=<<<M
<p>
  Señor(a) usuario,
</p>
<p>
  Usted ha solicitado la desuscripción de su correo electrónico a la
  lista de anuncios de las actividades de la Comunidad Académica de la
  Facultad de Ciencias Exactas y Naturales.
</p>
<p>
  Si esta de acuerdo confirme su deseo 
  haciendo click en el siguiente enlace:
  <center>
  <a href="$SITEURL/comaca.php?mode=agenda&action=Desuscribirse&emailsubscribe=$email&subaction=confirmar">Desuscribirme a la lista de anuncios</a>.
  </center>
</p>
<p>
  <b>Comité de Currículo</b><br/>FCEN
</p>
M;
        sendMail($email,$subject,$message,$EHEADERS);
	statusMsg("Hemos enviado un correo a su dirección para que confirme la desuscripción");
      }else{
	errorMsg("Su correo no ha sido suscrito todavía.");
      }
    }
    goto endaction;
  }

  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //SALIR DE LA EDICION
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  if($action=="Salir"){
    $mode="agenda";
    goto endaction;
  }

  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //CREAR ACTIVIDADES
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  if($action=="Borrar"){
    $mode="agenda";

    if(mysqlCmd("select * from Boletas where Actividades_actid='$actid'")){
      errorMsg("Hay al menos una boleta registrada de esta actividad");
      goto endaction;
    }

    if(!($result=mysqlCmd("select * from Actividades where actid='$actid'"))){
      errorMsg("Actividad no existe");
      goto endaction;
    }

    mysqlCmd("delete from Actividades where actid='$actid'");
    statusMsg("Actividad $actid borrada");
    goto endaction;
  }

  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //CREAR ACTIVIDADES
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  if($action=="Guardar"){
    $mode="agenda";

    //FECHAS
    $fecharango=str2Array($fecha_actividad);
    $fechaini=$fecharango["start"];
    $fechafin=$fecharango["end"];
    
    //VALIDAR

    //GUARDAR
    insertSql("Actividades",$ACTIVIDADES_FIELDS);

    statusMsg("Actividad $actid guardada");
 }

  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //CREAR ACTIVIDADES
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  if($action=="Registrar"){

    $mode="resultado";
    $numero=$numero+0;

    //OBTENER INFORMACIÓN SOBRE LA ACTIVIDAD
    $actividad=mysqlCmd("select * from Actividades where actid='$Actividades_actid'");

    //VALIDAR
    if(isBlank($boletaid)){
      $mode="registrar";
      errorMsg("Debe proveer un código de boleta no nulo");
      goto endaction;
    }
    if(isBlank($numero)){
      $mode="registrar";
      errorMsg("Debe proveer un número de boleta no nulo");
      goto endaction;
    }
    if(!($boleta=mysqlCmd("select * from Boletas where boletaid='$boletaid' and numero+0=$numero"))){
      $mode="registrar";
      errorMsg("El código de la boleta y su número no coinciden");
      goto endaction;
    }
    if($actividad["tipo"]!=$boleta["tipo"]){
      $mode="registrar";
      errorMsg("El tipo de actividad no coincide con la boleta");
      goto endaction;
    }
    //VALIDA QUE NO HAYA SIDO REGISTRADA
    if($nboleta=mysqlCmd("select * from Boletas where Actividades_actid='$Actividades_actid' and Usuarios_documento='$Usuarios_documento'")){
      $mode="registrar";
      errorMsg("Usted ya registro una boleta para esta actividad");
      goto endaction;
    }
    //VALIDA QUE NO HAYA SIDO REGISTRADA
    if($nboleta=mysqlCmd("select * from Boletas where boletaid='$boletaid' and numero+0=$numero and Usuarios_documento<>''")){
      $mode="registrar";
      $fechahora=$nboleta["fechahora"];
      errorMsg("La boleta $boletaid ya ha sido registrada por otro estudiante (hora de registro: $fechahora). Si usted tiene la boleta consigo reporte esta irregularidad al coordinador");
      goto endaction;
    }
    //VALIDA RANGO
    $rango=preg_split("/-/",$actividad["Boletas_rango"]);
    if($numero<$rango[0] or $numero>$rango[1]){
      $mode="registrar";
      errorMsg("La boleta no fue distribuida en esta actividad");
      goto endaction;
    }
    
    //VALIDA TIEMPO TRANSCURRIDO
    $fechafin=$actividad["fechafin"]." ".$actividad["horafin"].":00";
    $date1=date_create($DATE." UTC-5");
    $date2=date_create($fechafin." UTC-5");
    $dif=date_diff($date1,$date2);
    $hours=24*$dif->format("%d")+$dif->format("%h");
    if($hours>$PLAZO){
      $resultado="<i style='color:red'>Boleta registrada después del plazo reglamentario. La actividad ocurrió hace $hours horas y el plazo era de $PLAZO horas.</i>";
      $tarde=1;
    }else{
      $resultado="Boleta registrada exitosamente.";
      $tarde=0;
    }

    //GUARDAR
    $semestre=$actividad["semestre"];
    $IP=get_client_ip();
    $result=mysqlCmd("select now();",$qout=0);
    $fechahora=$result[0];
    insertSql("Boletas",array("boletaid"=>"",
			      "Usuarios_documento"=>"",
			      "Actividades_actid"=>"",
			      "fechahora"=>"",
			      "IP"=>"",
			      "tarde"=>"",
			      "semestre"=>""));
 }

  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //CREAR ACTIVIDADES
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  if($action=="Consultar"){
    $mode="consultar";

    if(isBlank($semestre)){
      errorMsg("Debe proveer el semestre");
      goto endaction;
    }
    if(isBlank($documentos)){
      errorMsg("Debe proveer al menos un documento");
      goto endaction;
    }

    //SPLIT DOCUMENTOS
    $documentos=preg_split("/\s*,\s*/",$documentos);
    $asistencias=array();
    $numasistencias=array();

    //GET INFORMATION ABOUT EACH USER
    foreach($documentos as $documento){
      if(!($result=mysqlCmd("select * from Boletas where Usuarios_documento='$documento' and semestre='$semestre'",$qout=true))){
	$asistencias["$documento"]="<i>No ha asistido</i>";
      }else{
	$asistencias["$documento"]=$result;
	$numasistencias["$documento"]=array();
	foreach(array_keys($TIPOS_ACTIVIDAD) as $tipo){
	  $result=mysqlCmd("select count(boletaid) from Boletas where Usuarios_documento='$documento' and tipo='$tipo' and tarde='0'");
	  $numasistencias["$documento"]["$tipo"]=$result[0];
	}
      }
    }

    //GET INFORMATION ABOUT EACH ACTIVITY
    $result=mysqlCmd("select * from Actividades where semestre='$semestre'",$qout=1);
    $actividades=array();
    foreach($result as $actividad){
      $actid=$actividad["actid"];
      $actividades["$actid"]=$actividad;
    }

    $mode="consultado";
    goto endaction;
  }

  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //CARGAR ACTIVIDADES
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  if($action=="loadact"){
    if($result=mysqlCmd("select * from Actividades where actid='$actid'")){
      foreach(array_keys($ACTIVIDADES_FIELDS) as $field) $$field=$result["$field"];
      $fecha_actividad=array("start"=>$fechaini,"end"=>$fechafin);
      statusMsg("Actividad $actid cargada");
      $mode="agregar";
    }else{
      $mode="lista";
      errorMsg("La actividad no existe");
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
    if(!isset($actid) or isset($submode)){$actid=generateRandomString(5);}
    else{}
    if(!isset($fecha_actividad)){$fechaini="";$fechafin="";}
    else{}
    if(!isset($Boletas_rango)){$Boletas_rango="1-100000";}
    else{}

    $fecha_menu=fechaRango("fecha_actividad",$fechaini,$fechafin);
    $tiposel=generateSelection($TIPOS_ACTIVIDAD,
			       "tipo","$tipo");
    $institutosel=generateSelection($INSTITUTOS,
				    "instituto","$instituto");

$content.=<<<C

<center>
<h4>Registro de actividad $actid</h4>

<form action="comaca.php" method="post" enctype="multipart/form-data" accept-charset="utf-8">
<input type="hidden" name="actid" value="$actid">

<table border=0px width=60% cellspacing=0px>

<tr class="field">
  <td class="campo" id="instituto">Instituto$HELPICON</td>
  <td class="form">
    $institutosel
  </td>
</tr>
<tr class="ayuda" id="instituto_help" >
  <td colspan=2>Instituto al que esta adscrita la actividad.</td>
</tr>

<tr class="field">
  <td class="campo" id="tipo">Tipo de actividad$HELPICON</td>
  <td class="form">
    $tiposel
  </td>
</tr>
<tr class="ayuda" id="tipo_help" >
  <td colspan=2>Tipo de actividad.</td>
</tr>

<tr class="field">
  <td class="campo" id="nombre">Nombre de la actividad$HELPICON</td>
  <td class="form">
    <input type="text" name="nombre" value="$nombre">
  </td>
</tr>
<tr class="ayuda" id="nombre_help" >
  <td colspan=2>Nombre de la actividad.</td>
</tr>

<tr class="field">
  <td class="campo" id="encargado">A cargo de$HELPICON</td>
  <td class="form">
    <input type="text" name="encargado" value="$encargado">
  </td>
</tr>
<tr class="ayuda" id="nombre_help" >
  <td colspan=2>Nombre de la persona que realizará la actividad.</td>
</tr>

<tr class="field">
  <td class="campo" id="lugar">Lugar de la actividad$HELPICON</td>
  <td class="form">
    <input type="text" name="lugar" value="$lugar">
  </td>
</tr>
<tr class="ayuda" id="lugar_help" >
  <td colspan=2>Lugar de la actividad.</td>
</tr>

<tr class="field">
  <td class="campo" id="fecha">Fecha de la actividad$HELPICON</td>
  <td class="form">
  $fecha_menu
  </td>
</tr>
<tr class="ayuda" id="fecha_help" >
  <td colspan=2>Fecha de la actividad.</td>
</tr>

<tr class="field">
  <td class="campo" id="horaini">Hora inicial de la actividad$HELPICON</td>
  <td class="form">
    <input type="text" name="horaini" value="$horaini" placeholder="HH:MM" size="8">
  </td>
</tr>
<tr class="ayuda" id="horaini_help" >
  <td colspan=2>Hora inicial de la actividad.</td>
</tr>

<tr class="field">
  <td class="campo" id="horafin">Hora final de la actividad$HELPICON</td>
  <td class="form">
    <input type="text" name="horafin" value="$horafin" placeholder="HH:MM" size="8">
  </td>
</tr>
<tr class="ayuda" id="hora_help" >
  <td colspan=2>Hora final de la actividad.</td>
</tr>

<tr class="field">
  <td class="campo" id="semestre">Período académico$HELPICON</td>
  <td class="form">
    <input type="text" name="semestre" value="$semestre" placeholder="2016-2">
  </td>
</tr>
<tr class="ayuda" id="semestre_help" >
  <td colspan=2>Período académico al que esta asociada la actividad.</td>
</tr>

<tr class="field">
  <td class="campo" id="resumen">Resumen de la actividad$HELPICON</td>
  <td class="form">
    <textarea name="resumen" rows="10" cols="50">$resumen</textarea>
  </td>
</tr>
<tr class="ayuda" id="resumen_help" >
  <td colspan=2>Resumen de la actividad.</td>
</tr>

<tr class="field">
  <td class="campo" id="rango">Rango de boletas$HELPICON</td>
  <td class="form">
    <input type="text" name="Boletas_rango" value="$Boletas_rango" placeholder="121-143">
  </td>
</tr>
<tr class="ayuda" id="rango_help" >
  <td colspan=2>Indique el rango numérico de las boletas distribuidas en la actividad.</td>
</tr>

<tr class="field">
  <td colspan=2 class="botones_simple">
    <input type="submit" name="action" value="Guardar">
    <input type="submit" name="action" value="Borrar">
    <input type="submit" name="action" value="Salir">
  </td>
</tr>

</table>
</center>

</form>
C;

  }

  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //AGENDA DE ACTIVIDADES
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  else if($mode=="agenda"){
    
    if(!isset($sort)){$sort="TIMESTAMP(fechaini) asc, horafin ";}
    if(!isset($order)){$order="asc";}
    if(!isset($search)){$search="where TIMESTAMP(fechafin)>=TIMESTAMP(CURDATE()) and actid<>'' ";}

    //LEER TODAS LAS ACTIVIDADES
    $sql="select * from Actividades $search order by $sort $order";
    if(!($results=mysqlCmd($sql,$qout=1))){
      $content.="<i>No hay actividades con el criterio de búsqueda provisto.</i>";
      goto end;
    }
    if($order=="asc"){$order="desc";}
    else{$order="asc";}

    $sql="select * from Actividades where TIMESTAMP(fechafin)<TIMESTAMP(CURDATE()) and actid<>'' order by $sort $order";
    $resultspasadas=mysqlCmd($sql,$qout=1);

$table=<<<T
<center>
<table border=0px style='font-size:0.8em' cellspacing:0px>
<thead>
<tr style="background:lightgray;padding:5px">
  <td width=5% class="field level3">
    <a href="?mode=agenda&order=$order&sort=actid">
      Id.
    </a>
  </td>
  </span>
  <td width=5% class="field">
    <a href="?mode=agenda&order=$order&sort=semestre">
      Período
    </a>
  </td>
  <td width=10% class="field">
    <a href="?mode=agenda&order=$order&sort=fechaini">
      Fechas
    </a>
  </td>
  <td width=10% class="field">
    <a href="?mode=agenda&order=$order&sort=lugar">
      Lugar
    </a>
  </td>
  <td width=10% class="field">
    <a href="?mode=agenda&order=$order&sort=instituto">
      Instituto
    </a>
  </td>
  <td width=10% class="field">
    <a href="?mode=agenda&order=$order&sort=tipo">
      Tipo
    </a>
  </td>
  <td width=50% class="field">
    <a href="?mode=agenda&order=$order&sort=titulo">
      Actividad
    </a>
  </td>
</tr>
</thead>
T;
$table_pasadas="".$table;

$agenda=<<<A
<table border=1px width=100%>
A;

    foreach($results as $actividad){
      foreach(array_keys($ACTIVIDADES_FIELDS) as $field){
	$$field=$actividad["$field"];
      }
      if($fechaini==$fechafin){$fechas="$fechaini, $horaini-$horafin";}
      else{$fechas="$fechaini a $fechafin, $horaini-$horafin";}

      $fechaend=$actividad["fechafin"]." ".$actividad["horafin"].":00";
      $dif=strtotime($fechaend)-strtotime('now');
      if($dif<0){$pasadost="style=color:lightgray";}
      else{$pasadost="";}

      $Instituto=$INSTITUTOS["$instituto"];
      $Tipo=$TIPOS_ACTIVIDAD["$tipo"];

      $fechagoogle="";
      $fechagoogle.=preg_replace("/-/","",$fechaini);
      $fechagoogle.="T";
      $fechaparts=preg_split("/:/",$horaini);
      $hora=$fechaparts[0]+5;
      $fechagoogle.=$hora.$fechaparts[1]."00Z";
      $fechagoogle.="/";
      $fechagoogle.=preg_replace("/-/","",$fechafin);
      $fechagoogle.="T";
      $fechaparts=preg_split("/:/",$horafin);
      $hora=$fechaparts[0]+5;
      $fechagoogle.=$hora.$fechaparts[1]."00Z";
      //echo "Fecha: $fechagoogle</br>";
      
$link=<<<L
<a href="http://www.google.com/calendar/event?
action=TEMPLATE
&text=$nombre
&dates=$fechagoogle
&details=A cargo de $encargado%0A%0A$resumen
&location=$lugar
&trp=false
&sprop=
&sprop=name:"
target="_blank" rel="nofollow">$fechas</a>
L;

    if($rowcolor=="white"){$rowcolor="lightgray";}
    else{$rowcolor="white";}

$table.=<<<T
<tr style="background:$rowcolor">
  <td class="field level3" $pasadost>
  <center>
    <a href=?mode=agregar&action=loadact&actid=$actid>
      $actid
    </a><br/>
    <a href=?mode=agregar&action=loadact&actid=$actid&submode=duplicar style=font-size:8px>
      Duplicar
    </a>
  </center>
  </td>
  <td class="field" $pasadost>
    $semestre
  </td>
  <td class="field" $pasadost>
    $link
  </td>
  <td class="field" $pasadost>
    $lugar
  </td>
  <td class="field" $pasadost>
    $Instituto
  </td>
  <td class="field" $pasadost>
    $Tipo
  </td>
  <td class="field" $pasadost>
    <b>$nombre</b><br/>
    A cargo de <i>$encargado</i><br/>
    <a href="JavaScript:void(null)" onclick="$('#resumen_$actid').toggle()">
      Resumen
    </a><br/>
    <div id="resumen_$actid" style="display:none;padding:10px">
      $resumen
    </div>
  </td>
</tr>
T;
    }

    //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
    //ASISTENCIAS PASADAS
    //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
    foreach($resultspasadas as $actividad){
      foreach(array_keys($ACTIVIDADES_FIELDS) as $field){
	$$field=$actividad["$field"];
      }
      if($fechaini==$fechafin){$fechas="$fechaini, $horaini-$horafin";}
      else{$fechas="$fechaini a $fechafin, $horaini-$horafin";}

      $fechaend=$actividad["fechafin"]." ".$actividad["horafin"].":00";
      $dif=strtotime($fechaend)-strtotime('now');
      if($dif<0){$pasadost="style=color:lightgray";}
      else{}
      $pasadost="";

      $Instituto=$INSTITUTOS["$instituto"];
      $Tipo=$TIPOS_ACTIVIDAD["$tipo"];

      //CHECK ASISTENCIAS
      if($QPERMISO<3){
	//USUARIO REGULAR
	$sql="select * from Boletas where Actividades_actid='$actid' and Usuarios_documento='$DOCUMENTO'";
	$resultado=mysqlCmd($sql);
	//echo "$actid,".print_r($resultado,true)."<br/>";
	if($resultado==0){
	  $numero="No";
	}else{
	  $numero="Si";
	  if($resultado["tarde"]>0){$numero.=" <i style=color:red>(tarde)</i>";}
	}


      }else{
	//USUARIO ADMINISTRADOR
	$sql="select count(boletaid) from Boletas where Actividades_actid='$actid' and Usuarios_documento<>''";
	$numero=mysqlCmd($sql)[0];
      }


      $fechagoogle="";
      $fechagoogle.=preg_replace("/-/","",$fechaini);
      $fechagoogle.="T";
      $fechaparts=preg_split("/:/",$horaini);
      $hora=$fechaparts[0]+5;
      $fechagoogle.=$hora.$fechaparts[1]."00Z";
      $fechagoogle.="/";
      $fechagoogle.=preg_replace("/-/","",$fechafin);
      $fechagoogle.="T";
      $fechaparts=preg_split("/:/",$horafin);
      $hora=$fechaparts[0]+5;
      $fechagoogle.=$hora.$fechaparts[1]."00Z";
      //echo "Fecha: $fechagoogle</br>";
      
$link=<<<L
<a href="http://www.google.com/calendar/event?
action=TEMPLATE
&text=$nombre
&dates=$fechagoogle
&details=A cargo de $encargado%0A%0A$resumen
&location=$lugar
&trp=false
&sprop=
&sprop=name:"
target="_blank" rel="nofollow">$fechas</a>
L;

    if($rowcolor=="white"){$rowcolor="lightgray";}
    else{$rowcolor="white";}

$table_pasadas.=<<<T
<tr style="background:$rowcolor">
  <td class="field level3" $pasadost>
  <center>
    <a href=?mode=agregar&action=loadact&actid=$actid>
      $actid
    </a><br/>
    <a href=?mode=agregar&action=loadact&actid=$actid&submode=duplicar style=font-size:8px>
      Duplicar
    </a>
  </center>
  </td>
  <td class="field" $pasadost>
    $semestre
  </td>
  <td class="field" $pasadost>
    $link
  </td>
  <td class="field" $pasadost>
    $lugar
  </td>
  <td class="field" $pasadost>
    $Instituto
  </td>
  <td class="field" $pasadost>
    $Tipo
  </td>
  <td class="field" $pasadost>
    <b>$nombre</b><br/>
    A cargo de <i>$encargado</i><br/>
    <span class="level1" style="color:green">Asistencia: $numero<br/></span>
    <a href="JavaScript:void(null)" onclick="$('#resumen_$actid').toggle()">
      Resumen
    </a><br/>
    <div id="resumen_$actid" style="display:none;padding:10px">
      $resumen
    </div>
  </td>
</tr>
T;
    }
    
$table.=<<<T
</table>
</center>
T;
$table_pasadas.=<<<T
</table>
</center>
T;

 $emailsubscripe="";
 if(isset($EMAIL)){$emailsubscribe=$EMAIL;}

$content.=<<<C
<h4>Agenda de actividades</h4>
<p>
Esta es la agenda de actividades de la <b>Comunidad Académica</b>.  Haga click en el nombre de cada columna para cambiar el orden en el que se listan las actividades.  Puede hacer click en "Resumen" para desplegar información detallada sobre cada actividad.  También puede hacer click en la fecha del evento para agregarlo a su calendario en Google.
</p>
<p>
Puede ver la agenda también en Google Calendar: <a href=http://bit.ly/fcen-comaca-calendario target=_blank>http://bit.ly/fcen-comaca-calendario</a>
</p>
<p>
Puede suscribir su correo electrónico para recibir notificaciones cada
vez que se aproxime una actividad.  También puede desuscribirse evitando que le sigan llegando notificaciones indeseadas.
</p>

<p>
<form>
<input type="hidden" name="mode" value="agenda">
  Correo electrónico: <input type="text" name="emailsubscribe" placeholder="nombre@mail.com" value="$emailsubscribe" size="40"> <input type="submit" name="action" value="Suscribirse"> <input type="submit" name="action" value="Desuscribirse">
</form>
</p>

$table

<h4>Actividades pasadas</h4>
<p>
Estas son las actividades pasadas:
</p>
$table_pasadas
C;

    goto end;
  }

  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //REGISTRAR ASISTENCIA
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  else if($mode=="registrar" or $mode=="resultado"){

$content.=<<<C
<center>
<h4>Registro de asistencia</h4>

<p style=font-size:0.8em;font-style:italic>
Para un tutorial sobre como realizar el registro de asistencia vaya a la <a href=$SITEURL/ayuda.php>página de ayuda</a>.
</p>

C;

 if($mode=="resultado"){
$content.=<<<C
<div style="width:80%;background:lightgray;padding:20px">
$resultado
</div>
C;
 }
 
 if($mode=="registrar"){
    $sql="select * from Actividades where TIMESTAMP(fechafin)<Now() order by TIMESTAMP(fechaini) desc";
    if(!($results=mysqlCmd($sql,$qout=1))){
      $content.="<i>No hay actividades con el criterio de búsqueda provisto.</i>";
      goto end;
    }

    $Usuarios_documento=$_SESSION["documento"];
    $nombre=$_SESSION["nombre"];
    $email=$_SESSION["email"];
    
    $actividades=array();
    $i=0;
    foreach($results as $actividad){
      foreach(array_keys($ACTIVIDADES_FIELDS) as $field){
	$name="act_$field";
	$$name=$actividad["$field"];
      }
      if($i==0 and !isset($Actividades_actid)){
	$Actividades_actid=$act_actid;
      }
      $act_tipo=$TIPOS_ACTIVIDAD["$act_tipo"];
      $act_nombre=substr($act_nombre,0,20)."...";
      $act_encargado=substr($act_encargado,0,10)."...";
      $actividades["$act_actid"]="$act_tipo : $act_nombre / $act_encargado / $act_fechaini,$act_horaini";
      $i++;
    }
    $actsel=generateSelection($actividades,"Actividades_actid","$Actividades_actid");

$content.=<<<C
<form action="comaca.php" method="post" enctype="multipart/form-data" accept-charset="utf-8">

<table border=0px width=60% cellspacing=0px>

<tr class="field">
  <td class="campo" id="documento">Documento del asistente$HELPICON</td>
  <td class="form">
  <input type="text" name="Usuarios_documento" value="$Usuarios_documento" onchange="updateStudentForm(this)" readonly>
  </td>
</tr>
<tr class="ayuda" id="documento_help" >
  <td colspan=2>Documento de identidad del asistente.</td>
</tr>

<tr class="field">
  <td class="campo">Nombre del asistente</td>
  <td class="form">
  <input type="text" name="nombre" value="$nombre" disabled>
  </td>
</tr>

<tr class="field">
  <td class="campo">E-mail</td>
  <td class="form">
  <input type="text" name="email" value="$email" disabled>
  </td>
</tr>

<tr class="field">
  <td class="campo" id="actividad">Actividad$HELPICON</td>
  <td class="form">
    $actsel
  </td>
</tr>
<tr class="ayuda" id="actividad_help" >
  <td colspan=2>Actividad a la que asistió.</td>
</tr>

<tr class="field">
  <td class="campo" id="boletaid">Código de la boleta$HELPICON</td>
  <td class="form">
    <input type="text" name="boletaid" value="$boletaid">
  </td>
</tr>
<tr class="ayuda" id="boletaid_help" >
  <td colspan=2>Identificador alfanumérico de la boleta.</td>
</tr>

<tr class="field">
  <td class="campo" id="numero">Número de la boleta$HELPICON</td>
  <td class="form">
    <input type="text" name="numero" value="$numero">
  </td>
</tr>
<tr class="ayuda" id="numero_help" >
  <td colspan=2>Número de la boleta.</td>
</tr>

<tr class="field">
  <td colspan=2 class="botones_simple">
    <input type="submit" name="action" value="Registrar">
    <input type="submit" name="action" value="Salir">
  </td>
</tr>

</table>
</center>
C;

    }
    goto end;
  }

  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //CONSULTAR ASISTENCIA
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  else if($mode=="consultar" or $mode=="consultado"){
    
$content.=<<<C
<h4>Consulta de Asistencias</h4>
C;

    if($mode=="consultar"){

$content.=<<<C
<form action="comaca.php?loadact" method="post" enctype="multipart/form-data" accept-charset="utf-8">

<table border=0px cellspacing=0px>

<tr class="field">
  <td class="campo" id="semestre">Semestre$HELPICON</td>
  <td class="form">
    <input type="text" name="semestre" value="2016-2">
  </td>
</tr>
<tr class="ayuda" id="semestre_help" >
  <td colspan=2>Semestre en el que se realiza la consulta.</td>
</tr>

<tr class="field">
  <td class="campo" id="documentos">Documento(s)$HELPICON</td>
  <td class="form">
    <input type="text" name="documentos" value="$DOCUMENTO">
  </td>
</tr>
<tr class="ayuda" id="documentos_help" >
  <td colspan=2>Indique los documentos de los estudiantes que quiere consultar separados por ",".</td>
</tr>

<tr class="field">
  <td colspan=2 class="botones_simple">
    <input type="submit" name="action" value="Consultar">
    <input type="submit" name="action" value="Salir">
  </td>
</tr>
</table>

</form>
C;

    }
    if($mode=="consultado"){

$content.=<<<C
  <h5>Semestre $semestre</h5>
  <table border=1px cellspacing=0>
    <thead>
      <tr>
	<td>Documento</td>
C;

       foreach($TIPOS_ACTIVIDAD as $Tipo){
	 
$content.=<<<C
        <td>$Tipo</td>
C;
       }
$content.=<<<C
	<td>Total</td>
	<td>Umbrales</td>
	<td>Nota</td>
    </tr></thead>
C;

       foreach($documentos as $documento){
    
         //GENERATE DETAILS
	 $detalles="";
	 foreach($asistencias["$documento"] as $asistencia){
	   $actid=$asistencia["Actividades_actid"];
	   $actividad=$actividades["$actid"];
	   if($asistencia["tarde"]>0){$tardetxt="(reportada tarde)";}
	   else{$tardetxt="";}
	   $info=$actividad["fechafin"].",".$actividad["horafin"].": ".$actividad["tipo"]." '".$actividad["nombre"]."' ".$tardetxt;
	   $detalles.=$info."<br/>";
	 }

$content.=<<<C
    <tr>
      <td>
	<a href="JavaScript:void(null)" onclick="$('#detalles_$documento').toggle()">
	  $documento
	</a>
	<div id="detalles_$documento" style="display:none">
	  $detalles
	</div>
      </td>
C;

            $tot=0;
            $numbrales=0;
            foreach(array_keys($TIPOS_ACTIVIDAD) as $tipo){
	        $n=$numasistencias["$documento"]["$tipo"];
                if($n>=$UMBRALES_ACTIVIDAD["$tipo"]){
	           $numbrales++;
	        }
		$tot+=$n;
$content.=<<<C
      <td>
	$n
      </td>
C;
	     }

             $nota=($numbrales/4)*5;
      
$content.=<<<C
      <td>$tot</td>
      <td>$numbrales</td>
      <td>$nota</td>
    </tr>
C;
       }

$content.=<<<C
  </table>
C;
    }

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
