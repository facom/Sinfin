<?php
$columns=mysqlCmd("show columns from Usuarios;",$qout=1,$qlog=0);
$ncolumns=count($columns);
$FIELDS_USUARIOS=array();
for($i=0;$i<$ncolumns;$i++){$column=$columns[$i];array_push($FIELDS_USUARIOS,$column["Field"]);}
?>