<html>
<?php
////////////////////////////////////////////////////////////////////////
//LOAD LIBRARY
////////////////////////////////////////////////////////////////////////
$HOST=$_SERVER["HTTP_HOST"];
$SCRIPTNAME=$_SERVER["SCRIPT_FILENAME"];
$ROOTDIR=rtrim(shell_exec("dirname $SCRIPTNAME"));
require("$ROOTDIR/etc/library.php");
//echo "<div style='font-size:0.8em'>".$_SERVER["QUERY_STRING"]."</div>";

////////////////////////////////////////////////////////////////////////
//INITIALIZATION
////////////////////////////////////////////////////////////////////////
$content="";
$content.=getHeaders();
$content.=getHead();
$content.=getMainMenu();

////////////////////////////////////////////////////////////////////////
//SUBMENU
////////////////////////////////////////////////////////////////////////
$content.=<<<M
<div class="moduletitle">
  Modulo de Reconocimientos
</div>
<div class="submenu">
  <a href="?">Inicio</a> 
  <span class="level1">| <a href="?mode=lista">Lista</a></span>
  <span class="level1">| <a href="?mode=edit&action=common">Nuevo</a></span>
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
  //LOAD DATA
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  if($action=="load"){

    if(!($recdir=getRecdir($recid))){
      errorMsg("No se encontro el reconocimiento '$recid'");
    }else{    
      $recfile=$recdir."/recon.dat";
      if(!isset($recfile)){
	errorMsg("Debe proveer un archivo para cargar los datos");
      }
    }
    if(strlen($ERRORS)==0){
      //READ DATA
      if(file_exists($recfile)){
	$fl=fopen($recfile,"r");
	$object=fread($fl,filesize($recfile));
	$data=unserialize($object);
	fclose($fl);
      }else{
	errorMsg("File '$recfile' not found.");
	goto endaction;
      }

      //COMBINE DATA
      $_GET=array_merge($_GET,$data);
      $_POST=array_merge($_POST,$data);
      foreach(array_keys($_GET) as $field){$$field=$_GET[$field];}
      foreach(array_keys($_POST) as $field){$$field=$_POST[$field];}

      if($status<=2){
	if($QPERMISO<=2){
	  errorMsg("Usted no tiene permisos para editar esta solicitud");
	  $mode="lista";
	  goto endaction;
	}
      }

      statusMsg("Datos cargados...");
    }
  }
  if($action=="delete"){
    if(!($recdir=getRecdir($recid))){
      errorMsg("No se encontro el reconocimiento '$recid'");
    }else{
      $recfile=$recdir."/recon.dat";
      if(!isset($recfile)){
	errorMsg("No se encontro el archivo de reconocimiento para borrarlo");
      }
    }
    if(strlen($ERRORS)==0){
      if($QPERMISO<=1){
	errorMsg("Usted no tiene permisos para eliminar esta solicitud");
	$mode="lista";
	goto endaction;
      }
      //REMOVE FILE
      shell_exec("mv \$(dirname $recfile) trash;");
      statusMsg("Reconocimiento '$recid' borrado...");
      //REMOVE DATABASE ENTRY
      mysqlCmd("delete from Reconocimientos where recid='$recid';");
    }
    goto endaction;
  }

  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //COMMON CODE
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //////////////////////////////////////////
  //PROGRAMS
  //////////////////////////////////////////
  $results_programs=mysqlCmd("select programaid,programa from Programas",$qout=1);
  $programas=array("--"=>"--");
  foreach($results_programs as $program){
    $code=$program["programaid"];
    $name=$program["programa"];
    $results_versions=mysqlCmd("select version,modificacion from Planes where Programas_programaid='$code' order by modificacion desc",$qout=1);
    $versions="";
    foreach($results_versions as $version){
      $ver=$version["version"];
      if(!preg_match("/$ver;/",$versions)){
	$mod=$version["modificacion"];
	$pid="$code-v$ver-m$mod";
	$programas["$pid"]="$name / version $ver / modificación $mod / $pid";
      }
      $versions.="$ver;";
    }
  }
  $selprogramas=generateSelectionOptions($programas,"planid",$planid);
  $programas["other"]="Otro";
  $cselprogramas=generateSelectionOptions($programas,"planid",$cplanid);

  //////////////////////////////////////////
  //INSTITUTO
  //////////////////////////////////////////
  $selinstituto=generateSelectionOptions($INSTITUTOS,"instituto",$instituto);

  //////////////////////////////////////////
  //GET COURSES THAT CHANGED
  //////////////////////////////////////////
  $content.="<script>$(document).ready(function(){";

  $qelements=array();
  $qasignaturas=array();
  $qmaterias=array();
  $qchecked=array();
  $nqreconocimientos=0;
  $nqmaterias=0;
  $nqasignaturas=0;
  foreach(array_keys($_POST) as $key){
    if(preg_match("/^asignatura_/",$key)){
      array_push($qasignaturas,$key);
    }
    if(preg_match("/^materia_/",$key)){
      array_push($qmaterias,$key);
    }
    if(preg_match("/^q(\w+)/",$key,$matches)){
      $varname=$matches[1];
      if($_POST["$key"]>0){
	$qchecked["$varname"]=1;
	if(preg_match("/reconocimiento/",$varname)){
	  $nqreconocimientos++;
	}
	if(preg_match("/materia/",$varname)){$nqmaterias++;}
	if(preg_match("/asignatura/",$varname)){$nqasignaturas++;}

	$content.="$('#i$varname').show();";
	$parts=preg_split("/_/",$varname);
	$type=$parts[0];
	if($type=="materia"){$varname="smateria_".$parts[1]."_".$parts[2];}
	$codigo=$$varname;
	if($codigo=="000000:0"){
	  $parts=preg_split("/_/",$varname);
	  $section=$parts[1];
	  $num=$parts[2];
	  $content.="$('#sm${type}_${section}_${num}').show();";
	}
      }else{
	$qchecked["$varname"]=0;
      }
    }
  }
  //SHOW AND HIDE ELEMENTS ACCORDING TO TYPE OF RECONOCIMIENTO
  if(isset($cplanid)){
    if($cplanid!="other"){
      $content.="$('.ccursos').show();$('.ccursos_input').hide();";
    }   
  }
  if($qchecked["materia_1_1"]){
    $content.="$('#materia_1_0').hide();";
  }
  if($qchecked["asignatura_1_1"]){
    $content.="$('#asignatura_1_0').hide();";
  }
  $content.="});</script>";

  //////////////////////////////////////////
  //SELECTION TABLE FOR COURSES
  //////////////////////////////////////////
  //TO SELECT TARGET COURSE
  $cursos=updateCursos($planid);
  foreach($qasignaturas as $asignatura){
    $parts=preg_split("/_/",$asignatura);
    $section=$parts[1];
    $ncourse=$parts[2];
    $checked=$qchecked["$asignatura"];
    if($checked){
      $scurso=$$asignatura;
      $parts=preg_split("/:/",$$asignatura);
      $ncred="creditos_${section}_${ncourse}";
      //$$ncred=$parts[1];
      $nsel="selasignatura_${section}_${ncourse}";
      $$nsel=generateSelectionOptions($cursos,"$asignatura",$scurso);
    }
    else{
      $scurso="--";
      $nsel="selasignatura_${section}_${ncourse}";
      $$nsel=generateSelectionOptions($cursos,"$asignatura",$scurso);
    }
  }

  //TO SELECT SOURCE COURSE
  if($cplanid!="other" and
     $cplanid!="--"){
    $ccursos=updateCursos($cplanid);
    foreach($qmaterias as $materia){
      $parts=preg_split("/_/",$materia);
      $section=$parts[1];
      $ncourse=$parts[2];
      $checked=$qchecked["$materia"];
      if($checked){
	$name="s".$materia;
	$smateria=$$name;
	$nsel="selmateria_${section}_${ncourse}";
	$$nsel=generateSelectionOptions($ccursos,"$name",$smateria);
      }
      else{
	$name="s".$materia;
	$smateria="--";
	$nsel="selmateria_${section}_${ncourse}";
	$$nsel=generateSelectionOptions($ccursos,"$name",$smateria);
      }
    }
  }

  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //CANCELAR 
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  if($action=="Cancelar"){
    statusMsg("Edición cancelada.");
    goto endaction;
  }
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //GUARDAR DATA
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  if($action=="Guardar" or
     $action=="Revisado" or
     $action=="Realizado" or
     $action=="Aprobado" or
     $action=="Solicitar" or
     $action=="Rechazado"
     ){
    //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
    //BASIC CHECKS
    //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
    if(!$nqreconocimientos){
      errorMsg("Debe proveer al menos un reconocimiento");
      $mode="edit";
    }
    else if(!$nqmaterias){
      errorMsg("Debe proveer al menos una materia");
      $mode="edit";
    }
    /*
    //No se requiere para que el estudiante pueda entrar el reconocimiento
    else if(!$nqasignaturas){
      errorMsg("Debe proveer al menos una asignatura para reconocer");
      $mode="edit";
    }
    */
    else if($nota_1_1+0==0){
      errorMsg("Debe proveer una nota para la materia a reconocer");
      $mode="edit";
    }
    if(isBlank($documento)){
      errorMsg("Debe proveer un documento de identificación");
      $mode="edit";
    }
    if(isBlank($universidad)){
      errorMsg("Debe proveer el nombre de una institución");
      $mode="edit";
    }
    if($planid=="--"){
      errorMsg("Debe escoger un plan de estudios al que llega");
      $mode="edit";
    }
    if($cplanid=="--"){
      errorMsg("Debe escoger un plan de estudios del que salió");
      $mode="edit";
    }
    if($action=="Revisado"){
      if(isBlank($responsables)){
	errorMsg("Debe proveer un nombre o lista de nombres de responsables");
	$mode="edit";
      }else{
	$status=1;
      }
    }
    if($action=="Aprobado"){
      if(isBlank($acto)){
	errorMsg("Debe proveer un acto administrativo");
	$mode="edit";
      }else{
	$status=2;
      }
    }
    if($action=="Rechazado"){
      $status=4;
      //SEND A NOTIFICATION MESSAGE
      $subject="[SInfIn] Reconocimiento de Materias '$recid' Rechazado";
$message=<<<M
<p>
  Señor(a) Estudiante,
</p>
<p>
  Su solicitud de reconocimiento radicada en $SINFIN y presentada a nombre de <b>$nombre</b>
  (ID <b>$documento</b>), e-mail <b>$email</b>, ha sido revisada y rechazada.
</p>
<p>
  Estas son las observaciones realizadas por el revisor:
  <blockquote style="color:red;font-style:italic">
  $observaciones
  </blockquote>
</p>
<p>
  Modifique la solicitud en correspondencia a las observaciones
  realizadas o comience una nueva solicitud si así lo sugiere el
  revisor.
</p>
<p>
  Recuerde ver completamente el videotutorial que encontrará en
  <a href="http://www.youtube.com/watch?v=O85cGBINggU">este
  enlace</a>.
<p>
  <b>Sistema Integrado de Información Curricular</b><br/>
</p>
M;
      sendMail($email,$subject,$message,$EHEADERS);
      sendMail($EMAIL_USERNAME,"[Historico] ".$subject,$message,$EHEADERS);
      statusMsg("Notificación de rechazo enviada a $email");
    }
    if($action=="Solicitar"){
      //SEND A NOTIFICATION MESSAGE
      $subject="[SInfIn] Reconocimiento de Materias '$recid' Solicitado";
$message=<<<M
<p>
  Señor(a) Coordinador,
</p>
<p>
  Una nueva solicitud de reconocimiento ha sido radicada en $SINFIN.
  La solicitud ha sido presentada a nombre de <b>$nombre</b>
  (ID <b>$documento</b>), e-mail <b>$email</b>.
</p>
<p>
  Conéctese y proceda con la revisión y eventual aprobación de la solicitud.
</p>
<p>
  <b>Sistema Integrado de Información Curricular</b><br/>
</p>
M;
      sendMail($EMAIL_USERNAME,$subject,$message,$EHEADERS);
      statusMsg("Notificación de solicitud enviada a la coordinación");
    }

    $_POST["status"]=$status;
    if(strlen($ERRORS)==0){
      
      //GENERATE recid
      $recdir="$ROOTDIR/data/recon/${documento}_${planid}_${recid}";
      $recfile="$recdir/recon.dat";

      //STORING RESULTS ON DISK
      if(!file_exists($recfile)){
	$recdir="$ROOTDIR/data/recon/${documento}_${planid}_${recid}";
	$recfile="$recdir/recon.dat";
	if(!is_dir($recdir)){shell_exec("mkdir -p $recdir");}
	statusMsg("Nuevo reconocimiento creado");
      }

      //GET INSTITUTO
      if(isBlank($instituto)){
	preg_match("/(\d+)-/",$planid,$matches);
	$programaid=$matches[1];
	$results=mysqlCmd("select instituto from Programas where programaid='$programaid'");
	$instituto=$results["instituto"];
	$_POST["instituto"]=$instituto;
      }

      //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
      //UPLOAD FILE
      //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
      $n=0;
      foreach(array_keys($_FILES) as $key){
	$file=$_FILES["$key"];
	$filename=$file["name"];
	if(!isBlank($filename)){
	  preg_match("/(.+)\.(\w+)$/",$filename,$matches);
	  $filebase=$matches[1];
	  $fileext=$matches[2];
	  $tmp=$file["tmp_name"];
	  $filefinal="$key.$fileext";
	  shell_exec("cp -rf $tmp $recdir/$filefinal");
	  $_POST["$key"]=$filefinal;
	  $n++;
	}
      }
      statusMsg("$n archivos subidos...");

      //UNSET VARIABLES
      unset($_POST["action"]);
      unset($_POST["mode"]);
      $_POST["recid"]=$recid;

      //SET STATUS
      $qnotificado=0;
      if($action=="Solicitar"){$status=0;}
      if($action=="Guardar"){$status=3;}
      if($action=="Revisado"){$status=1;}
      if($action=="Aprobado"){
	$status=2;
	if(!isset($notificado) or isBlank($notificado)){
	  $notificado=$DATE;
	}else{
	  $qnotificado=1;
	}
      }
      if(!isset($notificado)){
	$notificado="";
      }
      $_POST["notificado"]=$notificado;

      //SAVE SERIALIZED ARRAY
      $fl=fopen($recfile,"w");
      fwrite($fl,serialize($_POST));
      fclose($fl);

      //UPDATING STUDENTS DATABASE
      insertSql("Estudiantes",array("documento"=>"",
				    "nombre"=>"",
				    "email"=>"",
				    "universidad"=>""));

      //UPDATING RECONOCIMIENTOS
      insertSql("Reconocimientos",array("recid"=>"",
					"fecha"=>"date",
					"fechahora"=>"DATE",
					"acto"=>"",
					"responsables"=>"",
					"status"=>"",
					"notificado"=>"",
					"instituto"=>"",
					"Planes_planid"=>"planid",
					"Estudiantes_documento"=>"documento"));

      //SEND EMAIL
      if($action=="Aprobado" and !$qnotificado){
        $Plan=mysqlCmd("select * from Planes where planid='$planid'");
	$programaid=$Plan["Programas_programaid"];
	$version=$Plan["version"];
	$Programa=mysqlCmd("select * from Programas where programaid='$programaid'");
	$programa=$Programa["programa"];
	$recdir=getRecdir($recid);
	$recbase="$recdir/recon";
	$recurl="$SITEURL/".preg_replace("/^\/.+\/data/","data",$recbase).".pdf";

	$subject="[SInfIn] Reconocimiento de Materias Aprobado";
$message=<<<M
<p>
  Señor(a) Estudiante,
</p>
<p>
  La Coordinación de Pregrado ha analizado una solicitud de materias
  radicada a nombre de <b>$nombre</b> (documento de
  identidad <b>$documento</b>) para el programa de
  pregrado <b>$programa</b> (versión <b>$version</b>).
</p>
<p>
  Puede encontrar los detalles de los reconocimientos en este documento:
  <a href="$recurl" target="_blank">Formato de reconocimientos diligenciado</a>.
</p>
<p>
  Su solicitud ha sido entregada al Departamento de Admisiones y
  Registro para que la procesen.  En este momento el trámite esta en
  manos de ellos. Solo a través suyo usted puede personalmente
  averiguar el estado del proceso a partir de ahora.
</p>
<p>
  Si tiene inquietudes sobre el resultado de la solicitud no dude en
  contactar su coordinación de pregrado y preguntar por el caso <b>$recid</b>
</p>
<p>Atentamente,</p>
<p>
  <b>Coordinación de Pregrado</b><br/>
  <b>Facultad de Ciencias Exactas y Naturales</b>
</p>
M;
         sendMail($email,$subject,$message,$EHEADERS);
         sendMail($EMAIL_USERNAME,"[Historico] ".$subject,$message,$EHEADERS);
	 statusMsg("Mensaje enviado a $email y $EMAIL_USERNAME");
      }

      //SHOW STATUS
      statusMsg("Reconocimiento $recid almacenado...");
    }
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
  <p>Este es el módulo de reconocimientos de $SINFIN.  Por
  reconocimiento entendemos el procedimiento en el que una o varias
  asignaturas que fueron matriculadas o aprobadas por un estudiante en
  la Universidad o en otra institución, son reconocidas como si las
  hubiera matriculado y ganado en su programa de estudio actual.</p>

  <p>A través de este módulo podrá:</p>

  <ul>

    <li>Crear una solicitud de reconocimiento</li>

    <li>Ver la lista de solicitudes presentadas</li>

    <li>Generar formatos de texto requeridos para el trámite en la Universidad</li>

  </ul>

  <p>
    Para acceder a todos los servicios de este módulo
    debe <a href=usuarios.php?mode=nuevo>crear una cuenta de
    usuario</a> o <a href=usuarios.php>iniciar sesión con una cuenta
    existente</a>.
  </p>

  <p>Encuentre abajo un video tutorial sobre como presentar solicitudes de reconocimiento</p>

  <p style="text-align:center">
    <iframe width="$WIDTHVID" height="$HEIGHTVID"
            src="https://www.youtube.com/embed/O85cGBINggU"
            frameborder="0" allowfullscreen>
    </iframe>

  </p>

C;

}
else{

  if(0){}
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //REFRESH (EXPERIMENTAL)
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  else if($mode=="refresh"){
    //$content.="<script>window.location.href='reconoce.php';</script>";
    goto end;
  }

  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //REFRESH (EXPERIMENTAL)
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  else if($mode=="lista"){

    $content.="<h2>Solicitudes de Reconocimiento de Materias</h2>";
    $table="";
$table.=<<<C
<table class="level1" width=100% border=1px style="font-size:12px">
  <thead>
    <tr>
      <th>ID</th>
      <th>Documento</th>
      <th>Nombre</th>
      <th>Fecha</th>
      <th>Instituto</th>
      <th>Estado</th>
      <th>Universidad</th>
      <th>Programa</th>
      <th>Acciones</th>
    </tr>
  </thead>
C;

    $qtable=0;

    $where="";
    if($QPERMISO>=1){
      $seleccion="<p class='level1'><b>Selección</b>: ";
    }else{
      $seleccion="<p><i>Usted no tiene permisos para ver esta lista</i></p>";
    }
    if(isset($DOCUMENTO)){
      //REGULAR USER
      if($QPERMISO<=1){
	$where="where Estudiantes_documento='$DOCUMENTO'";
	$seleccion.="documento $DOCUMENTO<br/>";
      }else
      //COORDINADOR
      if($QPERMISO==3){
	if(preg_match("/instituto=(\w+);/",$PARAMETROS,$matches)){
	  $instituto=$matches[1];
	  $where="where instituto='$instituto'";
	  $seleccion.="instituto $instituto<br/>";
	}else{
	  $where="Estudiantes_documento=''";
	  $seleccion.="<i>No parametros</i><br/>";
	}
      }
      //ADMINISTRADOR
      if($QPERMISO==4){
	$where="";
	$seleccion.="<i>Todos</i>";
      }
    }
    $seleccion.="</p>";
    $content.="$seleccion";

    $results=mysqlCmd("select * from Reconocimientos $where order by status,fechahora desc,Estudiantes_documento asc",$qout=1);

    if($results){
      $qtable=1;
      $i=1;

      $fields=array("recid","acto","status","fecha","Estudiantes_documento","Planes_planid","notificado","instituto");
      foreach($results as $result){
	$color="lightgray";
	foreach($fields as $field){
	  $name="l".$field;
	  $$name=$result["$field"];
	}
	$Estudiante=mysqlCmd("select * from Estudiantes where documento='$lEstudiantes_documento'");
	$lnombre=$Estudiante["nombre"];
	$luniversidad=$Estudiante["universidad"];
	if(isBlank($luniversidad)){$luniversidad="--";}

	$Plan=mysqlCmd("select * from Planes where planid='$lPlanes_planid'");
	$lprogramaid=$Plan["Programas_programaid"];
	$lversion=$Plan["version"];
	$Programa=mysqlCmd("select * from Programas where programaid='$lprogramaid'");
	$lprograma=$Programa["programa"];
	
	if(isBlank($lstatus)){$lstatus=0;}
	$lstatus=$RECONSTATUS[$lstatus];
	if($lstatus=="Revisado"){$color="pink";}
	if($lstatus=="Solicitado"){$color="yellow";}
	if($lstatus=="Rechazado"){$color="white";}
	if($lstatus=="Aprobado"){
	  $color="lightblue";
	  if(!isBlank($lnotificado)){
	    $color="lightgreen";
	    $lstatus="Notificado";
	  }
	}
	if(isBlank($lacto)){$lacto="Plataforma";}

	if($lstatus=="Editado" or $lstatus=="Rechazado" or 
	   $QPERMISO>1){
	  $edit="<a href=?action=load&mode=edit&recid=$lrecid>Editar</a><br/>";
	}else{$edit="";}

	
	if($lstatus=="Editado" or $lstatus=="Rechazado" or 
	   $QPERMISO>1){
	  $delete="<a class='level1' href=?action=delete&mode=lista&recid=$lrecid>Borrar</a><br/>";
	}else{$delete="";}
	$generar="<a class='level3' href=genrecon.php?recid=$lrecid target=_blank>Generar</a><br/>";
	
	$recdir=getRecdir($lrecid);
	$recbase="$recdir/recon";
	$recurl=preg_replace("/^\/.+\/data/","data",$recbase);

	if(file_exists("$recbase.pdf")){
	  if($lstatus=="Revisado" or $lstatus=="Aprobado" or $lstatus=="Notificado"){
	    $view="<a href=$recurl.pdf target=_blank>Ver</a><br/>";
	  }else{
	    $view="Solicitado<br/>";
	  }
	}else{
	  $view="No disponible<br/>";
	}

      //RENDER TABLE ROW
$table.=<<<C
<tr style="background:$color">
  <td>$lrecid</td>
  <td>$lEstudiantes_documento</td>
  <td>$lnombre</td>
  <td>$lfecha</td>
  <td>$linstituto</td>
  <td>$lstatus<br/><i>$lacto</i></td>
  <td>$luniversidad</td>
  <td>$lprograma (versión $lversion)</td>
  <td>
    $edit
    $view
    $generar
    $delete
  </td>
</tr>
C;
      }
    }

$table.=<<<C
</table>
C;

    if($qtable){
      $content.=$table;
    }else{
      $content.="<i>No hay reconocimientos con este criterio de búsqueda</i>";
    }
    
    goto end;


  }

  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //EDIT
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  else if($mode=="edit"){

    //TABLE OF RECONOCIMIENTOS
    $reconocimientos=generateReconocimientos();

    //RECID
    if(!isset($recid)){
	$recid=generateRandomString(6);
    }

    //RECDIR
    $recdir=getRecdir($recid);
    $recurl="$SITEURL/".preg_replace("/^\/.+\/data/","data",$recdir);

    //INPUT FILE
    if(isset($recfile)){
      $inprecfile="<input type='hidden' name='recfile' value='$recfile'><input type='hidden' name='recid' value='$recid'>";
    }else{
      $inprecfile="";
    }

    if(isset($status)){
      if(!isBlank($status)){
	$rstatus=$RECONSTATUS["$status"];
      }else{
	$status=3;
	$rstatus="Nuevo";
      }	
    }
    else{
      $status=3;
      $rstatus="Nuevo";
    }

    //FORM
$buttons.=<<<B
    <tr class="boton">
      <td colspan=2>
	<input class="level3" type="submit" name="action" value="Revisado">
	<input class="level3" type="submit" name="action" value="Aprobado">
	<input class="level3" type="submit" name="action" value="Realizado">
	<input class="level3" type="submit" name="action" value="Rechazado">
	<input type="submit" name="action" value="Solicitar">
	<input type="submit" name="action" value="Guardar">
	<input type="submit" name="action" value="Cancelar">
      </td>
    </tr>
B;

$content.=<<<C
<center>
$FORM
  $inprecfile
  <input type="hidden" name="notificado" value="$notificado">
  <input type="hidden" name="mode" value="lista">
  <table border="${TBORDER}px" width="${TWIDTH}px">
  $buttons
    <!-- &&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&& -->
    <!-- INFORMACION -->
    <!-- &&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&& -->
    <tr>
      <td colspan=2 class="section">Información Estudiante</td>
    </tr>

    <tr class="form-field">
      <td class="field">ID:</td>
      <td class="input">
        $recid
      </td>
      <input type="hidden" name="recid" value="$recid">
    </tr>

    <tr class="form-field">
      <td class="field">Fecha:</td>
      <td class="input">
	$DATE_ARRAY[0]
	<input type="hidden" name="date" value="$DATE_ARRAY[0]">
      </td>
    </tr>

    <tr class="form-field">
      <td class="field">Documento de identidad:</td>
      <td class="input"><input type="text" name="documento" value="$documento" onchange="updateStudentForm(this)"></td>
    </tr>

    <tr class="form-field">
      <td class="field">Nombre estudiante:</td>
      <td class="input"><input type="text" name="nombre" value="$nombre"></td>
    </tr>

    <tr class="form-field">
      <td class="field">Correo electrónico:</td>
      <td class="input"><input type="text" name="email" value="$email"></td>
    </tr>

    <tr class="form-field">
      <td class="field">Programa en el que vio los cursos:</td>
      <td class="input">
	<select name="cplanid" onchange="activateUniv(this);updatecCourses(this)">
	  $cselprogramas
	</select>
      </td>
    </tr>

    <tr class="form-field">
      <td class="field">Institución de la que proviene:</td>
      <td class="input">
	<input type="text" id="universidad" name="universidad" value="$universidad" onchange="updateUniv(this)">
      </td>
    </tr>

    <tr class="form-field">
      <td class="field">Certificado de notas:</td>
      <td class="input">
	<input type="file" name="certificado_notas"><br/>
	<i class="archivo">Archivo: <a href=$recurl/$certificado_notas target=_blank>$certificado_notas</a></i>
	<input type="hidden" name="certificado_notas" value="$certificado_notas"><br/>
      </td>
    </tr>

    <tr class="form-field">
      <td class="field">Programa al que ingresa:</td>
      <td class="input">
	<select name="planid" onchange="updateCourses(this);updateInstituto(this);">
	  $selprogramas
	</select>
      </td>
    </tr>

    <tr class="form-field">
      <td class="field">Instituto al que ingresa:</td>
      <td class="input">
	<span id="instituto">$instituto</span>
	<input id="instituto_form" type="hidden" name="instituto" value="$instituto">
	<!--
	<select name="instituto">
	  $selinstituto
	</select>
	-->
      </td>
    </tr>

    <tr class="reservado">
      <td style="border-top:solid black 1px;" colspan=2>
	<b>Reservado para la Coordinación</b>
      </td>
    </tr>
  
    <tr class="form-field reservado">
      <td class="field">Estado:</td>
      <td class="input">
        $rstatus
	<input type="hidden" name="status" value="$status">
      </td>
    </tr>

    <tr class="form-field reservado">
      <td class="field level3">Acto administrativo:</td>
      <td class="input level3"><input type="text" name="acto" value="$acto"></td>
    </tr>

    <tr class="form-field reservado">
      <td class="field level3">Responsables:</td>
      <td class="input level3"><input type="text" name="responsables" value="$responsables"></td>
    </tr>

    <tr class="form-field reservado">
      <td class="field level1">Observaciones:</td>
      <td class="input level1"><textarea name="observaciones">$observaciones</textarea></td>
    </tr>

    <!-- &&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&& -->
    <!-- RECONOCIMIENTOS -->
    <!-- &&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&& -->
    <tr>
      <td colspan=2 class="section">Reconocimientos</td>
    </tr>
  
    <tr>
      <td colspan=2>
	<div id="reconocimiento_0" class="agregar" style="background:lightgreen;">
	  <a href="JavaScript:void(null)" onclick="addRecon(this);">Agregar reconocimiento</a>
	</div>
	$reconocimientos
      </td>
    </tr>
    
    <!-- &&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&& -->
    <!-- BUTTONS -->
    <!-- &&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&& -->
  $buttons
  </table>
</form>
</center>
C;
    goto end;
  }

  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //DEFAULT
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  else {
    $content.="Default behavior";
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
</body>
</html>
