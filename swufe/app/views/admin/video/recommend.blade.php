@extends('layout.bs3_admin_layout')

@section('title')
首页课程推荐 - 西南财经大学
@stop

@section('content-wrapper')


<div class="page-header">
    <h1><span class="text-light-gray">课程 / </span>首页课程推荐</h1>
</div> <!-- / .page-header -->

<div class="row">
    <div class="col-sm-12">
        <div class="panel">
            <div class="panel-heading">
                <span class="panel-title">首页课程推荐</span>
            </div>
            <div class="alert alert-page alert-danger alert-dark hidden">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>删除成功！</strong> 已成功删除了 ID 为 XX 的用户。
            </div>
            <div class="panel-body">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th></th>
                        <th>#</th>
                        <th>guid</th>
                        <th>country</th>
                        <th>language</th>
                        <th>length</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($videos as $video)
                    <tr>
                        <td>
                            <input type="checkbox" id="inlineCheckbox1" value="option1">
                        </td>
                        <td>{{$video->video_id}}</td>
                        <td>{{$video->guid}}</td>
                        <td>{{$video->country}}</td>
                        <td>{{$video->language}}</td>
                        <td>{{$video->length}}</td>
                        <td>
                            <button class="btn btn-xs">资料</button>
                            <button class="btn btn-xs">登入身份</button>
                            <button class="btn btn-xs btn-danger">删除</button>
                        </td>
                    </tr>
                    @endforeach
                    <tr>
                        <td colspan="8">
                            <div class="pagination">
                                <ul class="pagination">
                                    <li class="disabled"><a href="#">«</a></li>
                                    <li class="active"><a href="#">1 <span class="sr-only">(current)</span></a></li>
                                    <li><a href="#">2</a></li>
                                    <li><a href="#">3</a></li>
                                    <li><a href="#">4</a></li>
                                    <li><a href="#">5</a></li>
                                    <li><a href="#">»</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@stop