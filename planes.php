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
  Modulo de Planes de Estudio
</div>
<div class="submenu">
  <a href="?">Inicio</a> 
  | <a href="?mode=mode">Modo</a>
</div>
M;

////////////////////////////////////////////////////////////////////////
//BODY
////////////////////////////////////////////////////////////////////////
$content.=<<<C
<p>
wLorem ipsum dolor sit amet, consectetur adipiscing elit. Donec auctor,
diam id pretium tempor, arcu massa finibus turpis, quis blandit odio
lorem quis est. Vivamus gravida pretium neque at dictum. Vivamus
lacinia arcu efficitur turpis consectetur porta. Morbi ex tortor,
hendrerit eu fermentum et, mollis non libero. Pellentesque molestie
neque eu tortor condimentum, ac vulputate dolor dictum. Aliquam et
hendrerit lectus, id aliquet eros. Curabitur ut vulputate tortor. Cras
feugiat viverra auctor. Integer posuere, nisi quis tincidunt cursus,
turpis orci facilisis urna, quis egestas est tortor vel ex. Maecenas
commodo aliquet sapien non efficitur.
</p>
<p>
Suspendisse vestibulum fermentum nisl, id auctor magna. Suspendisse
non tincidunt magna, elementum sollicitudin arcu. Morbi eu justo at
arcu fermentum semper non at lorem. Suspendisse elit arcu, placerat ac
mi vitae, tincidunt maximus metus. Morbi libero turpis, viverra et
laoreet ut, semper sit amet quam. Ut nisi urna, consectetur nec
fermentum ac, convallis vestibulum purus. Quisque ac sodales
metus. Donec consectetur est in orci lacinia, id sagittis erat
iaculis. Nunc ac ante ut diam efficitur lobortis nec quis
sem. Vestibulum id pulvinar elit. Donec leo velit, facilisis vel felis
eu, ornare vulputate diam. Proin ut urna at nunc fermentum
placerat. Nullam euismod eu mi in convallis. Maecenas commodo congue
mauris, ac accumsan massa pellentesque ac. Aenean malesuada ac nibh
quis interdum. Duis turpis nibh, tincidunt eget vehicula in, venenatis
nec lorem.
</p>
<p>
Pellentesque tempor posuere arcu, egestas tristique nibh finibus
tincidunt. Donec tincidunt ut sapien quis ultricies. Proin sit amet
laoreet lacus. Pellentesque odio mi, placerat vitae tristique vitae,
semper in lacus. Quisque faucibus pellentesque enim, ut aliquam metus
dignissim a. Suspendisse suscipit enim quis quam sagittis, ut bibendum
justo sodales. Proin ac porta arcu. Vestibulum quis eros et leo
eleifend tincidunt in sed turpis. Cras scelerisque, arcu a tempus
efficitur, magna sapien scelerisque ante, et iaculis risus diam non
nulla. Nunc et ipsum efficitur, rhoncus mi eget, ultrices
orci. Maecenas mattis a urna sed scelerisque. Suspendisse aliquet elit
a nisl varius, sit amet elementum felis dapibus. Morbi consectetur
augue quis convallis posuere. Cras sed turpis metus.
</p>
<p>
Curabitur risus neque, commodo eget tortor id, consectetur ultrices
magna. Nullam purus nunc, accumsan ut feugiat vitae, commodo at
quam. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices
posuere cubilia Curae; Vivamus ipsum dolor, consectetur vitae magna
ut, molestie cursus justo. Nam consequat suscipit mauris, ac sagittis
augue tincidunt vel. Donec vestibulum convallis velit, non tempor dui
sagittis et. Vivamus vitae magna eros. Morbi scelerisque purus a neque
maximus, sed bibendum nulla auctor. Proin vel aliquet metus, mollis
semper libero. Cras tristique, lacus nec pulvinar feugiat, neque erat
porttitor nulla, vitae luctus massa felis a odio. Vivamus sagittis
tempus purus, id aliquet odio tempor id. Fusce porta enim a mi euismod
euismod. Etiam non magna at sapien tempus faucibus. Nunc pulvinar
turpis dignissim ultrices blandit. Vestibulum ornare suscipit turpis a
varius.
</p>
<p>
Duis sed ipsum id magna malesuada porttitor ut in dui. Sed non massa
ipsum. Aenean cursus felis fermentum, tincidunt nibh sed, molestie
purus. Proin mattis eros tempus risus gravida placerat. Proin
pellentesque nunc a semper posuere. Nullam tincidunt nisl ante, sit
amet sagittis nunc rutrum vitae. Nullam in justo viverra, elementum
velit a, bibendum lacus. Praesent sit amet risus eros. Vestibulum
cursus enim ut nisl pharetra porta. Sed finibus tincidunt posuere. In
hac habitasse platea dictumst. Vivamus in condimentum nisl.
</p>
C;

////////////////////////////////////////////////////////////////////////
//FOOTER AND RENDER
////////////////////////////////////////////////////////////////////////
$content.=getFooter();
echo $content;
?>
</html>
