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
//$VERSION="Alpha 1.0";
$VERSION="Beta 1.0";

//COLOQUE AQUÍ EL E-MAIL DEL VICEDECANATO
$EMAIL_ADMIN="vicedecacen@udea.edu.co";
//$EMAIL_ADMIN="pregradofisica@udea.edu.co";

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
  $PASS=$_SESSION["password"];
  $PARAMETROS=$_SESSION["parametros"];
  $EMAIL=$_SESSION["email"];
  $TIPO=$_SESSION["tipo"];
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
		  "matematicas"=>"Instituto de Matemáticas",
		  "facultad"=>"Toda la Facultad");

$TIPOS=array("visitante"=>"Visitante",
	     "estudiante"=>"Estudiante activo",
	     "profesor"=>"Profesor",
	     "administrativo"=>"Usuario administrativo");

////////////////////////////////////////////////////////////////////////
//MOVILIDAD
////////////////////////////////////////////////////////////////////////
$TIPO_EVENTO=array("pasantia"=>"Pasantía",
		   "evento"=>"Evento académico");

$DURACION_EVENTO=array("corto"=>"Corta duración (1 a 7 días)",
		       "largo"=>"Larga duración (8 a 35 días)",
		       "prolongado"=>"Prolongado (mayor o igual a 35 días)");

$LUGAR_EVENTO=array("colombia"=>"Colombia",
		    "andino"=>"Pacto andino, centro américa o el Caribe",
		    "resto"=>"Resto del mundo incluyendo México");

$PROGRAMAS_FCEN=array("astronomia"=>"Astronomía",
		      "biologia"=>"Biología",
		      "estadística"=>"Estadística",
		      "fisica"=>"Física",
		      "matematicas"=>"Matemáticas",
		      "quimica"=>"Química",
		      "tecnoquimica"=>"Tecnología Química",
		      "ninguno"=>"Ninguno escogido"
		      );

$APOYOS=array("nalcorto"=>"Nacional Corto",
	      "nallargo"=>"Nacional Largo",
	      "nalprolongado"=>"Nacional Prolongado",
	      "andinocorto"=>"Andino Corto",
	      "andinolargo"=>"Andino Largo",
	      "andinoprolongado"=>"Andino Prolongado",
	      "internalcorto"=>"Internacional Corto",
	      "internallargo"=>"Internacional Largo",
	      "internalprolongado"=>"Internacional Prolongado"
	      );

$ESTADOS=array("nueva"=>"Nueva solicitud",
	       "guardada"=>"Guardada",
	       "pendiente_apoyo"=>"Pendiente confirmación profesor",
	       "pendiente_aprobacion"=>"Pendiente aprobación FCEN",
	       "aprobada"=>"Aprobada",
	       "devuelta"=>"Devuelta",
	       "realizada"=>"Realizada",
	       "cumplida"=>"Cumplida",
	       "rechazada"=>"Rechazada",
	       "terminada"=>"Terminada");

$ESTADOS_COLOR=array("nueva"=>"white",
		     "guardada"=>"#ffffcc",
		     "pendiente_apoyo"=>"#ccffff",
		     "pendiente_aprobacion"=>"#99ccff",
		     "aprobada"=>"yellow",
		     "devuelta"=>"#ffccff",
		     "realizada"=>"#d1d1e0",
		     "cumplida"=>"#ffcccc",
		     "rechazada"=>"#ff6666",
		     "terminada"=>"#99ff99");

$COMISIONES_COLOR=array(
			"solicitada"=>"#FFFF99",
			"solicitada_noremunerada"=>"#FFCC99",
			"vistobueno"=>"#99CCFF",
			"vistobueno_noremunerada"=>"#99CCFF",
			"devuelta"=>"#FF99FF",
			"devuelta_noremunerada"=>"#FF99FF",
			"aprobada"=>"#00CC99",
			"aprobada_noremunerada"=>"#33CCCC",
			"cumplida"=>"lightgray"
			);

$BOOLEAN=array("0"=>"No",
	       "1"=>"Si");

//COMISIONES
$TIPOSEMP=array("Vinculado"=>"Docente de Tiempo Completo",
	     "Ocasional"=>"Docente Ocasional de Tiempo Completo",
	     "Visitante"=>"Profesor Visitante",
	     "Secretaria"=>"Secretaria",
	     "Empleado"=>"Empleado");

$TIPOSCOM=array("servicios"=>"Comisión de Servicios",
		"estudio"=>"Comisión de Estudios",	
		"noremunerada"=>"Permiso"
		);

$INSTITUTOS=array("fisica"=>"Instituto de Física",
		  "biologia"=>"Instituto de Biología",
		  "quimica"=>"Instituto de Química",
		  "matematicas"=>"Instituto de Matemáticas",
		  "decanatura"=>"Decanatura"
		  );

$CAMPOSHELP=array("tipoid"=>"cedula,ce,pasaporte (todo en minusculas)",
		  "nombre"=>"NOMBRES APELLIDOS (mayúscula sostenida)",
		  "tipo"=>"Vinculado, Ocasional, Visitante, Empleado (mayúscula inicial)",
		  "institutoid"=>"fisica, quimica, biologia, matematicas, decanatura (todo en minuscula)",
		  "dedicacion"=>"Si, No (mayúscula inicial)"
		  );

$ESTADOS=array("solicitada"=>"Solicitada",
	       "devuelta"=>"Devuelta",
	       "vistobueno"=>"Visto Bueno Director",
	       "aprobada"=>"Aprobada por Decano",
	       "cumplida"=>"Cumplido entregado");

$TIPOSID=array("cedula"=>"Cédula de Ciudadanía",
	       "extranjeria"=>"Cédula de Extranjería",
	       "pasaporte"=>"Pasaporte");

$SINO=array("No"=>"No","Si"=>"Si");

//%%%%%%%%%%%%%%%%%%%%
//TEST SITE
//%%%%%%%%%%%%%%%%%%%%
//CHECK IF THIS IS THE MAIN SITE OR THE TEST SITE
$QTEST=0;
if($HOST=="localhost"){$QTEST=1;}
//$QTEST=0; //Decomente para obligar que sea servidor

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
//		   0		1	   2		3	4		5	6
$RECONSTATUS=array("Solicitado","Revisado","Aprobado","Editado","Rechazado","Entregado","Confirmado");
$SINFIN="<b>SInfIn</b>";

$HOST=$_SERVER["HTTP_HOST"];
$FILENAME=$_SERVER["SCRIPT_NAME"];
$SCRIPTNAME=$_SERVER["SCRIPT_FILENAME"];
$BASEDIR=rtrim(shell_exec("dirname $FILENAME"));
$SITEURL="http://$HOST$BASEDIR/";
if(isset($_SERVER["HTTP_REFERER"])){
  $REFERER=$_SERVER["HTTP_REFERER"];
}else{
  $REFERER=$SITEURL;
}

$WIDTHVID=400;
$HEIGHTVID=$WIDTHVID/1.4;
$WIDTHVID2=$WIDTHVID*2;
$HEIGHTVID2=$WIDTHVID2/1.4;

/*
echo "COOKIES:";
print_r($_COOKIES);
echo "<br/>SESSION:";
print_r($_SESSION);
//*/
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
  $selection.="<select $options name='$name'>";
  foreach(array_keys($parts) as $part){
    $show=$parts[$part];
    $selected="";
    if($part==$value){$selected="selected";}
    $selection.="<option value='$part' $selected>$show";
  }
  $selection.="</select>";
  return $selection;
}

function generateSelectionOptions($values,$name,$value,$options="",$readonly=0)
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

function mysqlCmdDB($db,$sql,$qout=0)
{
  if(!($out=mysqli_query($db,$sql))){
    die("Error:".mysqli_error($db));
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
  $characters = '0123456789abc0defghijkmnpqrstuvwxyz';//ABCDEFGHIJKLMNOPQRSTUVWXYZ';
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
		  <td class="field">Semestre:<br/>
		    <span class="help">Año-Semestre. Ej. 2008-2</span></td><td class="input">
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

function getHeaders($diagonal=true,$script="")
{
  global $PERMCSS,$QPERMISO,$VERSION,$QTEST; 
 
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
    $color="white";
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
    background: blue;//green;
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

    if(!$QTEST){
$internet=<<<I
<meta name="google-signin-scope" content="profile email">
<meta name="google-signin-client_id" content="182980586400-sp8ds3i2bkpgjia6pn8fhjdnncs9rb7l.apps.googleusercontent.com">
<script src="https://apis.google.com/js/platform.js"></script>
I;
}else{
$internet="";
}

    $style.="</style>\n";

$header=<<<H
<!-- ---------------------------------------------------------------------- -->
<!-- HEADER -->
<!-- ---------------------------------------------------------------------- -->
<head>
  <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
  <link rel="stylesheet" href="lib/jquery-ui/jquery-ui.min.css">
  <link href="lib/daterangepicker/jquery.comiseo.daterangepicker.css" rel="stylesheet">
  <link rel="stylesheet" href="css/sinfin.css" />
  $internet
  <script src="lib/jquery-ui/jquery.min.js"></script>
  <script src="lib/jquery-ui/jquery.min.js"></script>
  <script src="lib/jquery-ui/jquery-ui.min.js"></script>
  <script src="lib/jquery-ui/moment.min-locales.js"></script>
  <script src="lib/daterangepicker/jquery.comiseo.daterangepicker.js"></script>
  <script src="js/sinfin.js"></script>
  $style
  
  <script>
  $script
  </script>
</head>
<body>
H;

 if($diagonal){

$header.=<<<H
<div id="diagonal_label">
  <a href="novedades.php" target="_blank">
    <span><b>&nbsp;</b></span><br/>
    <span>Versión $VERSION</span><br id='break' />
    <span></span>
  </a>
</div>
H;

 }

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
    <span style="color:blue">S</span>istema de
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
  $urlref=urlencode($_SERVER["REQUEST_URI"]);
$menu=<<<M
<div class="mainmenu menuperm">
  <a href="index.php"><img src="img/iPrincipal-green.png" class="icon"></a>
  <a href="ayuda.php"><img src="img/iAyuda-green.png" class="icon"></a>
  <span class="level0"><a href="usuarios.php?urlref=$urlref"><img src="img/iUsuario-green.png" class="icon"></a></span>

  <span class="level1">
  <a href="actions.php?action=Cerrar"><img src="img/iCerrar-green.png" class="icon"></a>
  </span>

  <span class="level1">
    <a href="comite.php"><img src="img/iComite-green.png" class="icon"></a>
    <a href="documentos.php"><img src="img/iDocs-green.png" class="icon"></a>
  </span>

  <span class="level1">
    <a href="reconoce.php"><img src="img/iReconoce-green.png" class="icon"></a>
    <a href="planes.php"><img src="img/iPensums-green.png" class="icon"></a> 
    <a href="asignaturas.php"><img src="img/iCursos-green.png" class="icon"></a> 
  <span class="level1">
    <a href="movilidad.php"><img src="img/iMovilidad-green.png" class="icon"></a> 
  </span>
  <span class="level1">
    <a href="comaca.php"><img src="img/iComunidad-green.png" class="icon"></a> 
  </span>
  </span>
</div>
M;
 return $menu;
}

function getFooter()
{
  global $_SERVER,$NOMBRE,$QPERMISO,$PERMISOS;
  $permiso=$PERMISOS["$QPERMISO"];
$filetime=date(DATE_RFC2822,filemtime($_SERVER["SCRIPT_FILENAME"]));
$menu=<<<M
<div class="footer">
  <span class="level1">
  Esta conectado como <a href="usuarios.php?mode=cambiar"><b>$NOMBRE</b></a> ($permiso)<br/>
  </span>
  Última actualización: $filetime - 
  Desarrollado por <a href=mailto:jorge.zuluaga@udea.edu.co>Jorge I. Zuluaga</a> (C) 2016
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
  //echo "SQL: $sql<br/>";
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

function fechaRango($id,$start="",$end=""){

$code=<<<C
<input type="hidden" id="$id" name="$id">
<script>
    $("#$id").daterangepicker({
        presetRanges: [{
            text: 'Hoy',
	    dateStart: function() { return moment() },
	    dateEnd: function() { return moment() }
	}, {
            text: 'Mañana',
	    dateStart: function() { return moment().add('days', 1) },
	    dateEnd: function() { return moment().add('days', 1) }
	}, {
            text: 'La próxima semana',
            dateStart: function() { return moment().add('weeks', 1).startOf('week') },
            dateEnd: function() { return moment().add('weeks', 1).endOf('week') }
	}, {
            text: 'La semana anterior',
            dateStart: function() { return moment().add('weeks',-1).startOf('week') },
            dateEnd: function() { return moment().add('weeks',-1).endOf('week') }
	}],
	datepickerOptions: {
            minDate: null,
            maxDate: null
        },
	applyOnMenuSelect: false,
	initialText : 'Seleccione el rango de fechas...',
	applyButtonText : 'Escoger',
	clearButtonText : 'Limpiar',
	cancelButtonText : 'Cancelar',
    });
    jQuery(function($){
        $.datepicker.regional['es'] = {
            closeText: 'Cerrar',
            prevText: '&#x3c;Ant',
            nextText: 'Sig&#x3e;',
            currentText: 'Hoy',
            monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
                         'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
            monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun',
                              'Jul','Ago','Sep','Oct','Nov','Dic'],
            dayNames: ['Domingo','Lunes','Martes','Mi&eacute;rcoles','Jueves','Viernes','S&aacute;bado'],
            dayNamesShort: ['Dom','Lun','Mar','Mi&eacute;','Juv','Vie','S&aacute;b'],
            dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','S&aacute;'],
            weekHeader: 'Sm',
            dateFormat: 'dd/mm/yy',
            firstDay: 1,
            isRTL: false,
            showMonthAfterYear: false,
            yearSuffix: ''};
        $.datepicker.setDefaults($.datepicker.regional['es']);
    });
C;

  if(!isBlank($start)){
$code.=<<<C
  $("#$id").daterangepicker({
      onOpen: $("#$id").daterangepicker(
          "setRange",
          {start:$.datepicker.parseDate("yy-mm-dd","$start"),
           end:$.datepicker.parseDate("yy-mm-dd","$end")}
      )
  });
C;
  }else{
$code.=<<<C
  var today = moment().toDate();
  var tomorrow = today;//moment().add('days', 1).startOf('day').toDate();
  $("#$id").daterangepicker({
    onOpen: $("#$id").daterangepicker("setRange",{start: today,end: tomorrow})
    });
C;
  }
    
  $code.="</script>";
  return $code;
}

function get_client_ip() {
  $ipaddress = '';
  if (getenv('HTTP_CLIENT_IP'))
    $ipaddress = getenv('HTTP_CLIENT_IP');
  else if(getenv('HTTP_X_FORWARDED_FOR'))
    $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
  else if(getenv('HTTP_X_FORWARDED'))
    $ipaddress = getenv('HTTP_X_FORWARDED');
  else if(getenv('HTTP_FORWARDED_FOR'))
    $ipaddress = getenv('HTTP_FORWARDED_FOR');
  else if(getenv('HTTP_FORWARDED'))
    $ipaddress = getenv('HTTP_FORWARDED');
  else if(getenv('REMOTE_ADDR'))
    $ipaddress = getenv('REMOTE_ADDR');
  else
    $ipaddress = 'UNKNOWN';
  return $ipaddress;
}

function getComisionInfo($comisionid)
{
  global $FIELDS_COMISIONES,$FIELDS_PROFESORES;
  $results=mysqlCmd("select * from Comisiones where comisionid='$comisionid'");
  $comision=array();
  foreach($FIELDS_COMISIONES as $field){
    if($field=="extra1"){$field="diaspermiso";}
    $comision["$field"]=$results[$field];
  }
  $cedula=$comision["cedula"];
  $profesor=mysqlCmd("select * from Empleados where cedula='$cedula';");
  foreach($FIELDS_PROFESORES as $field){
    $comision["$field"]=$profesor[$field];
  }
  $institutoid=$comision["institutoid"];
  $instituto=mysqlCmd("select * from Institutos where institutoid='$institutoid';");
  $comision["instituto"]=$instituto["instituto"];
  return $comision;
}

////////////////////////////////////////////////////////////////////////
//CONNECT TO DATABASE
////////////////////////////////////////////////////////////////////////
$DB=mysqli_connect("localhost",$USER,$PASSWORD,$DATABASE);
mysqli_set_charset($DB,'utf8');
mysqli_query($DB,"set names 'utf8'");
$result=mysqlCmd("select now();",$qout=0);
$DATE=$result[0];
$DATE_ARRAY=preg_split("/ /",$DATE);

////////////////////////////////////////////////////////////////////////
//TABLE FIELDS
////////////////////////////////////////////////////////////////////////

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//MOVILIDAD
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
$results=mysqlCmd("describe Movilidad;",$qout=1);
$MOVILIDAD_FIELDS=array();
foreach($results as $field){
  $fieldname=$field[0];
  $MOVILIDAD_FIELDS["$fieldname"]=$fieldname;
}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//COMACA
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
$results=mysqlCmd("describe Actividades;",$qout=1);
$ACTIVIDADES_FIELDS=array();
foreach($results as $field){
  $fieldname=$field[0];
  $ACTIVIDADES_FIELDS["$fieldname"]=$fieldname;
}

$results=mysqlCmd("describe Boletas;",$qout=1);
$BOLETAS_FIELDS=array();
foreach($results as $field){
  $fieldname=$field[0];
  $BOLETAS_FIELDS["$fieldname"]=$fieldname;
}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//COMISIONES
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
$columns=mysqlCmd("show columns from Comisiones;",$qout=1,$qlog=0);
$ncolumns=count($columns);
$FIELDS_COMISIONES=array();
for($i=0;$i<$ncolumns;$i++){
  $column=$columns[$i];
  array_push($FIELDS_COMISIONES,$column["Field"]);
}

$columns=mysqlCmd("show columns from Empleados;",$qout=1,$qlog=0);
$ncolumns=count($columns);
$FIELDS_PROFESORES=array();
for($i=0;$i<$ncolumns;$i++){
  $column=$columns[$i];
  array_push($FIELDS_PROFESORES,$column["Field"]);
}

$out=mysqlCmd("select cedulajefe,institutoid from Institutos",$qout=1);
$DIRECTORS=array();
foreach($out as $instituto){
  $DIRECTORS[$instituto["institutoid"]]=$instituto["cedulajefe"];
}
$out=mysqlCmd("select cedula,institutoid from Empleados where tipo='Secretaria'",$qout=1);
$SECRETARIAS=array();
foreach($out as $instituto){
  $SECRETARIAS[$instituto["institutoid"]]=$instituto["cedula"];
}

$RANDOMMODE=generateRandomString(100);
?>
