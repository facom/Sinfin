<?php
////////////////////////////////////////////////////////////////////////
//MOVILIDAD
////////////////////////////////////////////////////////////////////////
$MOV_TIPO=array(
		"pasantia"=>"Pasantía",
		"evento"=>"Evento académico",
		);

$MOV_DURACION=array(
		    "corto"=>"Corta duración (1 a 7 días)",
		    "largo"=>"Larga duración (8 a 35 días)",
		    "prolongado"=>"Prolongado (mayor o igual a 35 días)",
		    );

$MOV_LUGAR=array(
		 "colombia"=>"Colombia",
		 "andino"=>"Pacto andino, centro américa o el Caribe",
		 "resto"=>"Resto del mundo incluyendo México",
		 );

$MOV_APOYOS=array(
		  "nalcorto"=>"Nacional Corto",
		  "nallargo"=>"Nacional Largo",
		  "nalprolongado"=>"Nacional Prolongado",
		  "andinocorto"=>"Andino Corto",
		  "andinolargo"=>"Andino Largo",
		  "andinoprolongado"=>"Andino Prolongado",
		  "internalcorto"=>"Internacional Corto",
		  "internallargo"=>"Internacional Largo",
		  "internalprolongado"=>"Internacional Prolongado",
		  );

$MOV_ESTADOS=array(
		   "nueva"=>"Nueva solicitud",
		   "guardada"=>"Guardada",
		   "pendiente_apoyo"=>"Pendiente confirmación profesor",
		   "pendiente_aprobacion"=>"Pendiente aprobación FCEN",
		   "aprobada"=>"Aprobada",
		   "devuelta"=>"Devuelta",
		   "realizada"=>"Realizada",
		   "cumplida"=>"Cumplida",
		   "rechazada"=>"Rechazada",
		   "terminada"=>"Terminada"
		   );

$MOV_ESTADOS_COLOR=array(
			 "nueva"=>"white",
			 "guardada"=>"#ffffcc",
			 "pendiente_apoyo"=>"#ccffff",
			 "pendiente_aprobacion"=>"#99ccff",
			 "aprobada"=>"yellow",
			 "devuelta"=>"#ffccff",
			 "realizada"=>"#d1d1e0",
			 "cumplida"=>"#ffcccc",
			 "rechazada"=>"#ff6666",
			 "terminada"=>"#99ff99"
			 );

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//MOVILIDAD
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
$results=mysqlCmd("describe Movilidad_Solicitudes;",$qout=1);
$MOVILIDAD_FIELDS=array();
foreach($results as $field){$fieldname=$field[0];$MOVILIDAD_FIELDS["$fieldname"]=$fieldname;}
?>