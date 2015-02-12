<!-- 页脚 -->
<footer id="footer">
    <div class="content-inner">
        <nav>
            <a href="/">首页</a>
            @foreach($topCategories as $k => $category)
            <a href="/videos?top={{$category->id}}">{{$category->name}}</a>
            @endforeach
            <a href="/clips">剪集</a>
            <a href="/app">手机版</a>
        </nav>
        <div class="copyright">
            <p>Copyright © 西南财经大学版权所有, Powered by <a target="_blank" href="http://eastiming.com">深圳市东方泰明科技有限公司</a></p>
            <p>最低浏览屏幕分辨率1200x800px</p>
        </div>
    </div>
</footer>

<!-- ============= Outdated Browser ============= -->
<div id="outdated-wrap">
    <div id="outdated">
        <h6>哇哦，您的浏览器太旧啦!</h6>
        <p>为了更好的体验我们的网站，请升级您的浏览器吧:) <a id="btnUpdateBrowser" href="http://browsehappy.com/">升级我的浏览器 </a></p>
        <p class="last"></p>
    </div>
</div>
<script>
$(function () {
    var Sys = {};
    var ua = navigator.userAgent.toLowerCase();
    var s;
    var outdated = document.getElementById('outdated-wrap');
    (s = ua.match(/rv:([\d.]+)\) like gecko/)) ? Sys.ie = s[1] :
    (s = ua.match(/msie ([\d.]+)/)) ? Sys.ie = s[1] :
    (s = ua.match(/firefox\/([\d.]+)/)) ? Sys.firefox = s[1] :
    (s = ua.match(/chrome\/([\d.]+)/)) ? Sys.chrome = s[1] :
    (s = ua.match(/opera.([\d.]+)/)) ? Sys.opera = s[1] :
    (s = ua.match(/version\/([\d.]+).*safari/)) ? Sys.safari = s[1] : 0;

    if (Sys.ie < 8) {
        outdated.style.display = 'block';
    };
});
</script>
<!-- GA -->
<script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

    ga('create', 'UA-42870595-7', 'auto');
    ga('send', 'pageview');

</script>