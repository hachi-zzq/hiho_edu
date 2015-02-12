<!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Upload</title>
    <script src="/bootstrap/js/jquery.js" type="text/javascript"></script>
    <script src="/static/admin/js/jquery.uploadify.min.js" type="text/javascript"></script>
    <script src="/bootstrap/js/bootstrap.js"></script>
    <link href="/bootstrap/css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/static/admin/css/uploadify.css">
    <style type="text/css">
        body {
            font: 13px Arial, Helvetica, Sans-serif;
        }
    </style>
</head>

<body>
<h2>上传视频</h2>
<form>
    <div id="queue"></div>
    <input id="file_upload" name="file_upload" type="file" multiple="true">
</form>

<script type="text/javascript">
    <?php $timestamp = time();?>
    $(function() {
        $('#file_upload').uploadify({
            'formData'     : {
                'timestamp' : '<?php echo $timestamp;?>',
                'token'     : '<?php echo md5('unique_salt' . $timestamp);?>'
            },
            'swf'      : '/static/admin/images/uploadify.swf',
            'uploader' : '{{route("adminVideoDoUpload")}}',
           'buttonText':'选择文件',
            'fileTypeExts':'*.mp4;*.flv;*.m3u8',
            'removeCompleted':false
        });
    });

    function closeReload(){
        window.opener.location.reload();
        window.close();
    }
</script>
<button class="btn" onclick="closeReload()">完成</button>

</body>
</html>