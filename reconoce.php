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
  | <a href="?mode=lista">Lista</a>
  | <a href="?mode=edit&action=common">Nuevo</a> 
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
    if(strlen($ERROR)==0){
      //READ DATA
      $fl=fopen($recfile,"r");
      $object=fread($fl,filesize($recfile));
      $data=unserialize($object);
      fclose($fl);
      //COMBINE DATA
      $_GET=array_merge($_GET,$data);
      $_POST=array_merge($_POST,$data);
      foreach(array_keys($_GET) as $field){$$field=$_GET[$field];}
      foreach(array_keys($_POST) as $field){$$field=$_POST[$field];}
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
  $selprogramas=generateSelection($programas,"planid",$planid);
  $programas["other"]="Otro";
  $cselprogramas=generateSelection($programas,"planid",$cplanid);

  //////////////////////////////////////////
  //GET COURSES THAT CHANGED
  //////////////////////////////////////////
  $content.="<script>$(document).ready(function(){";

  $qelements=array();
  $qasignaturas=array();
  $qmaterias=array();
  $qchecked=array();
  foreach(array_keys($_GET) as $key){
    if(preg_match("/^asignatura_/",$key)){
      array_push($qasignaturas,$key);
    }
    if(preg_match("/^materia_/",$key)){
      array_push($qmaterias,$key);
    }
    if(preg_match("/^q(\w+)/",$key,$matches)){
      $varname=$matches[1];
      if($_GET["$key"]>0){
	$qchecked["$varname"]=1;
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
      $$nsel=generateSelection($cursos,"$asignatura",$scurso);
    }
    else{
      $scurso="--";
      $nsel="selasignatura_${section}_${ncourse}";
      $$nsel=generateSelection($cursos,"$asignatura",$scurso);
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
	$$nsel=generateSelection($ccursos,"$name",$smateria);
      }
      else{
	$name="s".$materia;
	$smateria="--";
	$nsel="selmateria_${section}_${ncourse}";
	$$nsel=generateSelection($ccursos,"$name",$smateria);
      }
    }
  }

  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //GUARDAR DATA
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  if($action=="Cancelar"){
    statusMsg("Edición cancelada.");
    goto endaction;
  }
  if($action=="Guardar" or
     $action=="Revisado" or
     $action=="Aprobado"
     ){
    //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
    //BASIC CHECKS
    //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
    if(!$qchecked["reconocimiento_1"]){
      errorMsg("Debe proveer al menos un reconocimiento");
      $mode="edit";
    }
    else if(!$qchecked["materia_1_1"]){
      errorMsg("Debe proveer al menos una materia");
      $mode="edit";
    }
    else if(!$qchecked["asignatura_1_1"]){
      errorMsg("Debe proveer al menos una asignatura para reconocer");
      $mode="edit";
    } 
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
    $_GET["status"]=$status;
  
    if(strlen($ERRORS)==0){

      //STORING RESULTS ON DISK
      if(!isset($recfile)){
	$recid=generateRandomString(6);
	$recdir="$ROOTDIR/data/recon/${documento}_${planid}_${recid}";
	$recfile="$recdir/recon.dat";
	if(!is_dir($recdir)){shell_exec("mkdir -p $recdir");}
      }

      //UNSET VARIABLES
      unset($_GET["action"]);
      unset($_GET["mode"]);

      //SAVE SERIALIZED ARRAY
      $fl=fopen($recfile,"w");
      fwrite($fl,serialize($_GET));
      fclose($fl);

      //SET STATUS
      if($action=="Guardar"){$status=0;}
      if($action=="Revisada"){$status=1;}
      if($action=="Aprobada"){$status=2;}

      //UPDATING STUDENTS DATABASE
      insertSql("Estudiantes",array("documento"=>"",
				    "nombre"=>"",
				    "email"=>""));
      //UPDATING RECONOCIMIENTOS
      insertSql("Reconocimientos",array("recid"=>"",
					"fecha"=>"date",
					"acto"=>"",
					"responsables"=>"",
					"status"=>"",
					"Planes_planid"=>"planid",
					"Estudiantes_documento"=>"documento"));

      //SEND EMAIL
      if($action=="Aprobado"){
        $Plan=mysqlCmd("select * from Planes where planid='$planid'");
	$programaid=$Plan["Programas_programaid"];
	$version=$Plan["version"];
	$Programa=mysqlCmd("select * from Programas where programaid='$programaid'");
	$programa=$Programa["programa"];
	$recdir=getRecdir($recid);
	$recbase="$recdir/recon";
	$recurl="$SITEURL/".preg_replace("/^\/.+\/data/","data",$recbase).".pdf";

	$headers="";
	$headers.="From: noreply@udea.edu.co\r\n";
	$headers.="Reply-to: noreply@udea.edu.co\r\n";
	$headers.="MIME-Version: 1.0\r\n";
	$headers.="MIME-Version: 1.0\r\n";
	$headers.="Content-type: text/html\r\n";
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
         sendMail($email,$subject,$message,$headers);
         sendMail($EMAIL_USERNAME,"[Historico] ".$subject,$message,$headers);
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
  <p>Por reconocimiento entendemos el procedimiento en el que una o
  varias asignaturas que fueron matriculadas o aprobadas por un
  estudiante en la Universidad o en otra institución, son reconocidas
  como si las hubiera matriculado y ganado en su programa de estudio
  actual.</p>

  <p>A través de este módulo podrá:</p>

  <ul>

    <li>Crear una solicitud de reconocimiento</li>

    <li>Ver la lista de solicitudes presentadas</li>

    <li>Generar formatos de texto requeridos para el trámite en la Universidad</li>

  </ul>
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
<table width=100% border=1px>
  <thead>
    <tr>
      <th>ID</th>
      <th>Fecha</th>
      <th>Estado</th>
      <th>Documento</th>
      <th>Nombre</th>
      <th>Programa</th>
      <th>Acciones</th>
    </tr>
  </thead>
C;

    $qtable=0;
    $results=mysqlCmd("select * from Reconocimientos",$qout=1);
    if($results){
      $qtable=1;
      $i=1;

      $fields=array("recid","acto","status","fecha","Estudiantes_documento","Planes_planid");
      foreach($results as $result){
	$color="white";
	foreach($fields as $field){
	  $name="l".$field;
	  $$name=$result["$field"];
	}
	$Estudiante=mysqlCmd("select * from Estudiantes where documento='$lEstudiantes_documento'");
	$lnombre=$Estudiante["nombre"];

	$Plan=mysqlCmd("select * from Planes where planid='$lPlanes_planid'");
	$lprogramaid=$Plan["Programas_programaid"];
	$lversion=$Plan["version"];
	$Programa=mysqlCmd("select * from Programas where programaid='$lprogramaid'");
	$lprograma=$Programa["programa"];
	
	if(isBlank($lstatus)){$lstatus=0;}
	if($lstatus==1){$color="pink";}
	if($lstatus==2){$color="lightblue";}
	$lstatus=$RECONSTATUS[$lstatus];
	if(isBlank($lacto)){$lacto="Plataforma";}

	$edit="<a href=?action=load&mode=edit&recid=$lrecid>Editar</a><br/>";
	$delete="<a href=?action=delete&mode=lista&recid=$lrecid>Borrar</a><br/>";
	$preview="<a href=genrecon.php?recid=$lrecid target=_blank>Ver</a><br/>";

      //RENDER TABLE ROW
$table.=<<<C
<tr style="background:$color">
  <td>$lrecid</td>
  <td>$lfecha</td>
  <td>$lstatus<br/><i>$lacto</i></td>
  <td>$lEstudiantes_documento</td>
  <td>$lnombre</td>
  <td>$lprograma (versión $lversion)</td>
  <td>
    $edit
    $delete
    $preview
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

    //INPUT FILE
    if(isset($recfile)){
      $inprecfile="<input type='hidden' name='recfile' value='$recfile'><input type='hidden' name='recid' value='$recid'>";
    }else{
      $inprecfile="";
    }

    if(isset($status)){
      $rstatus=$RECONSTATUS["$status"];
    }
    else{
      $status=0;
      $rstatus="Nuevo";
    }

    //FORM
$content.=<<<C
<center>
<form>
  $inprecfile
  <input type="hidden" name="mode" value="lista">
  <table border="${TBORDER}px" width="${TWIDTH}px">
    <!-- &&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&& -->
    <!-- INFORMACION -->
    <!-- &&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&& -->
    <tr>
      <td colspan=2 class="section">Información Estudiante</td>
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
      <td class="field">Programa al que ingresa:</td>
      <td class="input">
	<select name="planid" onchange="updateCourses(this)">
	  $selprogramas
	</select>
      </td>
    </tr>

    <tr class="reservado"><td colspan=2><hr/><b>Reservado para la Coordinación</b></td></tr>
  
    <tr class="form-field reservado">
      <td class="field">Estado:</td>
      <td class="input">
	$rstatus
	<input type="hidden" name="status" value="$status">
      </td>
    </tr>

    <tr class="form-field reservado">
      <td class="field">Acto administrativo:</td>
      <td class="input"><input type="text" name="acto" value="$acto"></td>
    </tr>

    <tr class="form-field reservado">
      <td class="field">Responsables:</td>
      <td class="input"><input type="text" name="responsables" value="$responsables"></td>
    </tr>

    <!-- &&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&& -->
    <!-- RECONOCIMIENTOS -->
    <!-- &&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&& -->
    <tr>
      <td colspan=2 class="section">Reconocimientos</td>
    </tr>
  
    <tr>
      <td colspan=2>
	<div id="reconocimiento_0" class="agregar">
	  <a href="JavaScript:void(null)" onclick="addRecon(this);">Agregar reconocimiento</a>
	</div>
	$reconocimientos
      </td>
    </tr>
    
    <!-- &&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&& -->
    <!-- BUTTONS -->
    <!-- &&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&& -->
    <tr class="boton">
      <td colspan=2>
	<input type="submit" name="action" value="Guardar">
	<input type="submit" name="action" value="Cancelar">
	<input type="reset" value="Limpiar">
	<input type="submit" name="action" value="Revisado">
	<input type="submit" name="action" value="Aprobado">
      </td>
    </tr>

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
