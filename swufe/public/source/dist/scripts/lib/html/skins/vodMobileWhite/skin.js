!function(e){e.SewisePlayerSkin={version:"1.0.0"},e.SewisePlayer=e.SewisePlayer||{}}(window),function(e){e.SewisePlayer.IVodPlayer=e.SewisePlayer.IVodPlayer||{play:function(){},pause:function(){},stop:function(){},seek:function(){},changeClarity:function(){},setVolume:function(){},toPlay:function(){},duration:function(){},playTime:function(){},type:function(){},showTextTip:function(){},hideTextTip:function(){},muted:function(){}}}(window),function(e){e.SewisePlayerSkin.IVodSkin=e.SewisePlayerSkin.IVodSkin||{player:function(){},started:function(){},paused:function(){},stopped:function(){},seeking:function(){},buffering:function(){},bufferProgress:function(){},loadedProgress:function(){},programTitle:function(){},duration:function(){},playTime:function(){},startTime:function(){},loadSpeed:function(){},initialClarity:function(){},lang:function(){},logo:function(){},timeUpdate:function(){},volume:function(){},clarityButton:function(){},timeDisplay:function(){},controlBarDisplay:function(){},topBarDisplay:function(){},customStrings:function(){},fullScreen:function(){},noramlScreen:function(){},initialAds:function(){}}}(window),function(){SewisePlayerSkin.Utils={stringer:{time2FigFill:function(e){var i,e=Math.floor(e);return i=10>e?"0"+e:""+e},secondsToHMS:function(e){if(!(0>e)){var i=this.time2FigFill(Math.floor(e/3600)),t=this.time2FigFill(e/60%60),e=this.time2FigFill(e%60);return i+":"+t+":"+e}},secondsToMS:function(e){if(!(0>e)){var i=this.time2FigFill(e/60),e=this.time2FigFill(e%60);return i+":"+e}},dateToTimeString:function(e){var i;i=e?e:new Date;var e=i.getFullYear(),t=i.getMonth()+1,n=i.getDate(),o=this.time2FigFill(i.getHours()),s=this.time2FigFill(i.getMinutes());return i=this.time2FigFill(i.getSeconds()),e+"-"+t+"-"+n+" "+o+":"+s+":"+i},dateToTimeStr14:function(e){var i=e.getFullYear(),t=this.time2FigFill(e.getMonth()+1),n=this.time2FigFill(e.getDate()),o=this.time2FigFill(e.getHours()),s=this.time2FigFill(e.getMinutes()),e=this.time2FigFill(e.getSeconds());return i+t+n+o+s+e},dateToStrHMS:function(e){var i=this.time2FigFill(e.getHours()),t=this.time2FigFill(e.getMinutes()),e=this.time2FigFill(e.getSeconds());return i+":"+t+":"+e},dateToYMD:function(e){var i=e.getFullYear(),t=this.time2FigFill(e.getMonth()+1),e=this.time2FigFill(e.getUTCDate());return i+"-"+t+"-"+e},hmsToDate:function(e){var e=e.split(":"),i=new Date;return i.setHours(e[0]?e[0]:0,e[1]?e[1]:0,e[2]?e[2]:0),i}},language:{zh_cn:{stop:"停止播放",pause:"暂停",play:"播放",fullScreen:"全屏",normalScreen:"恢复",soundOn:"打开声音",soundOff:"关闭声音",clarity:"清晰度",titleTip:"正在播放：",claritySetting:"清晰度设置",clarityOk:"确定",clarityCancel:"取消",backToLive:"回到直播",loading:"缓冲节目",subtitles:"字幕","default":"默认"},en_us:{stop:"Stop",pause:"Pause",play:"Play",fullScreen:"Full Screen",normalScreen:"Normal Screen",soundOn:"Sound On",soundOff:"Sound Off",clarity:"Clarity",titleTip:"Playing: ",claritySetting:"Definition Setting",clarityOk:"OK",clarityCancel:"Cancel",backToLive:"Back To Live",loading:"Loading",subtitles:"Subtitles","default":"Default"},lang:{},init:function(e){switch(e){case"en_US":this.lang=this.en_us;break;case"zh_CN":this.lang=this.zh_cn;break;default:this.lang=this.zh_cn}},getString:function(e){return this.lang[e]}}}}(),function(e){SewisePlayerSkin.BannersAds=function(i,t){function n(){d=SewisePlayerSkin.Utils.stringer.dateToYMD(new Date),f=t[d]||t["0000-00-00"],h=0,void 0!=f&&(S=f.length)}function o(){for(var e=(new Date).getTime(),i=0;S>i;i++){var t=SewisePlayerSkin.Utils.stringer.hmsToDate(f[i].time).getTime();if(S-1>i){var n=SewisePlayerSkin.Utils.stringer.hmsToDate(f[i+1].time).getTime();if(e>t&&n>e){h=i,r(h);break}}else if(e>t){h=i,r(h);break}}}function s(){var e=(new Date).getTime();if(S-1>h){var i=SewisePlayerSkin.Utils.stringer.hmsToDate(f[h+1].time).getTime();e>i&&(h++,r(h))}else SewisePlayerSkin.Utils.stringer.dateToYMD(new Date)!=d&&(n(),o())}function r(e){c=f[e].ads[0].picurl,u=f[e].ads[1].picurl,""==c&&""==u||(""==c&&""!=u?c=u:""!=c&&""==u&&(u=c),a.attr("src",c),w.attr("src",u))}var l=e(' <div style="position:absolute; display:table; width:100%; height:100%;"><div style="display:table-cell; text-align:left; vertical-align:middle;"><img style="pointer-events:auto; cursor:pointer;"></div></div> ');l.appendTo(i);var a=l.find("img"),l=e(' <div style="position:absolute; display:table; width:100%; height:100%;"><div style="display:table-cell; text-align:right; vertical-align:middle;"><img style="pointer-events:auto; cursor:pointer;"></div></div> ');l.appendTo(i);var c,u,d,f,h,S,w=l.find("img");n(),void 0!=f&&(1==S&&""==f[0].time?r(0):(o(),setInterval(s,1e4)),a.click(function(e){e.originalEvent.stopPropagation(),window.open(f[h].ads[0].link_url,"_blank")}),w.click(function(e){e.originalEvent.stopPropagation(),window.open(f[h].ads[1].link_url,"_blank")}))}}(window.jQuery),function(e){SewisePlayerSkin.AdsContainer=function(i,t){var n=i.$container,o=i.$sewisePlayerUi,s=t.banners;if(s){var r=e("<div class='sewise_player_ads_banner'></div>");r.css({position:"absolute",width:"100%",height:"100%",left:"0px",top:"0px",overflow:"hidden","pointer-events":"none"}),r.appendTo(n),o.css("z-index",1),SewisePlayerSkin.BannersAds(r,s)}}}(window.jQuery),function(e){SewisePlayerSkin.ElementObject=function(){this.$sewisePlayerUi=e(".sewise-player-ui"),this.$container=this.$sewisePlayerUi.parent(),this.$controlbar=this.$sewisePlayerUi.find(".controlbar"),this.$playBtn=this.$sewisePlayerUi.find(".controlbar-btns-play"),this.$pauseBtn=this.$sewisePlayerUi.find(".controlbar-btns-pause"),this.$stopBtn=this.$sewisePlayerUi.find(".controlbar-btns-stop"),this.$fullscreenBtn=this.$sewisePlayerUi.find(".controlbar-btns-fullscreen"),this.$normalscreenBtn=this.$sewisePlayerUi.find(".controlbar-btns-normalscreen"),this.$soundopenBtn=this.$sewisePlayerUi.find(".controlbar-btns-soundopen"),this.$soundcloseBtn=this.$sewisePlayerUi.find(".controlbar-btns-soundclose"),this.$shareBtn=this.$sewisePlayerUi.find(".controlbar-btns-share"),this.$playtime=this.$sewisePlayerUi.find(".controlbar-playtime"),this.$controlBarProgress=this.$sewisePlayerUi.find(".controlbar-progress"),this.$progressPlayedLine=this.$sewisePlayerUi.find(".controlbar-progress-playedline"),this.$progressPlayedPoint=this.$sewisePlayerUi.find(".controlbar-progress-playpoint"),this.$progressLoadedLine=this.$sewisePlayerUi.find(".controlbar-progress-loadedline"),this.$progressSeekLine=this.$sewisePlayerUi.find(".controlbar-progress-seekline"),this.$logo=this.$sewisePlayerUi.find(".logo"),this.$logoIcon=this.$sewisePlayerUi.find(".logo-icon"),this.$topbar=this.$sewisePlayerUi.find(".topbar"),this.$programTip=this.$sewisePlayerUi.find(".topbar-program-tip"),this.$programTitle=this.$sewisePlayerUi.find(".topbar-program-title"),this.$topbarClock=this.$sewisePlayerUi.find(".topbar-clock"),this.$buffer=this.$sewisePlayerUi.find(".buffer"),this.$bigPlayBtn=this.$sewisePlayerUi.find(".big-play-btn"),SewisePlayerSkin.defStageWidth||(SewisePlayerSkin.defStageWidth=this.defStageWidth=this.$container.width(),SewisePlayerSkin.defStageHeight=this.defStageHeight=this.$container.height())}}(window.jQuery),function(){SewisePlayerSkin.ElementLayout=function(e){var i=e.$container,t=e.$controlBarProgress,n=e.$playtime,o=this,s=e.defStageWidth,r=e.defStageHeight,l=parseInt(s)-220;this.screenRotate=!1,0>l&&(l+=n.width(),n.hide()),t.css("width",l),this.fullScreen=function(){if(window.toFullScreen&&"function"==typeof window.toFullScreen){window.toFullScreen(),i.get(0).style.transform="rotateZ(90deg)",i.get(0).style.MsTransform="rotateZ(90deg)",i.get(0).style.MozTransform="rotateZ(90deg)",i.get(0).style.WebkitTransform="rotateZ(90deg)",i.get(0).style.OTransform="rotateZ(90deg)";var e=document.getElementsByTagName("html")[0].clientWidth,s=document.getElementsByTagName("html")[0].clientHeight,r=(e-s)/2;i.css({width:s,height:e,left:r,bottom:r}),o.screenRotate=!0}else i.css("width",window.screen.width),i.css("height",window.screen.height);e=parseInt(i.width())-220,0>e?(e+=n.width(),n.hide()):n.show(),t.css("width",e)},this.normalScreen=function(){window.toNormalScreen&&"function"==typeof window.toNormalScreen&&(window.toNormalScreen(),i.get(0).style.transform="rotateZ(0deg)",i.get(0).style.MsTransform="rotateZ(0deg)",i.get(0).style.MozTransform="rotateZ(0deg)",i.get(0).style.WebkitTransform="rotateZ(0deg)",i.get(0).style.OTransform="rotateZ(0deg)",o.screenRotate=!1),i.css({width:s,height:r,left:0,bottom:0}),l=parseInt(s)-220,0>l?(l+=n.width(),n.hide()):n.show(),t.css("width",l)}}}(window.jQuery),function(){SewisePlayerSkin.LogoBox=function(e){var i=e.$logoIcon;this.setLogo=function(e){i.css("background","url("+e+") 0px 0px no-repeat"),i.attr("href","http://www.sewise.com/")}}}(window.jQuery),function(){SewisePlayerSkin.TopBar=function(e){var i=e.$topbar,t=e.$programTip,n=e.$programTitle,o=e.$topbarClock;setInterval(function(){var e=SewisePlayerSkin.Utils.stringer.dateToTimeString();o.text(e)},1e3),this.setTitle=function(e){n.text(e)},this.show=function(){i.css("visibility","visible")},this.hide=function(){i.css("visibility","hidden")},this.hide2=function(){i.hide()},this.initLanguage=function(){var e=SewisePlayerSkin.Utils.language.getString("titleTip");t.text(e)}}}(window.jQuery),function(i){SewisePlayerSkin.ControlBar=function(t,n){function o(e){e=M+(e[j]-L),e>0&&_>e&&(P.css("width",e),b.css("left",e-E/2))}function s(e){u.unbind("mousemove",o),i(document).unbind("mouseup",s),C=e[j],L!=C&&(M=P.width(),O=M/_,c.seek(O*B)),x=!1}function r(i){e=i.originalEvent,1==e.targetTouches.length&&(e.preventDefault(),i=M+(e.targetTouches[0][j]-L),i>0&&_>i&&(P.css("width",i),b.css("left",i-E/2)))}function l(i){e=i.originalEvent,b.unbind("touchmove",r),b.unbind("touchend",l),1==e.changedTouches.length&&(C=e.changedTouches[0][j],L!=C&&(M=P.width(),O=M/_,c.seek(O*B))),x=!1}function a(){document.exitFullscreen?document.exitFullscreen():document.msExitFullscreen?document.msExitFullscreen():document.mozCancelFullScreen?document.mozCancelFullScreen():document.webkitCancelFullScreen&&document.webkitCancelFullScreen()}var c,u=t.$container,d=t.$controlbar,f=t.$playBtn,h=t.$pauseBtn,S=t.$stopBtn,w=t.$fullscreenBtn,g=t.$normalscreenBtn,y=t.$soundopenBtn,p=t.$soundcloseBtn,m=t.$shareBtn,k=t.$playtime,P=t.$progressPlayedLine,b=t.$progressPlayedPoint,$=t.$progressLoadedLine,v=t.$progressSeekLine,T=t.$buffer,F=t.$bigPlayBtn,U=this,B=1,I=0,D="000:00",V="000:00",E=0,x=!1,L=0,C=0,M=0,_=0,O=0,R=!1,j="pageX",E=b.width(),_=v.width();y.hide(),p.hide(),S.hide(),h.hide(),g.hide(),p.hide(),T.hide(),f.click(function(){c.play()}),h.click(function(){c.pause()}),S.click(function(){c.stop()}),F.click(function(e){e.originalEvent.stopPropagation(),c.play()}),w.click(function(){U.fullScreen()}),g.click(function(){U.noramlScreen()}),this.fullScreen=function(){if(SewisePlayerSkin.mobileExtEvent.block)return!1;var e=u.get(0);e.requestFullscreen?e.requestFullscreen():e.msRequestFullscreen?e.msRequestFullscreen():e.mozRequestFullScreen?e.mozRequestFullScreen():e.webkitRequestFullscreen&&e.webkitRequestFullscreen(),n.fullScreen(),R=n.screenRotate,j="pageY",w.hide(),g.show()},this.noramlScreen=function(){return SewisePlayerSkin.mobileExtEvent.block?!1:(a(),n.normalScreen(),R=n.screenRotate,j="pageX",w.show(),void g.hide())},SewisePlayerSkin.mobileExtEvent={block:!1,fullScreen:w,normalScreen:g,intoFullScreen:U.fullScreen,exitFullScreen:U.noramlScreen},SewisePlayerSkin.exitFullscreen=function(){a(),n.normalScreen(),R=n.screenRotate,j="pageX",w.show(),g.hide()},d.click(function(e){e.originalEvent.stopPropagation()}),y.click(function(){c.muted(!0),y.hide(),p.show()}),p.click(function(){c.muted(!1),y.show(),p.hide()}),m.click(function(){window.shareVideo&&"function"==typeof window.shareVideo?window.shareVideo():console.log("Not found the shareVideo function of window")}),v.mousedown(function(e){M=R?e[j]-e.target.getBoundingClientRect().top:e[j]-e.target.getBoundingClientRect().left,_=v.width(),P.css("width",M),b.css("left",M-E/2),O=M/_,c.seek(O*B)}),b.mousedown(function(e){this.blur(),x=!0,L=e[j],M=P.width(),_=v.width(),u.bind("mousemove",o),i(document).bind("mouseup",s)}),b.bind("touchstart",function(i){e=i.originalEvent,b.blur(),i=e.targetTouches[0],x=!0,L=i[j],M=P.width(),_=v.width(),b.bind("touchmove",r),b.bind("touchend",l)}),this.setPlayer=function(e){c=e},this.started=function(){f.hide(),h.show(),F.hide()},this.paused=function(){f.show(),h.hide(),F.show()},this.stopped=function(){f.show(),h.hide(),F.show()},this.setDuration=function(e){B=e,e>=1&&(V=SewisePlayerSkin.Utils.stringer.secondsToMS(B)),SewisePlayerSkin.duration=B},SewisePlayerSkin.duration&&this.setDuration(SewisePlayerSkin.duration),this.timeUpdate=function(e){I=e,D=SewisePlayerSkin.Utils.stringer.secondsToMS(I),k.text(D+"/"+V),x||(M=100*(I/B)+"%",P.css("width",M),e=P.width()-E/2,b.css("left",e))},this.loadProgress=function(e){$.css("width",100*e+"%")},this.hide2=function(){d.hide()}}}(window.jQuery),function(e,i){i(document).ready(function(){var e,i,t,n,o,s;SewisePlayerSkin.init=function(){s=o=n=t=i=e=null,e=SewisePlayer.IVodPlayer,i=new SewisePlayerSkin.ElementObject,t=new SewisePlayerSkin.ElementLayout(i),n=new SewisePlayerSkin.LogoBox(i),o=new SewisePlayerSkin.TopBar(i),s=new SewisePlayerSkin.ControlBar(i,t,o)},SewisePlayerSkin.reinit=function(){t=i=null,i=new SewisePlayerSkin.ElementObject,t=new SewisePlayerSkin.ElementLayout(i)},SewisePlayerSkin.init(),SewisePlayerSkin.IVodSkin.player=function(i){e=i,s.setPlayer(e)},SewisePlayerSkin.IVodSkin.started=function(){s.started()},SewisePlayerSkin.IVodSkin.paused=function(){s.paused()},SewisePlayerSkin.IVodSkin.stopped=function(){s.stopped()},SewisePlayerSkin.IVodSkin.duration=function(e){s.setDuration(e)},SewisePlayerSkin.IVodSkin.timeUpdate=function(e){s.timeUpdate(e)},SewisePlayerSkin.IVodSkin.loadedProgress=function(e){s.loadProgress(e)},SewisePlayerSkin.IVodSkin.programTitle=function(e){o.setTitle(e)},SewisePlayerSkin.IVodSkin.logo=function(e){n.setLogo(e)},SewisePlayerSkin.IVodSkin.volume=function(){},SewisePlayerSkin.IVodSkin.initialClarity=function(){},SewisePlayerSkin.IVodSkin.clarityButton=function(){},SewisePlayerSkin.IVodSkin.timeDisplay=function(){},SewisePlayerSkin.IVodSkin.controlBarDisplay=function(e){"enable"!=e&&s.hide2()},SewisePlayerSkin.IVodSkin.topBarDisplay=function(e){"enable"!=e&&o.hide2()},SewisePlayerSkin.IVodSkin.customStrings=function(){},SewisePlayerSkin.IVodSkin.fullScreen=function(){s.fullScreen()},SewisePlayerSkin.IVodSkin.noramlScreen=function(){s.noramlScreen()},SewisePlayerSkin.IVodSkin.initialAds=function(e){e&&SewisePlayerSkin.AdsContainer(i,e)},SewisePlayerSkin.IVodSkin.lang=function(e){SewisePlayerSkin.Utils.language.init(e),o.initLanguage()};try{SewisePlayer.CommandDispatcher.dispatchEvent({type:SewisePlayer.Events.PLAYER_SKIN_LOADED,playerSkin:SewisePlayerSkin.IVodSkin})}catch(r){console.log("No Main Player")}})}(window,window.jQuery);
//# sourceMappingURL=skin.js.map