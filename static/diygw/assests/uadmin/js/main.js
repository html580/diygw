$(function () {
    //BEGIN MENU SIDEBAR
    //$('#sidebar').css('min-height', '100%');
    $('#side-menu').metisMenu();
    $(window).on("load resize", function () {
        if ($(this).width() < 768) {
            $('body').removeClass();
            $('div.sidebar-collapse').addClass('collapse');
        } else {
            var menu_style = $.cookie('menu_style')||'sidebar-default';
            $('body').addClass(menu_style + ' header-fixed');
            $('div.sidebar-collapse').removeClass('collapse');
            $('div.sidebar-collapse').css('height', 'auto');
        }

        //$("#sidebar").css({height:$(window).height()-50});
        $("#page-wrapper").css({height:$(window).height()-50});
        if($('#pathFrame').parent().is(".design")){
            $('#pathFrame').css({height:$('.design').height()});
        }
    });
    if(!$('#pathFrame').parent().is(".design")){
        $('#pathFrame').iframeAutoHeight({
            debug: true,
            resetToMinHeight:true,
            minHeight: $(window).height()-50,
            diagnostics: true
        });
    }
    $("#sidebar").niceScroll({cursorcolor:"#666"});
    //$("#page-wrapper").niceScroll({cursorcolor:"#666"})

    $("[data-toggle='tooltip'], [data-hover='tooltip']").tooltip();

    $("[data-toggle='popover'], [data-hover='popover']").popover();

    $("[data-toggle='control-sidebar']").click(function(){
        if($(".control-sidebar").is(".control-sidebar-open")){
            $(".control-sidebar").removeClass("control-sidebar-open");
        }else{
            $(".control-sidebar").addClass("control-sidebar-open");
        }
    });

    $("#sidebar a").click(function(){
        if($(this).attr("target")=='pathFrame'){
            $("#sidebar").find("li").removeClass("active");
            $(this).parent().addClass("active");
        }
        return true;
    });




});



