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
  if(0){}
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
    if(strlen($ERRORS)==0){
      //CHECK IF USER EXISTS
      if($results=mysqlCmd("select * from Usuarios where email='$email'")){
	$subpass=md5($password);
	$nombre=$results["nombre"];
	if($subpass==$results["password"]){
	  statusMsg("Ingreso exitoso");
	  $urlref="index.php";
	  if(!preg_match("/usuarios/",$REFERER)){
	    $urlref=$REFERER;
	  }
	  echo "Start session:<br/>";
	  session_start();
	  $_SESSION["nombre"]=$nombre;
	  header("Refresh:1;url=$urlref");
	}else{
	  errorMsg("Password invalido");
	}
      }else{
	errorMsg("Usuario no existe");
      }
    }
  }
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  //FINAL
  //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
  else{}
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
<form>
<h3>Usuario existente</h3>
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
<table>
<tr>
  <td>Nombre:</td>
  <td><input type="text" name="nombre" placeholder="Nombre completo"></td>
</tr>
<tr>
  <td>E-mail:</td>
  <td><input type="text" name="email" placeholder="Su e-mail"></td>
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
