<?php
////////////////////////////////////////////////////////////////////////
//SITE CONFIGURATION
////////////////////////////////////////////////////////////////////////
$QDEPURACION=1;

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//VERSION
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
$VERSION="Beta 2.0";
$VER=2;

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//DATABASE
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
$USER="sinfin";
$PASSWORD="123";
$DATABASE="Sinfin2";

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//EMAIL
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
$EMAIL_USERNAME="pregradofisica@udea.edu.co";
$EMAIL_PASSWORD="Gmunu-Tmunu=0";
$EMAIL_ADMIN="vicedecacen@udea.edu.co";
//$EMAIL_ADMIN="pregradofisica@udea.edu.co";

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//IDENTIFICACIONES
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
$DECANOTXT="decana";
$COORDINADOR="Dra. Aura Aleida Jaramillo Valencia";
$COORDINADORTXT="coordinadora";

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//IMAGENES
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
$LOGOUDEA="http://astronomia-udea.co/principal/sites/default/files";
$MANATWORK="<p><center><img src=img/manatwork.png width=10%></center></p>";

////////////////////////////////////////////////////////////////////////
//LISTAS
////////////////////////////////////////////////////////////////////////
$PROGRAMAS_FCEN=array(
		      "astronomia"=>"Astronomía",
		      "biologia"=>"Biología",
		      "estadística"=>"Estadística",
		      "fisica"=>"Física",
		      "matematicas"=>"Matemáticas",
		      "quimica"=>"Química",
		      "tecnoquimica"=>"Tecnología Química",
		      "ninguno"=>"Ninguno escogido"
		      );

$DEPENDENCIAS=array(
		    "fisica"=>"Instituto de Física",
		    "biologia"=>"Instituto de Biología",
		    "quimica"=>"Instituto de Química",
		    "matematicas"=>"Instituto de Matemáticas",
		    "facultad"=>"Toda la Facultad",
		    "decanato"=>"Decanato",
		    "vicedecanato"=>"Vicedecanato",
		    "investigacion"=>"Centro de Investigaciones",
		    );

////////////////////////////////////////////////////////////////////////
//DESTINATARIOS DE CUMPLIDOS
////////////////////////////////////////////////////////////////////////
if(!$QTEST){
  $DESTINATARIOS_CUMPLIDOS=array(
   array("Secretaria del Decanato","Luz Mary Castro","luz.castro@udea.edu.co"),
   array("Secretaria del CIEN","Ana Catalina Fernández","ana.fernandez@udea.edu.co"),
   array("Programa de Extensión","Natalia López","njlopez76@gmail.com"),
   array("Fondo de Pasajes Internacionales","Mauricio Toro","fondosinvestigacion@udea.edu.co"),
   array("Vicerrectoria de Investigación","Mauricio Toro","tramitesinvestigacion@udea.edu.co"),
   array("Centro de Investigaciones SIU","Ana Eugenia","aeugenia.restrepo@udea.edu.co"),
   array("Fondos de Vicerrectoría de Docencia","Sandra Monsalve","programacionacademica@udea.edu.co")
);
}else{
$DESTINATARIOS_CUMPLIDOS=array(
   array("Secretaria del Decanato","Luz Mary Castro","pregradofisica@udea.edu.co"),
   array("Secretaria del CIEN","Maricela Botero","zuluagajorge@gmail.com"),
   array("Programa de Extensión","Natalia López","astronomia.udea@gmail.com"),
   array("Fondo de Pasajes Internacionales","Mauricio Toro","jorge.zuluaga@udea.edu.co"),
   array("Vicerrectoria de Investigación","Mauricio Toro","newton@udea.edu.co"),
   array("Centro de Investigaciones SIU","Ana Eugenia","newton@udea.edu.co"),
   array("Fondos de Vicerrectoría de Docencia","Sandra Perez","newton@udea.edu.co")
);
}

?>
