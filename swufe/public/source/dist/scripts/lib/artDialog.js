!function(){function t(e){var o=i[e],s="exports";return"object"==typeof o?o:(o[s]||(o[s]={},o[s]=o.call(o[s],t,o[s],o)||o[s]),o[s])}function e(t,e){i[t]=e}var i={};e("jquery",function(){return jQuery}),e("popup",function(t){function e(){this.destroyed=!1,this.__popup=i("<div />").attr({tabindex:"-1"}).css({display:"none",position:"absolute",left:0,top:0,bottom:"auto",right:"auto",margin:0,padding:0,outline:0,border:"0 none",background:"transparent"}).html(this.innerHTML).appendTo("body"),this.__backdrop=i("<div />"),this.node=this.__popup[0],this.backdrop=this.__backdrop[0],o++}var i=t("jquery"),o=0,s=!("minWidth"in i("html")[0].style),n=!s;return i.extend(e.prototype,{node:null,backdrop:null,fixed:!1,destroyed:!0,open:!1,returnValue:"",autofocus:!0,align:"bottom left",backdropBackground:"#000",backdropOpacity:.7,innerHTML:"",className:"ui-popup",show:function(t){if(this.destroyed)return this;var e=this,o=this.__popup;return this.__activeElement=this.__getActive(),this.open=!0,this.follow=t||this.follow,this.__ready||(o.addClass(this.className),this.modal&&this.__lock(),o.html()||o.html(this.innerHTML),s||i(window).on("resize",this.__onresize=function(){e.reset()}),this.__ready=!0),o.addClass(this.className+"-show").attr("role",this.modal?"alertdialog":"dialog").css("position",this.fixed?"fixed":"absolute").show(),this.__backdrop.show(),this.reset().focus(),this.__dispatchEvent("show"),this},showModal:function(){return this.modal=!0,this.show.apply(this,arguments)},close:function(t){return!this.destroyed&&this.open&&(void 0!==t&&(this.returnValue=t),this.__popup.hide().removeClass(this.className+"-show"),this.__backdrop.hide(),this.open=!1,this.blur(),this.__dispatchEvent("close")),this},remove:function(){if(this.destroyed)return this;this.__dispatchEvent("beforeremove"),e.current===this&&(e.current=null),this.__unlock(),this.__popup.remove(),this.__backdrop.remove(),this.blur(),s||i(window).off("resize",this.__onresize),this.__dispatchEvent("remove");for(var t in this)delete this[t];return this},reset:function(){var t=this.follow;return t?this.__follow(t):this.__center(),this.__dispatchEvent("reset"),this},focus:function(){var t=this.node,o=e.current;if(o&&o!==this&&o.blur(!1),!i.contains(t,this.__getActive())){var s=this.__popup.find("[autofocus]")[0];!this._autofocus&&s?this._autofocus=!0:s=t,this.__focus(s)}return e.current=this,this.__popup.addClass(this.className+"-focus"),this.__zIndex(),this.__dispatchEvent("focus"),this},blur:function(){var t=this.__activeElement,e=arguments[0];return e!==!1&&this.__focus(t),this._autofocus=!1,this.__popup.removeClass(this.className+"-focus"),this.__dispatchEvent("blur"),this},addEventListener:function(t,e){return this.__getEventListener(t).push(e),this},removeEventListener:function(t,e){for(var i=this.__getEventListener(t),o=0;o<i.length;o++)e===i[o]&&i.splice(o--,1);return this},__getEventListener:function(t){var e=this.__listener;return e||(e=this.__listener={}),e[t]||(e[t]=[]),e[t]},__dispatchEvent:function(t){var e=this.__getEventListener(t);this["on"+t]&&this["on"+t]();for(var i=0;i<e.length;i++)e[i].call(this)},__focus:function(t){try{this.autofocus&&!/^iframe$/i.test(t.nodeName)&&t.focus()}catch(e){}},__getActive:function(){try{var t=document.activeElement,e=t.contentDocument,i=e&&e.activeElement||t;return i}catch(o){}},__zIndex:function(){var t=e.zIndex++;this.__popup.css("zIndex",t),this.__backdrop.css("zIndex",t-1),this.zIndex=t},__center:function(){var t=this.__popup,e=i(window),o=i(document),s=this.fixed,n=s?0:o.scrollLeft(),a=s?0:o.scrollTop(),r=e.width(),c=(e.height(),t.width(),t.height(),(r-520)/2-n),h=0,l=t[0].style;l.left=Math.max(parseInt(c),n)+"px",l.top=Math.max(parseInt(h),a)+"px"},__follow:function(t){var e=t.parentNode&&i(t),o=this.__popup;if(this.__followSkin&&o.removeClass(this.__followSkin),e){var s=e.offset();if(s.left*s.top<0)return this.__center()}var n=this,a=this.fixed,r=i(window),c=i(document),h=r.width(),l=r.height(),u=c.scrollLeft(),d=c.scrollTop(),p=o.width(),f=o.height(),_=e?e.outerWidth():0,v=e?e.outerHeight():0,m=this.__offset(t),g=m.left,b=m.top,k=a?g-u:g,w=a?b-d:b,y=a?0:u,x=a?0:d,E=y+h-p,z=x+l-f,I={},L=this.align.split(" "),$=this.className+"-",C={top:"bottom",bottom:"top",left:"right",right:"left"},N={top:"top",bottom:"top",left:"left",right:"left"},T=[{top:w-f,bottom:w+v,left:k-p,right:k+_},{top:w,bottom:w-f+v,left:k,right:k-p+_}],M={left:k+_/2-p/2,top:w+v/2-f/2},V={left:[y,E],top:[x,z]};i.each(L,function(t,e){T[t][e]>V[N[e]][1]&&(e=L[t]=C[e]),T[t][e]<V[N[e]][0]&&(L[t]=C[e])}),L[1]||(N[L[1]]="left"===N[L[0]]?"top":"left",T[1][L[1]]=M[N[L[1]]]),$+=L.join("-")+" "+this.className+"-follow",n.__followSkin=$,e&&o.addClass($),I[N[L[0]]]=parseInt(T[0][L[0]]),I[N[L[1]]]=parseInt(T[1][L[1]]),o.css(I)},__offset:function(t){var e=t.parentNode,o=e?i(t).offset():{left:t.pageX,top:t.pageY};t=e?t:t.target;var s=t.ownerDocument,n=s.defaultView||s.parentWindow;if(n==window)return o;var a=n.frameElement,r=i(s),c=r.scrollLeft(),h=r.scrollTop(),l=i(a).offset(),u=l.left,d=l.top;return{left:o.left+u-c,top:o.top+d-h}},__lock:function(){var t=this,o=this.__popup,s=this.__backdrop,a={position:"fixed",left:0,top:0,width:"100%",height:"100%",overflow:"hidden",userSelect:"none",opacity:0,background:this.backdropBackground};o.addClass(this.className+"-modal"),e.zIndex=e.zIndex+2,this.__zIndex(),n||i.extend(a,{position:"absolute",width:i(window).width()+"px",height:i(document).height()+"px"}),s.css(a).animate({opacity:this.backdropOpacity},150).insertAfter(o).attr({tabindex:"0"}).on("focus",function(){t.focus()})},__unlock:function(){this.modal&&(this.__popup.removeClass(this.className+"-modal"),this.__backdrop.remove(),delete this.modal)}}),e.zIndex=1024,e.current=null,e}),e("dialog-config",{backdropBackground:"#151719",backdropOpacity:.8,content:'<span class="ui-dialog-loading">Loading..</span>',title:"",statusbar:"",button:null,ok:null,cancel:null,okValue:"ok",cancelValue:"cancel",cancelDisplay:!0,width:"",height:"",padding:"",skin:"",quickClose:!1,cssUri:"",innerHTML:'<div i="content" class="modal-wrap"></div>'}),e("dialog",function(t){var e=t("jquery"),i=t("popup"),o=t("dialog-config"),s=o.cssUri;if(s){var n=t[t.toUrl?"toUrl":"resolve"];n&&(s=n(s),s='<link rel="stylesheet" href="'+s+'" />',e("base")[0]?e("base").before(s):e("head").append(s))}var a=0,r=new Date-0,c=!("minWidth"in e("html")[0].style),h="createTouch"in document&&!("onmousemove"in document)||/(iPhone|iPad|iPod)/i.test(navigator.userAgent),l=!c&&!h,u=function(t,i,o){var s=t=t||{};("string"==typeof t||1===t.nodeType)&&(t={content:t,fixed:!h}),t=e.extend(!0,{},u.defaults,t),t._=s;var n=t.id=t.id||r+a,c=u.get(n);return c?c.focus():(l||(t.fixed=!1),t.quickClose&&(t.modal=!0,s.backdropOpacity||(t.backdropOpacity=0)),e.isArray(t.button)||(t.button=[]),void 0!==o&&(t.cancel=o),t.cancel&&t.button.push({id:"cancel",value:t.cancelValue,callback:t.cancel,display:t.cancelDisplay}),void 0!==i&&(t.ok=i),t.ok&&t.button.push({id:"ok",value:t.okValue,callback:t.ok,autofocus:!0}),u.list[n]=new u.create(t))},d=function(){};d.prototype=i.prototype;var p=u.prototype=new d;return u.create=function(t){var o=this;e.extend(this,new i);var s=e(this.node).html(t.innerHTML);return this.options=t,this._popup=s,e.each(t,function(t,e){"function"==typeof o[t]?o[t](e):o[t]=e}),t.zIndex&&(i.zIndex=t.zIndex),s.attr({"aria-labelledby":this._$("title").attr("id","title:"+this.id).attr("id"),"aria-describedby":this._$("content").attr("id","content:"+this.id).attr("id")}),this._$("close").css("display",this.cancel===!1?"none":"").attr("title",this.cancelValue).on("click",function(t){o._trigger("cancel"),t.preventDefault()}),this._$("dialog").addClass(this.skin),this._$("body").css("padding",this.padding),s.on("click","[data-id]",function(t){var i=e(this);i.attr("disabled")||o._trigger(i.data("id")),t.preventDefault()}),t.quickClose&&e(this.backdrop).on("onmousedown"in document?"mousedown":"click",function(){o._trigger("cancel")}),this._esc=function(t){var e=t.target,s=e.nodeName,n=/^input|textarea$/i,a=i.current===o,r=t.keyCode;!a||n.test(s)&&"button"!==e.type||27===r&&o._trigger("cancel")},e(document).on("keydown",this._esc),this.addEventListener("remove",function(){e(document).off("keydown",this._esc),delete u.list[this.id]}),a++,u.oncreate(this),this},u.create.prototype=p,e.extend(p,{content:function(t){return this._$("content").empty("")["object"==typeof t?"append":"html"](t),this.reset()},title:function(t){return this._$("title").text(t),this._$("header")[t?"show":"hide"](),this},width:function(t){return this._$("content").css("width",t),this.reset()},height:function(t){return this._$("content").css("height",t),this.reset()},button:function(t){t=t||[];var i=this,o="",s=0;return this.callbacks={},"string"==typeof t?o=t:e.each(t,function(t,e){e.id=e.id||e.value,i.callbacks[e.id]=e.callback;var n="";e.display===!1?n=' style="display:none"':s++,o+='<button type="button" data-id="'+e.id+'"'+n+(e.disabled?" disabled":"")+(e.autofocus?' autofocus class="ui-dialog-autofocus"':"")+">"+e.value+"</button>"}),this._$("footer")[s?"show":"hide"](),this._$("button").html(o),this},statusbar:function(t){return this._$("statusbar").html(t)[t?"show":"hide"](),this},_$:function(t){return this._popup.find("[i="+t+"]")},_trigger:function(t){var e=this.callbacks[t];return"function"!=typeof e||e.call(this)!==!1?this.close().remove():this}}),u.oncreate=e.noop,u.getCurrent=function(){return i.current},u.get=function(t){return void 0===t?u.list:u.list[t]},u.list={},u.defaults=o,u}),window.dialog=t("dialog")}();
//# sourceMappingURL=artDialog.js.map