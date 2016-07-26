<center>
<h4>Registro de actividad $actid</h4>

<form action="comaca.php?loadact" method="post" enctype="multipart/form-data" accept-charset="utf-8">
<input type="hidden" name="actid" value="$actid">

<table border=0px width=60% cellspacing=0px>

<tr class="field">
  <td class="campo" id="instituto">Instituto$helpicon</td>
  <td class="form">
    $institutosel
  </td>
</tr>
<tr class="ayuda" id="instituto_help" >
  <td colspan=2>Instituto al que esta adscrita la actividad.</td>
</tr>

<tr class="field">
  <td class="campo" id="tipo">Tipo de actividad$helpicon</td>
  <td class="form">
    $tiposel
  </td>
</tr>
<tr class="ayuda" id="tipo_help" >
  <td colspan=2>Tipo de actividad.</td>
</tr>

<tr class="field">
  <td class="campo" id="nombre">Nombre de la actividad$helpicon</td>
  <td class="form">
    <input type="text" name="nombre" value="$nombre">
  </td>
</tr>
<tr class="ayuda" id="nombre_help" >
  <td colspan=2>Nombre de la actividad.</td>
</tr>

<tr class="field">
  <td class="campo" id="lugar">Lugar de la actividad$helpicon</td>
  <td class="form">
    <input type="text" name="lugar" value="$lugar">
  </td>
</tr>
<tr class="ayuda" id="lugar_help" >
  <td colspan=2>Lugar de la actividad.</td>
</tr>

<tr class="field">
  <td class="campo" id="fecha">Fecha de la actividad$helpicon</td>
  <td class="form">
  $fecha_menu
  </td>
</tr>
<tr class="ayuda" id="fecha_help" >
  <td colspan=2>Fecha de la actividad.</td>
</tr>

<tr class="field">
  <td class="campo" id="hora">Hora de la actividad$helpicon</td>
  <td class="form">
    <input type="text" name="hora" value="$hora">
  </td>
</tr>
<tr class="ayuda" id="hora_help" >
  <td colspan=2>Hora de la actividad.</td>
</tr>

<tr class="field">
  <td class="campo" id="resumen">Resumen de la actividad$helpicon</td>
  <td class="form">
    <textarea name="resumen" rows="10" cols="50">$resumen</textarea>
  </td>
</tr>
<tr class="ayuda" id="resumen_help" >
  <td colspan=2>Resumen de la actividad.</td>
</tr>

<tr class="field">
  <td colspan=2 class="botones_simple">
    <input type="submit" name="action" value="Guardar">
    <input type="submit" name="action" value="Salir">
  </td>
</tr>

</table>
</center>
