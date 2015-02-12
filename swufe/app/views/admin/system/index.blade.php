@extends('layout.bs3_admin_layout')

@section('title')
系统设置 - 西南财经大学
@stop
<style type="text/css" xmlns="http://www.w3.org/1999/html">
    #active_email_content{
        height: 300px;
    }
</style>
@section('content-wrapper')
<div class="row" xmlns="http://www.w3.org/1999/html">

    <div class="panel">
        <div class="panel-heading">
            <span class="panel-title">系统/站点设置</span>
        </div>
        <div class="panel-body">
            <ul id="uidemo-tabs-default-demo" class="nav nav-tabs">
                <li class="{{Input::get('active')=='website' || Input::get('active')==''?'active':''}}">
                    <a href="#uidemo-tabs-default-demo-home" data-toggle="tab">站点设置 </a>
                </li>
                <li class="{{Input::get('active')=='school'?'active':''}}">
                    <a href="#uidemo-tabs-default-demo-school" data-toggle="tab">学校信息设置 </a>
                </li>
                <li class="{{Input::get('active')=='register'?'active':''}}">
                    <a href="#uidemo-tabs-default-demo-profile" data-toggle="tab">注册设置</a>
                </li>
                <li class="{{Input::get('active')=='email'?'active':''}}">
                    <a href="#uidemo-tabs-default-demo-email" data-toggle="tab">邮件服务器设置</a>
                </li>
                <li class="{{Input::get('active')=='message'?'active':''}}">
                    <a href="#uidemo-tabs-default-demo-message" data-toggle="tab">短信验证设置</a>
                </li>
            </ul>

            <div class="tab-content tab-content-bordered">
                <div class="tab-pane fade  {{Input::get('active')=='website' || Input::get('active')==''?'active in':''}}" id="uidemo-tabs-default-demo-home">
                    <form method="post" action="{{route('adminUpdateWebSetting')}}" enctype="multipart/form-data" >
                    <div class="form-group">
                        <label for="inputEmail2" class="col-sm-2 control-label">站点名称：</label>

                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="inputEmail"   name="website_name" value="{{isset($arrConfig['website_name']) ? $arrConfig['website_name'] :'' }}">

                            <p class="help-block">站点名称，将显示在浏览器窗口标题、页面底部等位置.</p>
                            <p class="help-block">例如：<span class="text-primary">HIHO Autotiming</span>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail2" class="col-sm-2 control-label">网站 URL：</label>

                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="inputEmail"  name="website_url" value="{{isset($arrConfig['website_url']) ? $arrConfig['website_url'] :'' }}">

                            <p class="help-block">网站 URL，将作为链接显示在页面底部.</p>
                            <p class="help-block">例如：<span class="text-primary">http://hiho.autotiming.com.</span>

                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail2" class="col-sm-2 control-label">SEO关键词:：</label>

                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="inputEmail"   name="seo_keyword" value="{{isset($arrConfig['seo_keyword']) ? $arrConfig['seo_keyword'] :'' }}">

                        </div>
                    </div>

                    <div class="form-group">
                        <label for="inputEmail2" class="col-sm-2 control-label">SEO描述信息:：</label>

                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="inputEmail"  name="seo_description" value="{{isset($arrConfig['seo_description']) ? $arrConfig['seo_description'] :'' }}">

                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail2" class="col-sm-2 control-label">管理员邮箱:：</label>

                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="inputEmail"   name="admin_email" value="{{isset($arrConfig['admin_email']) ? $arrConfig['admin_email'] :'' }}">

                            <p class="help-block">管理员 E-mail，将作为系统发邮件的时候的发件人地址.</p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="inputEmail2" class="col-sm-2 control-label">ICP备案号:：</label>

                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="inputEmail" placeholder="Email" name="icp" value="{{isset($arrConfig['icp']) ? $arrConfig['icp'] :'' }}">

                            <p class="help-block">页面底部可以显示 ICP 备案信息，如果网站已备案，在此输入您的授权码，它将显示在页面底部，如果没有请留空.</p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="inputPortrait" class="col-sm-2 control-label">网址logo</label>

                        <div class="col-sm-10">
                            <input type="file" id="input-upload-portrait" name="logo">
                            @if(isset($arrConfig['logo_url']))
                            <img src="{{$arrConfig['logo_url']}}" >
                            @endif
                            <p class="help-block">请上传png, gif, jpg格式的图片文件。LOGO图片的高度建议不要超过50px.</p>
                        </div>

                    </div>


                    <div class="form-group">
                        <label for="inputPortrait" class="col-sm-2 control-label">浏览器图标</label>

                        <div class="col-sm-10">
                            <input type="file" id="input-upload-icon" name="ico_file">
                            @if(isset($arrConfig['ico_url']))
                            <img src="{{$arrConfig['ico_url']}}" >
                            @endif
                            <p class="help-block">请上传ico格式的图标文件.</p>
                        </div>

                    </div>
                    <div class="form-group">
                        <label for="inputPortrait" class="col-sm-2 control-label">统计分析代码</label>

                        <div class="col-sm-10">
                            <textarea class="form-control" name="analytics_code">{{isset($arrConfig['analytics_code']) ? json_decode($arrConfig['analytics_code']) :'' }}</textarea>
                            <p class="help-block">建议使用下列统计分析的一种： <a href="http://www.google.cn/intl/zh-CN_ALL/analytics/" target="_blank">谷歌分析</a>、 <a href="http://tongji.baidu.com/" target="_blank">百度统计</a>、
                                <a href="http://ta.qq.com/" target="_blank">腾讯分析</a>、 <a href="http://www.cnzz.com/" target="_blank">CNZZ</a></p>
                        </div>

                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="submit" class="btn btn-primary">设置</button>
                        </div>
                    </div>
                    </form>
                </div> <!-- / .tab-pane -->
                <div class="tab-pane fade  {{Input::get('active')=='school'?'active in':''}}" id="uidemo-tabs-default-demo-school">
                    <form method="post" action="{{route('adminSchoolSetting')}}"  enctype="multipart/form-data" >
                        <div class="form-group">
                            <label for="inputEmail2" class="col-sm-2 control-label">学校名称：</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="inputEmail"   name="school_name" value="{{isset($arrConfig['school_name']) ? $arrConfig['school_name'] :'' }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputEmail2" class="col-sm-2 control-label">学校描述：</label>

                            <div class="col-sm-10">
                                <textarea class="form-control" name="school_description">{{isset($arrConfig['school_description']) ? json_decode($arrConfig['school_description']) :'' }}</textarea>

                            </div>
                        </div>

                        <div class="form-group" style="margin-bottom: 0;">
                            <div class="col-sm-offset-2 col-sm-10">
                                <button type="submit" class="btn btn-primary">设置</button>
                            </div>
                        </div>
                    </form>
                </div> <!-- / .tab-pane -->
                <div class="tab-pane fade  {{Input::get('active')=='register'?'active  in':''}}" id="uidemo-tabs-default-demo-profile">
                    <form method="post" action="{{route('adminRegisterSetting')}}">
                    <!-- / .form-group -->
                    <div class="form-group">
                        <label for="inputIsAdmin" class="col-sm-2 control-label">新用户注册</label>

                        <div class="col-sm-10">
                            <div class="radio">
                                <label>
                                    <input type="radio" name="register_mode" value="open" class="px" @if(isset($arrConfig['register_mode']) && $arrConfig['register_mode']=='open') {{'checked'}} @endif>
                                    <span class="lbl">开启</span>
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="register_mode"  value="close" class="px" @if(isset($arrConfig['register_mode']) && $arrConfig['register_mode']=='close') {{'checked'}} @endif>
                                    <span class="lbl">关闭</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail2" class="col-sm-2 control-label">新用户激活邮件标题</label>

                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="inputEmail"   name="active_email_title" value="{{isset($arrConfig['active_email_title']) ? ($arrConfig['active_email_title']) :'' }}">

                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputPortrait" class="col-sm-2 control-label">新用户激活邮件内容</label>

                        <div class="col-sm-10">
                            <textarea class="form-control" name="active_email_content" id="active_email_content">{{isset($arrConfig['active_email_content']) ? json_decode($arrConfig['active_email_content']) :'' }}</textarea>
                            <p class="help-block text-primary">{! nickname !}表示收件方用户昵称，{! sitename !}表示站点名称，{! verifyurl !}表示激活地址，为系统变量，管理员可直接使用，系统将自动解析</p>
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <div class="col-sm-offset-2 col-sm-10">
                                <button type="submit" class="btn btn-primary">设置</button>
                            </div>
                        </div>
                        </form>
                    </div>
                </div> <!-- / .tab-pane -->
                <div class="tab-pane fade {{Input::get('active')=='email'?'active in':''}}" id="uidemo-tabs-default-demo-email">
                    <form method="post" action="{{route('adminEmailSetting')}}">
                    <!-- / .form-group -->
                    <div class="form-group">
                        <label for="inputIsAdmin" class="col-sm-2 control-label">发送邮件</label>

                        <div class="col-sm-10">
                            <div class="radio">
                                <label>
                                    <input type="radio" name="send_email" value="open" class="px" @if(isset($arrConfig['send_email']) && $arrConfig['send_email']=='open') {{'checked'}} @endif>
                                    <span class="lbl">开启</span>
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="send_email"  value="close" class="px" @if(isset($arrConfig['send_email']) && $arrConfig['send_email']=='close') {{'checked'}} @endif>
                                    <span class="lbl">关闭</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="inputEmail2" class="col-sm-2 control-label">SMTP服务器地址</label>

                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="inputEmail"   name="smtp_address" value="{{isset($arrConfig['smtp_address']) ? ($arrConfig['smtp_address']) :'' }}">

                        </div>
                    </div>

                    <div class="form-group">
                        <label for="inputEmail2" class="col-sm-2 control-label">SMTP服务器端口</label>

                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="inputEmail"   name="smtp_port" value="{{isset($arrConfig['smtp_port']) ? ($arrConfig['smtp_port']) :'' }}">

                        </div>
                    </div>

                    <div class="form-group">
                        <label for="inputEmail2" class="col-sm-2 control-label">SMTP用户名</label>

                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="inputEmail"   name="smtp_username" value="{{isset($arrConfig['smtp_username']) ? ($arrConfig['smtp_username']) :'' }}">

                        </div>
                    </div>

                    <div class="form-group">
                        <label for="inputEmail2" class="col-sm-2 control-label">SMTP密码</label>

                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="inputEmail"   name="smtp_password" value="{{isset($arrConfig['smtp_password']) ? ($arrConfig['smtp_password']) :'' }}">

                        </div>
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="submit" class="btn btn-primary">设置</button>
                        </div>
                    </div>
                        </form>
                </div> <!-- / .tab-pane -->
            <div class="tab-pane fade  {{Input::get('active')=='message'?'active in':''}}" id="uidemo-tabs-default-demo-message">
                <form method="post" action="{{route('adminMsgSetting')}}"  enctype="multipart/form-data" >
                    <!-- / .form-group -->
                    <div class="form-group">
                        <label for="inputIsAdmin" class="col-sm-2 control-label">短信验证</label>

                        <div class="col-sm-10">
                            <div class="radio">
                                <label>
                                    <input type="radio" name="send_message" value="open" class="px" @if(isset($arrConfig['send_message']) && $arrConfig['send_message']=='open') {{'checked'}} @endif>
                                    <span class="lbl">开启</span>
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="send_message"  value="close" class="px" @if(isset($arrConfig['send_message']) && $arrConfig['send_message']=='close') {{'checked'}} @endif>
                                    <span class="lbl">关闭</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="submit" class="btn btn-primary">设置</button>
                        </div>
                    </div>
                </form>
            </div> <!-- / .tab-pane -->
            </div> <!-- / .tab-content -->
        </div>
    </div>
</div>
@stop