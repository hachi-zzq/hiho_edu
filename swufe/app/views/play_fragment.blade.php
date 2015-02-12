@extends('layout.master_play')

@section('title')
{{ $objFragment->title }} - 西南财经大学－教材资料馆
@stop

@section('js_head')
<!-- <script src="/static/js/lib/jQuery/1.11/jquery-1.11.0.min.js"></script> -->
@stop

@section("content")

<header class="player-header">
    <div class="back back-home">
        <a href="/" title="返回首页">
            <i class="play-logo"></i>
            <span>返回首页</span>
        </a>
    </div>
    <div class="title">
        <h2>{{ $objFragment->title }} <a href="/play/{{ $objVideo->playid }}" class="btn-line">查看原视频</a> <a
                href="javascript:;" id="addToNote" class="btn-line">添加到笔记</a></h2>
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
</header>
<div class="player-container">
    <div class="video-wrap">

        <div class="video-wrap-inner">

            <div id="playerHolder" class="video-cont" data-playMode="{{ $playmode }}"
            @if(!empty(Auth::user()->user_id)) data-userid="{{Auth::user()->user_id}}" @else data-userid="-1" @endif
            data-playid="{{ $objFragment->playid }}"
            data-videoplayid="{{ $objVideo->playid }}"
            data-guid="{{ $objFragment->guid }}"
            data-starttime="{{ $objFragment->start_time }}"
            data-endtime="{{ $objFragment->end_time }}"
            data-aspectratio="{{ $objVideo->aspect_ratio }}"
            data-language="{{$objVideo->language}}"
            data-title="{{ $objFragment->title }}"
            data-resourceflv="{{{ ($arrResource and $arrResource['FLV']['src']) ? $arrResource['FLV']['src'] : ''}}}"
            data-resourcemp4="{{{ ($arrResource and $arrResource['MP4']['src']) ? $arrResource['MP4']['src'] : ''}}}"
            data-resourcem3u8="{{{ ($arrResource and $arrResource['M3U8']['src']) ? $arrResource['M3U8']['src'] : ''}}}"
            data-videotype="fragment">
        </div>

    </div>

</div>

<div id="functionAside" class="function-wrap learn-mode ">
    <!-- tabs -->
    <div class="tab-wrap">
        <div class="shrink-btn">
            <a href=""></a>
        </div>
        <ul class="tab">
            <li><a href="" class="current">字幕</a></li>
        </ul>
    </div>
    <!--/ tabs -->

    <!-- 字幕 -->
    <div class="subtitle-wrap tab-content" id="subtitle">

        <!-- 剪辑前的actionbar -->
        <div class="subtitle-action sa-preview">
            <div class="search-w">
                <div class="search">
                    <form action="" name="subtitleSearch" id="subtitle_search">
                        <input type="text" class="input">
                        <button type="submit"><i class="icon-search"></i></button>
                        <!-- <span class="key-count">找到 3 个关键字</span> -->
                    </form>
                </div>
            </div>
        </div>
        <!-- 剪辑后的actionbar -->
        <!-- 剪辑前的actionbar -->
        <!-- <div class="subtitle-action sa-preview">
          <div class="btn-w">
          <a href="" class="btn-line btn-line-green"><i class="icon-video"></i><span>视频剪辑</span></a>
          </div>
          <div class="search-w">
          <div class="search">
            <input type="text">
            <button><i class="icon-search"></i></button>
            <span class="key-count">找到 3 个关键字</span>
          </div>
          </div>
        </div> -->
        <!--/ 剪辑前的actionbar -->

        <!-- 剪辑后的actionbar -->
        <!-- <div class="subtitle-action sa-edit">
          <div class="btn-w">
          <a href="" class="btn-line"><i class="icon-play"></i><span>预览</span></a>
          <a href="" class="btn-line"><i class="icon-note"></i><span>添加到笔记</span></a>
          </div>
          <div class="share-w">
          <ul class="share-list">
            <li><a href="" class="qzone">Qzone</a></li>
            <li><a href="" class="google">Google+</a></li>
            <li><a href="" class="weibo">Weibo</a></li>
            <li><a href="" class="wechat">Wechat</a></li>
          </ul>
          </div>
          <div class="close-w">
          <a href="" class="close"><i class="icon-times"></i></a>
          </div>
        </div> -->
        <!--/ 剪辑后的actionbar -->

        <div class="subtitle-content subtitle-edit">
            <!-- 字幕剪辑区域 -->
            <div class="subtitle-box">
                <ul class="subtitle">
                    <li>字幕加载中……</li>
                </ul>
                <!-- 字幕选择拖动手柄 -->
                <!-- <span class="clip-share-start" id="select_start" title="Clip start" style="left: 26px; top: 32px;"></span>
                <span class="clip-share-end" id="select_end" title="Clip end" style="left: 326px; top: 106px;"></span> -->
                <!--/ 字幕选择拖动手柄 -->
            </div>
            <!--/ 字幕剪辑区域 -->
        </div>
        <!-- 字幕范围选择器 -->
        <div class="clip-share-bar" id="clip_share_bar" style="display: none;">
            <div class="bar">
                <div class="slider" id="slider_start" style="left: 32px;">
                    <span class="el" title="slider start"></span>
                    <span class="time"><span>0' 20"</span></span>
                </div>
                <div class="slider" id="slider_end" style="left: 200px;">
                    <span class="el" title="slider end"></span>
                    <span class="time"><span>0' 53"</span></span>
                </div>
                <span class="range" id="slider_range" style="left: 32px; width: 168px;"></span>
            </div>
        </div>
        <!--/ 字幕范围选择器 -->
        <!-- 字幕注释浮层 -->
        <div id="annotationPane" class="sub-comment" style="display:none;">
            <div class="sub-comment-inner">
                <a href="" class="close"><i class="icon-times"></i></a>

                <div id="annotationContent" class="content"></div>
            </div>
        </div>
        <!--/ 字幕注释浮层 -->
    </div>
    <!--/ 字幕 -->
</div>
</div>
<div class="video-info">
    <div class="video-avatar-cate">
        <div class="avatar">
            <img src="{{{ $objMainSpeaker->portrait_src or '/static/hiho-edu/img/avatar_default.png' }}}"
                 alt="{{{ $objMainSpeaker->name or '无主讲'}}}">
        </div>
        <div class="teacher-info">
            <span><a href="#" target="_blank">{{{ $objMainSpeaker->name or '无主讲'}}}</a></span>

            <span>{{ \Tool::dateFormat($objVideoInfo->created_at) }}</span>
        </div>
        <ul class="video-meta-base">
            <li>分类: @if(empty($arrCategories)) 暂无 @else @foreach ($arrCategories as $cat) {{ $cat->name }} @endforeach
                @endif
            </li>
            <li>专业: @if(empty($arrSpecialities)) 暂无 @else @foreach ($arrSpecialities as $sp) {{ $sp->name }} @endforeach
                @endif
            </li>
            <li>语言: {{ \Tool::getLanguageName($objVideoInfo->language) }}</li>
        </ul>
    </div>
    <div class="vidoe-like">
        <!-- 收藏过的,在 a.like 添加"liked" class -->
        <a href="javascript:;" class="like {{ $favorited ? 'liked': '' }}">
            <div class="like-icon-w">
                <i class="like-icon"></i>
                <span>收藏</span>
            </div>
            <span class="like-count">{{ $objFragment->favourites }}</span>
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
        <script type="text/javascript" src="http://v3.jiathis.com/code/jia.js" charset="utf-8"></script>
        <script type="text/javascript" src="http://v3.jiathis.com/code/plugin.client.js" charset="utf-8"></script>
        <div style="position:absolute;width:0px;height:0px;">
          <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="0" height="0" id="JIATHISSWF"
            codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab">
          <param name="allowScriptAccess" value="always">
          <param name="swLiveConnect" value="true">
          <param name="movie" value="http://www.jiathis.com/code/swf/m.swf">
          <param name="FlashVars" value="z=a">
          <embed name="JIATHISSWF" src="http://www.jiathis.com/code/swf/m.swf" flashvars="z=a" width="0"
             height="0" allowscriptaccess="always" swliveconnect="true" type="application/x-shockwave-flash"
             pluginspage="http://www.macromedia.com/go/getflashplayer">
          </object>
        </div> -->
        <!-- JiaThis Button END -->
    <!-- </div> -->
</div>

<!-- 访问权限 -->
<div class="locked" style="display: none;">
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
        <h2>必须登陆</h2>

        <p>&nbsp;</p>
        <a href="javascript:void(0);" class="close"><i class="icon-times"></i></a>
    </div>
</div>
@stop

@section('js_foot')
<script type="text/template" id="tplHighlightItem">
    <li class="emphasis-item">
        <a href="javascript:;" data-start="{start}" class="hiliteLink">
            <div class="number">
                <span>{number}</span>
            </div>
            <div class="thumb">
                <img src="{thumb}" alt="{heading}">
            </div>
            <div class="content">
                <h4>{heading}</h4>
                <time>{start} - {end}</time>
            </div>
        </a>
    </li>
</script>
<script type="text/template" id="tplChoice">
    <li class="choiceItem" data-value="{value}"><a href="javascript:;"><span class="choice-label">{value}</span>{description}</a>
    </li>
</script>
<script src="{{\Config::get('app.pathToSource')}}/scripts/lib/require.js"
        data-main="{{\Config::get('app.pathToSource')}}/scripts/play.js"></script>
@stop