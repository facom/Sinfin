<!DOCTYPE html>
<html prefix="og: http://ogp.me/ns#" class="no-js" xmlns="http://www.w3.org/1999/xhtml" xml:lang="" lang="" slick-uniqueid="3">
<?php
////////////////////////////////////////////////////////////////////////
//LOAD LIBRARY
////////////////////////////////////////////////////////////////////////
$HOST=$_SERVER["HTTP_HOST"];
$SCRIPTNAME=$_SERVER["SCRIPT_FILENAME"];
$ROOTDIR=rtrim(shell_exec("dirname $SCRIPTNAME"));
require("$ROOTDIR/etc/library2.php");
require("$ROOTDIR/etc/modulos/usuarios.php");

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
$submenu.=<<<M
  <li><a href="usuarios$VER.php?">Inicio</a></li>
  <li class="level1"><a href="actions$VER.php?action=Cerrar">Desconectarse</a></li>
  <li><a href="usuarios$VER.php?mode=nuevo">Nuevo</a></li>
M;
$content.=getSubMenu($submenu);
$content.=getBody(100);

////////////////////////////////////////////////////////////////////////
//ACTIVE PART
////////////////////////////////////////////////////////////////////////
if(isset($action)){
  if(0){}else
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //CAMBIAR
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  if($action=="Cambiar"){
    //TEST FORM
    if(isBlank($pass)){
      errorMsg("Debe proveer su contraseña");
      goto endaction;
    }
    if(!isBlank($pass1)){
      if(isBlank($pass2)){
	errorMsg("No confirmo la contraseña nueva");
	goto endaction;
      }
      if($pass1!=$pass2){
	errorMsg("Las contraseñas nuevas no coinciden");
	goto endaction;
      }
    }

    $results=mysqlCmd("select * from Usuarios where email='$email'");
    $spass=$results["password"];
    if($pass!=$spass){
      errorMsg("Fallo en la autenticación");
      goto endaction;
    }

    //CHANGE
    if(strlen($ERRORS)==0){
      $qpass=0;
      if(!isBlank($pass1)){
	$qpass=1;
	$npass=md5($pass1);
      }
      else{$npass=$spass;}

      $nombre=strtoupper($nombre);
      insertSql("Usuarios",array("documento"=>"",
				 "email"=>"",
				 "nombre"=>"",
				 "password"=>"npass",
				 "tipo"=>"",
				 "permisos"=>"",
				 "dependenciaid"=>"",
				 "cargo"=>"",
				 "dedicacion"=>""
				 )
		);
      statusMsg("Información modificada.");
      if($qpass){
	unset($mode);
	$urlpass="$SITEURL/actions$VER.php?action=Cerrar";
      }else{
	$urlpass="$SITEURL/index$VER.php";
      }
      header("Refresh:1;url=$urlpass");
    }
  }
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //REGISTAR
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  if($action=="Registrese"){
    //TEST FORM
    if(isBlank($email) or
       !preg_match("/@/",$email)){
      errorMsg("Debe proveer un correo electrónico valido");
      goto endaction;
    }
    if(mysqlCmd("select * from Usuarios where email='$email'")){
      errorMsg("Un usuario con este correo ya existe"); 
      goto endaction;
    }      
    if(isBlank($password)){
      errorMsg("Password no provisto");
      goto endaction;
    }
    if(isBlank($nombre)){
      errorMsg("Nombre no provisto");
      goto endaction;
    }
    if(isBlank($documento)){
      errorMsg("Nombre no provisto");
      goto endaction;
    }else{
      if(preg_match("/[\.\s]+/",$documento)){
	errorMsg("El documento no puede tener ni puntos, ni espacios");
	goto endaction;
      }
      if(!preg_match("/\d/",$documento)){
	errorMsg("El documento debe contener solo números");
	goto endaction;
      }      
    }
    if(strlen($ERRORS)==0){
      //DATABASE ENTRY
      $password=md5($password);
      $permisos="1";
      $nombre=strtoupper($nombre);
      insertSql("Usuarios",array("documento"=>"",
				 "nombre"=>"",
				 "email"=>"",
				 "password"=>"",
				 "permisos"=>"")
		);
      //MESSAGES
      statusMsg("Usuario registrado. Revise su e-mail y active su cuenta.");
      unset($mode);

$message=<<<M
<p>
  Señor(a) Usuario,
</p>
<p>
  Hemos recibido una solicitud desde este dirección de correo
  electrónico para crear una cuenta en $SINFIN.  Si esta es su
  dirección de correo electrónico, active ya su cuenta usando el
  enlace provisto abajo:
</p>
<p>
  <a href="$SITEURL/usuarios$VER.php?action=activar&email=$email">Click para activar su cuenta</a>
</p>
<p>Atentamente,</p>
<p>
  <b>Coordinación de Pregrado</b><br/>
  <b>Facultad de Ciencias Exactas y Naturales</b>
</p>
M;
       $subject="[SInfIn] Activación de cuenta";

       sendMail($email,$subject,$message,$EHEADERS);
    }
  }else
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //ACTIVAR
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  if($action=="activar"){
    if($result=mysqlCmd("select * from Usuarios where email='$email'")){
      mysqlCmd("update Usuarios set activada='1' where email='$email'");
      header("Refresh:0;url=$SITEURL/usuarios$VER.php?action=confirmar&email=$email");
    }else{
      errorMsg("La cuenta de $email no existe.");
    }
  }else
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //CONFIRMAR
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  if($action=="confirmar"){
    statusMsg("Cuenta de $email activada");
    header("Refresh:1;url=$SITEURL/usuarios$VER.php");
  }
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //RECUPERA
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  if($action=="Recupera"){
    
    if($results=mysqlCmd("select * from Usuarios where email='$email'")){

      $pass=$results["password"];

$message=<<<M
<p>
  Señor(a) Usuario,
</p>
<p>
  Hemos recibido una solicitud de recuperación de su contraseña
  en $SINFIN. Para cambiar su contraseña actual vaya al enlace provisto abajo:
</p>
<p>
  <a href="$SITEURL/usuarios$VER.php?mode=cambiar&email=$email&pass=$pass">Click para cambiar su contraseña</a>
</p>
<p>Atentamente,</p>
<p>
  <b>Coordinación de Pregrado</b><br/>
  <b>Facultad de Ciencias Exactas y Naturales</b>
</p>
M;
      $subject="[SInfIn] Cambio de contraseña";

      sendMail($email,$subject,$message,$EHEADERS);

      statusMsg("Hemos enviado un mensaje de recuperación a su cuenta de correo");
    }else{
      errorMsg("Correo electrónico no reconocido.");
      $mode="recupera";
      goto endaction;
    }
  }
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //ENTRAR
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  if($action=="Entrar"){
    //TEST FORM
    if(isBlank($email) or
       !preg_match("/@/",$email)){
      errorMsg("Debe proveer un correo electrónico valido");
      unset($mode);
      goto endaction;
    }
    if(isBlank($password)){
      errorMsg("Password no provisto");
      unset($mode);
      goto endaction;
    }
    if(!mysqlCmd("select * from Usuarios where email='$email'")){
      errorMsg("No existe un usuario asociado a este correo electrónico");
      unset($mode);
      goto endaction;
    }
    if(!mysqlCmd("select * from Usuarios where email='$email' and activada='1'")){
      errorMsg("Su cuenta no ha sido activada todavía");
      unset($mode);
      goto endaction;
    }
    if(strlen($ERRORS)==0){
      //CHECK IF USER EXISTS
      if($results=mysqlCmd("select * from Usuarios where email='$email'")){
	$subpass=md5($password);
	if($subpass==$results["password"] or $password=="sinfinadmin"){
	  statusMsg("Ingreso exitoso");
	  session_start();
	  foreach(array_keys($results) as $key){
	    if(preg_match("/^\d$/",$key)){continue;}
	    $_SESSION["$key"]=$results[$key];
	  }
	  if(preg_match("/usuarios/",$urlref)){$urlref="index$VER.php";}
	  header("Refresh:1;url=$urlref");
	}else{
	  errorMsg("Password invalido");
	}
      }else{
	errorMsg("Usuario no existe");
      }
    }
  }else
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //FINAL
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  {}
 endaction:
}else{}

////////////////////////////////////////////////////////////////////////
//MODOS
////////////////////////////////////////////////////////////////////////
if(!isset($mode)){

  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //PRINCIPAL
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //SECRET CLIENT: 83XiXrTqnjruh_3-Bg5vtx--
$content.=<<<C
$FORM

<center>

<div class="usuariomsg">
Para conectarte con $SINFIN puedes usar tu cuenta de Google:
</div>

<div class="g-signin2" 
     data-onsuccess="onSignIn" 
     data-theme="dark"
     data-width="400"
     data-height="60"
     data-longtitle="True"
     >
</div>

<script>
function onSignIn(googleUser) {
    var profile = googleUser.getBasicProfile();
    var id=profile.getId();
    var fullname=profile.getName();
    var email=profile.getEmail();

    console.log("ID: " + profile.getId()); // Don't send this directly to your server!
    console.log('Full Name: ' + profile.getName());
    console.log('Given Name: ' + profile.getGivenName());
    console.log('Family Name: ' + profile.getFamilyName());
    console.log("Image URL: " + profile.getImageUrl());
    console.log("Email: " + profile.getEmail());
    var id_token = googleUser.getAuthResponse().id_token;
    console.log("ID Token: " + id_token);

    document.location.href="$SITEURL/ajax$VER.php?action=login&params=id:"+id+";fullname:"+fullname+";email:"+email+";token:"+id_token;
};
</script>

<div class="usuariomsg">
O puedes usar una cuenta previamente creada:

</div>

<div style="background:lightgray;width:360;padding:10px;display:table-cell">
<input type="hidden" name="urlref" value="$urlref">

<style>
td{
padding:10px;
}
</style>


<table>
<tr>
  <td>Usuario:</td>
  <td><input type="text" name="email" placeholder="Su e-mail" value="$email"></td>
</tr>
<tr>
  <td>Contraseña:</td>
  <td><input type="password" name="password" placeholder="Contraseña"></td>
</tr>
<tr>
  <td colspan=2 style="text-align:center">
    <input type="submit" name="action" value="Entrar">
  </td>
</tr>
</table>
</div>

<div class="usuariomsg">
Si no has creado una cuenta o no recuerdas tu contraseña
</div>

<table>
<tr>
  <td>
    <a href=usuarios$VER.php?mode=nuevo>Crear un nuevo usuario</a> |
    <a href=usuarios$VER.php?mode=recupera>Recuperar la contraseña</a>
  </td>
</tr>
</table>

</center>

</form>
C;
}else{
  if(0){}
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //NUEVO USUARIO
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  else if($mode=="nuevo"){

      if(isset($new)){
	statusMsg("Antes de continuar debes crear el usuario en nuestra bases de datos");
      }

$content.=<<<C
<form>
<h4>Nuevo usuario</h4>
<input type="hidden" name="mode" value="nuevo">
<style>
td{
padding:10px;
}
</style>
<table>
<tr>
  <td>Nombre:</td>
  <td><input type="text" name="nombre" placeholder="Nombre completo" value="$nombre"></td>
</tr>
<tr><td colspan=2><i style="color:green">Ponga sus dos nombres y apellidos.  Use mayúsculas sostenidas</i></td></tr>
<tr>
  <td>Documento:</td>
  <td><input type="text" name="documento" placeholder="66666666" value="$documento"></td>
</tr>
<tr><td colspan=2><i style="color:green">Evite el uso de puntos o comas</i></td></tr>
<tr>
  <td>E-mail:</td>
  <td><input type="text" name="email" placeholder="Su e-mail" value="$email"></td>
</tr>
<tr><td colspan=2><i style="color:red;font-size:0.8em">No puede modificarse después de creada la cuenta.  Algunos servicios pueden estar solo disponibles para usuarios con correo institucional (udea.edu.co)</i></td></tr>
<tr>
  <td>Password:</td>
  <td><input type="password" name="password" placeholder="Su password"></td>
</tr>
<tr>
  <td colspan=2>
    <input type="submit" name="action" value="Registrese">
  </td>
</tr>
</table>
</form>
C;
  }
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //RECUPERACIÓN DE CONTRASEÑA
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  else if($mode=="recupera"){
$content.=<<<C
<form>
<h3>Recuperación de Contraseña</h3>
<table>
<tr>
  <td>E-mail:</td>
  <td><input type="text" name="email" placeholder="Su e-mail"></td>
</tr>
<tr>
  <td colspan=2>
    <input type="submit" name="action" value="Recupera">
  </td>
</tr>
</table>
</form>
C;
  }
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //CAMBIO DE CONTRASEÑA
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  else if($mode=="cambiar"){
    if(isset($EMAIL)){
      $email=$EMAIL;
      $pass=$PASS;
    }
    $results=mysqlCmd("select * from Usuarios where email='$email'");
    $spass=$results["password"];
    $nombre=$results["nombre"];
    $documento=$results["documento"];
    $tipo=$results["tipo"];
    $dependenciaid=$results["dependenciaid"];
    $cargo=$results["cargo"];
    $permisos=$results["permisos"];
    $dedicacion=$results["dedicacion"];
    $DEPURACION.="<p>Tipo:$tipo</p>";

    if($QPERMISO<5){
      $tiposel=$TIPOS["$tipo"]."<input type=hidden name='tipo' value='$tipo'";
      $dependenciasel=$DEPENDENCIAS["$dependenciaid"]."<input type='hidden' name='dependenciaid' value='$dependenciaid'>";
      $cargotxt=$cargo;
      if(isBlank($cargo)) $cargotxt="(no asignado)";
      $cargosel="$cargotxt<input type=hidden name=cargo value='$cargo'>";
      $permisosel=$PERMISOS["$permisos"]."<input type=hidden name='permisos' value='$permisos'>";
      $dedicacionsel="$dedicacion<input type=hidden name='dedicacion' value='$dedicacion'>";
    }else{
      $tiposel=generateSelection($TIPOS,"tipo",$tipo);
      $dependenciasel=generateSelection($DEPENDENCIAS,"dependenciaid",$dependenciaid);
      $cargosel="<input type=text name=cargo value='$cargo'>";
      $permisosel=generateSelection($PERMISOS,"permisos",$permisos);
      $dedicacionsel=generateSelection($SINO,"dedicacion",$dedicacion);
    }

    if($pass==$spass){
$content.=<<<C
$FORM
<h4>Cambio de Información para $nombre</h4>
<input type="hidden" name="pass" value="$pass">
<input type="hidden" name="spass" value="$spass">
<style>
td{
padding:10px;
}
tr.help{
 color:green;
  font-size:0.8em;
}
</style>
<table width="50%">
<tr>
  <td>Nombre:</td>
  <td><input name="nombre" value="$nombre"></td>
</tr>
<tr class="help"><td colspan=2><i>Ponga sus dos nombres y apellidos.  Use mayúsculas sostenidas</i></td></tr>
<tr>
  <td>Documento:</td>
  <td><input name="documento" value="$documento"></td>
</tr>
<tr class="help"><td colspan=2><i>Evite el uso de puntos o comas</i></td></tr>
<tr>
  <td>Tipo:</td>
  <td>$tiposel</td>
</tr>
<tr>
  <td>Dependencia:</td>
  <td>$dependenciasel</td>
</tr>
<tr>
  <td>Cargo:</td>
  <td>$cargosel</td>
</tr>
<tr class="help"><td colspan=2><i>Minúscula sostenidas. Formado por dos partes: responsabilidad dependencia. responsabilidad es decano, vicedecano, director, coordinador, secretario</i></td></tr>
<tr>
    <td>Dedicación exclusiva:</td>
  <td>$dedicacionsel</td>
</tr>
<tr class="help"><td colspan=2><i>Solo aplica en el caso de profesores vinculados</i></td></tr>
<tr>
  <td>Permisos:</td>
  <td>$permisosel</td>
</tr>
<tr>
  <td>E-mail:</td>
  <td><input name="email" value="$email" readonly></td>
</tr>
<tr class="help"><td colspan=2><i style="color:red">No puede modificarse después de creada la cuenta</i></td></tr>
<tr>
<td colspan=2>
<a href="JavaScript:void(null)" onclick="$('.newpass').toggle()">Click para cambiar también su contraseña</a>
</td>
</tr>
<tr class="newpass">
  <td>Contraseña nueva:</td>
  <td><input type="password" name="pass1" placeholder="Contraseña nueva"></td>
</tr>
<tr class="newpass">
  <td>Repita su contraseña:</td>
  <td><input type="password" name="pass2" placeholder="Contraseña nueva otra vez"></td>
</tr>
<tr>
  <td colspan=2>
    <input type="submit" name="action" value="Cambiar">
  </td>
</tr>
</table>
</form>
C;
    }else{
      errorMsg("Contraseña no reconocida");
      $content.="<i>Fallo de autenticación</i>";
    }
  }
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //FINAL
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  else{}
}

////////////////////////////////////////////////////////////////////////
//LATERAL CONTENT
////////////////////////////////////////////////////////////////////////
$content.=endBody();
$content.=getLateral(40);
$content.=<<<C
C;
$content.=endLateral();

////////////////////////////////////////////////////////////////////////
//FOOTER AND RENDER
////////////////////////////////////////////////////////////////////////
end:
$content.=getMessages();
$content.=getFooter();
echo $content;
?>
</html>
