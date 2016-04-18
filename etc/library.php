<?php
////////////////////////////////////////////////////////////////////////
//EXTERNAL LIBRARIES
////////////////////////////////////////////////////////////////////////
require "lib/PHPMailer/PHPMailerAutoload.php";
session_start();
header("Content-Type: text/html;charset=UTF-8");
//echo "Usuario:".$_SESSION["nombre"];

////////////////////////////////////////////////////////////////////////
//CONFIGURATION
////////////////////////////////////////////////////////////////////////
$USER="sinfin";
$PASSWORD="123";
$DATABASE="Sinfin";
$EMAIL_USERNAME="pregradofisica@udea.edu.co";
$EMAIL_PASSWORD="Gmunu-Tmunu=0";

if(!file_exists(".arch")){
    $out=shell_exec("uname -a");
    if(preg_match("/86_64/",$out)){$arch="64";}
    else{$arch="32";}
}else{$arch=shell_exec("cat .arch");}

//HTML 2 PDF CONVERTER
if($arch==32){$H2PDF="$ROOTDIR/lib/wkhtmltopdf-i386";}
else{$H2PDF="$ROOTDIR/lib/wkhtmltopdf-amd64";}

////////////////////////////////////////////////////////////////////////
//VERIFY IDENTITY
////////////////////////////////////////////////////////////////////////
$QPERMISO=0;
$NOMBRE="Anynymous";
if(isset($_SESSION["permisos"])){
  $QPERMISO=$_SESSION["permisos"];
  $NOMBRE=$_SESSION["nombre"];
  $DOCUMENTO=$_SESSION["documento"];
  $PARAMETROS=$_SESSION["parametros"];
}
$PERMCSS="";
$type="inline";
$perm="$type";
$nperm="none";
if($QPERMISO){
  $perm="none";
  $nperm="$type";
}
$PERMCSS.=".level0{display:$perm;}\n.nolevel0{display:$nperm;}\n";
for($i=1;$i<=4;$i++){
  $perm="none";
  $nperm="$type";
  if($i<=$QPERMISO){$perm="$type";$nperm="none";}
  $PERMCSS.=".level$i{display:$perm;}\n.nolevel$i{display:$nperm;}\n";
}
$PERMCSS.=".level5{display:none;}\n.nolevel5{display:$type;}\n";
//echo "PERMS:<pre>$PERMCSS</pre><br/>";
/*
  Permisos
 */
$PERMISOS=array("0"=>"Anónimo",
		"1"=>"Consulta",
		"2"=>"Profesor",
		"3"=>"Coordinador",
		"4"=>"Administrador");

$INSTITUTOS=array("fisica"=>"Instituto de Física",
		  "biologia"=>"Instituto de Biología",
		  "quimica"=>"Instituto de Química",
		  "quimica"=>"Instituto de Matemáticas");

////////////////////////////////////////////////////////////////////////
//GLOBAL VARIABLES
////////////////////////////////////////////////////////////////////////
foreach(array_keys($_GET) as $field){
    $$field=$_GET[$field];
}
foreach(array_keys($_POST) as $field){
    $$field=$_POST[$field];
}
$TBORDER=0;
$TWIDTH=800;
$TCOLD=$TWIDTH/2;
$ERRORS="";
$STATUS="";
$RECONDIR="data/recon";
$RECONSTATUS=array("Solicitado","Revisado","Aprobado","Editado","Rechazado");
$SINFIN="<b>SInfIn</b>";

$HOST=$_SERVER["HTTP_HOST"];
$FILENAME=$_SERVER["SCRIPT_NAME"];
$SCRIPTNAME=$_SERVER["SCRIPT_FILENAME"];
$BASEDIR=rtrim(shell_exec("dirname $FILENAME"));
$SITEURL="http://$HOST$BASEDIR/";
$REFERER=$_SERVER["HTTP_REFERER"];

$WIDTHVID=400;
$HEIGHTVID=$WIDTHVID/1.4;

/*
echo "COOKIES:";
print_r($_COOKIES);
echo "<br/>SESSION:";
print_r($_SESSION);
*/
//phpinfo();

$EHEADERS="";
$EHEADERS.="From: noreply@udea.edu.co\r\n";
$EHEADERS.="Reply-to: noreply@udea.edu.co\r\n";
$EHEADERS.="MIME-Version: 1.0\r\n";
$EHEADERS.="MIME-Version: 1.0\r\n";
$EHEADERS.="Content-type: text/html\r\n";

$FORM="<form method='post' enctype='multipart/form-data' accept-charset='utf-8'>";
$MANATWORK="<p><center><img src=img/manatwork.png width=10%></center></p>";

////////////////////////////////////////////////////////////////////////
//ROUTINES
////////////////////////////////////////////////////////////////////////
function isBlank($string)
{
  if(!preg_match("/\w+/",$string)){return 1;}
  return 0;
}

function sqlNoblank($out)
{
  $res=mysqli_fetch_array($out);
  $len=count($res);
  if($len==0){return 0;}
  return $res;
}

function errorMessage($msg)
{
$error=<<<E
  <div style=background:lightgray;padding:10px>
    <i style='color:red'>$msg</i>
    </div><br/>
E;
 return $error;
}

function generateSelection($values,$name,$value,$options="",$readonly=0)
{
  $parts=$values;
  $selection="";
  if($readonly){
    $selection.="<input type='hidden' name='$name' value='$value'>";
    $selection.=$value;
    return $selection;
  }
  foreach(array_keys($parts) as $part){
    $show=$parts[$part];
    $selected="";
    if($part==$value){$selected="selected";}
    $selection.="<option value='$part' $selected>$show";
  }
  return $selection;
}

function mysqlCmd($sql,$qout=0)
{
  global $DB,$DATE;
  if(!($out=mysqli_query($DB,$sql))){
    die("Error:".mysqli_error($DB));
  }
  if(!($result=sqlNoblank($out))){
    return 0;
  }
  if($qout){
    $result=array($result);
    while($row=mysqli_fetch_array($out)){
      array_push($result,$row);
    }
  }
  return $result;
}

function generateRandomString($length = 10) {
  $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
  $randomString = '';
  for ($i = 0; $i < $length; $i++) {
    $randomString .= $characters[rand(0, strlen($characters) - 1)];
  }
  return $randomString;
}

function upAccents($string)
{
  $string=strtoupper($string);
  $accents=array("á"=>"Á","é"=>"É","í"=>"Í","ó"=>"Ó","ú"=>"Ú");
  foreach(array_keys($accents) as $acc){
    $string=preg_replace("/$acc/",$accents["$acc"],$string);
  }
  return $string;
}

function sendMail($email,$subject,$message,$headers="")
{
  date_default_timezone_set('Etc/UTC');
  $mail = new PHPMailer;
  $mail->isSMTP();
  $mail->SMTPDebug = 0;
  $mail->Debugoutput = 'html';
  $mail->Host = 'smtp.gmail.com';
  $mail->Port = 587;
  $mail->SMTPSecure = 'tls';
  $mail->SMTPAuth = true;
  $mail->Username = $GLOBALS["EMAIL_USERNAME"];
  $mail->Password = $GLOBALS["EMAIL_PASSWORD"];
  $mail->setFrom($mail->Username, 'Sistema de Información Curricular Integrada FCEN/UdeA');
  $mail->addReplyTo($mail->Username, 'Sistema de Información Curricular Integrada FCEN/UdeA');
  $mail->addAddress($email,"Destinatario");
  $mail->Subject=$subject;
  $mail->CharSet="UTF-8";
  $mail->Body=$message;
  $mail->IsHTML(true);
  if(!($status=$mail->send())) {
    $status="Mailer Error:".$mail->ErrorInfo;
  }
  return $status;
}

function sendShortMail($email,$subject,$message)
{
  $headers="";
  $headers.="From: noreply@udea.edu.co\r\n";
  $headers.="Reply-to: noreply@udea.edu.co\r\n";
  $headers.="MIME-Version: 1.0\r\n";
  $headers.="MIME-Version: 1.0\r\n";
  $headers.="Content-type: text/html\r\n";
$message.=<<<M
<p>
<b>Sistema de Información Curricular Integrada<br/>
Decanato, FCEN</b>
</p>
M;
  sendMail($email,$subject,$message,$headers);
}

function array2Globals($list)
{
  foreach(array_keys($list) as $key){
    $GLOBALS["$key"]=$list["$key"];
  }
}

function str2Array($string)
{
  $string=preg_replace("/[{}\"]/","",$string);
  $comps=preg_split("/,/",$string);
  
  $list=array();
  foreach($comps as $comp){
    $parts=preg_split("/:/",$comp);
    $key=$parts[0];
    $value=$parts[1];
    $list["$key"]=$value;
  }
  return $list;
}

function parseParams($params)
{
  $parameters=array();
  $parts=preg_split("/;/",$params);
  foreach($parts as $part){
    $comps=preg_split("/:/",$part);
    $param=$comps[0];
    $value=$comps[1];
    $parameters["$param"]=$value;
  }
  return $parameters;
}

function updateCursos($planid)
{
  $results=mysqlCmd("select * from Cursos where Planes_planid_s like '%$planid;%' order by nombre",$qout=1);
  $cursos=array("--"=>"--");
  foreach($results as $curso){
    $codigo=$curso["codigo"];
    $creditos=$curso["creditos"];
    $nombre=$curso["nombre"];
    $cursos["$codigo:$creditos"]=$nombre;
  }
  $cursos["000000:0"]="No listada";
  return $cursos;
}

function generateReconocimientos()
{
  global $GLOBALS;
  foreach(array_keys($GLOBALS) as $var){
    $$var=$GLOBALS["$var"];
  }

  $numrecon=20;
  $nummaterias=3;
  $numasignaturas=3;
  $chidden="hidden";

  $reconocimientos="";
  $hidden="";
  $recdir=getRecdir($recid);
  $recurl="$SITEURL/".preg_replace("/^\/.+\/data/","data",$recdir);

  for($ir=1;$ir<=$numrecon;$ir++){

    $nqr="qreconocimiento_${ir}";
    $vqr=$$nqr;

    $hidden="class='$chidden'";
$reconocimientos.=<<<RECON

    <table id="ireconocimiento_$ir" border="${TBORDER}px" width="${TWIDTH}px" $hidden>
    <tr><td  width=800px>

	<div class="reconocimiento">Reconocimiento $ir</div>
        <input type="hidden" name="qreconocimiento_${ir}" value="$vqr" class="confirm">

	<table border="${TBORDER}px" width="${TWIDTH}px">

	  <tr><td class="materias">Materia(s) vista(s)</td></tr>

	  <tr class="materias_vistas">

	    <td>
	      <div id="materia_${ir}_0" class="agregar">
		<a href="JavaScript:void(null)" onclick="addCourse(this)">Agregar materia</a>
	      </div>

RECON;

        for($im=1;$im<=$nummaterias;$im++){
	  $nmateria="materia_${ir}_${im}";
	  $vmateria=$$nmateria;
	  $nuniv="univ_${ir}_${im}";
	  $vuniv=$$nuniv;
	  $nnota="nota_${ir}_${im}";
	  $vnota=$$nnota;
	  $nqm="qmateria_${ir}_${im}";
	  $vqm=$$nqm;
	  $nsel="selmateria_${ir}_${im}";
	  $vsel=$$nsel;
	  $nmm="mmateria_${ir}_${im}";
	  $vmm=$$nmm;
	  $nsemestre="semestre_${ir}_${im}";
	  $vsemestre=$$nsemestre;
	  $nprograma="programa_${ir}_${im}";
	  $vprograma=$$nprograma;

	  $nobs="observaciones_${ir}_${im}";
	  $vobs=$$nobs;

	  //SELECT TYPE OF MATERIA INPUT
	  $input="";
$input.=<<<I
  <select id="materia_${ir}_${im}" name="smateria_${ir}_${im}" class="ccursos hidden" onchange="updateMateria(this)">
    $vsel
  </select>
I;
$input.=<<<I
  <input type="text" name="materia_${ir}_${im}" value="$vmateria" class="ccursos_input">
I;

$reconocimientos.=<<<RECON
	      <table id="imateria_${ir}_${im}" class="materia $chidden" border="${TBORDER}px">
		<tr><td class="field">Nombre de materia:</td><td class="input">
		    <input type="hidden" name="qmateria_${ir}_${im}" value="$vqm" class="confirm">
		    $input
		</td></tr>

		<tr class="ccursos_input">
		  <td class="field">Semestre:</td><td class="input">
		    <input type="text" name="semestre_${ir}_${im}" value="$vsemestre">
		  </td>
		</tr>
		
		<tr id="smmateria_${ir}_${im}" class="hidden">
		  <td class="field">Materia manual:</td>
		  <td class="input">
		    <input type="text" id="mmateria_${ir}_${im}" name="mmateria_${ir}_${im}" value="$vmm" class="confirm">
		  </td>
		</tr>
		
		<tr><!-- class="ccursos_input"-->
		  <td class="field">Programa de la asignatura:</td><td class="input">
		    <input type="file" name="programa_${ir}_${im}"><br/>
		    <i class="archivo">Archivo: <a href=$recurl/$vprograma target=_blank>$vprograma</a></i>
		    <input type="hidden" name="programa_${ir}_${im}" value="$vprograma"><br/>
		  </td>
		</tr>

		<tr><td class="field">Universidad:</td><td class="input"><input class="univ" type="text" name="univ_${ir}_${im}" value="$vuniv"></td></tr>
		<tr>
		  <td class="field">
		    Calificación:<br/>
		    <span class="help">Use "." no ","</span>
		  </td>
		  <td class="input"><input type="text" name="nota_${ir}_${im}" value="$vnota" onchange="updateAverage('${ir}')"></td>
		</tr>
		
		<tr>
		  <td class="field">
		    Observaciones:<br/>
		    <span class="help">
		      Información complementaria
		    </span>
		  </td>
		  <td class="input"><input type="text" name="observaciones_${ir}_${im}" value="$vobs"></td>
		</tr>

		<tr><td class="agregar" id="materia_${ir}_${im}" colspan=2>
RECON;

          if($im<$nummaterias){
$reconocimientos.=<<<RECON
		    <a href="JavaScript:void(null)" onclick="addCourse(this)">Agregar otra materia</a> |
RECON;
	  }

$reconocimientos.=<<<RECON
		    <a href="JavaScript:void(null)" onclick="removeCourse(this)">Remover esta materia</a>
		</td></tr>
	      </table>	  
RECON;
	}

$reconocimientos.=<<<RECON
	  <tr class="header level3">
	    <td width=800px class="materias">Reconocida por</td>
	  </tr>

	  <tr class="materias_reconocidas level3">

	    <td width=800px>

	      <div id="asignatura_${ir}_0" class="agregar">
		<a href="JavaScript:void(null)" onclick="addCourse(this)">Agregar asignatura</a>
	      </div>
RECON;

	for($ia=1;$ia<=$numasignaturas;$ia++){
	  $ncreditos="creditos_${ir}_${ia}";
	  $vcreditos=$$ncreditos;
	  $ndef="definitiva_${ir}_${ia}";
	  $vdef=$$ndef;
	  $nsel="selasignatura_${ir}_${ia}";
	  $vsel=$$nsel;

	  $nqa="qasignatura_${ir}_${ia}";
	  $vqa=$$nqa;

	  $nma="masignatura_${ir}_${ia}";
	  $vma=$$nma;

	  $nca="mcodigo_${ir}_${ia}";
	  $vca=$$nca;

$reconocimientos.=<<<RECON
	      <table id="iasignatura_${ir}_${ia}" class="materia $chidden" border="${TBORDER}px" width="${TWIDTH}px">
		<tr>
		  <td class="field">Asignatura:</td>
		  <td class="input">
		    <input type="hidden" name="qasignatura_${ir}_${ia}" value="$vqa" class="confirm">
		    <select id="asignatura_${ir}_${ia}" name="asignatura_${ir}_${ia}" class="cursos" onchange="updateCredits(this,'creditos_${ir}_${ia}')">
		      $vsel
		    </select>
		  </td>
		</tr>
		
		<tr id="smasignatura_${ir}_${ia}" class="hidden">
		  <td class="field">Asignatura manual:</td>
		  <td class="input">
		    <input type="text" id="masignatura_${ir}_${ia}" name="masignatura_${ir}_${ia}" value="$vma" class="confirm">
		  </td>
		</tr>

		<tr id="smcodigo_${ir}_${ia}" class="hidden">
		  <td class="field">Codigo manual:</td>
		  <td class="input">
		    <input type="text" id="mcodigo_${ir}_${ia}" name="mcodigo_${ir}_${ia}" value="$vca" class="confirm">
		  </td>
		</tr>

		<tr><td class="field">Créditos:</td><td class="input">
		    <input type="text" id="creditos_${ir}_${ia}" name="creditos_${ir}_${ia}" value="$vcreditos">
		</td></tr>
		<tr><td class="field">Definitiva:</td><td class="input"><input type="text" name="definitiva_${ir}_${ia}" value="$vdef"></td></tr>
		<tr><td class="agregar" id="asignatura_${ir}_${ia}" colspan=2>

RECON;

 if($ia<$numasignaturas){
$reconocimientos.=<<<RECON
		    <a href="JavaScript:void(null)" onclick="addCourse(this)">Agregar asignatura</a> | 
RECON;
 }

$reconocimientos.=<<<RECON
		    <a href="JavaScript:void(null)" onclick="removeCourse(this)">Remover asignatura</a>
		</td></tr>
	      </table>
RECON;
	}

$reconocimientos.=<<<RECON
	    </td>
	  </tr>
	</table>

	<div class="agregar" style="background:lightgreen;" id="reconocimiento_${ir}">
RECON;

 if($ir<$numrecon){
$reconocimientos.=<<<RECON
	  <a href="JavaScript:void(null)" onclick="addRecon(this)">Agregar reconocimiento</a> | 
RECON;
 }

$reconocimientos.=<<<RECON
	  <a href="JavaScript:void(null)" onclick="removeRecon(this)">Remover reconocimiento</a>
	</div>

    </td></tr>
    </table>
RECON;
  }
  return $reconocimientos;
}

function errorMsg($msg)
{
  global $ERRORS;
  $ERRORS.="<p>".$msg."</p>";
}

function statusMsg($msg)
{
  global $STATUS;
  $STATUS.="".$msg."<br/>";
}

function getHeaders()
{
  global $PERMCSS,$QPERMISO;
  
  //STYLES
  $style="<style>\n";

  //SPECIAL
  $colors=array("0"=>"white",
		"1"=>"lightblue",
		"2"=>"lightgreen",
		"3"=>"pink",
		"4"=>"pink");
  if($QPERMISO>0){
    $color=$colors[$QPERMISO];
    $style.=".menuperm{background:$color;}\n";
  }else{
    $style.=".menuperm{}\n";
  }

  //DIAGONAL LABEL
$style.=<<<S
    $PERMCSS 
    #diagonal_label {
    height:50px;
    line-height:25px;
    text-transform:uppercase;
    font-family:sans-serif;
    font-weight:bold;
    text-align:center;
    z-index: 20;
    }

    #diagonal_label a {
    display:block;
    height:100%;
    color:#000;
    text-decoration:none;
    background: green;
    }

    #diagonal_label span {
    display:inline-block;
    margin:0 10px;
    }
    #break {display:none;}

    @media only screen and (min-width : 480px) {

    #diagonal_label {
    width: 400px;
    height:70px;
    position:fixed;
    right:-120px;
    top:42px;
    line-height:20px;
    z-index: 20;
    }
    
    #diagonal_label a {
    -webkit-transform: rotate(45deg);
    -moz-transform: rotate(45deg);
    -o-transform: rotate(45deg);
    -ms-transform: rotate(45deg);
    transform: rotate(45deg);
    color: #fff;
    }
    
    #diagonal_label span {
    margin:0 3px;
    }
    
    #diagonal_label b {
    font-size:22px;
    font-weight:normal;
    display: inline-block;
    padding-top: 6px;
    }

    #break { display: block; }
    }    
S;


    $style.="</style>\n";

$header=<<<H
<!-- ---------------------------------------------------------------------- -->
<!-- HEADER -->
<!-- ---------------------------------------------------------------------- -->
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
  $style
</head>
<body>

<div id="diagonal_label">
  <a href="?" target="_blank">
    <span><b>&nbsp;</b></span><br/>
    <span>Versión Alpha 1.0</span><br id='break' />
    <span></span>
  </a>
</div>

H;
 return $header;
}

function getHead()
{
$head=<<<H
<!-- ---------------------------------------------------------------------- -->
<!-- HEAD -->
<!-- ---------------------------------------------------------------------- -->
<table width=100% border=0>
<tr>
<td width=100px><image src="img/udea_fcen.jpg"/ height=120px></td>
<td valign=bottom>
  <div class="title">
    <a href="index.php">SInfIn</a><br/>
  </div>
  <div class="subtitle">
    <span style="color:blue">S</span>istema 
    <span style="color:blue">Inf</span>ormación curricular
    <span style="color:blue">In</span>tegrada
  </div>
  <div class="affiliation">
    Facultad de Ciencias Exactas y Naturales<br/>
    Universidad de Antioquia
  </div>
</td>
</table>
H;
 return $head;
}

function getMainMenu()
{
  global $QPERMISO,$NOMBRE,$PERMISOS;
  $permiso=$PERMISOS["$QPERMISO"];
  $urlref=urlencode($_SERVER["REQUEST_URI"]);
$menu=<<<M
<div class="mainmenu menuperm">
  <a href="index.php">Principal</a>
  | <a href="comite.php">Comite de Curriculo</a>
  | <a href="reconoce.php">Reconocimientos</a>
  | <a href="planes.php">Planes de Estudio</a> 
  | <a href="asignaturas.php">Planes de Asignatura</a> 
  | <a href="documentos.php">Documentos</a>
  | <a href="ayuda.php">Ayuda</a>
  <span class="level0">| <a href="usuarios.php?urlref=$urlref">Usuarios</a></span>
  <span class="level1">
  | Sesión de <b>$NOMBRE</b> ($permiso) - <a href="actions.php?action=Cerrar">Cerrar</a>
  </span>
</div>
M;
 return $menu;
}

function getFooter()
{
  global $_SERVER;
$filetime=date(DATE_RFC2822,filemtime($_SERVER["SCRIPT_FILENAME"]));
$menu=<<<M
<div class="footer">
  Última actualización: $filetime - 
  <a href=mailto:jorge.zuluaga@udea.edu.co>Jorge I. Zuluaga</a> (C) 2016
</div>
</body>
M;
 return $menu;
}

function getMessages()
{
  global $ERRORS,$STATUS;
  $msg="";

  if(strlen($STATUS)){
$msg.=<<<M
  <div class="status">
  $STATUS
  </div>
M;
  }

  if(strlen($ERRORS)){
$msg.=<<<M
  <div class="errors">
  $ERRORS
  </div>
M;
  }
  return $msg;
}

function readRecon($recfile){
  $fl=fopen($recfile,"r");
  $object=fread($fl,filesize($recfile));
  $data=unserialize($object);
  fclose($fl);
  return $data;
}

function insertSql($table,$mapfields)
{
  global $GLOBALS;
  foreach(array_keys($GLOBALS) as $var){$$var=$GLOBALS["$var"];}
  
  $fields="(";
  $values="(";
  $udpate="";
  $i=0;
  foreach(array_keys($mapfields) as $field){
    $nvalue=$mapfields["$field"];
    if($nvalue==""){$nvalue=$field;}
    $value=$$nvalue;
    $fields.="$field,";
    $values.="'$value',";
    if($i>0){$update.="$field=VALUES($field),";}
    $i++;
  }
  $fields=rtrim($fields,",").")";
  $values=rtrim($values,",").")";
  $update=rtrim($update,",");
  $sql="insert into $table $fields values $values on duplicate key update $update";
  $result=mysqlCmd($sql);
  
  return $result;
}

function getRecdir($recid)
{
  global $ROOTDIR;
  if($results=mysqlCmd("select * from Reconocimientos where recid='$recid'")){
    $documento=$results["Estudiantes_documento"];
    $planid=$results["Planes_planid"];
    $recdir="$ROOTDIR/data/recon/${documento}_${planid}_${recid}";
  }else{
    $recdir=0;
  }
  return $recdir;
}

////////////////////////////////////////////////////////////////////////
//CONNECT TO DATABASE
////////////////////////////////////////////////////////////////////////
$DB=mysqli_connect("localhost",$USER,$PASSWORD,$DATABASE);
mysqli_set_charset($DB,'utf8');
mysql_query("set names 'utf8'");
$result=mysqlCmd("select now();",$qout=0);
$DATE=$result[0];
$DATE_ARRAY=preg_split("/ /",$DATE);
?>
