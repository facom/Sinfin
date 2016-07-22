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
  | <a href="?mode=registrar">Registrar asistencia</a>
  | <a href="?mode=consultar">Consultar asistencia</a>
  <span class="level1">
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
científica</b> para los estudiantes de la Facultad.  La participación
de todos en las actividades organizadas en el seno de esta comunidad
(seminarios, ciclos de conferencias, actividades divulgativas, etc.)
es parte integral del currículo.

</p>

<p>

En este módulo se ofrece información sobre la agenda de actividades de la comunidad académica y acceso a los mecanismos utilizados en algunos programas para hacer seguimiento a la participación en dichas actividades.

</p>

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
	  $value="<a href=$movildir/$value target=_blank>$value</a>";
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
    $table="<center><table border=0px style='font-size:0.8em' cellspacing:0px><caption>Posibles estados de las solicitudes</caption><tr>";
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
      $table.="</td>";

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
    echo "Estado: $estado<br/>";

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
