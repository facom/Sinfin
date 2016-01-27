<html>
<?php
////////////////////////////////////////////////////////////////////////
//LOAD LIBRARY
////////////////////////////////////////////////////////////////////////
$HOST=$_SERVER["HTTP_HOST"];
$SCRIPTNAME=$_SERVER["SCRIPT_FILENAME"];
$ROOTDIR=rtrim(shell_exec("dirname $SCRIPTNAME"));
require("$ROOTDIR/etc/library.php");
//echo "QUERY:<br/>".$_SERVER["QUERY_STRING"]."";

////////////////////////////////////////////////////////////////////////
//LOAD
////////////////////////////////////////////////////////////////////////
if($action=="load"){
  if(!isset($recfile)){
    errorMsg("Debe proveer un archivo para cargar los datos");
  }
  if(strlen($ERROR)==0){
    $fl=fopen($recfile,"r");
    $object=fread($fl,filesize($recfile));
    $_GET=unserialize($object);
    fclose($fl);
    $_GET["recfile"]=$recfile;
    foreach(array_keys($_GET) as $field){
      $$field=$_GET[$field];
    }
    foreach(array_keys($_POST) as $field){
      $$field=$_POST[$field];
    }

    statusMsg("Datos cargados...");
  }
}

////////////////////////////////////////////////////////////////////////
//INITIALIZATION
////////////////////////////////////////////////////////////////////////
$content="";
$content.=<<<C
<head>
  <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
  <link rel="stylesheet" href="lib/jquery-ui/jquery-ui.min.css">
  <link rel="stylesheet" href="css/sinfin.css" />
  <script src="lib/jquery-ui/jquery.min.js"></script>
  <script src="lib/jquery-ui/jquery.min.js"></script>
  <script src="lib/jquery-ui/jquery-ui.min.js"></script>
  <script src="lib/jquery-ui/moment.min-locales.js"></script>
  <script src="lib/jquery-ui/moment.min-locales.js"></script>
  <script src="js/sinfin.js"></script>
</head>
<body>
C;

////////////////////////////////////////////////////////////////////////
//LOAD FIELDS
////////////////////////////////////////////////////////////////////////
$qelements=array();
$content.=<<<C
<script>
$(document).ready(function(){
C;
$qasignaturas=array();
$qchecked=array();
$qmaterias=array();
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
$content.=<<<C
  $('#i$varname').show();
C;
    }else{
      $qchecked["$varname"]=0;
    }
  }
}

if(isset($cplanid)){
  if($cplanid!="other"){
$content.=<<<C
  $('.ccursos').show();
  $('.ccursos_input').hide();
C;
  }   
}

if($qchecked["materia_1_1"]){
$content.=<<<C
  $('#materia_1_0').hide();
C;
}
if($qchecked["asignatura_1_1"]){
$content.=<<<C
  $('#asignatura_1_0').hide();
C;
}

$content.=<<<C
});
</script>
C;

////////////////////////////////////////////////////////////////////////
//ACTIONS
////////////////////////////////////////////////////////////////////////

//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
//SUBMIT
//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
if(isset($submit)){
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //BASIC CHECKS
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  if(!$qchecked["reconocimiento_1"]){
    errorMsg("Debe proveer al menos un reconocimiento");
  }
  else if(!$qchecked["materia_1_1"]){
    errorMsg("Debe proveer al menos una materia");
  }
  else if(!$qchecked["asignatura_1_1"]){
    errorMsg("Debe proveer al menos una asignatura para reconocer");
  } 
  else if($nota_1_1+0==0){
    errorMsg("Debe proveer una nota para la materia a reconocer");
  }
  if(isBlank($cedula)){
    errorMsg("Debe proveer un documento de identificación");
  }
  if(isBlank($universidad)){
    errorMsg("Debe proveer el nombre de una institución");
  }
  if($planid=="--"){
    errorMsg("Debe escoger un plan de estudios al que llega");
  }
  if($cplanid=="--"){
    errorMsg("Debe escoger un plan de estudios del que salió");
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
    unset($_GET["submit"]);

    //SAVE SERIALIZED ARRAY
    $fl=fopen($recfile,"w");
    fwrite($fl,serialize($_GET));
    fclose($fl);

    //SHOW STATUS
    statusMsg("Reconocimiento almacenado en $recfile...");
  }
}

////////////////////////////////////////////////////////////////////////
//CONTENT
////////////////////////////////////////////////////////////////////////

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//ERRORS
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
if(strlen($ERRORS)>0){
$content.=<<<C
<div class="error">
  $ERRORS
</div>
C;
}
if(strlen($STATUS)>0){
$content.=<<<C
<div class="status">
  $STATUS
</div>
C;
}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//PROGRAMS
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
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


//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//COURSES
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
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
      $$nsel=generateSelection($materias,"$name",$smateria);
    }
  }
}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//LISTA
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
exec("ls $RECONDIR",$dirs);
foreach($dirs as $dir){
  $qrecfile="$RECONDIR/$dir/recon.dat";
  $urecfile=urlencode($qrecfile);
  echo "<a href='?action=load&recfile=$urecfile'>$qrecfile</a><br/>";
}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//RECFILE
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
if(isset($recfile)){
  $inprecfile="<input type='hidden' name='recfile' value='$recfile'>";
 }else{
  $inprecfile="";
 }

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//RECONOCIMIENTOS
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
$reconocimientos=generateReconocimientos();

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//FORM
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
$content.=<<<C
<h1><a href="reconoce.php">Reconocimientos</a></h1>
<form>
  $inprecfile
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
	<input type="submit" name="submit" value="Enviar">
	<input type="reset" value="Cancelar">
      </td>
    </tr>

  </table>
</form>
C;

////////////////////////////////////////////////////////////////////////
//RENDER
////////////////////////////////////////////////////////////////////////
echo $content;
?>
</body>
</html>
