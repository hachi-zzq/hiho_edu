!function(e){e.SewisePlayerSkin={version:"1.0.0"},e.SewisePlayer=e.SewisePlayer||{}}(window),function(e){e.SewisePlayer.ILivePlayer=e.SewisePlayer.ILivePlayer||{live:function(){},play:function(){},pause:function(){},stop:function(){},seek:function(){},changeClarity:function(){},setVolume:function(){},playChannel:function(){},toPlay:function(){},duration:function(){},liveTime:function(){},playTime:function(){},type:function(){},showTextTip:function(){},hideTextTip:function(){},muted:function(){}}}(window),function(e){e.SewisePlayerSkin.ILiveSkin=e.SewisePlayerSkin.ILiveSkin||{player:function(){},started:function(){},paused:function(){},stopped:function(){},seeking:function(){},buffering:function(){},bufferProgress:function(){},programTitle:function(){},duration:function(){},playTime:function(){},startTime:function(){},loadSpeed:function(){},initialClarity:function(){},lang:function(){},logo:function(){},timeUpdate:function(){},volume:function(){},clarityButton:function(){},timeDisplay:function(){},controlBarDisplay:function(){},topBarDisplay:function(){},customStrings:function(){},fullScreen:function(){},noramlScreen:function(){},initialAds:function(){}}}(window),function(){SewisePlayerSkin.Utils={stringer:{time2FigFill:function(e){var i,e=Math.floor(e);return i=10>e?"0"+e:""+e},secondsToHMS:function(e){if(!(0>e)){var i=this.time2FigFill(Math.floor(e/3600)),t=this.time2FigFill(e/60%60),e=this.time2FigFill(e%60);return i+":"+t+":"+e}},secondsToMS:function(e){if(!(0>e)){var i=this.time2FigFill(e/60),e=this.time2FigFill(e%60);return i+":"+e}},dateToTimeString:function(e){var i;i=e?e:new Date;var e=i.getFullYear(),t=i.getMonth()+1,n=i.getDate(),s=this.time2FigFill(i.getHours()),o=this.time2FigFill(i.getMinutes());return i=this.time2FigFill(i.getSeconds()),e+"-"+t+"-"+n+" "+s+":"+o+":"+i},dateToTimeStr14:function(e){var i=e.getFullYear(),t=this.time2FigFill(e.getMonth()+1),n=this.time2FigFill(e.getDate()),s=this.time2FigFill(e.getHours()),o=this.time2FigFill(e.getMinutes()),e=this.time2FigFill(e.getSeconds());return i+t+n+s+o+e},dateToStrHMS:function(e){var i=this.time2FigFill(e.getHours()),t=this.time2FigFill(e.getMinutes()),e=this.time2FigFill(e.getSeconds());return i+":"+t+":"+e},dateToYMD:function(e){var i=e.getFullYear(),t=this.time2FigFill(e.getMonth()+1),e=this.time2FigFill(e.getUTCDate());return i+"-"+t+"-"+e},hmsToDate:function(e){var e=e.split(":"),i=new Date;return i.setHours(e[0]?e[0]:0,e[1]?e[1]:0,e[2]?e[2]:0),i}},language:{zh_cn:{stop:"停止播放",pause:"暂停",play:"播放",fullScreen:"全屏",normalScreen:"恢复",soundOn:"打开声音",soundOff:"关闭声音",clarity:"清晰度",titleTip:"正在播放：",claritySetting:"清晰度设置",clarityOk:"确定",clarityCancel:"取消",backToLive:"回到直播",loading:"缓冲节目",subtitles:"字幕","default":"默认"},en_us:{stop:"Stop",pause:"Pause",play:"Play",fullScreen:"Full Screen",normalScreen:"Normal Screen",soundOn:"Sound On",soundOff:"Sound Off",clarity:"Clarity",titleTip:"Playing: ",claritySetting:"Definition Setting",clarityOk:"OK",clarityCancel:"Cancel",backToLive:"Back To Live",loading:"Loading",subtitles:"Subtitles","default":"Default"},lang:{},init:function(e){switch(e){case"en_US":this.lang=this.en_us;break;case"zh_CN":this.lang=this.zh_cn;break;default:this.lang=this.zh_cn}},getString:function(e){return this.lang[e]}}}}(),function(e){SewisePlayerSkin.BannersAds=function(i,t){function n(){d=SewisePlayerSkin.Utils.stringer.dateToYMD(new Date),f=t[d]||t["0000-00-00"],h=0,void 0!=f&&(g=f.length)}function s(){for(var e=(new Date).getTime(),i=0;g>i;i++){var t=SewisePlayerSkin.Utils.stringer.hmsToDate(f[i].time).getTime();if(g-1>i){var n=SewisePlayerSkin.Utils.stringer.hmsToDate(f[i+1].time).getTime();if(e>t&&n>e){h=i,l(h);break}}else if(e>t){h=i,l(h);break}}}function o(){var e=(new Date).getTime();if(g-1>h){var i=SewisePlayerSkin.Utils.stringer.hmsToDate(f[h+1].time).getTime();e>i&&(h++,l(h))}else SewisePlayerSkin.Utils.stringer.dateToYMD(new Date)!=d&&(n(),s())}function l(e){c=f[e].ads[0].picurl,u=f[e].ads[1].picurl,""==c&&""==u||(""==c&&""!=u?c=u:""!=c&&""==u&&(u=c),a.attr("src",c),w.attr("src",u))}var r=e(' <div style="position:absolute; display:table; width:100%; height:100%;"><div style="display:table-cell; text-align:left; vertical-align:middle;"><img style="pointer-events:auto; cursor:pointer;"></div></div> ');r.appendTo(i);var a=r.find("img"),r=e(' <div style="position:absolute; display:table; width:100%; height:100%;"><div style="display:table-cell; text-align:right; vertical-align:middle;"><img style="pointer-events:auto; cursor:pointer;"></div></div> ');r.appendTo(i);var c,u,d,f,h,g,w=r.find("img");n(),void 0!=f&&(1==g&&""==f[0].time?l(0):(s(),setInterval(o,1e4)),a.click(function(e){e.originalEvent.stopPropagation(),window.open(f[h].ads[0].link_url,"_blank")}),w.click(function(e){e.originalEvent.stopPropagation(),window.open(f[h].ads[1].link_url,"_blank")}))}}(window.jQuery),function(e){SewisePlayerSkin.AdsContainer=function(i,t){var n=i.$container,s=i.$sewisePlayerUi,o=t.banners;if(o){var l=e("<div class='sewise_player_ads_banner'></div>");l.css({position:"absolute",width:"100%",height:"100%",left:"0px",top:"0px",overflow:"hidden","pointer-events":"none"}),l.appendTo(n),s.css("z-index",1),SewisePlayerSkin.BannersAds(l,o)}}}(window.jQuery),function(e){SewisePlayerSkin.ElementObject=function(){this.$sewisePlayerUi=e(".sewise-player-ui"),this.$container=this.$sewisePlayerUi.parent(),this.$video=this.$container.find("video").get(0),this.$controlbar=this.$sewisePlayerUi.find(".controlbar"),this.$playBtn=this.$sewisePlayerUi.find(".controlbar-btns-play"),this.$pauseBtn=this.$sewisePlayerUi.find(".controlbar-btns-pause"),this.$liveBtn=this.$sewisePlayerUi.find(".controlbar-btns-live"),this.$fullscreenBtn=this.$sewisePlayerUi.find(".controlbar-btns-fullscreen"),this.$normalscreenBtn=this.$sewisePlayerUi.find(".controlbar-btns-normalscreen"),this.$soundopenBtn=this.$sewisePlayerUi.find(".controlbar-btns-soundopen"),this.$soundcloseBtn=this.$sewisePlayerUi.find(".controlbar-btns-soundclose"),this.$shareBtn=this.$sewisePlayerUi.find(".controlbar-btns-share"),this.$playtime=this.$sewisePlayerUi.find(".controlbar-playtime"),this.$controlBarProgress=this.$sewisePlayerUi.find(".controlbar-progress"),this.$progressPlayedLine=this.$sewisePlayerUi.find(".controlbar-progress-playedline"),this.$progressPlayedPoint=this.$sewisePlayerUi.find(".controlbar-progress-playpoint"),this.$progressLoadedLine=this.$sewisePlayerUi.find(".controlbar-progress-loadedline"),this.$progressSeekLine=this.$sewisePlayerUi.find(".controlbar-progress-seekline"),this.$logo=this.$sewisePlayerUi.find(".logo"),this.$logoIcon=this.$sewisePlayerUi.find(".logo-icon"),this.$topbar=this.$sewisePlayerUi.find(".topbar"),this.$programTip=this.$sewisePlayerUi.find(".topbar-program-tip"),this.$programTitle=this.$sewisePlayerUi.find(".topbar-program-title"),this.$topbarClock=this.$sewisePlayerUi.find(".topbar-clock"),this.$buffer=this.$sewisePlayerUi.find(".buffer"),this.$bigPlayBtn=this.$sewisePlayerUi.find(".big-play-btn"),this.defStageWidth=this.$container.width(),this.defStageHeight=this.$container.height(),this.defLeftValue=parseInt(this.$container.css("left")),this.defTopValue=parseInt(this.$container.css("top")),this.defOffsetX=this.$container.get(0).getBoundingClientRect().left,this.defOffsetY=this.$container.get(0).getBoundingClientRect().top,this.defOverflow=e("body").css("overflow")}}(window.jQuery),function(e){SewisePlayerSkin.ElementLayout=function(i){function t(){null!=document.fullscreenElement||null!=document.msFullscreenElement||null!=document.mozFullScreenElement||null!=document.webkitFullscreenElement?l.fullScreen():l.normalScreen()}var n=i.$container,s=i.$controlBarProgress,o=i.$playtime,l=this,r=i.defStageWidth,a=i.defStageHeight,c=i.defLeftValue,u=i.defTopValue,d=i.defOffsetX,f=i.defOffsetY,h=i.defOverflow,g=parseInt(r)-288;0>g&&(g+=o.width(),o.hide()),s.css("width",g),document.addEventListener("fullscreenchange",t),document.addEventListener("MSFullscreenChange",t),document.addEventListener("mozfullscreenchange",t),document.addEventListener("webkitfullscreenchange",t),this.fullScreen=function(i){if("not-support"==i){var i=e(window).width(),t=e(window).height()-8;n.css("width",i),n.css("height",t),i=u-f+"px",n.css("left",c-d+"px"),n.css("top",i),e("body").css("overflow","hidden")}else n.css("width","100%"),n.css("height","100%");i=parseInt(n.width())-288,0>i?(i+=o.width(),o.hide()):o.show(),s.css("width",i)},this.normalScreen=function(){n.css("width",r),n.css("height",a),n.css("left",c),n.css("top",u),e("body").css("overflow",h),g=parseInt(r)-288,0>g?(g+=o.width(),o.hide()):o.show(),s.css("width",g)}}}(window.jQuery),function(){SewisePlayerSkin.LogoBox=function(e){var i=e.$logoIcon;this.setLogo=function(e){i.css("background","url("+e+") 0px 0px no-repeat"),i.attr("href","http://www.sewise.com/")}}}(window.jQuery),function(){SewisePlayerSkin.TopBar=function(e){var i=e.$topbar,t=e.$programTip,n=e.$programTitle,s=e.$topbarClock;this.setClock=function(e){e=SewisePlayerSkin.Utils.stringer.dateToTimeString(e),s.text(e)},this.setTitle=function(e){n.text(e)},this.show=function(){i.css("visibility","visible")},this.hide=function(){i.css("visibility","hidden")},this.hide2=function(){i.hide()},this.initLanguage=function(){var e=SewisePlayerSkin.Utils.language.getString("titleTip");t.text(e)}}}(window.jQuery),function(i){SewisePlayerSkin.ControlBar=function(t,n,s){function o(e){e=x+(e.pageX-E),e>0&&O>e&&(k.css("width",e),P.css("left",e-I/2))}function l(e){d.unbind("mousemove",o),i(document).unbind("mouseup",l),M=e.pageX,E!=M&&(x=k.width(),_=x/O,c(_)),C=!1}function r(i){e=i.originalEvent,1==e.targetTouches.length&&(e.preventDefault(),i=x+(e.targetTouches[0].pageX-E),i>0&&O>i&&(k.css("width",i),P.css("left",i-I/2)))}function a(i){e=i.originalEvent,P.unbind("touchmove",r),P.unbind("touchend",a),1==e.changedTouches.length&&(M=e.changedTouches[0].pageX,E!=M&&(x=k.width(),_=x/O,c(_))),C=!1}function c(e){e=new Date(36e5*Math.floor(u.playTime().getTime()/1e3/3600)+1e3*e*U),e=SewisePlayerSkin.Utils.stringer.dateToTimeStr14(e),u.seek(e)}var u,d=t.$container,f=t.$controlbar,h=t.$playBtn,g=t.$pauseBtn,w=t.$liveBtn,S=t.$fullscreenBtn,p=t.$normalscreenBtn,y=t.$soundopenBtn,m=t.$soundcloseBtn,v=t.$playtime,k=t.$progressPlayedLine,P=t.$progressPlayedPoint,b=t.$progressLoadedLine,$=t.$progressSeekLine,T=t.$buffer,F=t.$bigPlayBtn,L=this,U=.1,B="00:00:00",D="00:00:00",I=0,C=!1,E=0,M=0,x=0,O=0,_=0,z=!0,I=P.width(),O=$.width();g.hide(),p.hide(),m.hide(),T.hide(),f.click(function(e){e.originalEvent.stopPropagation()}),d.click(function(){z?(f.css("visibility","hidden"),s.hide(),z=!1):(f.css("visibility","visible"),s.show(),z=!0)}),h.click(function(){u.play()}),g.click(function(){u.pause()}),w.click(function(){u.live()}),F.click(function(e){e.originalEvent.stopPropagation(),u.play()}),S.click(function(){L.fullScreen()}),p.click(function(){L.noramlScreen()}),y.click(function(){u.muted(!0),y.hide(),m.show()}),m.click(function(){u.muted(!1),y.show(),m.hide()}),$.mousedown(function(e){x=e.pageX-e.target.getBoundingClientRect().left,O=$.width(),k.css("width",x),P.css("left",x-I/2),_=x/O,c(_)}),P.mousedown(function(e){this.blur(),C=!0,E=e.pageX,x=k.width(),O=$.width(),d.bind("mousemove",o),i(document).bind("mouseup",l)}),P.bind("touchstart",function(i){e=i.originalEvent,P.blur(),i=e.targetTouches[0],C=!0,E=i.pageX,x=k.width(),O=$.width(),P.bind("touchmove",r),P.bind("touchend",a)}),this.setPlayer=function(e){u=e},this.started=function(){h.hide(),g.show(),F.hide()},this.paused=function(){h.show(),g.hide(),F.show()},this.stopped=function(){h.show(),g.hide(),F.show()},this.setDuration=function(e){U=e},this.timeUpdate=function(){if(u.playTime()&&(D=SewisePlayerSkin.Utils.stringer.dateToStrHMS(new Date(1e3*Math.ceil(u.playTime().getTime()/1e3/U)*U)),B=SewisePlayerSkin.Utils.stringer.dateToStrHMS(new Date(1e3*Math.floor(u.playTime().getTime()/1e3/U)*U)),v.text(B+"/"+D),!C)){x=100*(Math.floor(u.playTime().getTime()/1e3)%U/U)+"%",k.css("width",x);var e=k.width()-I/2;P.css("left",e),e=Math.ceil(u.playTime().getTime()/1e3/3600),e=100*(Math.floor(u.liveTime().getTime()/1e3/3600)>=e?1:Math.floor(u.liveTime().getTime()/1e3)%U/U)+"%",b.css("width",e)}},this.hide2=function(){f.hide()},this.fullScreen=function(){S.hide(),p.show();var e=d.get(0);e.requestFullscreen?e.requestFullscreen():e.msRequestFullscreen?e.msRequestFullscreen():e.mozRequestFullScreen?e.mozRequestFullScreen():e.webkitRequestFullscreen?e.webkitRequestFullscreen():n.fullScreen("not-support")},this.noramlScreen=function(){S.show(),p.hide(),document.exitFullscreen?document.exitFullscreen():document.msExitFullscreen?document.msExitFullscreen():document.mozCancelFullScreen?document.mozCancelFullScreen():document.webkitCancelFullScreen?document.webkitCancelFullScreen():n.normalScreen()}}}(window.jQuery),function(e,i){i(document).ready(function(){var e=SewisePlayer.ILivePlayer,i=new SewisePlayerSkin.ElementObject,t=new SewisePlayerSkin.ElementLayout(i),n=new SewisePlayerSkin.LogoBox(i),s=new SewisePlayerSkin.TopBar(i),o=new SewisePlayerSkin.ControlBar(i,t,s);SewisePlayerSkin.ILiveSkin.player=function(i){e=i,o.setPlayer(e)},SewisePlayerSkin.ILiveSkin.started=function(){o.started()},SewisePlayerSkin.ILiveSkin.paused=function(){o.paused()},SewisePlayerSkin.ILiveSkin.stopped=function(){o.stopped()},SewisePlayerSkin.ILiveSkin.duration=function(e){o.setDuration(e)},SewisePlayerSkin.ILiveSkin.timeUpdate=function(){o.timeUpdate(),s.setClock(e.playTime())},SewisePlayerSkin.ILiveSkin.programTitle=function(e){s.setTitle(e)},SewisePlayerSkin.ILiveSkin.logo=function(e){n.setLogo(e)},SewisePlayerSkin.ILiveSkin.volume=function(){},SewisePlayerSkin.ILiveSkin.clarityButton=function(){},SewisePlayerSkin.ILiveSkin.timeDisplay=function(){},SewisePlayerSkin.ILiveSkin.controlBarDisplay=function(e){"enable"!=e&&o.hide2()},SewisePlayerSkin.ILiveSkin.topBarDisplay=function(e){"enable"!=e&&s.hide2()},SewisePlayerSkin.ILiveSkin.customStrings=function(){},SewisePlayerSkin.ILiveSkin.fullScreen=function(){o.fullScreen()},SewisePlayerSkin.ILiveSkin.noramlScreen=function(){o.noramlScreen()},SewisePlayerSkin.ILiveSkin.initialAds=function(e){e&&SewisePlayerSkin.AdsContainer(i,e)},SewisePlayerSkin.ILiveSkin.lang=function(e){SewisePlayerSkin.Utils.language.init(e),s.initLanguage()};try{SewisePlayer.CommandDispatcher.dispatchEvent({type:SewisePlayer.Events.PLAYER_SKIN_LOADED,playerSkin:SewisePlayerSkin.ILiveSkin})}catch(l){console.log("No Main Player")}})}(window,window.jQuery);
//# sourceMappingURL=skin.js.map