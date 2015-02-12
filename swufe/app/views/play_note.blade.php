@extends('layout.master_play')

@section('title')
{{$objPlaylist->title}} - 西南财经大学－教材资料馆
@stop

@section('js_head')
<!-- <script src="/static/js/lib/jQuery/1.11/jquery-1.11.0.min.js"></script> -->
@stop

@section("content")

<header class="player-header">
    <!-- 返回首页 -->
    <div class="back back-home">
        <a href="/" title="返回首页">
            <i class="play-logo"></i>
            <span>返回首页</span>
        </a>
    </div>
    <!--/ 返回首页 -->
    <div class="title">
        <h2>{{ $objPlaylist->title}}</h2>
    </div>
    <!-- <div class="search"></div> -->
    @if(Auth::guest())

    <div class="user-signin">
        <a href="/login">登录</a>
    </div>

    @else
    <div class="user">
        <div class="avatar">
            <img src="{{ Auth::user()->getAvatar() }}" alt="My User Avatar">
        </div>
        <div class="name">
            <span>{{ Auth::user()->nickname ? Auth::user()->nickname : Auth::user()->email }}</span>
        </div>
        <div class="user-downarrow">
            <i class="icon-caret-down"></i>
        </div>
        <!-- user dropdown -->
        <div class="user-dropdown">
            <ul>
                <li><a href="/my/note" target="_blank">我的笔记</a></li>
                <li><a href="/favorite/videos" target="_blank">我的收藏</a></li>
                <li><a href="/logout" target="_blank">退出账户</a></li>
            </ul>
        </div>
        <!--/ user dropdown -->
    </div>
    @endif
    </div>
</header>
<!-- 当字幕区域收缩时，请在div.player-container添加"shrinked" class -->
<div class="player-container">
    <div class="video-wrap">

        <div class="video-wrap-inner">

            <div id="playerHolder" class="video-cont"
            @if(!empty(Auth::user()->user_id)) data-userid="{{Auth::user()->user_id}}" @else data-userid="-1" @endif
            data-playid="{{ $objPlaylist->playid }}"
            data-totel_length="{{ $objPlaylist->totel_district }}"
            data-title="{{ $objPlaylist->title }}">
            <!-- origin player here -->
        </div>

    </div>

</div>
<div class="function-wrap" id="note-list-wrap">
    <!-- tabs -->
    <div class="tab-wrap">

        <div class="shrink-btn">
            <a href=""></a>
        </div>
        <ul class="tab">
            <li class="current">
                <p>当前播放第 <span id="noteIndex">1</span>/{{$objPlaylistFragments->count()}} 个视频 <strong
                        id="currentTitle"></strong></p>
            </li>
        </ul>
    </div>
    <!--/ tabs -->

    <!-- 重点片段 -->
    <div class="note-wrap tab-content" id="note">
        <div class="video-note-list">
            <ul id="noteList">
                @foreach ($objPlaylistFragments as $objPf)
                <!-- 笔记 -->
                <li class="note-item" data-title="{{ $objPf->title }}" data-playid="{{ $objPf->playid }}"
                    data-length="{{ $objPf->length }}">
                    <div class="thumb-w">
                        <a href="javascript:;" class="playLink">
                            <div class="thumb">
                                <img src="{{ $objPf->cover }}" alt="">
                                <time>{{ gmstrftime('%H:%M:%S', round($objPf->length, 0)) }}</time>
                                <div class="thumb-hover">
                                    <i class="icon-play-c"></i>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="info">
                        <h3><a href="javascript:;" class="playLink">{{ $objPf->title }}</a></h3>

                        <p>{{ $objPf->description }}</p>

                        <div class="note-btns">
                            <a href="javascript:;" class="addNoteToFav" alt="加入收藏"><i class="icon-love"></i></a>
                            <a href="javascript:;" class="addNoteToNote" alt="添加到笔记"><i class="icon-note"></i></a>
                        </div>
                    </div>
                </li>
                @endforeach

            </ul>
        </div>
    </div>
</div>
</div>

<div class="video-info">
    <div class="video-avatar-cate">
        <div class="avatar">
            <img src="{{{$objPlaylist->user->avatar or '/static/hiho-edu/img/avatar_default.png'}}}" alt="">
        </div>
        <div class="teacher-info">
            <span>{{{$objPlaylist->user->nickname or '无'}}}</span>
            <span>{{ \Tool::dateFormat($objPlaylist->created_at) }}</span>
        </div>
        <ul class="video-meta-base">
            <li>笔记总长: {{ gmstrftime('%H:%M:%S',round($objPlaylist->totel_district,0)) }}</li>
            <li>包含短视频: {{ $objPlaylist->totel_number }}</li>
            <li>语言: 中文</li>
        </ul>
    </div>

    <div class="vidoe-like">
        <!-- 收藏过的,在 a.like 添加"liked" class -->
        <a href="javascript:;" id="addToFavPlaylists" class="like {{ $favorited ? 'liked': '' }}">
            <div class="like-icon-w">
                <i class="like-icon"></i>
                <span>收藏</span>
            </div>
            <span class="like-count">{{ $objPlaylist->liked }}</span>
        </a>
    </div>
    <!-- <div class="video-share"> -->
        <!-- <div class="share-to">
            <span><b>｜ </b>分享到</span>
        </div> -->
        <!-- JiaThis Button BEGIN -->
        <!-- <div class="jiathis_style_32x32">
            <a class="jiathis_button_qzone" title="分享到QQ空间"><span class="jiathis_txt jtico jtico_qzone"></span></a>
            <a class="jiathis_button_googleplus" title="Google+"><span
                    class="jiathis_txt jtico jtico_googleplus"></span></a>
            <a class="jiathis_button_tsina" title="Sina weibo"><span class="jiathis_txt jtico jtico_tsina"></span></a>
            <a class="jiathis_button_weixin" title="Weixin"><span class="jiathis_txt jtico jtico_weixin"></span></a>
        </div>
        <script type="text/javascript" src="http://v3.jiathis.com/code/jia.js" charset="utf-8"></script> -->
        <!-- JiaThis Button END -->
    <!-- </div> -->
</div>

<!-- 访问权限 -->
<div class="locked" style="display:none;">
    <div class="lock-img"></div>
    <h2>抱歉！您无权限访问本视频，您可以<a href="">登录</a>有权限的账户或者<a href="">返回首页</a></h2>
</div>
<!--/ 访问权限 -->

<!-- modal -->
<div class="modal-wrap hidden" id="addToNoteDialog">
    <div class="modal new-note">
        <div class="modal-title">
            <a href='javascript:;' id="cloaseAddToNoteDialog" class="close"><i class="icon-times"></i></a>

            <h2>新建笔记</h2>
        </div>
        <div class="modal-content">
            <div class="mc-row">
                <div class="mc-label">
                    <span>添加到</span>
                </div>
                <div class="mc-input mc-input-select">
                    <select id="playlist_id">
                    </select>
                    <a href="#" id="createNoteBookButton" class="new-note">新建笔记本</a>
                </div>
            </div>
            <div class="mc-row">
                <div class="mc-label">
                    <span>标题</span>
                </div>
                <div class="mc-input">
                    <input type="text" id="title">
                </div>
            </div>
            <div class="mc-row">
                <div class="mc-label">
                    <span>描述</span>
                </div>
                <div class="mc-input">
                    <textarea name="" id="description"></textarea>
                </div>
            </div>
            <div class="mc-row">
                <div class="mc-label">
                    <span>标签</span>
                </div>
                <div class="mc-input">
                    <input type="text">

                    <p>使用逗号隔开不同的标签</p>
                </div>
            </div>

        </div>
        <div class="modal-btns">
            <button id="addToNoteButton" class="button" type="button">确认</button>
            <button id="cancelAddToNoteButton" class="button cancel" type="button">取消</button>
        </div>
    </div>
</div>

<div class="modal-wrap hidden" id="createNewPlaylist">
    <form method="post" id="create_form">
        <div class="modal new-note">
            <div class="modal-title">
                <a href="#" id="closeCreateNewPlaylist" class="close"><i class="icon-times"></i></a>

                <h2>新建笔记本</h2>
            </div>
            <div class="modal-content">
                <div class="mc-row">
                    <div class="mc-label">
                        <span>标题</span>
                    </div>
                    <div class="mc-input">
                        <input type="text" id="playlist_title" name="title">
                    </div>
                </div>
                <div class="mc-row">
                    <div class="mc-label">
                        <span>描述</span>
                    </div>
                    <div class="mc-input">
                        <textarea name="description" id="description"></textarea>
                    </div>
                </div>


            </div>
            <div class="modal-btns">
                <button id="createNewPlaylistConfirm" class="button" type="button">确认</button>
                <button id="createNewPlaylistCancel" class="button cancel" type="button">取消</button>
            </div>
        </div>
    </form>
</div>

<div class="notification success hidden">
    <div class="inner">
        <div class="notify-icon"><i class="icon-ok-c"></i></div>
        <h2>您已收藏过</h2>

        <p>&nbsp;</p>
        <a href="javascript:void(0);" class="close"><i class="icon-times"></i></a>
    </div>
</div>

<div class="notification login hidden">
    <div class="inner">
        <div class="notify-icon"><i class="icon-ok-c"></i></div>
        <h2>必须登录</h2>

        <p>&nbsp;</p>
        <a href="javascript:void(0);" class="close"><i class="icon-times"></i></a>
    </div>
</div>
@stop

@section('js_foot')
<script src="{{\Config::get('app.pathToSource')}}/scripts/lib/require.js"
        data-main="{{\Config::get('app.pathToSource')}}/scripts/playnote.js"></script>
@stop