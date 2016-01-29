<html>
<?php
////////////////////////////////////////////////////////////////////////
//LOAD LIBRARY
////////////////////////////////////////////////////////////////////////
$HOST=$_SERVER["HTTP_HOST"];
$SCRIPTNAME=$_SERVER["SCRIPT_FILENAME"];
$ROOTDIR=rtrim(shell_exec("dirname $SCRIPTNAME"));
require("$ROOTDIR/etc/library.php");
$REFERER=$_SERVER["HTTP_REFERER"];
$debug=0;

////////////////////////////////////////////////////////////////////////
//MESSAGES
////////////////////////////////////////////////////////////////////////
echo "Generando formato de reconocimientos para '$recid'...<br/>";
$recdir=getRecdir($recid);
$recbase="$recdir/recon";
$recurl=preg_replace("/^\/.+\/data/","data",$recbase);
if($debug){echo "Directory: $recdir<br/>";}
echo "HTML: <a href=$recurl.html target=_blank>$recurl</a><br/>";
echo "PDF: <a href=$recurl.pdf target=_blank>$recurl</a><br/>";

////////////////////////////////////////////////////////////////////////
//PROPERTIES
////////////////////////////////////////////////////////////////////////
$border="border-right:solid 1px;border-bottom:solid 1px;border-collapse:collapse";
$bordera="border:solid 1px;border-collapse:collapse";
$borderd="border-style:none double solid none;border-width:0px 5px 1px 0px;border-color:black black black black";
$borderdate="border-style:none solid none none;border-width:0 1 0 0;border-color:black black black black";
$fsize="10px";

////////////////////////////////////////////////////////////////////////
//GET RECONOCIMIENTO INFORMATION
////////////////////////////////////////////////////////////////////////
$recfile=$recbase.".dat";
$fl=fopen($recfile,"r");
$object=fread($fl,filesize($recfile));
$data=unserialize($object);
fclose($fl);
foreach(array_keys($data) as $key){$$key=$data["$key"];}

////////////////////////////////////////////////////////////////////////
//GET PROGRAMA INFORMATION
////////////////////////////////////////////////////////////////////////
$parts=preg_split("/-/",$date);
$ano=$parts[0];
$mes=$parts[1];
$dia=$parts[2];

$results=mysqlCmd("select * from Planes where planid='$planid'");
$programaid=$results["Programas_programaid"];
$results=mysqlCmd("select * from Programas where programaid='$programaid'");
$programa=$results["programa"];
$signature="<i>$nombre</i>";

////////////////////////////////////////////////////////////////////////
//GET INFORMATION FROM ASIGNATURES
////////////////////////////////////////////////////////////////////////
$reconocimientos=array();
foreach(array_keys($data) as $key){
  if(preg_match("/qreconocimiento/",$key)){
    $value=$$key;
    if($value){
      $parts=preg_split("/_/",$key);
      array_push($reconocimientos,$parts[1]);
    }
  }
}
$hrow="";
$totcred=0;
$nobs=0;
$obstextm=array();
$obstexta=array();
foreach($reconocimientos as $ir){
  array_push($obstextm,""); 
  array_push($obstexta,"");
}
foreach($reconocimientos as $ir){
  if($debug){echo "<h2>Reconocimiento $ir:</h2>";}
  $materias=array();
  $nmaterias=0;
  for($im=1;$im<=3;$im++){
    $nmateria="qmateria_${ir}_${im}";
    $qmateria=$$nmateria;
    if($qmateria){
      $nmaterias++;
      $name="univ_${ir}_${im}";
      $univ=$$name;
      $name="materia_${ir}_${im}";
      $materia=$$name;
      $name="smateria_${ir}_${im}";
      $select=$$name;
      $name="mmateria_${ir}_${im}";
      $manual=$$name;
      if(!isBlank($manual)){
	$materia=$manual;
      }
      if(!isBlank($materia)){
	$materia=$materia;
      }else{
	$codigo=$select;
	$parts=preg_split("/:/",$codigo);
	$codigo=$parts[0];
	$results=mysqlCmd("select nombre from Cursos where codigo='$codigo' limit 1;");
	$materia=$results[0];
      }
      array_push($materias,
		 array("universidad"=>$univ,
		       "materia"=>$materia)
		 );
    }
  }
  if($debug){
    echo "<b>Materias:</b><br/>";
    print_r($materias);
    echo "<br/>";
  }
  $asignaturas=array();
  $nasignaturas=0;
  for($ia=1;$ia<=3;$ia++){
    $nasignatura="qasignatura_${ir}_${ia}";
    $qasignatura=$$nasignatura;
    if($qasignatura){
      $nasignaturas++;
      $name="asignatura_${ir}_${ia}";
      $asignatura=$$name;
      $name="masignatura_${ir}_${ia}";
      $manual=$$name;
      $name="mcodigo_${ir}_${ia}";
      $mcodigo=$$name;
      $name="creditos_${ir}_${ia}";
      $creditos=$$name;
      $name="definitiva_${ir}_${ia}";
      $definitiva=$$name;
      if(!isBlank($manual)){
	$codigo=$mcodigo;
	$asignatura=$manual;
      }else{
	$codigo=$asignatura;
	$parts=preg_split("/:/",$codigo);
	$codigo=$parts[0];
	$results=mysqlCmd("select nombre from Cursos where codigo='$codigo' limit 1;");
	$asignatura=$results[0];
      }
      array_push($asignaturas,
		 array("creditos"=>$creditos,
		       "codigo"=>$codigo,
		       "asignatura"=>$asignatura,
		       "definitiva"=>$definitiva)
		 );
    }
  }
  if($debug){
    echo "<b>Asignaturas:</b><br/>";
    print_r($asignaturas);
    echo "<br/>";
    echo "<hr/>";
  }

  $nrows=max($nmaterias,$nasignaturas);
  if($nmaterias!=$nasignaturas){
    $nobs++;
    $obs=$nobs;
  }else{
    $obs="";
  }

  $im=0;$ia=0;
  if($debug){echo "Nrows: $nrows<br/>";}
  for($irow=1;$irow<=$nrows;$irow++){
    $hrow.="<tr>";
    if($debug){echo "Row: $irow<br/>";}
    if(isset($materias["$im"])){
      if($debug){echo "Materia $im:<br/>";}
      $materia=$materias[$im]["materia"];
      $obstextm[$ir].="$materia,";
      $universidad=$materias[$im]["universidad"];
      if($debug){echo "$im:$materia,$universidad<br/>";}
      $hrow.="<td class=content style='$border'>$materia</td>";
      $hrow.="<td class=content style='$borderd'>$universidad</td>";
    }else{
      if($debug){echo "No hay materia $im<br/>";}
      $hrow.="<td class=content style='$border'></td>";
      $hrow.="<td class=content style='$borderd'></td>";
    }
    if(isset($asignaturas["$ia"])){
      if($debug){echo "Asignatura $ia:<br/>";}
      $asignatura=$asignaturas["$ia"]["asignatura"];
      $obstexta[$ir].="$asignatura,";
      $codigo=$asignaturas["$ia"]["codigo"];
      $creditos=$asignaturas["$ia"]["creditos"];
      $definitiva=$asignaturas["$ia"]["definitiva"];
      if($debug){echo "$ia,$asignatura,$codigo,$creditos,$definitiva<br/>";}
      $hrow.="<td class=content style='$border'>$obs</td>";
      $hrow.="<td class=content style='$border'>$codigo</td>";
      $hrow.="<td class=content style='$border'>$asignatura</td>";
      $hrow.="<td class=content style='$border'>$creditos</td>";
      $hrow.="<td class=content style='$border'>$definitiva</td>";
      $totcred+=$creditos;
    }else{
      if($debug){echo "No hay asignatura $ia<br/>";}
      $hrow.="<td class=content style='$border'></td>";
      $hrow.="<td class=content style='$border'></td>";
      $hrow.="<td class=content style='$border'></td>";
      $hrow.="<td class=content style='$border'></td>";
      $hrow.="<td class=content style='$border'></td>";
    }
    $im++;$ia++;
    $hrow.="</tr>";
  }
  $obstextm[$ir]=rtrim($obstextm[$ir],",");
  $obstexta[$ir]=rtrim($obstexta[$ir],",");
}
if($debug){echo "<hr/>";}
if($debug){
  echo "Observations:";
  print_r($obstextm);
  print_r($obstexta);
}
$notas="";
if(!isBlank($acto)){
  $notas.="Acto administrativo $acto<br/>";
}

$obs=1;
foreach($reconocimientos as $ir){
  if(preg_match("/,/",$obstextm[$ir]) or
     preg_match("/,/",$obstexta[$ir])){
    //$notas.="<tr><td class=content>$obs ) ".$obstextm[$ir]." se homologa (reconoce) por ".$obstexta[$ir]."</td><td></td></tr>";
    $notas.="$obs ) ".$obstextm[$ir]." se homologa (reconoce) por ".$obstexta[$ir]."<br/>";
    $obs++;
  }
}

////////////////////////////////////////////////////////////////////////
//GENERATE CONTENT
////////////////////////////////////////////////////////////////////////
$format="";
$format.=<<<F
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <style type="text/css">
  td{
  /* Warning: Needed for oldIE support, but words are broken up letter-by-letter */
  -ms-word-break: break-all;
  word-break: break-all;
  
  /* Non standard for webkit */
  word-break: break-word;
  
  -webkit-hyphens: auto;
  -moz-hyphens: auto;
  -ms-hyphens: auto;
  hyphens: auto;
  font-size: $fsize;
  }
  td.title{
  text-align:center;
  padding:5px;
  background:lightgray;
  }
  td.content{
  height:20px;
  padding:10px;
  }
  td.date{
  padding:5px;
  text-align:center;
  }
  </style>
</head>
<body>
  <!-- EXTERNAL TABLE -->
  <table border=0 width=100% style='$bordera;' cellspacing=0 cellpadding=0>

    <!-- HEADER -->
    <tr>
      <td>
	<table border=0 width=100% style='$border;' cellspacing=0 cellpadding=0>
	  <thead>
	    <tr><td width=15%></td><td width=50%></td><td width=15%></tr>
	  </thead>
	  <tr>
	    <td class=title>
	      <span style="font-family:Times">
		UNIVERSIDAD<br/>DE ANTIOQUIA<br/>
	      </span>
	      <span style="font-size:8px">
		1803
	      </span>
	    </td>
	    <td class=title>
	      SOLICITUD DE RECONOCIMIENTO DE MATERIAS
	    </td>
	    <td class=title style="text-align:left">
	      DEPTO. DE<br/>
	      ADMISIONES<br/>
	      Y REGISTRO
	    </td>
	  </tr>
	</table>
      </td>
    </tr>

    <!-- INFO PERSONAL -->
    <tr>
      <td>
	<table border=0 style='$border;' width=100% cellspacing=0 cellpadding=0>
	  <thead>
	    <tr>
	      <td width=15%></td><td width=65%></td>
	      <td width=20%></td>
	    </tr>
	  </thead>
	  <tr>
	    <td class=title style="$border">DOCUMENTO</td>
	    <td class=title style="$border">APELLIDOS Y NOMBRE DEL ESTUDIANTE</td>
	    <td class=title colspan=3 style="$border">FECHA DE SOLICITUD</td>
	  </tr>

	  <tr>
	    <td class=content style="$border;font-size:1.0em;">$documento</td>
	    <td class=content style="$border;font-size:1.0em;">$nombre</td>

	    <td style="padding:0px;border-right:solid black 1px;">

	      <table border=0 width=100% cellspacing=0 cellpadding=0 height=100%>
		<thead>
		  <tr>
		    <td width=30%></td>
		    <td width=30%></td>
		    <td width=30%></td>
		  </tr>
		</thead>
		<tr>
		  <td class=title style="$border">Día</td>
		  <td class=title style="$border">Mes</td>
		  <td class=title style="$border;border-right:none;">Año</td>
		</tr>
		<tr>
		  <td class=date style="$borderdate">$dia</td>
		  <td class=date style="$borderdate">$mes</td>
		  <td class=date >$ano</td>
		</tr>
	      </table>

	    </td>
	  </tr>
	</table>
      </td>
    </tr>

    <!-- PROGRAMA -->
    <tr>
      <td>
	<table border=0 style='$border;' width=100% cellspacing=0 cellpadding=0>
	  <thead>
	    <tr><td width=50%></td><td width=50%></td></tr>
	  </thead>
	  <tr>
	    <td class=title style="$border">PROGRAMA EN EL QUE ESTA MATRICULADO</td>
	    <td class=title style="$border">FIRMA DEL ESTUDIANTE</td>
	  </tr>
	  <tr>
	    <td class=content style="$border;font-size:1.0em;">$programa</td>
	    <td class=content style="$border;font-size:1.0em;">$signature</td>
	  </tr>
	</table>
      </td>
    </tr>

    <!-- RECONOCIMIENTOS -->
    <tr>
      <td>
	<table border=0 style='$border;' width=100% cellspacing=0 cellpadding=0>
	  <thead>
	    <tr>
	      <td width=30%></td>
	      <td width=10%></td>

	      <td width=7%></td>
	      <td width=10%></td>
	      <td width=27%></td>
	      <td width=7%></td>
	      <td width=7%></td>
	    </tr>
	  </thead>

	  <tr>
	    <td class=title style="$borderd" colspan=2>
	      MATERIAS SOLICITADAS<br/>
	      <span class=explicacion>(Para ser llenado por el Estudiante)</span>
	    </td>
	    <td class=title style="$border" colspan=5>
	      MATERIAS RECONOCIDAS<br/>
	      <span class=explicacion>(Para ser procesado por la Universidad)</span>
	    </td>
	  </tr>

	  <tr>
	    <td class=title style="$border">
	      MATERIA CURSADA
	    </td>
	    <td class=title style="$borderd">
	      UNIV
	    </td>


	    <td class=title style="$border">
	      OBS.
	    </td>
	    <td class=title style="$border">
	      CÓDIGO
	    </td>
	    <td class=title style="$border">
	      NOMBRE
	    </td>
	    <td class=title style="$border">
	      CRD.
	    </td>
	    <td class=title style="$border">
	      CALIF.
	    </td>
	  </tr>

          $hrow

	  <tr>
	    <td class=content style="$border;text-align:right;" colspan=5>TOTAL DE CRÉDITOS (CRD.) RECONOCIDOS:</td>
	    <td class=content style="$border;text-align:right;">$totcred</td>
	    <td class=content style="$border"></td>
	  </tr>

	</table>
      </td>
    </tr>

    <!-- RECONOCIMIENTOS -->
    <tr>
      <td>
	<table border=0 style="$border;" width=100% cellspacing=0 cellpadding=0>
	  <thead>
	    <tr>
	      <td width=80%></td>
	      <td width=20%></td>
	    </tr>
	  </thead>

	  <tr>
	    <td class=content style="border-right:solid 1px;text-align:left">
	    </td>
	    <td class="title" style="border-bottom:solid 1px;text-align:left;font-size:8px;">
	      FECHA RECONOCIMIENTO
	    </td>
	  </tr>

	  <tr>
	    <td class=content style="border-right:solid 1px;text-align:left" valign=top>
	      OBSERVACIONES (OBS):<br/>
              $notas
	    </td>

	    <td>
	      <table border=0 width=100% cellspacing=0 cellpadding=0 height=100% style="border-bottom:solid 1px">
		<thead>
		  <tr>
		    <td width=30%></td>
		    <td width=30%></td>
		    <td width=30%></td>
		  </tr>
		</thead>
		<tr>
		  <td class=title style="$border">Día</td>
		  <td class=title style="$border">Mes</td>
		  <td class=title style="$border;border-right:none;">Año</td>
		</tr>
		<tr>
		  <td class=date style="$borderdate">$dia</td>
		  <td class=date style="$borderdate">$mes</td>
		  <td class=date >$ano</td>
		</tr>
	      </table>

	      <div style="height:50px;border-bottom:solid 1px;width:100%;">
	      </div>
	      <div style="text-align:center">
		Facultad - Firma y Sello
	      </div>
	    </td>
	  </tr>
           
	</table>
      </td>
    </tr>
  
  </table>
</body>
</html>
F;

////////////////////////////////////////////////////////////////////////
//STORE CONTENT
////////////////////////////////////////////////////////////////////////
$fl=fopen($recbase.".html","w");
fwrite($fl,$format);
fclose($fl);

if($debug){echo $format;}

////////////////////////////////////////////////////////////////////////
//GENERATE PDF
////////////////////////////////////////////////////////////////////////
shell_exec("cd $recdir;$H2PDF $recbase.html $recbase.pdf");

////////////////////////////////////////////////////////////////////////
//REFRESH TO REFERRING PAGE
////////////////////////////////////////////////////////////////////////
header("Refresh:0;url=$recurl.pdf");
?>
</html>	
