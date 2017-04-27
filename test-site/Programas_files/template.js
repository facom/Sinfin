
jQuery(function($){
    $("#toggle-menu").on('click',function(){
        $(this).next().slideToggle();
    });
    $('._sub_menu > img').remove();
    $('._sub_menu > .image-title').addClass('icon-home');
    $('.acordeon').on('click','h2',function(){
        var t = $(this);
        var td = t.next();
        var d = t.parent().siblings().find('div');
        td.slideToggle()
        d.slideUp()

    });

    var $header = $('#header'), $menu = $('#menu');
    var $aH = $header.height();
    $("#clr").css("height",$aH);
    $header.removeClass('fixed');
    $(window).on('scroll', function(){
        var scroll = $(this).scrollTop();
        if (scroll > $aH) {
            $header.addClass('fixed');
            $menu.css({'width':'auto','margin-top':'1em'});
        }else{
            $header.removeClass('fixed');
            $menu.css({'width':'100%','margin-top':'0'});
        }
    })

    var hD=$('#componente').height();
    var hw=$(window).height();
    if(hD<hw){
	$('#blank').css("height",0.2*hw);
    }
});

function movimiento(index,l){
    if(l === 1){
        if(index < 7){
            $('.evento_contenedor .borde:nth-child('+index+')').addClass('none').removeClass('block');
            index = index+3;
            $('.evento_contenedor .borde:nth-child('+(index)+')').addClass('block').removeClass('none');
        }
    }else{
        if(index > 0){
            index = index+3;
            $('.evento_contenedor .borde:nth-child('+index+')').addClass('none').removeClass('block');
            index = (-index+6)+1;
            $('.evento_contenedor .borde:nth-child('+index+')').addClass('block').removeClass('none');

        }
    }
}

function mover(index, obj){

    if(index == 0){
        obj.css('left',0);
    } else if(index > 0){
        obj.css('left','-'+100*index+'%');
    }
}

//script de boton de busqueda


$(document).ready(function(){
});

