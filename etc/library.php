<?php
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

function generateSelection($values,$name,$value,$disabled="",$readonly=0)
{
  $parts=$values;
  $selection="";
  if($readonly){
    $selection.="<input type='hidden' name='$name' value='$value'>";
    $selection.=$value;
    return $selection;
  }
  $selection.="<select name='$name' style='' $disabled>";
  foreach(array_keys($parts) as $part){
    $show=$parts[$part];
    $selected="";
    if($part==$value){$selected="selected";}
    $selection.="<option value='$part' $selected>$show";
  }
  $selection.="</select>";
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

function generateRandomString($length = 10) {
  $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
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
  $mail->setFrom($mail->Username, 'Sistema de Solicitud de Comisiones FCEN/UdeA');
  $mail->addReplyTo($mail->Username, 'Sistema de Solicitud de Comisiones FCEN/UdeA');
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
<b>Sistema de Solicitud de Comisiones<br/>
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

?>
