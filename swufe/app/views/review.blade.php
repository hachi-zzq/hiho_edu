<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Play preview</title>
    <link href="/static/css/default/v3/common.css" rel="stylesheet" type="text/css" />
</head>

<body style="height:704px;">
<script src="/static/js/lib/sewise.player/sewise.player.min.js"></script>
<script>
    SewisePlayer.setup({
        server: "vod",
        videourl: "{{$video['src']}}",
        title: "{{$data->title}}",
        skin: "vodWhite",
        playername: " ",
        copyright: " "
    });
</script>


</body>
</html>
