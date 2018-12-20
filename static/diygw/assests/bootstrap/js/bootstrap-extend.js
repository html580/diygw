(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as an anonymous module unless amdModuleId is set
        define(["jquery"], function (a0) {
            return (factory(a0));
        });
    } else if (typeof module === 'object' && module.exports) {
        // Node. Does not work with strict CommonJS, but
        // only CommonJS-like environments that support module.exports,
        // like Node.
        module.exports = factory(require("jquery"));
    } else {
        factory(root["jQuery"]);
    }
}(this, function (jQuery) {

    (function ($) {
        "use strict";
        var obj;// 表单元素暂存
        var gettype=Object.prototype.toString;// 取得类型
        var setVal = function (name, ival) {
            var $oinput = obj.find(":input[name='" + name + "']");// 取得当前字段对应的元素
            if ($oinput.attr("type") == "checkbox") {// 复选框类型
                if (ival !== null) {
                    var checkboxObj = $("[name='" + name + "']");
                    var checkArray = ival.split(",");
                    for (var i = 0; i < checkboxObj.length; i++) {
                        for (var j = 0; j < checkArray.length; j++) {
                            if (checkboxObj[i].value == checkArray[j]) {
                                checkboxObj[i].click();
                            }
                        }
                    }
                }
            }
            else if ($oinput.attr("type") == "radio") {// 单选按钮
                $oinput.each(function () {
                    var radioObj = $("[name='" + name + "']");
                    for (var i = 0; i < radioObj.length; i++) {
                        if (radioObj[i].value == ival) {
                            radioObj[i].click();
                        }
                    }
                });
            }
            else if ($oinput.attr("type") == "textarea") {// 文本域
                obj.find("[name='" + name + "']").html(ival);
            }
            else if ($oinput[0] && $oinput[0].tagName == "SELECT") {// 下拉选择
                obj.find("[name='" + name + "']").val(ival).trigger("change");
            }
            else {// 其它
                var field = obj.find("[name='" + name + "']");
                if(field.length>0){
                    if(field.is(".usereditor")){
                        var _editor = UE.getEditor(field.data("id")).setContent(ival);
                    }else{
                        field.val(ival);
                        field.each(function(){
                            if($(this).is(".image")){
                                var value = $(this).val();
                                var parent = $(this).parent();
                                var arg = value.split(",");
                                var template = _.template($("#template"+parent.data("id")).html());
                                if(arg.length>0){
                                    if(parent.data("multi")=="0"){
                                        parent.find('.uploader-box').remove();
                                    }
                                    for(var i=0;i<arg.length;i++){
                                        var html = template({img:arg[i]});
                                        parent.append(html);
                                    }
                                }
                            }
                        });
                    }

                }
            }
        };

        /**
         * 将josn对象赋值给form,
         * 1. 支持1级 对象.属性,
         * 2. 支持2级 对象.对象.属性
         * 3. 不支持数组元素
         * 3. 复选框用","号分隔的字符串
         * 4. 下拉目前只支持单选
         *  @param {dom} 指定的选择器
         * @param {obj} 需要给form赋值的json对象
         * @method serializeJson
         * */
        $.fn.setform = function(jsonValue){
            obj = this;
            $.each(jsonValue,function(name,ival){
                if (gettype.call(ival) =="[object Object]"){// 处理对象
                    $.each(ival,function (key,val) {
                        setVal(name+"."+key,val);
                    });
                }else if(gettype.call(ival) =="[object Array]"){// 处理数组
                    console.log("["+name + "][object Array]字段是数组类型,斩不支持");
                }else {
                    setVal(name, ival);// 普通类型
                }
            });
        }
    })(jQuery);

    +function ($) {
        "use strict";
        var Toast = {
            _positionClasses : ['bottom-left', 'bottom-right', 'top-right', 'top-left', 'bottom-center', 'top-center', 'mid-center'],
            _defaultIcons : ['success', 'error', 'info', 'warning'],

            init: function (options, elem) {
                this.prepareOptions(options, $.toast.options);
                this.process();
            },

            prepareOptions: function(options, options_to_extend) {
                var _options = {};
                if ( ( typeof options === 'string' ) || ( options instanceof Array ) ) {
                    _options.text = options;
                } else {
                    _options = options;
                }
                this.options = $.extend( {}, options_to_extend, _options );
            },

            process: function () {
                this.setup();
                this.addToDom();
                this.position();
                this.bindToast();
                this.animate();
            },

            setup: function () {

                var _toastContent = '';

                this._toastEl = this._toastEl || $('<div></div>', {
                        class : 'jq-toast-single'
                    });

                // For the loader on top
                _toastContent += '<span class="jq-toast-loader"></span>';

                if ( this.options.allowToastClose ) {
                    _toastContent += '<span class="close-jq-toast-single">&times;</span>';
                };

                if ( this.options.text instanceof Array ) {

                    if ( this.options.heading ) {
                        _toastContent +='<h2 class="jq-toast-heading">' + this.options.heading + '</h2>';
                    };

                    _toastContent += '<ul class="jq-toast-ul">';
                    for (var i = 0; i < this.options.text.length; i++) {
                        _toastContent += '<li class="jq-toast-li" id="jq-toast-item-' + i + '">' + this.options.text[i] + '</li>';
                    }
                    _toastContent += '</ul>';

                } else {
                    if ( this.options.heading ) {
                        _toastContent +='<h2 class="jq-toast-heading">' + this.options.heading + '</h2>';
                    };
                    _toastContent += this.options.text;
                }

                this._toastEl.html( _toastContent );

                if ( this.options.bgColor !== false ) {
                    this._toastEl.css("background-color", this.options.bgColor);
                };

                if ( this.options.textColor !== false ) {
                    this._toastEl.css("color", this.options.textColor);
                };

                if ( this.options.textAlign ) {
                    this._toastEl.css('text-align', this.options.textAlign);
                }

                if ( this.options.icon !== false ) {
                    this._toastEl.addClass('jq-has-icon');

                    if ( $.inArray(this.options.icon, this._defaultIcons) !== -1 ) {
                        this._toastEl.addClass('jq-icon-' + this.options.icon);
                    };
                };
            },

            position: function () {
                if ( ( typeof this.options.position === 'string' ) && ( $.inArray( this.options.position, this._positionClasses) !== -1 ) ) {

                    if ( this.options.position === 'bottom-center' ) {
                        this._container.css({
                            left: ( $(window).outerWidth() / 2 ) - this._container.outerWidth()/2,
                            bottom: 20
                        });
                    } else if ( this.options.position === 'top-center' ) {
                        this._container.css({
                            left: ( $(window).outerWidth() / 2 ) - this._container.outerWidth()/2,
                            top: 20
                        });
                    } else if ( this.options.position === 'mid-center' ) {
                        this._container.css({
                            left: ( $(window).outerWidth() / 2 ) - this._container.outerWidth()/2,
                            top: ( $(window).outerHeight() / 2 ) - this._container.outerHeight()/2
                        });
                    } else {
                        this._container.addClass( this.options.position );
                    }

                } else if ( typeof this.options.position === 'object' ) {
                    this._container.css({
                        top : this.options.position.top ? this.options.position.top : 'auto',
                        bottom : this.options.position.bottom ? this.options.position.bottom : 'auto',
                        left : this.options.position.left ? this.options.position.left : 'auto',
                        right : this.options.position.right ? this.options.position.right : 'auto'
                    });
                } else {
                    this._container.addClass( 'bottom-left' );
                }
            },

            bindToast: function () {

                var that = this;

                this._toastEl.on('afterShown', function () {
                    that.processLoader();
                });

                this._toastEl.find('.close-jq-toast-single').on('click', function ( e ) {

                    e.preventDefault();

                    if( that.options.showHideTransition === 'fade') {
                        that._toastEl.trigger('beforeHide');
                        that._toastEl.fadeOut(function () {
                            that._toastEl.trigger('afterHidden');
                        });
                    } else if ( that.options.showHideTransition === 'slide' ) {
                        that._toastEl.trigger('beforeHide');
                        that._toastEl.slideUp(function () {
                            that._toastEl.trigger('afterHidden');
                        });
                    } else {
                        that._toastEl.trigger('beforeHide');
                        that._toastEl.hide(function () {
                            that._toastEl.trigger('afterHidden');
                        });
                    }
                });

                if ( typeof this.options.beforeShow == 'function' ) {
                    this._toastEl.on('beforeShow', function () {
                        that.options.beforeShow();
                    });
                };

                if ( typeof this.options.afterShown == 'function' ) {
                    this._toastEl.on('afterShown', function () {
                        that.options.afterShown();
                    });
                };

                if ( typeof this.options.beforeHide == 'function' ) {
                    this._toastEl.on('beforeHide', function () {
                        that.options.beforeHide();
                    });
                };

                if ( typeof this.options.afterHidden == 'function' ) {
                    this._toastEl.on('afterHidden', function () {
                        that.options.afterHidden();
                    });
                };

                if ( typeof this.options.onClick == 'function' ) {
                    this._toastEl.on('click', function () {
                        that.options.onClick();
                    });
                };
            },

            addToDom: function () {

                var _container = $('.jq-toast-wrap');

                if ( _container.length === 0 ) {

                    _container = $('<div></div>',{
                        class: "jq-toast-wrap"
                    });

                    $('body').append( _container );

                } else if ( !this.options.stack || isNaN( parseInt(this.options.stack, 10) ) ) {
                    _container.empty();
                }

                _container.find('.jq-toast-single:hidden').remove();

                _container.append( this._toastEl );

                if ( this.options.stack && !isNaN( parseInt( this.options.stack ), 10 ) ) {

                    var _prevToastCount = _container.find('.jq-toast-single').length,
                        _extToastCount = _prevToastCount - this.options.stack;

                    if ( _extToastCount > 0 ) {
                        $('.jq-toast-wrap').find('.jq-toast-single').slice(0, _extToastCount).remove();
                    };

                }

                this._container = _container;
            },

            canAutoHide: function () {
                return ( this.options.hideAfter !== false ) && !isNaN( parseInt( this.options.hideAfter, 10 ) );
            },

            processLoader: function () {
                // Show the loader only, if auto-hide is on and loader is demanded
                if (!this.canAutoHide() || this.options.loader === false) {
                    return false;
                }

                var loader = this._toastEl.find('.jq-toast-loader');

                // 400 is the default time that jquery uses for fade/slide
                // Divide by 1000 for milliseconds to seconds conversion
                var transitionTime = (this.options.hideAfter - 400) / 1000 + 's';
                var loaderBg = this.options.loaderBg;

                var style = loader.attr('style') || '';
                style = style.substring(0, style.indexOf('-webkit-transition')); // Remove the last transition definition

                style += '-webkit-transition: width ' + transitionTime + ' ease-in; \
	                      -o-transition: width ' + transitionTime + ' ease-in; \
	                      transition: width ' + transitionTime + ' ease-in; \
	                      background-color: ' + loaderBg + ';';


                loader.attr('style', style).addClass('jq-toast-loaded');
            },

            animate: function () {

                var that = this;

                this._toastEl.hide();

                this._toastEl.trigger('beforeShow');

                if ( this.options.showHideTransition.toLowerCase() === 'fade' ) {
                    this._toastEl.fadeIn(function ( ){
                        that._toastEl.trigger('afterShown');
                    });
                } else if ( this.options.showHideTransition.toLowerCase() === 'slide' ) {
                    this._toastEl.slideDown(function ( ){
                        that._toastEl.trigger('afterShown');
                    });
                } else {
                    this._toastEl.show(function ( ){
                        that._toastEl.trigger('afterShown');
                    });
                }

                if (this.canAutoHide()) {

                    var that = this;

                    window.setTimeout(function(){

                        if ( that.options.showHideTransition.toLowerCase() === 'fade' ) {
                            that._toastEl.trigger('beforeHide');
                            that._toastEl.fadeOut(function () {
                                that._toastEl.trigger('afterHidden');
                            });
                        } else if ( that.options.showHideTransition.toLowerCase() === 'slide' ) {
                            that._toastEl.trigger('beforeHide');
                            that._toastEl.slideUp(function () {
                                that._toastEl.trigger('afterHidden');
                            });
                        } else {
                            that._toastEl.trigger('beforeHide');
                            that._toastEl.hide(function () {
                                that._toastEl.trigger('afterHidden');
                            });
                        }

                    }, this.options.hideAfter);
                };
            },

            reset: function ( resetWhat ) {

                if ( resetWhat === 'all' ) {
                    $('.jq-toast-wrap').remove();
                } else {
                    this._toastEl.remove();
                }

            },

            update: function(options) {
                this.prepareOptions(options, this.options);
                this.setup();
                this.bindToast();
            }
        };

        $.toast = function(options) {
            var toast = Object.create(Toast);
            toast.init(options, this);

            return {

                reset: function ( what ) {
                    toast.reset( what );
                },

                update: function( options ) {
                    toast.update( options );
                }
            }
        };

        $.toast.options = {
            text: '',
            heading: '',
            showHideTransition: 'fade',
            allowToastClose: true,
            hideAfter: 2000,
            loader: true,
            loaderBg: '#9EC600',
            stack: 5,
            position: 'bottom-left',
            bgColor: false,
            textColor: false,
            textAlign: 'left',
            icon: false,
            beforeShow: function () {},
            afterShown: function () {},
            beforeHide: function () {},
            afterHidden: function () {},
            onClick: function () {}
        };
    }(jQuery);

    +function ($) {
        "use strict";

        $.extend({
            getUrlParams:function(){
				/*var pattern = /(\w*)=([a-zA-Z0-9\u4e00-\u9fa5]+)/ig, params = {};//定义正则表达式和一个空对象
				 decodeURIComponent(window.location.href, true).replace(pattern, function(a, b, c){ params[b] = c; });
				 return params;*/
                var url = window.location.href;
                var _params = {},
                    qStart = url.indexOf('?'),
                    hStart = url.indexOf('#'),
                    q = url.substr(qStart + 1),
                    tmp,
                    parts,
                    i;

                if(hStart === -1) hStart = url.length;

                if(q) {
                    tmp = q.split('&');
                    i = tmp.length;
                    while(i--) {
                        parts = tmp[i].split('=');
                        _params[parts[0]] = decodeURIComponent(parts[1]).replace(/\+/g,' ');
                        //_params[parts[0]] = parts[1];
                    }
                }
                return _params;
            },
            getUrlParam:function(name){
                return this.getUrlParams()[name];
            },
            getJsonData:function($thiz){
                var postData={};
                $.each($thiz.data(),function(name,value){
                    if(typeof value != 'object' && name !='url'){
                        postData[name]=value;
                    }
                });
                return postData;
            },
            getParamData:function($thiz,option){
                var postData= this.getJsonData($thiz);
                postData = $.extend(postData,option||{});
                return $.param(postData);
            },
            arrToStr:function(arr){
                return (arr|| []).join(',').replace(/^[]/, '')
            }
        });

        $(window).on('load', function () {
            if($('.uploader-input').length>0){
                var _editor = UE.getEditor('upload_ue');
                window.picEditor = _editor;
                _editor.ready(function () {
                    _editor.setDisabled('insertimage');
                    _editor.hide();//此处隐藏图片上传的编辑器
                    _editor.addListener('afterinsertimage', function (t, arg) {
                        _editor.setContent('');
                    });
                });
            };

            $(".usereditor").each(function(){
                UE.getEditor($(this).attr("id"));
            });

            $('[data-ride="form"]').each(function () {
                var $thiz= $(this);
                $thiz.bootstrapValidator().on('success.form.bv', function(e) {
                    e.preventDefault();
                    var form = $(e.target);
                    var jc = null;
                    form.ajaxSubmit({
                        type:"post",
                        dataType:'json',
                        beforeSend:function(){
                            jc = $.dialog({
                                title: '温馨提示',
                                content :'正在保存数据，请等待...'
                            });
                        },
                        success: function(result) {
                            if(jc){jc.close();}
                            $.toast({
                                heading: '温馨提示',
                                text: result.message,
                                position: 'top-center',
                                stack: false
                            });
                            if (result.status == true || result.status == 'true' || result.status == 'success') {
                                var goPage = result.goPage;
                                if(goPage){
                                    location.href = goPage;
                                }else{
                                    if($thiz.attr("href")){
                                        location.href = $thiz.attr("href");
                                    }else{
                                        history.back();
                                    }
                                }
                            }
                            $thiz.bootstrapValidator('disableSubmitButtons', false);
                        }
                    });
                });;
            });


            //修改页面
            if($.getUrlParam("_ajax_edit_page")&&$.getUrlParam("_ajax_edit_page")=='1'){
                var loadlength =  $('[data-ride="loadData"]').length;
                var timeoutid=null;

                function ajaxEditData(){
                    var pattern = /(\w*)=([a-zA-Z0-9_\u4e00-\u9fa5]+)/ig, postData = {};//定义正则表达式和一个空对象
                    decodeURIComponent(window.location.href, true).replace(pattern, function(a, b, c){ postData[b] = c; });
                    var $thiz = $("form#form"+postData.formid);
                    if($thiz.length==0){
                        $thiz = $("form.form"+postData.formid);
                    }
                    if($thiz.length==1){
                        $.ajax({
                            type: 'POST',
                            url: $thiz.data('url'),
                            data: postData ,
                            dataType: 'json',
                            success: function(data){
                                if (data.status == 'success' || data.status == 1) {
                                    $thiz.setform(data.rows[0]);
                                } else {
                                    $.toast({
                                        heading: '温馨提示',
                                        text: data.message,
                                        position: 'top-center',
                                        stack: false,
                                        icon: 'success'
                                    });
                                }
                            },
                            error: function(xhr, type){
                                $thiz.data("load", "1");
                                $.toast({
                                    heading: '温馨提示',
                                    text: '加载数据出错',
                                    position: 'top-center',
                                    stack: false,
                                    icon: 'error'
                                });
                            }
                        });
                    }

                }
                function pdata(){
                    //如果已经加载完毕，直接取数
                    var length = 0;
                    $('[data-ride="loadData"]').each(function(){
                        if($(this).data("load")=="1"){
                            length++;
                        }
                    });
                    if(length== loadlength){
                        if(timeoutid!=null){
                            clearTimeout(timeoutid);
                        }
                        ajaxEditData();
                    }else{
                        timeoutid= setTimeout(function() {
                            pdata();
                        }, 100);
                    }
                }
                pdata();

            }
            //编辑表单结束

            //编辑图片组件开始
            $(document).off('change.input-image','input.image').on('change.input-image','input.image',function(e){
                e.stopPropagation();
                e.preventDefault();
                var arr = [];
                $(this).siblings(".uploader-box").each(function(){
                    var src = $(this).find("img").attr('src');
                    if(src.indexOf('/images/upload.jpg')<0){
                        arr.push(src);
                    }
                });
                $(this).val($.arrToStr(arr));
                $(this).trigger("input.update.bv");
            });
            $(document).off('click.uploader-input','.uploader-box').on('click.uploader-box','.uploader-box',function(e){
                e.stopPropagation();
                e.preventDefault();
                var $thiz = $(this);
                var parent = $thiz.parent();
                window.picEditor.setOptFalse("selectOneImage",parent.data("multi")=="0");
                var myImage = window.picEditor.getDialog("insertimage");
                window.picEditor.removeListener('beforeInsertImage');
                window.picBox = $thiz;
                window.picEditor.addListener('beforeInsertImage', function (t, arg) {
                    var parent = window.picBox.parent();
                    if(parent.length>0){
                        var template = _.template($("#template"+parent.data("id")).html());
                        if(arg.length>0){
                            if(parent.data("multi")=="0"){
                                parent.find('.uploader-box').remove();
                            }
                            var imgs = parent.find('input.image').val();
                            for(var i=0;i<arg.length;i++){
                                if(parent.data("multi")=="0") {
                                    var html = template({img: arg[i].src});
                                    parent.append(html);
                                }else{
                                    if (imgs.indexOf(arg[i].src) < 0) {
                                        var html = template({img: arg[i].src});
                                        parent.append(html);
                                    }

                                }
                            }
                            parent.find('input.image').trigger('change.input-image');
                        }
                    }
                });
                myImage.open();
            });
            $(document).off('click.uploader-input.btn','.uploader-box .btn').on('click.uploader-box.btn','.uploader-box .btn',function(e){
                e.stopPropagation();
                e.preventDefault();
                var box = $(this).closest(".uploader-box");
                var parent = box.parent();
                if(parent.data("multi")=="0"){
                    box.find("img").attr("src","/static/images/upload.jpg");
                    box.find(".btn-toolbar").remove();
                }else{
                    box.remove();
                }
                parent.find('input.image').trigger('change.input-image');
            });
            //编辑图片组件结束

            $(document).off('click.ajaxremove','.ajax-remove').on('click.ajaxremove','.ajax-remove',function(e){
                e.stopPropagation();
                e.preventDefault();
                var thiz= $(this);
                function remove(){
                    $.ajax({
                        type:"post",
                        dataType:'json',
                        url : (thiz.data('url')||thiz.attr('url')||thiz.data('href')||thiz.attr('href')||'/data/remove.html'),
                        data:thiz.data(),
                        success: function(result) {
                            if (result.status == 'success') {
                                $.toast({
                                    heading: '温馨提示',
                                    text: thiz.data('successmsg')||result.message,
                                    position: 'top-center',
                                    stack: false,
                                    icon: 'success'
                                });
                                if(trigger&&$(trigger)){
                                    $(trigger).trigger("removeSuccess");
                                }
                            }else{
                                $.toast({
                                    heading: '温馨提示',
                                    text: result.message,
                                    position: 'top-center',
                                    stack: false,
                                    icon: 'error'
                                });
                            }
                        }
                    });
                }
                $.confirm({
                    keyboardEnabled: true,
                    title: '温馨提示',
                    content: thiz.data('removemsg')||'你确定删除吗？',
                    confirmButton: false,
                    columnClass: 'col-xs-4 col-xs-offset-4',
                    buttons: {
                        confirm:{
                            btnClass: 'btn-primary',
                            text: '确定',
							action:function () {
                                 remove();
                            },
						},
                        cancel: {
                            btnClass: 'btn-danger',
                            text: '取消',
                        }
                    }
                });

                return false;
            });

        });

    }(jQuery);
}));
	   