!function(e){e.SewisePlayerSkin={version:"1.0.0"},e.SewisePlayer=e.SewisePlayer||{}}(window),function(e){e.SewisePlayer.IVodPlayer=e.SewisePlayer.IVodPlayer||{play:function(){},pause:function(){},stop:function(){},seek:function(){},changeClarity:function(){},setVolume:function(){},toPlay:function(){},duration:function(){},playTime:function(){},type:function(){},showTextTip:function(){},hideTextTip:function(){},muted:function(){}}}(window),function(e){e.SewisePlayerSkin.IVodSkin=e.SewisePlayerSkin.IVodSkin||{player:function(){},started:function(){},paused:function(){},stopped:function(){},seeking:function(){},buffering:function(){},bufferProgress:function(){},loadedProgress:function(){},programTitle:function(){},duration:function(){},playTime:function(){},startTime:function(){},loadSpeed:function(){},initialClarity:function(){},lang:function(){},logo:function(){},timeUpdate:function(){},volume:function(){},clarityButton:function(){},timeDisplay:function(){},controlBarDisplay:function(){},topBarDisplay:function(){},customStrings:function(){},fullScreen:function(){},noramlScreen:function(){},initialAds:function(){}}}(window),function(){SewisePlayerSkin.Utils={stringer:{time2FigFill:function(e){var i,e=Math.floor(e);return i=10>e?"0"+e:""+e},secondsToHMS:function(e){if(!(0>e)){var i=this.time2FigFill(Math.floor(e/3600)),n=this.time2FigFill(e/60%60),e=this.time2FigFill(e%60);return i+":"+n+":"+e}},secondsToMS:function(e){if(!(0>e)){var i=this.time2FigFill(e/60),e=this.time2FigFill(e%60);return i+":"+e}},dateToTimeString:function(e){var i;i=e?e:new Date;var e=i.getFullYear(),n=i.getMonth()+1,t=i.getDate(),s=this.time2FigFill(i.getHours()),o=this.time2FigFill(i.getMinutes());return i=this.time2FigFill(i.getSeconds()),e+"-"+n+"-"+t+" "+s+":"+o+":"+i},dateToTimeStr14:function(e){var i=e.getFullYear(),n=this.time2FigFill(e.getMonth()+1),t=this.time2FigFill(e.getDate()),s=this.time2FigFill(e.getHours()),o=this.time2FigFill(e.getMinutes()),e=this.time2FigFill(e.getSeconds());return i+n+t+s+o+e},dateToStrHMS:function(e){var i=this.time2FigFill(e.getHours()),n=this.time2FigFill(e.getMinutes()),e=this.time2FigFill(e.getSeconds());return i+":"+n+":"+e},dateToYMD:function(e){var i=e.getFullYear(),n=this.time2FigFill(e.getMonth()+1),e=this.time2FigFill(e.getUTCDate());return i+"-"+n+"-"+e},hmsToDate:function(e){var e=e.split(":"),i=new Date;return i.setHours(e[0]?e[0]:0,e[1]?e[1]:0,e[2]?e[2]:0),i}},language:{zh_cn:{stop:"停止播放",pause:"暂停",play:"播放",fullScreen:"全屏",normalScreen:"恢复",soundOn:"打开声音",soundOff:"关闭声音",clarity:"清晰度",titleTip:"正在播放：",claritySetting:"清晰度设置",clarityOk:"确定",clarityCancel:"取消",backToLive:"回到直播",loading:"缓冲节目",subtitles:"字幕","default":"默认"},en_us:{stop:"Stop",pause:"Pause",play:"Play",fullScreen:"Full Screen",normalScreen:"Normal Screen",soundOn:"Sound On",soundOff:"Sound Off",clarity:"Clarity",titleTip:"Playing: ",claritySetting:"Definition Setting",clarityOk:"OK",clarityCancel:"Cancel",backToLive:"Back To Live",loading:"Loading",subtitles:"Subtitles","default":"Default"},lang:{},init:function(e){switch(e){case"en_US":this.lang=this.en_us;break;case"zh_CN":this.lang=this.zh_cn;break;default:this.lang=this.zh_cn}},getString:function(e){return this.lang[e]}}}}(),function(e){SewisePlayerSkin.BannersAds=function(i,n){function t(){d=SewisePlayerSkin.Utils.stringer.dateToYMD(new Date),f=n[d]||n["0000-00-00"],h=0,void 0!=f&&(g=f.length)}function s(){for(var e=(new Date).getTime(),i=0;g>i;i++){var n=SewisePlayerSkin.Utils.stringer.hmsToDate(f[i].time).getTime();if(g-1>i){var t=SewisePlayerSkin.Utils.stringer.hmsToDate(f[i+1].time).getTime();if(e>n&&t>e){h=i,l(h);break}}else if(e>n){h=i,l(h);break}}}function o(){var e=(new Date).getTime();if(g-1>h){var i=SewisePlayerSkin.Utils.stringer.hmsToDate(f[h+1].time).getTime();e>i&&(h++,l(h))}else SewisePlayerSkin.Utils.stringer.dateToYMD(new Date)!=d&&(t(),s())}function l(e){c=f[e].ads[0].picurl,u=f[e].ads[1].picurl,""==c&&""==u||(""==c&&""!=u?c=u:""!=c&&""==u&&(u=c),a.attr("src",c),S.attr("src",u))}var r=e(' <div style="position:absolute; display:table; width:100%; height:100%;"><div style="display:table-cell; text-align:left; vertical-align:middle;"><img style="pointer-events:auto; cursor:pointer;"></div></div> ');r.appendTo(i);var a=r.find("img"),r=e(' <div style="position:absolute; display:table; width:100%; height:100%;"><div style="display:table-cell; text-align:right; vertical-align:middle;"><img style="pointer-events:auto; cursor:pointer;"></div></div> ');r.appendTo(i);var c,u,d,f,h,g,S=r.find("img");t(),void 0!=f&&(1==g&&""==f[0].time?l(0):(s(),setInterval(o,1e4)),a.click(function(e){e.originalEvent.stopPropagation(),window.open(f[h].ads[0].link_url,"_blank")}),S.click(function(e){e.originalEvent.stopPropagation(),window.open(f[h].ads[1].link_url,"_blank")}))}}(window.jQuery),function(e){SewisePlayerSkin.AdsContainer=function(i,n){var t=i.$container,s=i.$sewisePlayerUi,o=n.banners;if(o){var l=e("<div class='sewise_player_ads_banner'></div>");l.css({position:"absolute",width:"100%",height:"100%",left:"0px",top:"0px",overflow:"hidden","pointer-events":"none"}),l.appendTo(t),s.css("z-index",1),SewisePlayerSkin.BannersAds(l,o)}}}(window.jQuery),function(e){SewisePlayerSkin.ElementObject=function(){this.$sewisePlayerUi=e(".sewise-player-ui"),this.$container=this.$sewisePlayerUi.parent(),this.$video=this.$container.find("video").get(0),this.$controlbar=this.$sewisePlayerUi.find(".controlbar"),this.$playBtn=this.$sewisePlayerUi.find(".controlbar-btns-play"),this.$pauseBtn=this.$sewisePlayerUi.find(".controlbar-btns-pause"),this.$stopBtn=this.$sewisePlayerUi.find(".controlbar-btns-stop"),this.$fullscreenBtn=this.$sewisePlayerUi.find(".controlbar-btns-fullscreen"),this.$normalscreenBtn=this.$sewisePlayerUi.find(".controlbar-btns-normalscreen"),this.$soundopenBtn=this.$sewisePlayerUi.find(".controlbar-btns-soundopen"),this.$soundcloseBtn=this.$sewisePlayerUi.find(".controlbar-btns-soundclose"),this.$playtime=this.$sewisePlayerUi.find(".controlbar-playtime"),this.$controlBarProgress=this.$sewisePlayerUi.find(".controlbar-progress"),this.$progressPlayedLine=this.$sewisePlayerUi.find(".controlbar-progress-playedline"),this.$progressPlayedPoint=this.$sewisePlayerUi.find(".controlbar-progress-playpoint"),this.$progressLoadedLine=this.$sewisePlayerUi.find(".controlbar-progress-loadedline"),this.$progressSeekLine=this.$sewisePlayerUi.find(".controlbar-progress-seekline"),this.$logo=this.$sewisePlayerUi.find(".logo"),this.$logoIcon=this.$sewisePlayerUi.find(".logo-icon"),this.$topbar=this.$sewisePlayerUi.find(".topbar"),this.$programTip=this.$sewisePlayerUi.find(".topbar-program-tip"),this.$programTitle=this.$sewisePlayerUi.find(".topbar-program-title"),this.$topbarClock=this.$sewisePlayerUi.find(".topbar-clock"),this.$buffer=this.$sewisePlayerUi.find(".buffer"),this.$bigPlayBtn=this.$sewisePlayerUi.find(".big-play-btn"),this.defStageWidth=this.$container.width(),this.defStageHeight=this.$container.height(),this.defLeftValue=parseInt(this.$container.css("left")),this.defTopValue=parseInt(this.$container.css("top")),this.defOffsetX=this.$container.get(0).getBoundingClientRect().left,this.defOffsetY=this.$container.get(0).getBoundingClientRect().top,this.defOverflow=e("body").css("overflow")}}(window.jQuery),function(e){SewisePlayerSkin.ElementLayout=function(i){function n(){null!=document.fullscreenElement||null!=document.msFullscreenElement||null!=document.mozFullScreenElement||null!=document.webkitFullscreenElement?l.fullScreen():l.normalScreen()}var t=i.$container,s=i.$controlBarProgress,o=i.$playtime,l=this,r=i.defStageWidth,a=i.defStageHeight,c=i.defLeftValue,u=i.defTopValue,d=i.defOffsetX,f=i.defOffsetY,h=i.defOverflow,g=parseInt(r)-288;0>g&&(g+=o.width(),o.hide()),s.css("width",g),document.addEventListener("fullscreenchange",n),document.addEventListener("MSFullscreenChange",n),document.addEventListener("mozfullscreenchange",n),document.addEventListener("webkitfullscreenchange",n),this.fullScreen=function(i){if("not-support"==i){var i=e(window).width(),n=e(window).height()-8;t.css("width",i),t.css("height",n),i=u-f+"px",t.css("left",c-d+"px"),t.css("top",i),e("body").css("overflow","hidden")}else t.css("width","100%"),t.css("height","100%");i=parseInt(t.width())-288,0>i?(i+=o.width(),o.hide()):o.show(),s.css("width",i)},this.normalScreen=function(){t.css("width",r),t.css("height",a),t.css("left",c),t.css("top",u),e("body").css("overflow",h),g=parseInt(r)-288,0>g?(g+=o.width(),o.hide()):o.show(),s.css("width",g)}}}(window.jQuery),function(){SewisePlayerSkin.LogoBox=function(e){var i=e.$logoIcon;this.setLogo=function(e){i.css("background","url("+e+") 0px 0px no-repeat"),i.attr("href","http://www.sewise.com/")}}}(window.jQuery),function(){SewisePlayerSkin.TopBar=function(e){var i=e.$topbar,n=e.$programTip,t=e.$programTitle,s=e.$topbarClock,e=SewisePlayerSkin.Utils.language.getString("titleTip");n.text(e),setInterval(function(){var e=SewisePlayerSkin.Utils.stringer.dateToTimeString();s.text(e)},1e3),this.setTitle=function(e){t.text(e)},this.show=function(){i.css("visibility","visible")},this.hide=function(){i.css("visibility","hidden")},this.hide2=function(){i.hide()},this.initLanguage=function(){var e=SewisePlayerSkin.Utils.language.getString("titleTip");n.text(e)}}}(window.jQuery),function(i){SewisePlayerSkin.ControlBar=function(n,t,s){function o(e){e=M+(e.pageX-C),e>0&&O>e&&(P.css("width",e),b.css("left",e-L/2))}function l(e){u.unbind("mousemove",o),i(document).unbind("mouseup",l),x=e.pageX,C!=x&&(M=P.width(),_=M/O,c.seek(_*B)),E=!1}function r(i){e=i.originalEvent,1==e.targetTouches.length&&(e.preventDefault(),i=M+(e.targetTouches[0].pageX-C),i>0&&O>i&&(P.css("width",i),b.css("left",i-L/2)))}function a(i){e=i.originalEvent,b.unbind("touchmove",r),b.unbind("touchend",a),1==e.changedTouches.length&&(x=e.changedTouches[0].pageX,C!=x&&(M=P.width(),_=M/O,c.seek(_*B))),E=!1}var c,u=n.$container,d=n.$video,f=n.$controlbar,h=n.$playBtn,g=n.$pauseBtn,S=n.$stopBtn,w=n.$fullscreenBtn,p=n.$normalscreenBtn,y=n.$soundopenBtn,m=n.$soundcloseBtn,k=n.$playtime,P=n.$progressPlayedLine,b=n.$progressPlayedPoint,v=n.$progressLoadedLine,$=n.$progressSeekLine,F=n.$buffer,T=n.$bigPlayBtn,U=this,B=.1,I=0,V="00:00:00",D="00:00:00",L=0,E=!1,C=0,x=0,M=0,O=0,_=0,z=!0,L=b.width(),O=$.width();g.hide(),p.hide(),m.hide(),F.hide(),f.click(function(e){e.originalEvent.stopPropagation()}),u.click(function(){z?(f.css("visibility","hidden"),s.hide(),z=!1):(f.css("visibility","visible"),s.show(),z=!0)}),h.click(function(){c.play()}),g.click(function(){c.pause()}),S.click(function(){c.stop()}),T.click(function(e){e.originalEvent.stopPropagation(),c.play()}),w.click(function(){U.fullScreen()}),p.click(function(){U.noramlScreen()}),y.click(function(){c.muted(!0),y.hide(),m.show()}),m.click(function(){c.muted(!1),y.show(),m.hide()}),$.mousedown(function(e){M=e.pageX-e.target.getBoundingClientRect().left,O=$.width(),P.css("width",M),b.css("left",M-L/2),_=M/O,c.seek(_*B)}),b.mousedown(function(e){this.blur(),E=!0,C=e.pageX,M=P.width(),O=$.width(),u.bind("mousemove",o),i(document).bind("mouseup",l)}),b.bind("touchstart",function(i){e=i.originalEvent,b.blur(),i=e.targetTouches[0],E=!0,C=i.pageX,M=P.width(),O=$.width(),b.bind("touchmove",r),b.bind("touchend",a)}),this.setPlayer=function(e){c=e},this.started=function(){h.hide(),g.show(),T.hide()},this.paused=function(){h.show(),g.hide(),T.show()},this.stopped=function(){h.show(),g.hide(),T.show()},this.setDuration=function(e){B=e,D=SewisePlayerSkin.Utils.stringer.secondsToHMS(B)},this.timeUpdate=function(e){I=e,V=SewisePlayerSkin.Utils.stringer.secondsToHMS(I),k.text(V+"/"+D),E||(M=100*(I/B)+"%",P.css("width",M),e=P.width()-L/2,b.css("left",e))},this.loadProgress=function(e){v.css("width",100*e+"%")},this.hide2=function(){f.hide()},this.fullScreen=function(){w.hide(),p.show();var e=u.get(0);e.requestFullscreen?e.requestFullscreen():e.msRequestFullscreen?e.msRequestFullscreen():e.mozRequestFullScreen?e.mozRequestFullScreen():e.webkitRequestFullscreen?e.webkitRequestFullscreen():d.webkitEnterFullscreen?(d.play(),d.webkitEnterFullscreen(),w.show(),p.hide()):t.fullScreen("not-support")},this.noramlScreen=function(){w.show(),p.hide(),document.exitFullscreen?document.exitFullscreen():document.msExitFullscreen?document.msExitFullscreen():document.mozCancelFullScreen?document.mozCancelFullScreen():document.webkitCancelFullScreen?document.webkitCancelFullScreen():t.normalScreen()}}}(window.jQuery),function(e,i){i(document).ready(function(){var e=SewisePlayer.IVodPlayer,i=new SewisePlayerSkin.ElementObject,n=new SewisePlayerSkin.ElementLayout(i),t=new SewisePlayerSkin.LogoBox(i),s=new SewisePlayerSkin.TopBar(i),o=new SewisePlayerSkin.ControlBar(i,n,s);SewisePlayerSkin.IVodSkin.player=function(i){e=i,o.setPlayer(e)},SewisePlayerSkin.IVodSkin.started=function(){o.started()},SewisePlayerSkin.IVodSkin.paused=function(){o.paused()},SewisePlayerSkin.IVodSkin.stopped=function(){o.stopped()},SewisePlayerSkin.IVodSkin.duration=function(e){o.setDuration(e)},SewisePlayerSkin.IVodSkin.timeUpdate=function(e){o.timeUpdate(e)},SewisePlayerSkin.IVodSkin.loadedProgress=function(e){o.loadProgress(e)},SewisePlayerSkin.IVodSkin.programTitle=function(e){s.setTitle(e)},SewisePlayerSkin.IVodSkin.logo=function(e){t.setLogo(e)},SewisePlayerSkin.IVodSkin.volume=function(){},SewisePlayerSkin.IVodSkin.initialClarity=function(){},SewisePlayerSkin.IVodSkin.clarityButton=function(){},SewisePlayerSkin.IVodSkin.timeDisplay=function(){},SewisePlayerSkin.IVodSkin.controlBarDisplay=function(e){"enable"!=e&&o.hide2()},SewisePlayerSkin.IVodSkin.topBarDisplay=function(e){"enable"!=e&&s.hide2()},SewisePlayerSkin.IVodSkin.customStrings=function(){},SewisePlayerSkin.IVodSkin.fullScreen=function(){o.fullScreen()},SewisePlayerSkin.IVodSkin.noramlScreen=function(){o.noramlScreen()},SewisePlayerSkin.IVodSkin.initialAds=function(e){e&&SewisePlayerSkin.AdsContainer(i,e)},SewisePlayerSkin.IVodSkin.lang=function(e){SewisePlayerSkin.Utils.language.init(e),s.initLanguage()};try{SewisePlayer.CommandDispatcher.dispatchEvent({type:SewisePlayer.Events.PLAYER_SKIN_LOADED,playerSkin:SewisePlayerSkin.IVodSkin})}catch(l){console.log("No Main Player")}})}(window,window.jQuery);
//# sourceMappingURL=skin.js.map