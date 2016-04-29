<html>
<?php
////////////////////////////////////////////////////////////////////////
//LOAD LIBRARY
////////////////////////////////////////////////////////////////////////
$HOST=$_SERVER["HTTP_HOST"];
$SCRIPTNAME=$_SERVER["SCRIPT_FILENAME"];
$ROOTDIR=rtrim(shell_exec("dirname $SCRIPTNAME"));
require("$ROOTDIR/etc/library.php");

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
$content.=<<<M
<div class="moduletitle">
  Usuarios
</div>
<div class="submenu">
  <a href="?">Inicio</a> 
  | <a href="?mode=nuevo">Nuevo</a>
</div>
<div class="container">
M;

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
    $mpass=md5($pass);
    if($mpass!=$spass and $pass=!$spass){
      errorMsg("Fallo en la autenticación");
      goto endaction;
    }

    //CHANGE
    if(strlen($ERRORS)==0){
      if(!isBlank($pass1)){$npass=md5($pass1);}
      else{$npass=$spass;}
      $sql="update Usuarios set password='$npass',email='$email',nombre='$nombre',tipo='$tipo',documento='$documento' where email='$email'";
      //echo "SQL:$sql<br/>";
      mysqlCmd($sql);
      statusMsg("Información modificada. Conéctese de nuevo.");
      unset($mode);
      header("Refresh:1;url=$SITEURL/actions.php?action=Cerrar");
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
  <a href="$SITEURL/usuarios.php?action=activar&email=$email">Click para activar su cuenta</a>
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
      header("Refresh:0;url=$SITEURL/usuarios.php?action=confirmar&email=$email");
    }else{
      errorMsg("La cuenta de $email no existe.");
    }
  }else
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //CONFIRMAR
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  if($action=="confirmar"){
    statusMsg("Cuenta de $email activada");
    header("Refresh:1;url=$SITEURL/usuarios.php");
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
  <a href="$SITEURL/usuarios.php?mode=cambiar&email=$email&pass=$pass">Click para cambiar su contraseña</a>
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
	if($subpass==$results["password"]){
	  statusMsg("Ingreso exitoso");
	  session_start();
	  foreach(array_keys($results) as $key){
	    if(preg_match("/^\d$/",$key)){continue;}
	    $_SESSION["$key"]=$results[$key];
	  }
	  if(preg_match("/usuarios/",$urlref)){$urlref="index.php";}
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
$content.=<<<C
$FORM
<h3>Usuario existente</h3>
<input type="hidden" name="urlref" value="$urlref">
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
  <td colspan=2>
    <input type="submit" name="action" value="Entrar">
  </td>
</tr>
<tr>
  <td colspan=2>
    <a href=?mode=nuevo>Nuevo usuario</a> |
    <a href=?mode=recupera>Recuperar contraseña</a>
  </td>
</tr>
</table>
</form>
</div>
C;
}else{
  if(0){}
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //NUEVO USUARIO
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  else if($mode=="nuevo"){
$content.=<<<C
<form>
<h3>Nuevo usuario</h3>
<input type="hidden" name="mode" value="nuevo">
<table>
<tr>
  <td>Nombre:</td>
  <td><input type="text" name="nombre" placeholder="Nombre completo" value="$nombre"></td>
</tr>
<tr>
  <td>Documento:</td>
  <td><input type="text" name="documento" placeholder="66666666" value="$documento"></td>
</tr>
<tr>
  <td>E-mail:</td>
  <td><input type="text" name="email" placeholder="Su e-mail" value="$email"></td>
</tr>
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
    echo "EMAIL: $email<br/>";
    $results=mysqlCmd("select * from Usuarios where email='$email'");
    $spass=$results["password"];
    $nombre=$results["nombre"];
    $documento=$results["documento"];
    $tipo=$results["tipo"];
    $spass=$results["password"];
    $tiposel=generateSelection($TIPOS,"tipo",$tipo);

    if($pass==$spass){
$content.=<<<C
$FORM
<h3>Cambio de Información para $nombre</h3>
<table>
<tr>
  <td>Nombre:</td>
  <td><input name="nombre" value="$nombre"></td>
</tr>
<tr>
  <td>Documento:</td>
  <td><input name="documento" value="$documento"></td>
</tr>
<tr>
  <td>Tipo:</td>
  <td>$tiposel</td>
</tr>
<tr>
  <td>E-mail:</td>
  <td><input name="email" value="$email"></td>
</tr>
<tr>
  <td>Contraseña actual:</td>
  <td><input type="password" name="pass" placeholder="Su contraseña actual" value="$pass"></td>
</tr>
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
//FOOTER AND RENDER
////////////////////////////////////////////////////////////////////////
end:
$content.="</div>";
$content.=getMessages();
$content.=getFooter();
echo $content;
?>
</html>
