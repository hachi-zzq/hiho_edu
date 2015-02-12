<!-- 主导航 -->
<nav style="left: 72px;">
    <ul>
        <li><a href="/">首页</a></li>
        @foreach($topCategories as $k => $category)
        <li @if(\Input::get('top') == $category->id) class="current" @endif><a href="/videos?top={{$category->id}}">{{$category->name}}</a></li>
        @endforeach
        <li><a href="/clips">剪集</a></li>
        @if(Auth::user()) <li><a href="/my/note">笔记</a></li> @endif
        <li><a href="/app">手机版</a></li>
    </ul>
</nav>
@if(Auth::guest())
<!-- 注册和登录 -->
<div class="user-login">
    <a href="/login">登录</a>
    <a href="/signup/phone">注册</a>
</div>
@else
<!-- 用户登录后 -->
<div class="user-box">
    <div class="user-name">
        <span>{{Auth::user()->nickname ? Auth::user()->nickname : Auth::user()->email}}</span>
    </div>
    <div class="user-avatar">
        <img src="{{ Auth::user()->getAvatar() }}" alt="">
    </div>
    <div class="user-downarrow">
        <i class="icon-caret-down"></i>
    </div>

    <!-- 下拉菜单 -->
    <div class="user-dropdown">
        <ul>
            <li><a href="/my/note">我的笔记</a></li>
            <li><a href="/favorite/videos">我的收藏</a></li>
            <li><a href="/my/profile">个人设置</a></li>
            <li><a href="/logout">退出账户</a></li>
        </ul>
    </div>
</div>
@endif