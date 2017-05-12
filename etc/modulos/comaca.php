<?php
////////////////////////////////////////////////////////////////////////
//COMACA
////////////////////////////////////////////////////////////////////////
$COMACA_TIPOS=array(
		    "seminario"=>"Seminario",
		    "divulgacion"=>"Actividad divulgativa",
		    "reunion"=>"Reunión comunidad",
		    "clubrevistas"=>"Club de Revistas",
		    );

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//CAMPOS DE BASE DE DATOS
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
$results=mysqlCmd("describe Comaca_Actividades;",$qout=1);
$ACTIVIDADES_FIELDS=array();
foreach($results as $field){$fieldname=$field[0];$ACTIVIDADES_FIELDS["$fieldname"]=$fieldname;}

$results=mysqlCmd("describe Comaca_Boletas;",$qout=1);
$BOLETAS_FIELDS=array();
foreach($results as $field){$fieldname=$field[0];$BOLETAS_FIELDS["$fieldname"]=$fieldname;}
?>
