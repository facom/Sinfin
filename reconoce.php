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
  | <a href="?">Usuario</a> 
</div>
<div class="container">
M;

////////////////////////////////////////////////////////////////////////
//ACTIVE PART
////////////////////////////////////////////////////////////////////////
if(isset($action)){

  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //LOAD DATA
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  if($action=="load"){
    if(!isset($recfile)){
      errorMsg("Debe proveer un archivo para cargar los datos");
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
    if(!isset($recfile)){
      errorMsg("Debe proveer un archivo para borrarlo");
    }
    if(strlen($ERROR)==0){
      shell_exec("mv \$(dirname $recfile) trash");
      statusMsg("Reconocimiento borrado...");
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
      $$ncred=$parts[1];
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
  //SUBMIT DATA
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  if($action=="Guardar"){
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
    if(isBlank($cedula)){
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
  
    if(strlen($ERRORS)==0){
      //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
      //STORING RESULTS
      //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
      if(!isset($recfile)){
	$recid=generateRandomString();
	$recdir="$ROOTDIR/data/recon/${cedula}_${planid}_${recid}";
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

      //SHOW STATUS
      statusMsg("Reconocimiento almacenado en $recfile...");
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
  <p>¡Bienvenido al modulo de reconocimientos!</p>  

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

$content.=<<<C
<h2>Solicitudes</h2>
<table width=100% border=1px>
  <thead>
    <tr>
      <th>#</th>
      <th>Fecha</th>
      <th>Documento</th>
      <th>Estudiante</th>
      <th>Universidad</th>
      <th>Programa</th>
      <th>Acciones</th>
    </tr>
  </thead>
C;

    exec("ls $RECONDIR",$dirs);
    $i=1;
    $fields=array("date","cedula","nombre","universidad","planid");
    foreach($dirs as $dir){

      //READ DATA 
      $qrecfile="$RECONDIR/$dir/recon.dat";
      $data=readRecon($qrecfile);

      //LOAD DATA IN LOCAL VARIABLES
      foreach($fields as $field){
	$name="l".$field;
	$$name=$data["$field"];
      }
      
      $urecfile=urlencode($qrecfile);
      $edit="<a href=?action=load&mode=edit&recfile=$urecfile>Editar</a><br/>";
      $delete="<a href=?action=delete&mode=lista&recfile=$urecfile>Borrar</a><br/>";
      $preview="<a href=?action=generate&mode=preview&recfile=$urecfile>Ver</a><br/>";

      //RENDER TABLE ROW
$content.=<<<C
<tr>
  <td>$i</td>
  <td>$ldate</td>
  <td>$lcedula</td>
  <td>$lnombre</td>
  <td>$luniversidad</td>
  <td>$lplanid</td>
  <td>
    $edit
    $delete
    $preview
  </td>
</tr>
C;


      //$content.="<a href='?mode=edit&action=load&recfile=$urecfile'>$lnombre - $qrecfile</a><br/>";

      $i++;
    }

$content.=<<<C
</table>
C;


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
      $inprecfile="<input type='hidden' name='recfile' value='$recfile'>";
    }else{
      $inprecfile="";
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
      <td class="field">Nombre estudiante:</td>
      <td class="input"><input type="text" name="nombre" value="$nombre"></td>
    </tr>

    <tr class="form-field">
      <td class="field">Documento de identidad:</td>
      <td class="input"><input type="text" name="cedula" value="$cedula"></td>
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

    <!-- &&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&& -->
    <!-- RECONOCIMIENTOS -->
    <!-- &&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&& -->
    <tr>
      <td colspan=2 class="section">Reconocimientos</td>
    </tr>
  
    <tr>
      <td colspan=2>
	<div id="reconocimiento_0" class="agregar">
	  <a href="JavaScript:void(null)" onclick="addRecon(this)">Agregar reconocimiento</a>
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
	<input type="reset" value="Cancelar">
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
