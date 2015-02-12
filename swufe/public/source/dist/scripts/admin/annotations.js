define(["subtitleselect"],function(t){jQuery(function(n){function e(e){var a,i=e.annotationSchema,r=i.indexOf("id"),s=i.indexOf("st"),p=i.indexOf("et"),h=i.indexOf("content"),m=e.annotations,x=[],y=[],S=0;if(m.length){for(var O=0,j=m.length;j>O;O++){var C=m[O];x.push({id:C[r],start:C[s],end:C[p],content:C[h]}),y.push(v.replace(/\{id\}/g,C[r]).replace(/\{start\}/g,C[s]).replace(/\{end\}/g,C[p]).replace(/\{content\}/g,d(C[h])).replace(/\{content\|encoded\}/g,encodeURIComponent(C[h])))}u.html(y.join(""))}for(var b=e.subtitles,A=e.subtitleSchema,M=A.indexOf("token"),w=A.indexOf("st"),k=A.indexOf("et"),F=(A.indexOf("score"),0),T='<li class="line"></li>',_='<span class="word"></span>',I=[],P=0,E=b.length;E>P;P++){var L=b[P].length,Q=n(T);Q.attr({id:"line_"+P}).data({index:P});for(var R=null,U={index:P,st:b[P][0][w],et:b[P][L-1][k]},$=0;L>$;$++){var B=n(_),D=b[P][$],J=D[M],N=!1,q={index:F,st:D[w],et:D[k]};0==$&&Q.attr(U).data(U),null==R||!/^(\w|\d)/.test(J)||/^(ve|s|ll)$/i.test(J)&&/^(\'|’)$/.test(R)||(Q.append('<pre class="space'+(a?' word-highlight" data-highlightIndex="'+S+'"':'"')+"> </pre>"),N=!0),R=J,N&&(J=" "+J),B.attr(n.extend({id:"word_"+F},q)).data(q),B.text(J),B.appendTo(Q),I.push(B),F++,x[S]&&q.st===x[S].start&&(a=!0),a&&(B.addClass("word-highlight"),B.data("annotationindex",S)),a&&q.et===x[S].end&&(a=!1,S++)}Q.appendTo(c)}l=t.subtitleSelect({$subtitleList:c,onSelect:function(t){f.data("start",t.start).text(o(t.start)),g.data("end",t.end).text(o(t.end))}})}function a(){c.on("click",".addAnnotation",function(){y.get(0).reset(),S.code("")}),u.on("click",".removeAnnotation",function(){var t=n(this).parents("li"),e=parseInt(t.data("id")),a=t.data("st"),o=t.data("et");return n.ajax({url:"/admin/course/annotationDestroy",type:"POST",dataType:"json",data:{annotation_id:e}}).done(function(e){if(0===e.msgCode){t.remove();var i;c.find(".word-highlight").each(function(){var t=n(this);return t.data("st")===a&&(i=!0),i&&(t.removeClass("word-highlight"),t.data("et")===o)?!1:void 0}),r("注释删除成功")}else s("注释删除失败")}),!1});var t,e=n("#modifyAnnotationModal"),a=n("#modifyAnnotationForm"),i=n("#annotationStartModify"),m=n("#annotationEndModify"),x=n("#annotationContentModify");u.on("click",".modifyAnnotation",function(){t=n(this).parents("li");var e=t.data();a.data("id",e.id).get(0).reset(),i.data("start",e.st).text(o(e.st)),m.data("end",e.et).text(o(e.et)),x.code(decodeURIComponent(e.contentEncoded))});var y=n("#addAnnotationForm"),S=n("#annotationContent"),O=n("#addAnnotationButton");O.click(function(){var t=n.trim(S.code());if(!t)return s("请填写注释内容"),!1;var e={video_guid:h.guid,st:parseFloat(f.data("start")),et:parseFloat(g.data("end")),content:t};return n.ajax({url:"/admin/course/annotationCreate",type:"POST",data:e}).done(function(n){0===n.msgCode?(p.modal("hide"),u.append(v.replace(/\{id\}/g,n.data.id).replace(/\{content\}/g,d(t)).replace(/\{start\}/g,e.st).replace(/\{end\}/g,e.et)),l.turnToMark(),r("添加注释成功")):s("添加注释失败，请稍后再试")}),!1}),n("#modifyAnnotationButton").click(function(){var o=n.trim(x.code());if(!o)return s("请填写注释内容"),!1;var l={annotation_id:a.data("id"),video_guid:h.guid,st:parseFloat(i.data("start")),et:parseFloat(m.data("end")),content:o};return n.ajax({url:"/admin/course/annotationModify",type:"POST",data:l}).done(function(n){0===n.msgCode?(e.modal("hide"),t.data({contentEncoded:encodeURIComponent(o)}).find(".annotationPreview").text(d(o)),r("编辑注释成功")):s("编辑注释失败，请稍后再试")}),!1})}function o(t){var n=[];return n[0]=i(Math.floor(t/3600)),n[1]=i(Math.floor(t%3600/60)),n[2]=i(Math.floor(t%60)),n.join(":")}function i(t){var n="0"+(t||0);return n.slice(n.length-2)}function d(t){return n("<div>").append(t).text()}function r(t){alert(t)}function s(t){alert(t)}jQuery.extend({stringify:function(t){var n=typeof t;if("object"!=n||null===t)return"string"==n&&(t='"'+t+'"'),String(t);var e,a,o=[],i=t&&t.constructor==Array;for(e in t)a=t[e],n=typeof a,t.hasOwnProperty(e)&&("string"==n?a='"'+a+'"':"object"==n&&null!==a&&(a=jQuery.stringify(a)),o.push((i?"":'"'+e+'":')+String(a)));return(i?"[":"{")+String(o)+(i?"]":"}")}});var l,c=n("#subtitleList").empty(),u=n("#annotationList"),f=n("#annotationStart"),g=n("#annotationEnd"),p=n("#addAnnotationModal"),h=c.data(),v=n.trim(n("#tplAnnotationItem").remove().html());n.getJSON("/admin/getCourseSubtitle?video_guid="+h.guid+"&language="+h.language+"&type=json&version=2",function(t){""==t||void 0==t?console.log("Subtitle load error."):e(t)}).fail(function(){c.html("<li>Failed to load subtitles.</li>")}),a()})});
//# sourceMappingURL=annotations.js.map