@extends('emails.layout')

@section('content')
<td style="padding: 40px;">
    <h2 style="color: #384047;">亲爱的 {{$name}}</h2>
    <p style="color: #636669;line-height:18px; font-size: 13px;">欢迎加入西南财经大学教材资料馆! <br /><br />
        请点击下面的链接完成注册：
    </p>
    <div style="text-align: center;padding: 20px;border-radius: 4px; background: #f7f7f7;margin: 20px 0;">
        <a href="{{$activate_url}}" style="color: #00a8ff; font-size: 13px;">{{$activate_url}}</a>
    </div>
    <p style="color: #636669; line-height:18px; font-size: 13px;">如果以上链接无法点击，请将上面的地址复制到你的浏览器(如Chrome)的地址栏进入。</p>
</td>
@stop