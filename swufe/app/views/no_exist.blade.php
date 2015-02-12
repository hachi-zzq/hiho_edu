@extends('layout.master1')

@section('title') {{'西南财经大学－教材资料馆'}} @stop

@section('content')
<div class="main">
    <div class="content-inner">
        <div class="no-exist">
            <i class="sad-icon"></i>
            <p>很抱歉，{{ empty($data) ? '内容' : $data }}不存在，可能已被删除。</p>
            <div class="links">
                <a href="/">返回首页</a>
                <a href="javascript:history.back(-1)">返回上一页</a>
            </div>
        </div>
    </div>
</div>
@stop