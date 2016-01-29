$(document).ready(function(){
    //None
});

// Closure
(function() {
    /**
     * Decimal adjustment of a number.
     *
     * @param {String}  type  The type of adjustment.
     * @param {Number}  value The number.
     * @param {Integer} exp   The exponent (the 10 logarithm of the adjustment base).
     * @returns {Number} The adjusted value.
     */
    function decimalAdjust(type, value, exp) {
        // If the exp is undefined or zero...
        if (typeof exp === 'undefined' || +exp === 0) {
            return Math[type](value);
        }
        value = +value;
        exp = +exp;
        // If the value is not a number or the exp is not an integer...
        if (isNaN(value) || !(typeof exp === 'number' && exp % 1 === 0)) {
            return NaN;
        }
        // Shift
        value = value.toString().split('e');
        value = Math[type](+(value[0] + 'e' + (value[1] ? (+value[1] - exp) : -exp)));
        // Shift back
        value = value.toString().split('e');
        return +(value[0] + 'e' + (value[1] ? (+value[1] + exp) : exp));
    }

    // Decimal round
    if (!Math.round10) {
        Math.round10 = function(value, exp) {
            return decimalAdjust('round', value, exp);
        };
    }
    // Decimal floor
    if (!Math.floor10) {
        Math.floor10 = function(value, exp) {
            return decimalAdjust('floor', value, exp);
        };
    }
    // Decimal ceil
    if (!Math.ceil10) {
        Math.ceil10 = function(value, exp) {
            return decimalAdjust('ceil', value, exp);
        };
    }
})();

function defaultSuccess(result){
    alert('Success:\n'+result);
}
function defaultError(xhttp,status,error){
    alert('Error:\n'+getAttrs(error));
}

function getAttrs(object,context){
    var attributes="";
    var endline="<br/>";
    if(context=="alert"){endline="\n";}
    var attribute;
    for(attribute in object){
        attributes+=attribute+":"+object[attribute]+endline;
    }
    return attributes;
}

function ajaxDo(action,params,onsuc,onerr)
{
    if(typeof(params)=='undefined'){params='';}
    if(typeof(onsuc)=='undefined'){onsuc=defaultSuccess;}
    if(typeof(onerr)=='undefined'){onerr=defaultError;}

    var ajax='ajax.php';
    
    jQuery.ajax({
	url:'ajax.php?action='+action+'&params='+params,
	success:onsuc,
	error:onerr,
    });
}

function onUpdate(cursos)
{
    var $ecursos=$('.cursos');
    $ecursos.html(cursos);
}
function updateCourses(element)
{
    ajaxDo('updatecourses','planid:'+element[element.selectedIndex].value,onUpdate);
}

function oncUpdate(cursos)
{
    var $ecursos=$('.ccursos');
    $ecursos.html(cursos);
}
function updatecCourses(element)
{
    cplanid=element[element.selectedIndex].value;
    if(cplanid!='other'){
	$('.ccursos').show();
	$('.ccursos_input').hide();
	ajaxDo('updatecourses','planid:'+cplanid,oncUpdate);
    }else{
	$('.ccursos').hide();
	$('.ccursos_input').show();
    }
}

function onStudent(result)
{
    var info=JSON.parse(result);
    if(info){
	for(var key in info){
	    $('[name="'+key+'"]').val(info[key]);
	}
    }
}
function updateStudentForm(element)
{
    var documento=$(element).val();
    ajaxDo('updatestudent','documento:'+documento,onStudent);
}

function updateCredits(course,credit)
{
    var id=course.id;
    var parts=id.split("_");
    var value=course[course.selectedIndex].value;
    var props=value.split(":");
    var codigo=props[0];
    var creditos=props[1];
    var $credito_show=$('#'+credit);
    $credito_show.val(creditos);
    if(codigo=="000000"){
	$('#smasignatura_'+parts[1]+'_'+parts[2]).show();
	$('#smcodigo_'+parts[1]+'_'+parts[2]).show();
    }else{
	$('#smasignatura_'+parts[1]+'_'+parts[2]).hide();
	$('#masignatura_'+parts[1]+'_'+parts[2]).val('');
	$('#smcodigo_'+parts[1]+'_'+parts[2]).hide();
	$('#mcodigo_'+parts[1]+'_'+parts[2]).val('');
    }
}
function updateMateria(course)
{
    var id=course.id;
    var parts=id.split("_");
    var value=course[course.selectedIndex].value;
    var props=value.split(":");
    var codigo=props[0];
    var creditos=props[1];
    if(codigo=="000000"){
	$('#smmateria_'+parts[1]+'_'+parts[2]).show();
    }else{
	$('#smmateria_'+parts[1]+'_'+parts[2]).hide();
	$('#mmateria_'+parts[1]+'_'+parts[2]).val('');
    }
}



function updateAverage(numnota){
    var i;
    var n=0;
    var promedio=0;
    for(i=1;i<=3;i++){
	//GET NOTAS
	var nota="[name='nota_"+numnota+"_"+i+"']";
	var $val=jQuery(nota)[0]
	var value=$val['value'];
	if(value>0){
	    n++;
	    promedio+=Math.round10(value,-2);
	}
    }
    promedio/=n;
    for(i=1;i<=3;i++){
	var name="[name='definitiva_"+numnota+"_"+i+"']";
	var $def=jQuery(name)[0];
	$def['value']=Math.round10(promedio,-1);
    }
}
function updateUniv(elem){
    /*
    var univ=$(elem).val();
    var i;
    for(i=1;i<=10;i++){
	for(j=1;j<=3;j++){
	    name="[name='univ_"+i+"_"+j+"']";
	    var $def=jQuery(name)[0];
	    var val=$def['value'];
	    $def['value']=univ;
	}
    }
    */
}

function activateUniv(element){
    var $univ=$('#universidad');
    var programa=element[element.selectedIndex]['value'];
    if(programa=='other'){
	$univ.val('');
    }else{
	$univ.val('Universidad de Antioquia');
    }
    updateUniv('#universidad');
}

function addCourse(element,ctype)
{
    var parent=element.parentElement;
    var id=parent['id'];
    var parts=id.split("_");
    var type=parts[0];
    var section=parts[1];
    var nelem=parts[2];
    if(typeof(ctype)!="undefined"){type=ctype;}
    if(nelem==0){
	$('#'+type+'_'+section+'_0').css('display','none');
    }
    nelem=Math.round(nelem)+1
    var toggle=type+"_"+section+"_"+nelem;

    var $eblock=$('#i'+toggle);
    $eblock.css('display','block');

    var block=$('[name="q'+toggle+'"]')[0]
    block['value']=1;
}

function removeCourse(element)
{
    var parent=element.parentElement;
    var id=parent['id'];
    var parts=id.split("_");
    var type=parts[0];
    var section=parts[1];
    var nelem=parts[2];
    if(nelem=='1'){
	$('#'+type+'_'+section+'_0').css('display','block');
    }
    var toggle=type+"_"+section+"_"+nelem;
    var $eblock=$('#i'+toggle);
    $eblock.css('display','none');

    var block=$('[name="q'+toggle+'"]')[0]
    block['value']=0;
}

function addRecon(element)
{
    var parent=element.parentElement;
    var id=parent['id'];
    var parts=id.split("_");
    var section=parts[1];
    if(section==0){
	$('#reconocimiento_0').css('display','none');
    }
    section++;
    var toggle="reconocimiento_"+section;
    var $eblock=$('#i'+toggle);
    $eblock.css('display','block');

    var block=$('[name="q'+toggle+'"]')[0]
    block['value']=1;
}

function removeRecon(element)
{
    var parent=element.parentElement;
    var id=parent['id'];
    var parts=id.split("_");
    var section=parts[1];
    if(section==1){
	$('#reconocimiento_0').css('display','block');
    }
    var toggle="reconocimiento_"+section;
    var $eblock=$('#i'+toggle);
    $eblock.css('display','none');
    
    var block=$('[name="q'+toggle+'"]')[0]
    block['value']=0;
}

