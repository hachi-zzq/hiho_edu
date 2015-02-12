@extends('layout.master')

@section('title') {{$keywords.'-西南财经大学－教材资料馆'}} @stop

@section('js')
<script>
    $(document).ready(function(){
        $(".show-all").click(function(){
            var content = $(this).parent().siblings('li');
            content.each(function(i){
                $(this).removeClass('hidden');
            });
            $(this).hide();
            $(this).siblings(".hide").show();
        });
        $(".hide").click(function(){
            var content = $(this).parent().siblings('li');
            content.each(function(i){
                if(i > 9) {
                    $(this).addClass('hidden');
                }
            });
            $(this).hide();
            $(this).siblings(".show-all").show();
        });
    });
</script>
@stop

@section('content')

<div class="main">
    <div class="content-inner">
        @if(count($searchResult) > 0)
        <div class="search-result">
            @foreach($searchResult as $video)
            <!-- 搜索结果项 -->
            <div class="item" data-length="{{ $video->length }}">
                <div class="thumb-w">
                    <!-- 视频缩略图 -->
                    <div class="thumb">
                        <a href="{{ url('/play',array('playid'=>$video->playid)) }}?type=video" target="_blank">
                            <img src="{{ $video->thumbnails }}" alt="{{ $video->title }}">
                            <time>{{ gmdate('H:i:s', (int)$video->length)}}</time>
                            <div class="thumb-hover">
                                <i class="icon-play-c"></i>
                            </div>
                        </a>
                    </div>
                </div>
                <!-- 搜索内容 -->
                <div class="info">
                    <div class="title">
                        <h2><a href="{{ url('/play',array('playid'=>$video->playid)) }}?type=video">{{ $video->title }}</a></h2>
                    </div>
                    <div class="meta">
                        <span><i class="icon-preview"></i><b>{{ $video->viewed }}</b></span>
                        <span><i class="icon-love"></i><b>{{ $video->liked }}</b></span>
                        <span><i class="icon-time-c"></i><b>{{ $video->created_at->format('Y-m-d')}}</b></span>
                    </div>
                    <div class="time-line">
                    </div>
                    <div class="subtitle-result">
                        <ul>
                            <?php $size = count($video->fragments);?>
                            @if($size > 10)
                                @foreach($video->fragments as $k => $fragment)
                                    <li data-st="{{ $fragment['st'] }}" class="{{ ($k > 9) ? 'hidden' : '' }}">
                                        <time>{{ gmdate('H:i:s',$fragment['st']) }}</time>
                                        <a target="_blank"
                                           href="{{ url('/play',array('playid'=>$video->playid)) }}?type=video&st={{ $fragment['st'] }}&et={{ $video->length }}">
                                            {{ $fragment['text'] }}
                                        </a>
                                    </li>
                                @endforeach
                                <div style="text-align: center;">
                                    <a style="font-size: 9px;color: #8c989e;position: relative;" class="show-all" href="javascript:void(0);">查看全部</a>
                                    <a style="font-size: 9px;color: #8c989e;position: relative;" class="hide hidden" href="javascript:void(0);">隐藏</a>
                                </div>
                            @else
                                @foreach($video->fragments as $fragment)
                                <li data-st="{{ $fragment['st'] }}">
                                    <time>{{ gmdate('H:i:s',$fragment['st']) }}</time>
                                    <a target="_blank"
                                       href="{{ url('/play',array('playid'=>$video->playid)) }}?type=video&st={{ $fragment['st'] }}&et={{ $video->length }}">
                                        {{ $fragment['text'] }}
                                    </a>
                                </li>
                                @endforeach
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="search-no-result">
            <img class="sad" src="/static/hiho-edu/img/sad@2x.png" alt="not found">

            <p>很抱歉，您要找的关键字没有找到</p>
        </div>
        @endif
        <!-- 翻页 -->
        <div class="pagination">
            {{ $searchResult->appends(array('keywords'=>$keywords))->links() }}
        </div>
    </div>
</div>
@stop