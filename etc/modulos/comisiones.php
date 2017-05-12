<?php
////////////////////////////////////////////////////////////////////////
//COMISIONES
////////////////////////////////////////////////////////////////////////
$COM_TIPOS=array(
		 "servicios"=>"Comisión de Servicios",
		 "estudio"=>"Comisión de Estudios",	
		 "noremunerada"=>"Permiso",
		 "calamidad"=>"Calamidad"
		 );

$COM_HELP=array(
		"tipoid"=>"cedula,ce,pasaporte",
		"nombre"=>"NOMBRES APELLIDOS",
		"tipo"=>"Vinculado, Ocasional, Visitante, Empleado",
		"dependenciaid"=>"fisica, quimica, biologia, matematicas, decanatura",
		"dedicacion"=>"Si, No",
		);

$COM_ESTADOS=array(
		   "solicitada"=>"Solicitada",
		   "devuelta"=>"Devuelta",
		   "vistobueno"=>"Visto Bueno Director",
		   "aprobada"=>"Aprobada por Decano",
		   "cumplida"=>"Cumplido entregado",
		   );

$COM_COLORS=array(
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

$COM_TEXTS=array(
		 "presentacion","respuesta",
		 );

$COM_COLOR=array(
		 "solicitada"=>"#FFFF99",
		 "solicitada_noremunerada"=>"#FFCC99",
		 "vistobueno"=>"#99CCFF",
		 "vistobueno_noremunerada"=>"#99CCFF",
		 "devuelta"=>"#FF99FF",
		 "devuelta_noremunerada"=>"#FF99FF",
		 "aprobada"=>"#00CC99",
		 "aprobada_noremunerada"=>"#33CCCC",
		 "cumplida"=>"lightgray",
		 );

$COM_DIR="data/comisiones";

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//COMISIONES
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
$columns=mysqlCmd("show columns from Comisiones_Solicitudes;",$qout=1,$qlog=0);
$ncolumns=count($columns);
$FIELDS_COMISIONES=array();
for($i=0;$i<$ncolumns;$i++){$column=$columns[$i];array_push($FIELDS_COMISIONES,$column["Field"]);}

$out=mysqlCmd("select documento,dependenciaid from Usuarios where cargo like 'director%'",$qout=1);
$DIRECTORES=array();
foreach($out as $institutov){$DIRECTORES[$institutov["institutoid"]]=$institutov["documento"];}

$out=mysqlCmd("select documento,dependenciaid from Usuarios where cargo like 'secretaria%'",$qout=1);
$SECRETARIAS=array();
foreach($out as $institutov){$SECRETARIAS[$institutov["institutoid"]]=$institutov["cedula"];}
?>
