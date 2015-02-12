<!-- 翻页 -->
<div class="pagination">
    @if(empty($append))
        {{$data->links()}}
    @else
        {{$data->appends($append)->links()}}
    @endif
</div>