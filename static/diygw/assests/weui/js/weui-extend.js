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


+function ($) {
  'use strict';

  // CSS TRANSITION SUPPORT (Shoutout: http://www.modernizr.com/)
  // ============================================================

  function transitionEnd() {
    var el = document.createElement('bootstrap');

    var transEndEventNames = {
      WebkitTransition : 'webkitTransitionEnd',
      MozTransition    : 'transitionend',
      OTransition      : 'oTransitionEnd otransitionend',
      transition       : 'transitionend'
    };

    for (var name in transEndEventNames) {
      if (el.style[name] !== undefined) {
        return { end: transEndEventNames[name] }
      }
    }

    return false; // explicit for ie8 ( ._.)
  }

  $.fn.transitionEnd = function(callback) {
    var events = ['webkitTransitionEnd', 'transitionend', 'oTransitionEnd', 'MSTransitionEnd', 'msTransitionEnd'],
      i, dom = this;

    function fireCallBack(e) {
      /*jshint validthis:true */
      if (e.target !== this) return;
      callback.call(this, e);
      for (i = 0; i < events.length; i++) {
        dom.off(events[i], fireCallBack);
      }
    }
    if (callback) {
      for (i = 0; i < events.length; i++) {
        dom.on(events[i], fireCallBack);
      }
    }
    return this;
  };

  
  // http://blog.alexmaccaw.com/css-transitions
  $.fn.emulateTransitionEnd = function (duration) {
    var called = false;
    var $el = this;
    $(this).one('bsTransitionEnd', function () { called = true });
    var callback = function () { if (!called) $($el).trigger($.support.transition.end) };
    setTimeout(callback, duration);
    return this
  };

  $(function () {
	FastClick.attach(document.body);  
    $.support.transition = transitionEnd();

    if (!$.support.transition) return;

    $.event.special.bsTransitionEnd = {
      bindType: $.support.transition.end,
      delegateType: $.support.transition.end,
      handle: function (e) {
        if ($(e.target).is(this)) return e.handleObj.handler.apply(this, arguments)
      }
    }
  })

  $.support = (function() {
	    var support = {
	      touch: !!(('ontouchstart' in window) || window.DocumentTouch && document instanceof window.DocumentTouch)
	    };
	    return support;
  })();
  
  $.touchEvents = {
    start: $.support.touch ? 'touchstart' : 'mousedown',
    move: $.support.touch ? 'touchmove' : 'mousemove',
    end: $.support.touch ? 'touchend' : 'mouseup'
  };

  $.getTouchPosition = function(e) {
    e = e.originalEvent || e; //jquery wrap the originevent
    if(e.type === 'touchstart' || e.type === 'touchmove' || e.type === 'touchend') {
      return {
        x: e.targetTouches[0].pageX,
        y: e.targetTouches[0].pageY
      };
    } else {
      return {
        x: e.pageX,
        y: e.pageY
      };
    }
  };
  
  $.fn.scrollHeight = function() {
    return this[0].scrollHeight;
  };

  $.fn.transform = function(transform) {
    for (var i = 0; i < this.length; i++) {
      var elStyle = this[i].style;
      elStyle.webkitTransform = elStyle.MsTransform = elStyle.msTransform = elStyle.MozTransform = elStyle.OTransform = elStyle.transform = transform;
    }
    return this;
  };
  $.fn.transition = function(duration) {
    if (typeof duration !== 'string') {
      duration = duration + 'ms';
    }
    for (var i = 0; i < this.length; i++) {
      var elStyle = this[i].style;
      elStyle.webkitTransitionDuration = elStyle.MsTransitionDuration = elStyle.msTransitionDuration = elStyle.MozTransitionDuration = elStyle.OTransitionDuration = elStyle.transitionDuration = duration;
    }
    return this;
  };

  $.getTranslate = function (el, axis) {
    var matrix, curTransform, curStyle, transformMatrix;

    // automatic axis detection
    if (typeof axis === 'undefined') {
      axis = 'x';
    }

    curStyle = window.getComputedStyle(el, null);
    if (window.WebKitCSSMatrix) {
      // Some old versions of Webkit choke when 'none' is passed; pass
      // empty string instead in this case
      transformMatrix = new WebKitCSSMatrix(curStyle.webkitTransform === 'none' ? '' : curStyle.webkitTransform);
    }
    else {
      transformMatrix = curStyle.MozTransform || curStyle.OTransform || curStyle.MsTransform || curStyle.msTransform  || curStyle.transform || curStyle.getPropertyValue('transform').replace('translate(', 'matrix(1, 0, 0, 1,');
      matrix = transformMatrix.toString().split(',');
    }

    if (axis === 'x') {
      //Latest Chrome and webkits Fix
      if (window.WebKitCSSMatrix)
        curTransform = transformMatrix.m41;
      //Crazy IE10 Matrix
      else if (matrix.length === 16)
        curTransform = parseFloat(matrix[12]);
      //Normal Browsers
      else
        curTransform = parseFloat(matrix[4]);
    }
    if (axis === 'y') {
      //Latest Chrome and webkits Fix
      if (window.WebKitCSSMatrix)
        curTransform = transformMatrix.m42;
      //Crazy IE10 Matrix
      else if (matrix.length === 16)
        curTransform = parseFloat(matrix[13]);
      //Normal Browsers
	      else
	        curTransform = parseFloat(matrix[5]);
	    }

	    return curTransform || 0;
	  };
	  $.requestAnimationFrame = function (callback) {
	    if (window.requestAnimationFrame) return window.requestAnimationFrame(callback);
	    else if (window.webkitRequestAnimationFrame) return window.webkitRequestAnimationFrame(callback);
	    else if (window.mozRequestAnimationFrame) return window.mozRequestAnimationFrame(callback);
	    else {
	      return window.setTimeout(callback, 1000 / 60);
	    }
	  };

	  $.cancelAnimationFrame = function (id) {
	    if (window.cancelAnimationFrame) return window.cancelAnimationFrame(id);
	    else if (window.webkitCancelAnimationFrame) return window.webkitCancelAnimationFrame(id);
	    else if (window.mozCancelAnimationFrame) return window.mozCancelAnimationFrame(id);
	    else {
	      return window.clearTimeout(id);
	    }  
	  };

	  $.fn.join = function(arg) {
	    return this.toArray().join(arg);
	  }
  
  $.extend(Array.prototype, {
		contains : function(element) {
			for ( var i = 0; i < this.length; i++) {
				if (this[i] == element) {
					return true;
				}
			}
			return false;
		},
		indexOf : function(o) {
			for ( var i = 0, len = this.length; i < len; i++) {
				if (this[i] == o)
					return i;
			}
			return -1;
		},
		remove : function(o) {
			var index = this.indexOf(o);
			if (index != -1) {
				this.splice(index, 1);
			}
			return this;
		}
  });
	
}(jQuery);

+function ($) {
  'use strict';

  // CAROUSEL CLASS DEFINITION
  // =========================

  var Carousel = function (element, options) {
    this.$element    = $(element);
    this.$indicators = this.$element.find('.carousel-indicators');
    this.options     = options;
    this.paused      = null;
    this.sliding     = null;
    this.interval    = null;
    this.$active     = null;
    this.$items      = null;

    this.options.keyboard && this.$element.on('keydown.bs.carousel', $.proxy(this.keydown, this));

    this.options.pause == 'hover' && !('ontouchstart' in document.documentElement) && this.$element
      .on('mouseenter.bs.carousel', $.proxy(this.pause, this))
      .on('mouseleave.bs.carousel', $.proxy(this.cycle, this))
  };

  Carousel.VERSION  = '3.3.5';

  Carousel.TRANSITION_DURATION = 600;

  Carousel.DEFAULTS = {
    interval: 5000,
    pause: 'hover',
    wrap: true,
    keyboard: true
  };

  Carousel.prototype.keydown = function (e) {
    if (/input|textarea/i.test(e.target.tagName)) return;
    switch (e.which) {
      case 37: this.prev(); break;
      case 39: this.next(); break;
      default: return
    }

    e.preventDefault()
  };

  Carousel.prototype.cycle = function (e) {
    e || (this.paused = false);

    this.interval && clearInterval(this.interval);

    this.options.interval
      && !this.paused
      && (this.interval = setInterval($.proxy(this.next, this), this.options.interval));

    return this
  };

  Carousel.prototype.getItemIndex = function (item) {
    this.$items = item.parent().children('.item');
    return this.$items.index(item || this.$active)
  };

  Carousel.prototype.getItemForDirection = function (direction, active) {
    var activeIndex = this.getItemIndex(active);
    var willWrap = (direction == 'prev' && activeIndex === 0)
                || (direction == 'next' && activeIndex == (this.$items.length - 1));
    if (willWrap && !this.options.wrap) return active;
    var delta = direction == 'prev' ? -1 : 1;
    var itemIndex = (activeIndex + delta) % this.$items.length;
    return this.$items.eq(itemIndex)
  };

  Carousel.prototype.to = function (pos) {
    var that        = this;
    var activeIndex = this.getItemIndex(this.$active = this.$element.find('.item.active'));

    if (pos > (this.$items.length - 1) || pos < 0) return;

    if (this.sliding)       return this.$element.one('slid.bs.carousel', function () { that.to(pos) }); // yes,
																										// "slid"
    if (activeIndex == pos) return this.pause().cycle();

    return this.slide(pos > activeIndex ? 'next' : 'prev', this.$items.eq(pos))
  };

  Carousel.prototype.pause = function (e) {
    e || (this.paused = true);

    if (this.$element.find('.next, .prev').length && $.support.transition) {
      this.$element.trigger($.support.transition.end);
      this.cycle(true)
    }

    this.interval = clearInterval(this.interval);

    return this;
  };

  Carousel.prototype.next = function () {
    if (this.sliding) return;
    return this.slide('next');
  };

  Carousel.prototype.prev = function () {
    if (this.sliding) return;
    return this.slide('prev');
  };

  Carousel.prototype.slide = function (type, next) {
    var $active   = this.$element.find('.item.active');
    var $next     = next || this.getItemForDirection(type, $active);
    var isCycling = this.interval;
    var direction = type == 'next' ? 'left' : 'right';
    var that      = this;

    if ($next.hasClass('active')) return (this.sliding = false);

    var relatedTarget = $next[0];
    var slideEvent = $.Event('slide.bs.carousel', {
      relatedTarget: relatedTarget,
      direction: direction
    });
    this.$element.trigger(slideEvent);
    if (slideEvent.isDefaultPrevented()) return;

    this.sliding = true;

    isCycling && this.pause();

    if (this.$indicators.length) {
      this.$indicators.find('.active').removeClass('active');
      var $nextIndicator = $(this.$indicators.children()[this.getItemIndex($next)]);
      $nextIndicator && $nextIndicator.addClass('active')
    }

    var slidEvent = $.Event('slid.bs.carousel', { relatedTarget: relatedTarget, direction: direction }); // yes,
																											// "slid"
    if ($.support.transition && this.$element.hasClass('slide')) {
      $next.addClass(type);
      $next[0].offsetWidth; // force reflow
      $active.addClass(direction);
      $next.addClass(direction);
      $active
        .one('bsTransitionEnd', function () {
          $next.removeClass([type, direction].join(' ')).addClass('active');
          $active.removeClass(['active', direction].join(' '));
          that.sliding = false;
          setTimeout(function () {
            that.$element.trigger(slidEvent)
          }, 0)
        })
        .emulateTransitionEnd(Carousel.TRANSITION_DURATION)
    } else {
      $active.removeClass('active');
      $next.addClass('active');
      this.sliding = false;
      this.$element.trigger(slidEvent)
    }

    isCycling && this.cycle();

    return this
  };


  // CAROUSEL PLUGIN DEFINITION
  // ==========================

  function Plugin(option) {
    return this.each(function () {
      var $this   = $(this);
      var data    = $this.data('bs.carousel');
      var options = $.extend({}, Carousel.DEFAULTS, $this.data(), typeof option == 'object' && option);
      var action  = typeof option == 'string' ? option : options.slide;

      if (!data) $this.data('bs.carousel', (data = new Carousel(this, options)));
      if (typeof option == 'number') data.to(option);
      else if (action) data[action]();
      else if (options.interval) data.pause().cycle()
    })
  }

  var old = $.fn.carousel;

  $.fn.carousel             = Plugin;
  $.fn.carousel.Constructor = Carousel;


  // CAROUSEL NO CONFLICT
  // ====================

  $.fn.carousel.noConflict = function () {
    $.fn.carousel = old;
    return this
  };


  // CAROUSEL DATA-API
  // =================

  var clickHandler = function (e) {
    var href;
    var $this   = $(this);
    var $target = $($this.attr('data-target') || (href = $this.attr('href')) && href.replace(/.*(?=#[^\s]+$)/, '')); // strip
																														// for
																														// ie7
    if (!$target.hasClass('carousel')) return;
    var options = $.extend({}, $target.data(), $this.data());
    var slideIndex = $this.attr('data-slide-to');
    if (slideIndex) options.interval = false;

    Plugin.call($target, options);

    if (slideIndex) {
      $target.data('bs.carousel').to(slideIndex)
    }

    e.preventDefault()
  };

  $(document)
    .on('click.bs.carousel.data-api', '[data-slide]', clickHandler)
    .on('click.bs.carousel.data-api', '[data-slide-to]', clickHandler);

  $(window).on('load', function () {
    $('[data-ride="carousel"]').each(function () {
      var $carousel = $(this);
      Plugin.call($carousel, $carousel.data())
    });
    
  });


}(jQuery);
	
+ function($) {
  "use strict";
  var Slider = function (container, arg) {
    this.container = $(container);
    this.handler = this.container.find('.weui-slider__handler');
    this.track = this.container.find('.weui-slider__track');
    this.value = this.container.find('.weui-slider-box__value');
    this.input = this.container.find('input.hidden');
    this.bind();
    if (typeof arg === 'function') {
      this.callback = arg;
    }
  }

  Slider.prototype.bind = function () {
    this.container
      .on($.touchEvents.start, $.proxy(this.touchStart, this))
      .on($.touchEvents.end, $.proxy(this.touchEnd, this));
    $(document.body).on($.touchEvents.move, $.proxy(this.touchMove, this)); // move even outside container
  }

  Slider.prototype.touchStart = function (e) {
    e.preventDefault();
    this.start = $.getTouchPosition(e);
    this.width = this.container.find('.weui-slider__inner').width();
    this.left = parseInt(this.container.find('.weui-slider__handler').css('left'));
    this.touching = true;
  }

  Slider.prototype.touchMove = function (e) {
    if (!this.touching) return false;
    var p = $.getTouchPosition(e);
    var distance = p.x - this.start.x;
    var left = distance + this.left;
    var per = parseInt(left / this.width * 100);
    if (per < 0) per = 0;
    if (per > 100) per = 100;
    this.handler.css('left', per + '%');
    this.track.css('width', per + '%');
    this.value.text(per);
    this.input.val(per);
    this.callback && this.callback.call(this, per);
    this.container.trigger('change', per);
  }

  Slider.prototype.touchEnd = function (e) {
    this.touching = false;
  }

  $.fn.slider = function (arg) {
    this.each(function () {
      var $this = $(this);
      var slider = $this.data('slider');
      if (slider) return slider;
      else $this.data('slider', new Slider(this, arg));
    })
  };
  
  $(window).on('load', function () {
		$(".weui-slider-box").each(function(){
			$(this).slider();
		});
  });
  
}(jQuery);

+(function ($) {
	"use strict";
    var obj;// 表单元素暂存
    var gettype=Object.prototype.toString;// 取得类型
    var setVal = function (name, ival) {
        var $oinput = obj.find(":input[name='" + name + "']");// 取得当前字段对应的元素
        if ($oinput.attr("type") == "checkbox") {// 复选框类型
            if (ival !== null) {
                var checkboxObj = $("[name='" + name + "']");
                var checkArray = (ival+"").split(",");
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
	            field.val(ival);
	            var file = obj.find("[data-name='" + name + "']");
	            if(file&&file.length>0){
	            	var list = file.eq(0).siblings(".fileuploader-items").find(".fileuploader-items-list");
            		var arg = ival.split(",");
            		var template = _.template($("#template"+file.attr("id")).html());
		    		if(arg.length>0){
			    		 for(var i=0;i<arg.length;i++){
		    		 	  	var html = template({img:arg[i]});
			    		 	list.prepend(html);
			    		 }
		    		 }
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
            if(name=='is_def'){
               console.log(1);
            }
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
    };
})(jQuery);

	
+ function($) {
  "use strict";

  var defaults;
  
  $.modal = function(params, onOpen) {
    params = $.extend({}, defaults, params);


    var buttons = params.buttons;

    var buttonsHtml = buttons.map(function(d, i) {
      return '<a href="javascript:;" class="weui-dialog__btn ' + (d.className || "") + '">' + d.text + '</a>';
    }).join("");

    var tpl = '<div class="weui-dialog">' +
                '<div class="weui-dialog__hd"><strong class="weui-dialog__title">' + params.title + '</strong></div>' +
                ( params.text ? '<div class="weui-dialog__bd">'+params.text+'</div>' : '')+
                '<div class="weui-dialog__ft">' + buttonsHtml + '</div>' +
              '</div>';
    
    var dialog = $.openModal(tpl, onOpen);

    dialog.find(".weui-dialog__btn").each(function(i, e) {
      var el = $(e);
      el.click(function() {
        //先关闭对话框，再调用回调函数
        if(params.autoClose) $.closeModal();

        if(buttons[i].onClick) {
          buttons[i].onClick.call(dialog);
        }
      });
    });

    return dialog;
  };

  $.openModal = function(tpl, onOpen) {
    var mask = $("<div class='weui-mask'></div>").appendTo(document.body);
    mask.show();

    var dialog = $(tpl).appendTo(document.body);
 
    if (onOpen) {
      dialog.transitionEnd(function () {
        onOpen.call(dialog);
      });
    }   

    dialog.show();
    mask.addClass("weui-mask--visible");
    dialog.addClass("weui-dialog--visible");


    return dialog;
  }

  $.closeModal = function() {
    $(".weui-mask").removeClass("weui-mask").transitionEnd(function() {
      $(this).remove();
    });
    $(".weui-dialog--visible").removeClass("weui-dialog--visible").transitionEnd(function() {
      $(this).remove();
    });
  };

  $.alert = function(text, title, onOK) {
    var config;
    if (typeof text === 'object') {
      config = text;
    } else {
      if (typeof title === 'function') {
        onOK = arguments[1];
        title = undefined;
      }

      config = {
        text: text,
        title: title,
        onOK: onOK
      }
    }
    return $.modal({
      text: config.text,
      title: config.title,
      buttons: [{
        text: defaults.buttonOK,
        className: "primary",
        onClick: config.onOK
      }]
    });
  }

  $.confirm = function(text, title, onOK, onCancel) {
    var config;
    if (typeof text === 'object') {
      config = text
    } else {
      if (typeof title === 'function') {
        onCancel = arguments[2];
        onOK = arguments[1];
        title = undefined;
      }

      config = {
        text: text,
        title: title,
        onOK: onOK,
        onCancel: onCancel
      }
    }
    return $.modal({
      text: config.text,
      title: config.title,
      buttons: [
      {
        text: defaults.buttonCancel,
        className: "default",
        onClick: config.onCancel
      },
      {
        text: defaults.buttonOK,
        className: "primary",
        onClick: config.onOK
      }]
    });
  };

  //如果参数过多，建议通过 config 对象进行配置，而不是传入多个参数。
  $.prompt = function(text, title, onOK, onCancel, input) {
    var config;
    if (typeof text === 'object') {
      config = text;
    } else {
      if (typeof title === 'function') {
        input = arguments[3];
        onCancel = arguments[2];
        onOK = arguments[1];
        title = undefined;
      }
      config = {
        text: text,
        title: title,
        input: input,
        onOK: onOK,
        onCancel: onCancel,
        empty: false  //allow empty
      }
    }

    var modal = $.modal({
      text: '<p class="weui-prompt-text">'+(config.text || '')+'</p><input type="text" class="weui-input weui-prompt-input" id="weui-prompt-input" value="' + (config.input || '') + '" />',
      title: config.title,
      autoClose: false,
      buttons: [
      {
        text: defaults.buttonCancel,
        className: "default",
        onClick: function () {
          $.closeModal();
          config.onCancel && config.onCancel.call(modal);
        }
      },
      {
        text: defaults.buttonOK,
        className: "primary",
        onClick: function() {
          var input = $("#weui-prompt-input").val();
          if (!config.empty && (input === "" || input === null)) {
            modal.find('.weui-prompt-input').focus()[0].select();
            return false;
          }
          $.closeModal();
          config.onOK && config.onOK.call(modal, input);
        }
      }]
    }, function () {
      this.find('.weui-prompt-input').focus()[0].select();
    });

    return modal;
  };

  //如果参数过多，建议通过 config 对象进行配置，而不是传入多个参数。
  $.register = function(text, title, onOK, onCancel, username, password) {
    var config;
    if (typeof text === 'object') {
      config = text;
    } else {
      if (typeof title === 'function') {
        password = arguments[4];
        username = arguments[3];
        onCancel = arguments[2];
        onOK = arguments[1];
        title = undefined;
      }
      config = {
        text: text,
        title: title,
        username: username,
        password: password,
        onOK: onOK,
        onCancel: onCancel
      }
    }

    var modal = $.modal({
      text: '<form><p class="weui-prompt-text">'+(config.text || '')+'</p>' +
            '<input type="text" class="weui-input weui-prompt-input" name="username" value="" placeholder="输入账号" />' +
            '<input type="text" class="weui-input weui-prompt-input" name="nickname" value="" placeholder="输入昵称" />' +
            '<input type="password" class="weui-input weui-prompt-input" name="password" value="" placeholder="输入密码" />' +
            '<input type="password" class="weui-input weui-prompt-input" name="comfirm-password" value="" placeholder="输入确认密码" />' +
            '<p class="weui-prompt-text login jump">登录</p></form>',
      title: config.title,
      autoClose: false,
      buttons: [
      {
        text: defaults.buttonCancel,
        className: "default",
        onClick: function () {
          $.closeModal();
          config.onCancel && config.onCancel.call(modal);
        }
      }, {
        text: '注册',
        className: "primary",
        onClick: function() {
        	var flag=true;
        	this.find('form').find(".weui-input").each(function(){
        		  var val  = $(this).val();
        		  if (val=== "" || val === null) {
                    $(this).focus()[0].select();
                    $.toast("请"+$(this).attr('placeholder'),"forbidden");
                    flag=false;
                    return false;
                  }
        	});
            if(!flag){
            	return false;
            }
            var fromJson=this.find('form').formToJson();
            fromJson.dashboardid=$("body").data("dashboardid");
            $.ajax({
		        type: 'POST',
		        url: window.GlobalConfig.registerUrl,
		        data: fromJson,
		        dataType: 'json',
		        beforeSend:function(){
					$.toast('正在注册用户，请等待...',"text",null,false);
			    },
	            success: function(data){
	            	$.hideLoading();
		        	if (data.status == 'success' || data.status == 1) {
						$.toast(data.message||data.msg);
						$.closeModal();
						$('[data-ride="loadData"]').trigger('reload');
					} else {
						$.toast(data.message||data.msg, 'forbidden');
					}
		        },
		        error: function(xhr, type){
		        	 $.hideLoading();
		             $.toast('注册用户出错', 'forbidden');
		        }
		    });
            config.onOK && config.onOK.call(modal, username, password);
        }
      }]
    }, function () {
      this.find('.weui-prompt-input:eq(0)').focus()[0].select();
    });

    return modal;
  };
  
  //如果参数过多，建议通过 config 对象进行配置，而不是传入多个参数。
  $.login = function(text, title, onOK, onCancel, username, password) {
    var config;
    if (typeof text === 'object') {
      config = text;
    } else {
      if (typeof title === 'function') {
        password = arguments[4];
        username = arguments[3];
        onCancel = arguments[2];
        onOK = arguments[1];
        title = undefined;
      }
      config = {
        text: text,
        title: title,
        username: username,
        password: password,
        onOK: onOK,
        onCancel: onCancel
      }
    }

    var modal = $.modal({
      text: '<form><p class="weui-prompt-text">'+(config.text || '')+'</p>' +
            '<input type="text" class="weui-input weui-prompt-input" id="weui-prompt-username" value="' + (config.username || '') + '" placeholder="输入用户名" />' +
            '<input type="password" class="weui-input weui-prompt-input" id="weui-prompt-password" value="' + (config.password || '') + '" placeholder="输入密码" />'+
            '<p class="weui-prompt-text register jump">注册</p></form>',
      title: config.title,
      autoClose: false,
      buttons: [
      {
        text: defaults.buttonCancel,
        className: "default",
        onClick: function () {
          $.closeModal();
          config.onCancel && config.onCancel.call(modal);
        }
      }, {
        text: '登录',
        className: "primary",
        onClick: function() {
          var username = $("#weui-prompt-username").val();
          var password = $("#weui-prompt-password").val();
          if (!config.empty && (username === "" || username === null)) {
            modal.find('#weui-prompt-username').focus()[0].select();
            $.toast("请输入用户名","forbidden");
            return false;
          }
          if (!config.empty && (password === "" || password === null)) {
            modal.find('#weui-prompt-password').focus()[0].select();
            $.toast("请输入密码","forbidden");
            return false;
          }
          //$.closeModal();
           $.ajax({
		        type: 'POST',
		        url: window.GlobalConfig.loginUrl,
		        data: {
		        	username:username,
		        	password:password,
		        	dashboardid:$("body").data("dashboardid")
		        } ,
		        dataType: 'json',
		        success: function(data){
		        	if (data.status == 'success' || data.status == 1) {
						$.toast(data.message||data.msg);
						$.closeModal();
						if($("body").data("redirecturl")||$.getUrlParam('redirecturl')){
							location.href=($("body").data("redirecturl")||$.getUrlParam('redirecturl'));
						}
						$('[data-ride="loadData"]').trigger('reload');
					} else {
						$.toast(data.message||data.msg, 'forbidden');
					}
		        },
		        error: function(xhr, type){
		        	 //$thiz.data("load", "1");
		             $.toast('加载数据出错', 'forbidden');
		        }
		    });
            config.onOK && config.onOK.call(modal, username, password);
        }
      }]
    }, function () {
      this.find('#weui-prompt-username').focus()[0].select();
    });

    return modal;
  };

  defaults = $.modal.prototype.defaults = {
    title: "提示",
    text: undefined,
    buttonOK: "确定",
    buttonCancel: "取消",
    buttons: [{
      text: "确定",
      className: "primary"
    }],
    autoClose: true //点击按钮自动关闭对话框，如果你不希望点击按钮就关闭对话框，可以把这个设置为false
  };

  
  $.cookie = function(name, value, options) {
	    if (typeof value != 'undefined') { // name and value given, set cookie
	        options = options || {};
	        if (value === null) {
	            value = '';
	            options.expires = -1;
	        }
	        var expires = '';
	        if (options.expires && (typeof options.expires == 'number' || options.expires.toUTCString)) {
	            var date;
	            if (typeof options.expires == 'number') {
	                date = new Date();
	                date.setTime(date.getTime() + (options.expires * 24 * 60 * 60 * 1000));
	            } else {
	                date = options.expires;
	            }
	            expires = '; expires=' + date.toUTCString(); // use expires attribute, max-age is not supported by IE
	        }
	        // CAUTION: Needed to parenthesize options.path and options.domain
	        // in the following expressions, otherwise they evaluate to undefined
	        // in the packed version for some reason...
	        var path = options.path ? '; path=' + (options.path) : '';
	        var domain = options.domain ? '; domain=' + (options.domain) : '';
	        var secure = options.secure ? '; secure' : '';
	        document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, secure].join('');
	    } else { // only name given, get cookie
	        var cookieValue = null;
	        if (document.cookie && document.cookie != '') {
	            var cookies = document.cookie.split(';');
	            for (var i = 0; i < cookies.length; i++) {
	                var cookie = jQuery.trim(cookies[i]);
	                // Does this cookie string begin with the name we want?
	                if (cookie.substring(0, name.length + 1) == (name + '=')) {
	                    cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
	                    break;
	                }
	            }
	        }
	        return cookieValue;
	    }
	};
	
}(jQuery);


+function($) {
	"use strict";

	$.fn.scrollHeight = function() {
		return $(this).prop('scrollHeight');
	};
	
	$.fn.scrollBottom = function() {
		if ($(this).is(document))
			return $(this).height() - $(this).scrollTop() - $(window).height();
		else
			return $(this).prop('scrollHeight') - $(this).prop('offsetHeight')
					- $(this).scrollTop();
	};
	
	$.extend({
		// 矩形的碰撞检测
		/**
		 * x1,y1 第一个矩形的左上角 x2,y2 第一个矩形的右下角 x3,y3 第二个矩形的左上角 x4,y4 第二个矩形的右下角
		 * 
		 * return Boolean true=>碰撞
		 */
		isCollsion : function(x1, y1, x2, y2, x3, y3, x4, y4) {
			if ((x1 > x3 && x1 > x4) || (x3 > x1 && x3 > x2)
					|| (y1 > y3 && y1 > y4) || (y3 > y1 && y3 > y2)) {
				return false;
			} else {
				return true;
			}
		}
	});
	
	/**
	 * opt中包含了两个参数，元素实际位置的偏移
	 * 
	 * return Boolean 是否在可视范围之内
	 */
	$.fn.isVisable = function(opt) {
		opt = $.extend({
			offsetTop : 0, // 网页中元素比实际位置在垂直方向的偏移
			offsetLeft : 0, // 网页中元素比实际位置在水平方向的偏移
			addTop : 0, // 元素左上角坐标y轴偏移
			addRight : 0, // 元素右下角坐标x轴偏移
			addBottom : 0, // 元素右下角坐标y轴偏移
			addLeft : 0
				// 元素左上角坐标x轴偏移
			}, opt);
		var me = $(this), srcInfo = {
			begin_left : (me.offset().left + opt.offsetLeft + opt.addLeft),
			begin_top : (me.offset().top + opt.offsetTop + opt.addTop)
		};
		srcInfo.end_left = (srcInfo.begin_left + me.width() + opt.addRight);
		srcInfo.end_top = (srcInfo.begin_top + me.height() + opt.addBottom);
	
		var winInfo = {
			begin_left : $(window).scrollLeft(),
			begin_top : $(window).scrollTop()
		};
		winInfo.end_left = (winInfo.begin_left + $(window).width());
		winInfo.end_top = (winInfo.begin_top + $(window).height());
	
		// 检测是否”碰撞“”
		return $.isCollsion(srcInfo.begin_left, srcInfo.begin_top,
				srcInfo.end_left, srcInfo.end_top, winInfo.begin_left,
				winInfo.begin_top, winInfo.end_left, winInfo.end_top);
	};

	var Infinite = function(el, option) {
		this.option = $.extend({distance : 50,loadmore : '.weui-loadmore'},option | {});
		this.container = $(el);
		this.container.data("infinite", this);
		this.attachEvents();
	};

	Infinite.prototype.scroll = function() {
		var container = this.container;
		if (container.find(this.option.loadmore).isVisable()) {
			container.trigger("infinite");
		} else {
			var offset = container.scrollHeight()
					- ($(window).height() + container.scrollTop());
			if (offset <= this.option.distance) {
				container.trigger("infinite");
			}
		}
	};

	Infinite.prototype.attachEvents = function(off) {
		var el = this.container;
		var scrollContainer = (el[0].tagName.toUpperCase() === "BODY"
				? $(document)
				: el);
		scrollContainer[off ? "off" : "on"]("scroll", $
						.proxy(this.scroll, this));
	};
	Infinite.prototype.detachEvents = function(off) {
		this.attachEvents(true);
	}

	var infinite = function(el) {
		attachEvents(el);
	}

	$.fn.infinite = function(option) {
		return this.each(function() {
			new Infinite(this, option);
		});
	}
	$.fn.destroyInfinite = function() {
		return this.each(function() {
					var infinite = $(this).data("infinite");
					if (infinite && infinite.detachEvents)
						infinite.detachEvents();
				});
	};
	
	
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
			delete _params['do'];
			delete _params['c'];
			delete _params['a'];
			delete _params['m'];
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
	  	  	  return (arr|| []).join(',').replace(/^[]/, '');
	  	}
    });
    
	
    
	$(window).on('load', function () {
		$("img.lazyload").lazyload();
		$("#page").on("scroll", function() {
			$("img.lazyload").lazyload();
        });
		$(".weui-tabbar").each(function(){
			var height = $(this).height();
			var bottom = $('<div class="bottom"></div>');
			var buyBar = $(this).closest(".page").find(".buy_bar");;
			if(buyBar.length>0&&buyBar.css("position")=='fixed'){
				buyBar.css('bottom',height);
				height+=buyBar.height();
				buyBar.appendTo(bottom);
			}
			$(this).closest(".page").css("bottom",height+'px').after(bottom);
			$(this).appendTo(bottom);
		});
		if($(".weui-tabbar").length==0){
			$(".buy_bar").each(function(){
				var height = $(this).height();
				if($(this).css("position")=='fixed'){
					var bottom = $('<div class="bottom"></div>');
					$(this).closest(".page").css("bottom",height+'px').after(bottom);
					$(this).appendTo(bottom);
				}
			});
		}
		/*if (/(iPhone|iPad|iPod|iOS|Safari)/i.test(navigator.userAgent)) {
			$("#page").css("padding-top","40px");
		}else{
		 	$("#page").css("margin-top", "40px");
		}*/
		function loadRide(thiz,isAppend){
			var $thiz=$(thiz);
			var $loadMore=$('.weui-loadmore-'+$thiz.attr('id'));
			var $weui_loading=$loadMore.find(".weui-loading");
			var $weui_loadmore_data=$loadMore.find(".weui-loadmore_data");
			var $weui_loadmore_nodata=$loadMore.find(".weui-loadmore_nodata");
			
			//加载页面完毕时处理
			function loadEndPage(def){
				if(def){
					if($thiz.data("page")>$thiz.data("total")){
						$thiz.data("load", "0");
	                	$weui_loadmore_data.hide();
			         	$weui_loading.hide();
			         	$weui_loadmore_nodata.show();
			         	$thiz.destroyInfinite();
		        	}
	         		def.resolve();
	         	}
			}
			//加载分页数据
			function loadPageData(row,page,def){
				var postData={};
				$.each($thiz.data(),function(name,value){
					if(typeof value != 'object'){
						postData[name]=value;
					}
				});
				
				//如果地址传过来有参数，把参数加进来
    			var params = $.getUrlParams();
				if(params['_ajax_page']&&params['_ajax_page']=='1' && postData["param"]=="1"){
    				postData=$.extend(postData,params);
				}
				
				//判断是否有父容器
				var pcontainer = $thiz.closest(".pcontainer");
				//AJAX加载数据
				function ajaxData(){
                    if($thiz.data("page")>$thiz.data("total")){
                        return;
                    }
                     
                    if($thiz.data("load")!=1){
                    	return;
                    }
					if(pcontainer&&pcontainer.length==1){
						//遍历父容器的节点参数
						$.each(pcontainer.data(),function(name,value){
							if(typeof value != 'object'){
								postData[name]=value;
							}
						});
					}
					$.ajax({
			            type: 'POST',
			            url: $thiz.attr('url'),
			            data: postData ,
			            dataType: 'json',
			            headers: {
			                'Authorization':$.cookie(window.GlobalConfig.cookieName||('uid'+$("body").data("dashboardid")))
			            },
			            beforeSend:function(){
				        	 $thiz.data("load", "0");
					    },
			            success: function(data){
                            if(typeof data == 'string'){
                                $thiz.data("load", "0");
                                $thiz.data("total",0);
                                $.toast("加载数据出错", 'forbidden');
                                return;
                            }
			            	if (data.status == 'success' || data.status == 1) {
								var rows = data.rows;
								var trigger = $thiz.data("trigger");
								if(rows.length>0){
									$thiz.data("page", $thiz.data("page")+ 1);
									$thiz.data("total", data.totalPage);
									//获取触发节点
									var templateid = $thiz.attr("tempateid");
									var template ;
									if(templateid){
										template = _.template($("#"+templateid).html());
									}else{
										template = _.template($("#template"+$thiz.attr("id")).html());
									}									 
									var templatelinks=$thiz.attr("templatelinks");//查找是否有关联的
									var templates = null;
									if(templatelinks!=null){
										templates = templatelinks.split(",");
									}
									var html = "";
									for (var i = 0; i < rows.length; i++) {
										rows[i].i=i;
										rows[i].href=rows[i].href?rows[i].href:"";
										rows[i].templateclz='user-load';
										html +=template(rows[i]);
									}
									//插入数据到页面，放到最后面
									if($thiz.data("append")==0 || (typeof isAppend !== 'undefined' && isAppend ==0)){
                                        $thiz.find(".user-load").remove();
									}

									$thiz.append(html);
					                $("img.lazyload").lazyload();
									if(templates!=null){
										for (var j = 0; j < templates.length; j++) {
											var template = _.template($("#template"+templates[j]).html());
											html="";
											for (var i = 0; i < rows.length; i++) {
												html +=template(rows[i]);
											}
											$("#"+templates[j]).html(html);
										}
									}
									
					                //加载成功后触发
					                if(trigger&&$(trigger)){
					                	$(trigger).trigger("loadSuccess");
					                }else{
					                	$thiz.data("load", "1");
					                }
								}else{
									if(trigger&&$(trigger)){
					                	$(trigger).trigger("loadSuccess");
					                }
                                    $thiz.data("total",0);
                                }
							} else {
								$thiz.data("load", "1");
                                $thiz.data("total",0);
								$.toast(data.message||data.msg, 'forbidden');
							}
							loadEndPage(def);
			            },
			            error: function(xhr, type){
			            	 $thiz.data("load", "1");
			                 $.toast('加载数据出错', 'forbidden');
			            }
			        });
				}
				//判断父容器AJAX是否已经加载完毕
				var timeoutid=null;
				function pdata(){
					//如果已经加载完毕，直接取数
					if(pcontainer.data("load")=="1"){
						if(timeoutid!=null){
							clearTimeout(timeoutid);
						}
						ajaxData();
					}else{
						timeoutid= setTimeout(function() {
			         		pdata();
			    		}, 100);
					}
				}
				//判断是否存在父容器，如果无父容器直接加载
				if(pcontainer&&pcontainer.length==1){
					pdata();
				}else{
					
					ajaxData();
				}
			}
			
			//无限加载判断加载数据
			function loadData(row,page,def){
				var total = $thiz.data("total");
				total = total?total:"1";
				if(parseInt(page)<=parseInt(total)){
			        loadPageData(row,page,def);
				}else{
					loadEndPage(def);
				}
			}
				
			//如果文档高度不大于窗口高度，数据较少，自动加载下方数据
			function loadContinueData(def){
				def.done(function(){
				    if($loadMore.isVisable()&& $thiz.data("load")=="1"){
				    	var cdef = $.Deferred(); 
				    	loadContinueData(cdef);
				    }
				}).fail(function(){
					 $.toast('加载数据出错', 'forbidden');
				});
				//初始化加载
				if($thiz.isVisable()&&$thiz.data("load")=="1"){
	    		 	loadData($thiz.data("row"),$thiz.data("page"),def);
	    	    }
			}
			//判断是否无限加载
			if($thiz.data("isinfinite")){
				var def = $.Deferred(); 
				//初始化加载
				$weui_loadmore_data.show();
	         	$weui_loading.show();
	         	$weui_loadmore_nodata.hide();
			         	
				loadContinueData(def);
				//无限加载
				$("#page").infinite({loadmore:'.weui-loadmore-'+$thiz.attr('id')}).on("infinite", function() {
			        var self = this;
			        if(self.loading) return;
			        self.loading = true;
			        var def = $.Deferred(); 
			        loadData($thiz.data("row"),$thiz.data("page"),def);
			        def.done(function(){
						self.loading = false;
					}).fail(function(){
						self.loading = false;
					});
			    });
			}else{
				//隐藏加载提示
				$loadMore.hide();
				loadPageData($thiz.data("row"),$thiz.data("page"));
			}
		}
		
		//判断是否需要登录
		var loginCheck = false;
		if($('[data-login="1"]').length>0){
			var loginCheckUrl = window.GlobalConfig.loginCheckUrl;
			loginCheckUrl+=loginCheckUrl.indexOf("?")>0?"&":"?";
			$.get(loginCheckUrl+"dashboardid="+$("body").data("dashboardid"),function(data){
				if(data=="1"){
					loginCheck = true;
					$(".user-load").remove();
					$('[data-ride="loadData"]').trigger('reload');
				}else{
					$.login('','登录');
				}
			});
		}
		//初始化加载
		//初始化加载
		function reloadData(){
			$('[data-ride="loadData"]').each(function () {
				if($(this).data("reloadaction")){
					return;
				}
				
				$(this).data("reloadaction",1);
				var login = $(this).data("login");
				if(loginCheck || login=="0" || !login){
					if($(this).data("load")==1){
						loadRide(this);
					}
				}
				//触发重新加载数据
				$(this).off("reload").on("reload",function(){
					$(this).data({"load":"1",page:1,total:1});
					var append =$(this).data("append")?1:0;
					loadRide(this,append);
				});
		    });
		}
		reloadData();
		
		$("body").off("reload").on("reload",function(){
			reloadData();
		});
	    
		//保存数据
		function saveData($thiz){
			var postData={};
			$thiz.attr("disabled",true);
			$.each($thiz.data(),function(name,value){
				if(typeof value != 'object'){
					postData[name]=value;
				}
			});
			
			//如果地址传过来有参数，把参数加进来
			var params = $.getUrlParams();
			if(params['_ajax_page']&&params['_ajax_page']=='1' && postData["param"]=="1"){
				postData=$.extend(postData,params);
			}
			var linkfields = $thiz.attr("linkfields");
			if(linkfields){
				linkfields=linkfields.split(",");
				var linkJson={};
				for(var i =0;i<linkfields.length;i++){
					linkJson[linkfields[i]]=$thiz.siblings("."+linkfields[i]).html();
				}
				postData=$.extend(postData,linkJson);
			}
			var bodyfileds = $thiz.attr("bodyfileds");
			if(bodyfileds){
				bodyfileds=bodyfileds.split(",");
				var bodyJson={};
				var $body=$("body");
				for(var i =0;i<bodyfileds.length;i++){
					bodyJson[bodyfileds[i]]=$body.data(bodyfileds[i]);
				}
				postData=$.extend(postData,bodyJson);
			}
			$.ajax({
	            type: 'POST',
	            url: $thiz.data('url'),
	            data: postData ,
	            dataType: 'json',
	            beforeSend:function(){
					$.toast($thiz.attr("savemsg")||'正在保存数据，请等待...',"text",null,false);
			    },
	            success: function(data){
	            	$.hideLoading();
	            	if (data.status == 'success' || data.status == 1) {
	            		 var msg=(data.message||data.msg||$thiz.attr("successmsg"));
		            	 $.toast(msg);
		            	 if($thiz.data('successurl')){
		            		 var url = $thiz.data('successurl');
		            		 url += ((url.indexOf('?') == -1) ? '?' : '&')+$.param({_ajax_page:'1'});
		            		 location.href=url;
		            	 }
					}else{
						 var errormsg=(data.message||data.msg||$thiz.attr("errormsg"));
		            	 $.toast(errormsg,'forbidden');
					}
	            	$thiz.removeAttr("disabled");
	            },
	            error: function(xhr, type){
	            	$.hideLoading();
	            	var errormsg=$thiz.attr("errormsg");
	            	$.toast(errormsg,'forbidden');
	            	$thiz.removeAttr("disabled");
	            }
	       });
		}
		 
		//初始化保存数据
		$("body").on('click.saveData', '[data-ride="saveData"]',function () {
			var thiz = $(this);
			var loginCheckUrl = window.GlobalConfig.loginCheckUrl;
			loginCheckUrl+=loginCheckUrl.indexOf("?")>0?"&":"?";
			$.get(loginCheckUrl+"dashboardid="+$("body").data("dashboardid"),function(data){
				if(data=="1"){
					saveData(thiz);
				}else{
					$.login('','登录');
				}
			});
		});
		
		$("body").on('click.login_register','.jump',function(){
			 if($(this).is(".register")){
				 $.closeModal();
				 $.register('','注册');
			 }else if($(this).is(".login")){
				 $.closeModal();
				 $.login('','登录');
			 }
		});
		
		
	    if($.getUrlParam("_ajax_page")&&$.getUrlParam("_ajax_page")=='1'){
	    	$("#PageBack").show();
	    	$("#PageBack").click(function(){
	    		history.back();
	    	});
	    }
	    
	    
	    //增加ajax-page页面
	    $(document).off('click.ajax-page','.ajax-page').on('click.ajax-page','.ajax-page',function(){
	    	var url=$(this).attr('href')||$(this).attr('url')||$(this).data('href')||$(this).data('url');
	    	if(url==0||($(this).data('page')&&$(this).data('page')=='external')){
	    		return true;
	    	}
	    	if(!$(this).data('url')){
	    	 	$(this).data('url',url);
	    	}
	    	var backtohome=$(this).data('backtohome');
	    	if(backtohome){
	    		$(this).data('backtohome',null);
	    	}
    	  	var url = $(this).data('url');
    	 	url += ((url.indexOf('?') == -1) ? '?' : '&')+$.getParamData($(this),{_ajax_page:'1'});
    	 	$(this).attr('href',url);
    	 	if(backtohome){
    	 		location.href=$(this).attr('href');
    	 	}
	    	return true;
	    });
	    $(".weui-uploader-files").each(function(){
	    	var thiz = $(this);
		    thiz.fileuploader({
		    	
		        captions: {
		            button: function(options) { return '请选择图片'; },
		            feedback: function(options) { return '请选择图片上传'; },
		            feedback2: function(options) { return '选择了'+options.length; },
		            drop: '拖动图片到这里上传',
		            paste: '<div class="fileuploader-pending-loader"><div class="left-half" style="animation-duration: ${ms}s"></div><div class="spinner" style="animation-duration: ${ms}s"></div><div class="right-half" style="animation-duration: ${ms}s"></div></div>复制图片到这里，点击可以取消',
		            removeConfirmation: '你确定要删除吗？',
		            errors: {
		                filesLimit: '只允许上传 ${limit}张图片',
		                filesType: '只允许上传图片类型 ${extensions}',
		                fileSize: '${name}图片太大!图片大小不允许超出${fileMaxSize}MB.',
		                filesSizeAll: '图片太大!图片大小不允许超出${maxSize} MB.',
		                fileName: '你选择的图片${name}.',
		                folderUpload: '不允许上传文件夹'
		            }
		        },
		        limit:thiz.data("multi")=="0"?1:null,
		        extensions: ['jpg', 'jpeg', 'png', 'gif', 'bmp'],
				changeInput: ' ',
				theme: 'thumbnails',
		        enableApi: true,
				addMore: true,
				listInput:thiz.data("name"),
				thumbnails: {
					box: '<div class="fileuploader-items">' +
		                      '<ul class="fileuploader-items-list">' +
							      '<li class="fileuploader-thumbnails-input"><div class="fileuploader-thumbnails-input-inner">+</div></li>' +
		                      '</ul>' +
		                  '</div>',
					item: '<li class="fileuploader-item">' +
						       '<div class="fileuploader-item-inner">' +
		                           '<div class="thumbnail-holder">${image}</div>' +
		                           '<div class="actions-holder">' +
		                               '<a class="fileuploader-action fileuploader-action-remove" title="Remove"><i class="remove"></i></a>' +
		                           '</div>' +
		                       	   '<div class="progress-holder">${progressBar}</div>' +
		                       '</div>' +
		                   '</li>',
					item2: '<li class="fileuploader-item">' +
						       '<div class="fileuploader-item-inner">' +
		                           '<div class="thumbnail-holder">${image}</div>' +
		                           '<div class="actions-holder">' +
		                               '<a class="fileuploader-action fileuploader-action-remove" title="Remove"><i class="remove"></i></a>' +
		                           '</div>' +
		                       '</div>' +
		                   '</li>',
		            removeConfirmation: false,
					startImageRenderer: true,
					canvasImage: false,
					_selectors: {
						list: '.fileuploader-items-list',
						item: '.fileuploader-item',
						start: '.fileuploader-action-start',
						retry: '.fileuploader-action-retry',
						remove: '.fileuploader-action-remove'
					},
					onItemShow: function(item, listEl) {
						var plusInput = listEl.find('.fileuploader-thumbnails-input');
						
						plusInput.insertAfter(item.html);
						
						if(item.format == 'image') {
							item.html.find('.fileuploader-item-icon').hide();
						}
					}
				},
				afterRender: function(listEl, parentEl, newInputEl, inputEl) {
					var fileThiz = this;
					listEl.off("click.fileuploader-action-remove").on("click.fileuploader-action-remove",".fileuploader-action-remove",function(e){
						if(fileThiz.upload.uploadImages.length==0){
							var existImages = [];
						 	listEl.find("img.userload").each(function(){
						 		existImages.push($(this).attr("src"));
						 	});
						 	fileThiz.upload.uploadImages = existImages;
						}
						if($(this).closest("li").data('url')){
							fileThiz.upload.uploadImages.remove($(this).closest("li").data('url'));
						}else{
							fileThiz.upload.uploadImages.remove($(this).closest("li").find("img").attr("src"));
						}
						$(this).closest("li").remove();
						listEl.trigger("change.fileuploader-items-list");
					});
					listEl.off("change.fileuploader-items-list").on("change.fileuploader-items-list",function(){
						$("input[name='"+thiz.data('name')+"']").val($.arrToStr(fileThiz.upload.uploadImages)); 
					});
					
					var plusInput = listEl.find('.fileuploader-thumbnails-input'),
						api = $.fileuploader.getInstance(inputEl.get(0));
				
					plusInput.on('click', function() {
						api.open();
					});
				},
				upload: {
					url: window.UEDITOR_SERVER_URL+'?action=uploadimage',
		            data: null,
		            type: 'POST',
		            enctype: 'multipart/form-data',
		            start: true,
		            synchron: true,
		            beforeSend: null,
		            dataType:'json',
		            uploadImages:[],
		            onSuccess: function(data, item) {
		            	var uploadImage = eval("["+data+"]")[0];
		            	var url = null;
				 		if(uploadImage["url"]){
				 			url = window.STATIC_URL+uploadImage["url"];
				 			this.uploadImages.push(url);
				 		}
						setTimeout(function() {
							item.html.find('.progress-holder').hide();
							item.html.data({url:url});
							item.renderImage();
							item.data.url=url;
						}, 400);
		            },
		            onError: function(item) {
						item.html.find('.progress-holder').hide();
						item.html.find('.fileuploader-item-icon i').text('Failed!');
		            },
		            onProgress: function(data, item) {
		                var progressBar = item.html.find('.progress-holder');
						
		                if(progressBar.length > 0) {
		                    progressBar.show();
		                    progressBar.find('.fileuploader-progressbar .bar').width(data.percentage + "%");
		                }
		            },
		            onComplete: function(listEl, parentEl, newInputEl, inputEl, jqXHR, textStatus) {
						 var uploadImages = this.uploadImages;
						 var existImages = [];
						 listEl.find("img.userload").each(function(){
						 	  if(!uploadImages.contains($(this).attr("src"))){
						 	  	existImages.push($(this).attr("src"));
						 	  }
						 });
						 uploadImages = existImages.concat(uploadImages);
						 this.uploadImages = uploadImages;
						 if(uploadImages.length>0){
						 	$("input[name='"+thiz.data('name')+"']").val($.arrToStr(this.uploadImages));
						 }else{
						 	$("input[name='"+thiz.data('name')+"']").val("");
						 }
					}
		        },
				dragDrop: {
					container: '.fileuploader-thumbnails-input'
				}
			})
	    });
    
	    //保存表单
	    $('[data-ride="form"]').each(function () {
		      var $thiz= $(this);
		      var errors = $("#errors"+$thiz.attr("id"));
		      $thiz.bootstrapValidator()
			  .on('error.form.bv', function(e, data) {
			     var messages = errors.find("small");
	             if(messages.length>0){
	            	$.toast(messages.eq(0).html(),"forbidden");
	             }
	             $thiz.bootstrapValidator('disableSubmitButtons', false);
	          })
		      .on('success.field.bv', function(e, data) {
		            errors.find('[data-bv-for="' + data.field + '"]').remove();
		            $thiz.bootstrapValidator('disableSubmitButtons', false);
		      })
		      .on('error.field.bv', function(e, data) {
		            $thiz.bootstrapValidator('disableSubmitButtons', false);
		      })
		      .on('success.form.bv', function(e) {
			        e.preventDefault();
			        var form = $(e.target);
			        var flag =true;
			        var message = null; 
			        form.find("input.weui-uploader-files").each(function(){
			        	if($(this).attr("required") && $(this).val()==""){
			        		flag=false;
			        		message=$(this).data("bv-notempty-message");
			        		return;
			        	}
			        })
			        if(!flag){
			        	$.toast(message,"forbidden");
			        	$thiz.bootstrapValidator('disableSubmitButtons', false);
		        		return;
			        }
			        form.ajaxSubmit({
						type:"post",
						dataType:'json',
						beforeSend:function(){
							$.toast('正在保存数据，请等待...',"text",null,false);
					    },
						success: function(data){
				        	if (data.status == 'success' || data.status == 1) {
				        		$.hideLoading();
								$.toast(data.message||data.msg);
								var params = $.getUrlParams();
								var page = params['redirecturl']||$thiz.data("listhref")||thiz.data("href");
								if(page && !page.startsWith("empty")){
									location.href = page;
								}else{
									$thiz.bootstrapValidator('disableSubmitButtons', false);
								}
							} else {
								$.toast(data.message||data.msg, 'forbidden');
							}
							
				        },
				        error: function(xhr, type){
				        	 $thiz.bootstrapValidator('disableSubmitButtons', false);
				             $.toast('加载数据出错', 'forbidden');
				        }
					});
			    });
		});	
		
	    //增加ajax-page页面
	    $(document).off('click.save','.save').on('click.save','.save',function(e){
	    	e.stopPropagation();
			e.preventDefault();
	    	var thiz= $(this);
	    	function save(){
	    		$.ajax({
	            	type:"post",
					dataType:'json',
					url : (thiz.attr('url')||thiz.attr('href')||'/data/save.html'),
					data:thiz.data(),
					beforeSend:function(){
						if(!thiz.is(".hidetoast")){
							$.toast(thiz.attr("savemsg")||'正在保存数据，请等待...',"text",null,false);
						}
				    },
				    success: function(result) { 
				    	 $.hideLoading();
				    	 if (result.status == 'success') {
				    		 if(!thiz.is(".hidetoast")){
				    			 $.toast(result.message);
							 }
				    		 var trigger = thiz.data("trigger");
				    		 if(trigger&&$(trigger)){
			                	$(trigger).trigger("saveSuccess");
			                 }
				    	 }else{
				    	 	$.toast(result.message,"forbidden");
				    	 }
				    },
		            error: function(xhr, type){
		            	 $.hideLoading();
		            	 $.toast('保存数据出错','forbidden');
		            }
            	});
	    	}

            if($.cookie(window.GlobalConfig.cookieName||('uid'+$("body").data("dashboardid")))!=null) {
                save();
            }else{
                $.login('','登录');
            }

	    });
	    
	    //增加ajax-page页面
	    $(document).off('click.ajaxremove','.ajax-remove').on('click.ajaxremove','.ajax-remove',function(e){
	    	e.stopPropagation();
			e.preventDefault();
	    	var thiz= $(this);
	    	function remove(){
	    		var table = thiz.closest(".table-container");
	    		var data={ 
					formid: table.data('formid')||thiz.data("formid"),
					dashboardid: table.data('dashboardid')||thiz.data('dashboardid'),
                	values: [thiz.data("id")]
                };
	    		data= $.extend(data,thiz.data());
	    		$.ajax({
	            	type:"post",
					dataType:'json',
					url : (thiz.data('url')||thiz.attr('url')||thiz.data('href')||thiz.attr('href')||'/data/remove.html'),
					data:data,
				    success: function(result) { 
				    	 if (result.status == 'success') {
				    	 	$.toast(thiz.data('successmsg')||result.message);
				    	 	var trigger = thiz.data("trigger");
				    	 	thiz.closest(".user-load").remove();
				    	 	if(trigger&&$(trigger)){
			                	$(trigger).trigger("removeSuccess");
			                }
				    	 }else{
				    	 	$.toast(result.message,"forbidden");
				    	 }
				    }
            	});
	    	}

	    	
	    	if($.cookie(window.GlobalConfig.cookieName||('uid'+$("body").data("dashboardid")))!=null){
				 $.confirm({
					  title: '温馨提示',
					  text: thiz.data('removemsg')||'你确定删除吗？',
					  onOK: function () {
					      remove();
					  },
					  onCancel: function () {
					  }
			     });
			}else{
				$.login('','登录');
			}
	    	return false;
	    });
    
	    //增加ajax-page页面
	    $(document).off('click.ajaxedit','.ajax-edit').on('click.ajaxedit','.ajax-edit',function(e){
	    	e.stopPropagation();
			e.preventDefault();
	    	var thiz= $(this);
	    	if($.cookie(window.GlobalConfig.cookieName||('uid'+$("body").data("dashboardid")))!=null){
	    		var table = thiz.closest(".table-container");
	    		var href = thiz.attr('url')||thiz.attr('href')||table.attr("edithref");
		    	if(!href || href.startsWith("empty")){
		    	   $.toast("请先设置编辑页面哟",'forbidden');
		    	   return false;
		    	}
		    	var data = table.data();
		    	location.href=href+((href.indexOf('?') == -1) ? '?' : '&')+$.param({dashboardid:data.dashboardid,formid:data.formid,id:thiz.data("id"),_ajax_page:'1',_ajax_edit_page:'1'});
			}else{
				$.login('','登录');
			}
	    });
	    
	    //新增页面跳转
	    $(document).off('click.edit','.btn-plus .edit').on('click.edit','.btn-plus .edit',function(e){
	    	var thiz= $(this);
	    	var href = thiz.attr("href");
	    	if(href.startsWith("empty")||href==""){
	    	   $.toast("请先设置编辑页面哟",'forbidden');
	    	   return false;
	    	}
	    	if(!thiz.data('url')){
	    	 	thiz.data('url',thiz.attr('href'));
	    	}
    	  	var url = thiz.data('url');
    	 	url += ((url.indexOf('?') == -1) ? '?' : '&')+$.getParamData(thiz,{_ajax_page:'1'});
    	 	thiz.attr('href',url);
	    	return true;
	    });
	    
	    //修改页面
		if($.getUrlParam("_ajax_edit_page")&&$.getUrlParam("_ajax_edit_page")=='1'){
		    	var loadlength =  $('[data-ride="loadData"]').length;
		    	var timeoutid=null;
				//得到编辑值
				function ajaxEditData(){
					var postData = $.getUrlParams();
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
									$.toast(data.message||data.msg);
								}
				            },
				            error: function(xhr, type){
				            	 $thiz.data("load", "1");
				            	 $.toast('加载数据出错','forbidden');
				            }
				       });
					}
		   			
		        }
				function peditdata(){
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
			         		peditdata();
			    		}, 100);
					}
				}
				peditdata();
		  }
		  
		  $("body").off('click.backtohome').on('click.backtohome', '.backtohome',function () {
			   $("#home.ajax-page").data("backtohome",1);
			   $("#home.ajax-page").click();
		  });
	});
	
	
	 
}(jQuery);


+ function($) {
  "use strict";

  var defaults;
  
  var show = function(html, className) {
    className = className || "";
    var mask = $("<div class='weui-mask_transparent'></div>").appendTo(document.body);

    var tpl = '<div class="weui-toast ' + className + '">' + html + '</div>';
    var dialog = $(tpl).appendTo(document.body);

    dialog.show();
    dialog.addClass("weui-toast--visible");
  };

  var hide = function(callback) {
    $(".weui-mask_transparent").remove();
    $(".weui-toast--visible").removeClass("weui-toast--visible").one('bsTransitionEnd', function () {
        var $this = $(this);
        $this.remove();
        callback && callback($this);
    }).emulateTransitionEnd(500);
  };

  $.toast = function(text, style, callback , close) {
    if(typeof style === "function") {
      callback = style;
    }
    close = close||true;
    var className, iconClassName = 'weui-icon-success-no-circle';
    var duration = toastDefaults.duration;
    if(style == "cancel") {
      className = "weui-toast_cancel";
      iconClassName = 'weui-icon-cancel';
    } else if(style == "forbidden") {
      className = "weui-toast--forbidden";
      iconClassName = 'weui-icon-warn';
    } else if(style == "text") {
      className = "weui-toast--text";
    } else if(typeof style === typeof 1) {
      duration = style;
    }
    show('<i class="' + iconClassName + ' weui-icon_toast"></i><p class="weui-toast_content">' + (text || "已经完成") + '</p>', className);

    if(close){
    	setTimeout(function() {
	      hide(callback);
	    }, duration);
    }
    
  }

  $.showLoading = function(text) {
    var html = '<div class="weui_loading">';
    html += '<i class="weui-loading weui-icon_toast"></i>';
    html += '</div>';
    html += '<p class="weui-toast_content">' + (text || "数据加载中") + '</p>';
    show(html, 'weui_loading_toast');
  }

  $.hideLoading = function() {
    hide();
  }

  var toastDefaults = $.toast.prototype.defaults = {
    duration: 2500
  }

}(jQuery);

+function ($) {
  'use strict';

  // TOOLTIP PUBLIC CLASS DEFINITION
  // ===============================

  var Tooltip = function (element, options) {
    this.type       = null;
    this.options    = null;
    this.enabled    = null;
    this.timeout    = null;
    this.hoverState = null;
    this.$element   = null;
    this.inState    = null;

    this.init('tooltip', element, options)
  };

  Tooltip.VERSION  = '3.3.5';

  Tooltip.TRANSITION_DURATION = 150;

  Tooltip.DEFAULTS = {
    animation: true,
    placement: 'top',
    selector: false,
    template: '<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>',
    trigger: 'hover focus',
    title: '',
    delay: 0,
    html: false,
    container: false,
    viewport: {
      selector: 'body',
      padding: 0
    }
  };

  Tooltip.prototype.init = function (type, element, options) {
    this.enabled   = true;
    this.type      = type;
    this.$element  = $(element);
    this.options   = this.getOptions(options);
    this.$viewport = this.options.viewport && $($.isFunction(this.options.viewport) ? this.options.viewport.call(this, this.$element) : (this.options.viewport.selector || this.options.viewport));
    this.inState   = { click: false, hover: false, focus: false };

    if (this.$element[0] instanceof document.constructor && !this.options.selector) {
      throw new Error('`selector` option must be specified when initializing ' + this.type + ' on the window.document object!')
    }

    var triggers = this.options.trigger.split(' ');

    for (var i = triggers.length; i--;) {
      var trigger = triggers[i];

      if (trigger == 'click') {
        this.$element.on('click.' + this.type, this.options.selector, $.proxy(this.toggle, this))
      } else if (trigger != 'manual') {
        var eventIn  = trigger == 'hover' ? 'mouseenter' : 'focusin';
        var eventOut = trigger == 'hover' ? 'mouseleave' : 'focusout';

        this.$element.on(eventIn  + '.' + this.type, this.options.selector, $.proxy(this.enter, this));
        this.$element.on(eventOut + '.' + this.type, this.options.selector, $.proxy(this.leave, this))
      }
    }

    this.options.selector ?
      (this._options = $.extend({}, this.options, { trigger: 'manual', selector: '' })) :
      this.fixTitle()
  };

  Tooltip.prototype.getDefaults = function () {
    return Tooltip.DEFAULTS
  };

  Tooltip.prototype.getOptions = function (options) {
    options = $.extend({}, this.getDefaults(), this.$element.data(), options);

    if (options.delay && typeof options.delay == 'number') {
      options.delay = {
        show: options.delay,
        hide: options.delay
      }
    }

    return options
  };

  Tooltip.prototype.getDelegateOptions = function () {
    var options  = {};
    var defaults = this.getDefaults();

    this._options && $.each(this._options, function (key, value) {
      if (defaults[key] != value) options[key] = value
    });

    return options
  };

  Tooltip.prototype.enter = function (obj) {
    var self = obj instanceof this.constructor ?
      obj : $(obj.currentTarget).data('bs.' + this.type);

    if (!self) {
      self = new this.constructor(obj.currentTarget, this.getDelegateOptions());
      $(obj.currentTarget).data('bs.' + this.type, self)
    }

    if (obj instanceof $.Event) {
      self.inState[obj.type == 'focusin' ? 'focus' : 'hover'] = true
    }

    if (self.tip().hasClass('in') || self.hoverState == 'in') {
      self.hoverState = 'in';
      return
    }

    clearTimeout(self.timeout);

    self.hoverState = 'in';

    if (!self.options.delay || !self.options.delay.show) return self.show();

    self.timeout = setTimeout(function () {
      if (self.hoverState == 'in') self.show()
    }, self.options.delay.show)
  };

  Tooltip.prototype.isInStateTrue = function () {
    for (var key in this.inState) {
      if (this.inState[key]) return true
    }

    return false
  };

  Tooltip.prototype.leave = function (obj) {
    var self = obj instanceof this.constructor ?
      obj : $(obj.currentTarget).data('bs.' + this.type);

    if (!self) {
      self = new this.constructor(obj.currentTarget, this.getDelegateOptions());
      $(obj.currentTarget).data('bs.' + this.type, self)
    }

    if (obj instanceof $.Event) {
      self.inState[obj.type == 'focusout' ? 'focus' : 'hover'] = false
    }

    if (self.isInStateTrue()) return;

    clearTimeout(self.timeout);

    self.hoverState = 'out';

    if (!self.options.delay || !self.options.delay.hide) return self.hide();

    self.timeout = setTimeout(function () {
      if (self.hoverState == 'out') self.hide()
    }, self.options.delay.hide)
  };

  Tooltip.prototype.show = function () {
    var e = $.Event('show.bs.' + this.type);

    if (this.hasContent() && this.enabled) {
      this.$element.trigger(e);

      var inDom = $.contains(this.$element[0].ownerDocument.documentElement, this.$element[0]);
      if (e.isDefaultPrevented() || !inDom) return;
      var that = this;

      var $tip = this.tip();

      var tipId = this.getUID(this.type);

      this.setContent();
      $tip.attr('id', tipId);
      this.$element.attr('aria-describedby', tipId);

      if (this.options.animation) $tip.addClass('fade');

      var placement = typeof this.options.placement == 'function' ?
        this.options.placement.call(this, $tip[0], this.$element[0]) :
        this.options.placement;

      var autoToken = /\s?auto?\s?/i;
      var autoPlace = autoToken.test(placement);
      if (autoPlace) placement = placement.replace(autoToken, '') || 'top';

      $tip
        .detach()
        .css({ top: 0, left: 0, display: 'block' })
        .addClass(placement)
        .data('bs.' + this.type, this);

      this.options.container ? $tip.appendTo(this.options.container) : $tip.insertAfter(this.$element);
      this.$element.trigger('inserted.bs.' + this.type);

      var pos          = this.getPosition();
      var actualWidth  = $tip[0].offsetWidth;
      var actualHeight = $tip[0].offsetHeight;

      if (autoPlace) {
        var orgPlacement = placement;
        var viewportDim = this.getPosition(this.$viewport);

        placement = placement == 'bottom' && pos.bottom + actualHeight > viewportDim.bottom ? 'top'    :
                    placement == 'top'    && pos.top    - actualHeight < viewportDim.top    ? 'bottom' :
                    placement == 'right'  && pos.right  + actualWidth  > viewportDim.width  ? 'left'   :
                    placement == 'left'   && pos.left   - actualWidth  < viewportDim.left   ? 'right'  :
                    placement;

        $tip
          .removeClass(orgPlacement)
          .addClass(placement)
      }

      var calculatedOffset = this.getCalculatedOffset(placement, pos, actualWidth, actualHeight);

      this.applyPlacement(calculatedOffset, placement);

      var complete = function () {
        var prevHoverState = that.hoverState;
        that.$element.trigger('shown.bs.' + that.type);
        that.hoverState = null;

        if (prevHoverState == 'out') that.leave(that)
      };

      $.support.transition && this.$tip.hasClass('fade') ?
        $tip
          .one('bsTransitionEnd', complete)
          .emulateTransitionEnd(Tooltip.TRANSITION_DURATION) :
        complete()
    }
  };

  Tooltip.prototype.applyPlacement = function (offset, placement) {
    var $tip   = this.tip();
    var width  = $tip[0].offsetWidth;
    var height = $tip[0].offsetHeight;

    // manually read margins because getBoundingClientRect includes difference
    var marginTop = parseInt($tip.css('margin-top'), 10);
    var marginLeft = parseInt($tip.css('margin-left'), 10);

    // we must check for NaN for ie 8/9
    if (isNaN(marginTop))  marginTop  = 0;
    if (isNaN(marginLeft)) marginLeft = 0;

    offset.top  += marginTop;
    offset.left += marginLeft;

    // $.fn.offset doesn't round pixel values
    // so we use setOffset directly with our own function B-0
    $.offset.setOffset($tip[0], $.extend({
      using: function (props) {
        $tip.css({
          top: Math.round(props.top),
          left: Math.round(props.left)
        })
      }
    }, offset), 0);

    $tip.addClass('in');

    // check to see if placing tip in new offset caused the tip to resize itself
    var actualWidth  = $tip[0].offsetWidth;
    var actualHeight = $tip[0].offsetHeight;

    if (placement == 'top' && actualHeight != height) {
      offset.top = offset.top + height - actualHeight
    }

    var delta = this.getViewportAdjustedDelta(placement, offset, actualWidth, actualHeight);

    if (delta.left) offset.left += delta.left;
    else offset.top += delta.top;

    var isVertical          = /top|bottom/.test(placement);
    var arrowDelta          = isVertical ? delta.left * 2 - width + actualWidth : delta.top * 2 - height + actualHeight;
    var arrowOffsetPosition = isVertical ? 'offsetWidth' : 'offsetHeight';

    $tip.offset(offset);
    this.replaceArrow(arrowDelta, $tip[0][arrowOffsetPosition], isVertical)
  };

  Tooltip.prototype.replaceArrow = function (delta, dimension, isVertical) {
    this.arrow()
      .css(isVertical ? 'left' : 'top', 50 * (1 - delta / dimension) + '%')
      .css(isVertical ? 'top' : 'left', '')
  };

  Tooltip.prototype.setContent = function () {
    var $tip  = this.tip();
    var title = this.getTitle();

    $tip.find('.tooltip-inner')[this.options.html ? 'html' : 'text'](title);
    $tip.removeClass('fade in top bottom left right')
  };

  Tooltip.prototype.hide = function (callback) {
    var that = this;
    var $tip = $(this.$tip);
    var e    = $.Event('hide.bs.' + this.type);

    function complete() {
      if (that.hoverState != 'in') $tip.detach();
      that.$element
        .removeAttr('aria-describedby')
        .trigger('hidden.bs.' + that.type);
      callback && callback()
    }

    this.$element.trigger(e);

    if (e.isDefaultPrevented()) return;

    $tip.removeClass('in');

    $.support.transition && $tip.hasClass('fade') ?
      $tip
        .one('bsTransitionEnd', complete)
        .emulateTransitionEnd(Tooltip.TRANSITION_DURATION) :
      complete();

    this.hoverState = null;

    return this
  };

  Tooltip.prototype.fixTitle = function () {
    var $e = this.$element;
    if ($e.attr('title') || typeof $e.attr('data-original-title') != 'string') {
      $e.attr('data-original-title', $e.attr('title') || '').attr('title', '')
    }
  };

  Tooltip.prototype.hasContent = function () {
    return this.getTitle()
  };

  Tooltip.prototype.getPosition = function ($element) {
    $element   = $element || this.$element;

    var el     = $element[0];
    var isBody = el.tagName == 'BODY';

    var elRect    = el.getBoundingClientRect();
    if (elRect.width == null) {
      // width and height are missing in IE8, so compute them manually; see https://github.com/twbs/bootstrap/issues/14093
      elRect = $.extend({}, elRect, { width: elRect.right - elRect.left, height: elRect.bottom - elRect.top })
    }
    var elOffset  = isBody ? { top: 0, left: 0 } : $element.offset();
    var scroll    = { scroll: isBody ? document.documentElement.scrollTop || document.body.scrollTop : $element.scrollTop() };
    var outerDims = isBody ? { width: $(window).width(), height: $(window).height() } : null;

    return $.extend({}, elRect, scroll, outerDims, elOffset)
  };

  Tooltip.prototype.getCalculatedOffset = function (placement, pos, actualWidth, actualHeight) {
    return placement == 'bottom' ? { top: pos.top + pos.height,   left: pos.left + pos.width / 2 - actualWidth / 2 } :
           placement == 'top'    ? { top: pos.top - actualHeight, left: pos.left + pos.width / 2 - actualWidth / 2 } :
           placement == 'left'   ? { top: pos.top + pos.height / 2 - actualHeight / 2, left: pos.left - actualWidth } :
        /* placement == 'right' */ { top: pos.top + pos.height / 2 - actualHeight / 2, left: pos.left + pos.width }

  };

  Tooltip.prototype.getViewportAdjustedDelta = function (placement, pos, actualWidth, actualHeight) {
    var delta = { top: 0, left: 0 };
    if (!this.$viewport) return delta;

    var viewportPadding = this.options.viewport && this.options.viewport.padding || 0;
    var viewportDimensions = this.getPosition(this.$viewport);

    if (/right|left/.test(placement)) {
      var topEdgeOffset    = pos.top - viewportPadding - viewportDimensions.scroll;
      var bottomEdgeOffset = pos.top + viewportPadding - viewportDimensions.scroll + actualHeight;
      if (topEdgeOffset < viewportDimensions.top) { // top overflow
        delta.top = viewportDimensions.top - topEdgeOffset
      } else if (bottomEdgeOffset > viewportDimensions.top + viewportDimensions.height) { // bottom overflow
        delta.top = viewportDimensions.top + viewportDimensions.height - bottomEdgeOffset
      }
    } else {
      var leftEdgeOffset  = pos.left - viewportPadding;
      var rightEdgeOffset = pos.left + viewportPadding + actualWidth;
      if (leftEdgeOffset < viewportDimensions.left) { // left overflow
        delta.left = viewportDimensions.left - leftEdgeOffset
      } else if (rightEdgeOffset > viewportDimensions.right) { // right overflow
        delta.left = viewportDimensions.left + viewportDimensions.width - rightEdgeOffset
      }
    }

    return delta
  };

  Tooltip.prototype.getTitle = function () {
    var title;
    var $e = this.$element;
    var o  = this.options;

    title = $e.attr('data-original-title')
      || (typeof o.title == 'function' ? o.title.call($e[0]) :  o.title);

    return title
  };

  Tooltip.prototype.getUID = function (prefix) {
    do prefix += ~~(Math.random() * 1000000);
    while (document.getElementById(prefix));
    return prefix
  };

  Tooltip.prototype.tip = function () {
    if (!this.$tip) {
      this.$tip = $(this.options.template);
      if (this.$tip.length != 1) {
        throw new Error(this.type + ' `template` option must consist of exactly 1 top-level element!')
      }
    }
    return this.$tip
  };

  Tooltip.prototype.arrow = function () {
    return (this.$arrow = this.$arrow || this.tip().find('.tooltip-arrow'))
  };

  Tooltip.prototype.enable = function () {
    this.enabled = true
  };

  Tooltip.prototype.disable = function () {
    this.enabled = false
  };

  Tooltip.prototype.toggleEnabled = function () {
    this.enabled = !this.enabled
  };

  Tooltip.prototype.toggle = function (e) {
    var self = this;
    if (e) {
      self = $(e.currentTarget).data('bs.' + this.type);
      if (!self) {
        self = new this.constructor(e.currentTarget, this.getDelegateOptions());
        $(e.currentTarget).data('bs.' + this.type, self)
      }
    }

    if (e) {
      self.inState.click = !self.inState.click;
      if (self.isInStateTrue()) self.enter(self);
      else self.leave(self)
    } else {
      self.tip().hasClass('in') ? self.leave(self) : self.enter(self)
    }
  };

  Tooltip.prototype.destroy = function () {
    var that = this;
    clearTimeout(this.timeout);
    this.hide(function () {
      that.$element.off('.' + that.type).removeData('bs.' + that.type);
      if (that.$tip) {
        that.$tip.detach()
      }
      that.$tip = null;
      that.$arrow = null;
      that.$viewport = null
    })
  };


  // TOOLTIP PLUGIN DEFINITION
  // =========================

  function Plugin(option) {
    return this.each(function () {
      var $this   = $(this);
      var data    = $this.data('bs.tooltip');
      var options = typeof option == 'object' && option;

      if (!data && /destroy|hide/.test(option)) return;
      if (!data) $this.data('bs.tooltip', (data = new Tooltip(this, options)));
      if (typeof option == 'string') data[option]()
    })
  }

  var old = $.fn.tooltip;

  $.fn.tooltip             = Plugin;
  $.fn.tooltip.Constructor = Tooltip;


  // TOOLTIP NO CONFLICT
  // ===================

  $.fn.tooltip.noConflict = function () {
    $.fn.tooltip = old;
    return this
  }

}(jQuery);


/* ========================================================================
 * Bootstrap: popover.js v3.3.5
 * http://getbootstrap.com/javascript/#popovers
 * ========================================================================
 * Copyright 2011-2015 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 * ======================================================================== */


+function ($) {
  'use strict';

  // POPOVER PUBLIC CLASS DEFINITION
  // ===============================

  var Popover = function (element, options) {
    this.init('popover', element, options)
  };

  if (!$.fn.tooltip) throw new Error('Popover requires tooltip.js');

  Popover.VERSION  = '3.3.5';

  Popover.DEFAULTS = $.extend({}, $.fn.tooltip.Constructor.DEFAULTS, {
    placement: 'right',
    trigger: 'click',
    content: '',
    template: '<div class="popover" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'
  });


  // NOTE: POPOVER EXTENDS tooltip.js
  // ================================

  Popover.prototype = $.extend({}, $.fn.tooltip.Constructor.prototype);

  Popover.prototype.constructor = Popover;

  Popover.prototype.getDefaults = function () {
    return Popover.DEFAULTS
  };

  Popover.prototype.setContent = function () {
    var $tip    = this.tip();
    var title   = this.getTitle();
    var content = this.getContent();

    $tip.find('.popover-title')[this.options.html ? 'html' : 'text'](title);
    $tip.find('.popover-content').children().detach().end()[ // we use append for html objects to maintain js events
      this.options.html ? (typeof content == 'string' ? 'html' : 'append') : 'text'
    ](content);

    $tip.removeClass('fade top bottom left right in');

    // IE8 doesn't accept hiding via the `:empty` pseudo selector, we have to do
    // this manually by checking the contents.
    if (!$tip.find('.popover-title').html()) $tip.find('.popover-title').hide()
  };

  Popover.prototype.hasContent = function () {
    return this.getTitle() || this.getContent()
  };

  Popover.prototype.getContent = function () {
    var $e = this.$element;
    var o  = this.options;

    return $e.attr('data-content')
      || (typeof o.content == 'function' ?
            o.content.call($e[0]) :
            o.content)
  };

  Popover.prototype.arrow = function () {
    return (this.$arrow = this.$arrow || this.tip().find('.arrow'))
  };


  // POPOVER PLUGIN DEFINITION
  // =========================

  function Plugin(option) {
    return this.each(function () {
      var $this   = $(this);
      var data    = $this.data('bs.popover');
      var options = typeof option == 'object' && option;

      if (!data && /destroy|hide/.test(option)) return;
      if (!data) $this.data('bs.popover', (data = new Popover(this, options)));
      if (typeof option == 'string') data[option]()
    })
  }

  var old = $.fn.popover;

  $.fn.popover             = Plugin;
  $.fn.popover.Constructor = Popover;


  // POPOVER NO CONFLICT
  // ===================

  $.fn.popover.noConflict = function () {
    $.fn.popover = old;
    return this
  }

  if (window.console) {
	  console.log("\x25\x63\x44\x49\x59\u5b98\u7f51\u53ef\u89c6\u5316\u8bbe\u8ba1\n\u65e0\u987b\u7f16\u7a0b\x20\u96f6\u4ee3\u7801\u57fa\u7840\x20\u6240\u89c1\u5373\u6240\u5f97\u8bbe\u8ba1\u5de5\u5177\n\u8f7b\u677e\u5236\u4f5c\u5fae\u4fe1\u5c0f\u7a0b\u5e8f\u3001\u539f\u578b\u8bbe\u8ba1\u3001\x77\x65\x62\x61\x70\x70\u8bbe\u8ba1\u3001\x62\x6f\x6f\x74\x73\x74\x72\x61\x70\u3001\u5355\u9875\u52a8\u753b","\x63\x6f\x6c\x6f\x72\x3a\x67\x72\x65\x65\x6e\x3b\x6c\x69\x6e\x65\x2d\x68\x65\x69\x67\x68\x74\x3a\x32\x35\x70\x78\x3b");
	  console.log("\x25\x63\x44\x49\x59\u5b98\u7f51\u7f51\u7ad9\uff1a\x68\x74\x74\x70\x3a\x2f\x2f\x77\x77\x77\x2e\x64\x69\x79\x67\x77\x2e\x63\x6f\x6d","\x63\x6f\x6c\x6f\x72\x3a\x72\x65\x64\x3b\x66\x6f\x6e\x74\x2d\x73\x69\x7a\x65\x3a\x32\x30\x70\x78")
  }
}(jQuery);

}));
