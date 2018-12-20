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
            $("#sidebar").find("a").removeClass("active");
            $(this).parent().addClass("active");
        }
        return true;
    });

    var my_skins = [
        "skin-blue",
        "skin-black",
        "skin-red",
        "skin-yellow",
        "skin-purple",
        "skin-green",
        "skin-blue-light",
        "skin-black-light",
        "skin-red-light",
        "skin-yellow-light",
        "skin-purple-light",
        "skin-green-light"
    ];


    /**
     * Store a new settings in the browser
     * @param String name Name of the setting
     * @param String val Value of the setting
     * @returns void
     */
    function store(name, val) {
        if (typeof (Storage) !== "undefined") {
            if(val==null){
                localStorage.removeItem(name);
            }else{
                localStorage.setItem(name, val);
            }

        } else {
            $.cookie(name,val);
        }
    }

    /**
     * Get a prestored setting
     * @param String name Name of of the setting
     * @returns String The value of the setting | null
     */
    function get(name) {
        if (typeof (Storage) !== "undefined") {
            return localStorage.getItem(name);
        } else {
            return $.cookie(name);
        }
    }

    function change_layout(cls) {
        $("body").toggleClass(cls);
    }

    function setup() {

        function change_skin() {
            $.each(my_skins, function (i) {
                $("body").removeClass(my_skins[i]);
            });
            var skin = get('skin');
            if(skin){
                $("body").addClass(skin);
            }else{
                $("body").addClass('skin-green-light');
            }
            return false;
        }
        //change_skin();
        //Add the change skin listener
        $("[data-skin]").on('click', function (e) {
            e.preventDefault();
            store("skin", $(this).data('skin'));
            change_skin();
        });

        //Add the layout manager
        function layoutBox(){
            var layout_boxed = get("layout-boxed");
            if(layout_boxed){
                $("body").addClass("layout-boxed");
                $("[data-layout='layout-boxed']").attr("checked","checked");
            }else{
                $("body").removeClass("layout-boxed");
                $("[data-layout='layout-boxed']").removeAttr("checked");
            }
        }
        $("[data-layout='layout-boxed']").on('click', function () {
            if($(this).is(":checked")){
                store("layout-boxed", "layout-boxed");
            }else{
                store("layout-boxed", null);
            }
            layoutBox();
        });
        layoutBox();


        $("[data-controlsidebar]").on('click', function () {
            if($(".control-sidebar").is(".control-sidebar-open")){
                $(".control-sidebar").removeClass("control-sidebar-open");
            }else{
                $(".control-sidebar").addClass("control-sidebar-open");
            }
        });

        function changeSidebarSkin(){
            var sidebar = $(".control-sidebar");
            var control_sidebar_skin = get("control-sidebar-skin");
            if (control_sidebar_skin == "control-sidebar-light") {
                sidebar.removeClass("control-sidebar-dark");
                sidebar.addClass("control-sidebar-light");
                $("[data-sidebarskin='toggle']").removeAttr("checked");
            } else {
                $("[data-sidebarskin='toggle']").attr("checked","checked");
                sidebar.removeClass("control-sidebar-light");
                sidebar.addClass("control-sidebar-dark");
            }
        }
        $("[data-sidebarskin='toggle']").on('click', function () {
            if($(this).is(":checked")){
                store("control-sidebar-skin", "control-sidebar-dark");
            }else{
                store("control-sidebar-skin", "control-sidebar-light");
            }
            changeSidebarSkin();
        });
        changeSidebarSkin();

        $("[data-layout='sidebar-collapse']").on('click', function () {
            $('#menu-toggle').click();
        });
        /*$("[data-enable='expandOnHover']").on('click', function () {
         $(this).attr('disabled', true);
         if (!$('body').hasClass('sidebar-collapse'))
         $("[data-layout='sidebar-collapse']").click();
         });*/
    }
    setup();

    $("#theme").find("a").click(function(){
        var menu_style=$(this).data("style");
        $.cookie('menu_style',menu_style);
        $('body').addClass(menu_style + " header-fixed");
        //$('#theme-change').attr('href', globalApp.baseUrl+'/static/lib/uadmin/css/themes/style1/'+style+'.css');
    });

    if ($.cookie('style')) {
        var menu_style= $.cookie('menu_style');
        $('body').addClass(menu_style + " header-fixed");
        //$('#theme-change').attr('href', globalApp.baseUrl+'/static/lib/uadmin/css/themes/style1/'+style+'.css');
    };


    //BEGIN FULL SCREEN
    $('.btn-fullscreen').click(function() {

        if (!document.fullscreenElement &&    // alternative standard method
            !document.mozFullScreenElement && !document.webkitFullscreenElement && !document.msFullscreenElement ) {  // current working methods
            if (document.documentElement.requestFullscreen) {
                document.documentElement.requestFullscreen();
            } else if (document.documentElement.msRequestFullscreen) {
                document.documentElement.msRequestFullscreen();
            } else if (document.documentElement.mozRequestFullScreen) {
                document.documentElement.mozRequestFullScreen();
            } else if (document.documentElement.webkitRequestFullscreen) {
                document.documentElement.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
            }
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            } else if (document.msExitFullscreen) {
                document.msExitFullscreen();
            } else if (document.mozCancelFullScreen) {
                document.mozCancelFullScreen();
            } else if (document.webkitExitFullscreen) {
                document.webkitExitFullscreen();
            }
        }
    });
    //END FULL SCREEN


    //BEGIN BACK TO TOP
    $(window).scroll(function(){
        if ($(this).scrollTop() < 200) {
            $('#totop') .fadeOut();
        } else {
            $('#totop') .fadeIn();
        }
    });
    $('#totop').on('click', function(){
        $('html, body').animate({scrollTop:0}, 'fast');
        return false;
    });
    //END BACK TO TOP

});



