{{ 'Add a video to a playlist' }}

<form action="/list/addPost" method="post">
    添加到<select id="playlistSelect" name="playlistSelect">
        @foreach($data['playlists'] as $pl)
            <option value="{{$pl['id']}}">{{$pl['title']}}</option>
        @endforeach
    </select>
    标题<input type="text" name="title" id="title"/>
    描述<textarea name="description" id="description"></textarea>
    <input type="submit" name="btnSubmitAdd" id="btnSubmitAdd" value="保存"/>
    <input type="button" name="btnClose" id="btnClose" value="取消"/>

</form>