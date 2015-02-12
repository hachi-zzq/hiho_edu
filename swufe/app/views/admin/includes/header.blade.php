<div class="navbar-inner">
    <div class="container">
        <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <a class="brand" href="/admin/">Hiho_Edu</a>
        <div class="nav-collapse collapse">
            <ul class="nav">

                <li id="video_nav"><a href="{{route('adminVideoList')}}">视频管理 </a></li>
                <li id="playlist_nav"><a href="{{route('adminPlayLists')}}">笔记管理</a></li>
                <li id="fragment_nav"><a href="{{route('adminFragments')}}">碎片管理</a></li>
                <li id="department_nav"><a href="{{route('adminDepartments')}}">院系管理</a></li>
                <li id="teacher_nav"><a href="{{route('adminTeacherList')}}">讲师管理</a></li>
                <li id="category_nav"><a href="{{route('adminCategories')}}">分类管理</a></li>
                <li id="user_nav"><a href="{{route('adminUserList')}}">用户管理</a></li>
                <li id="comment_nav"><a href="{{route('adminCommentList')}}">评论管理</a></li>
                <li id="tag_nav"><a href="{{route('adminTags')}}">Tag标签管理</a></li>
            </ul>

            <ul class="nav" style="float: right">
                <li class="dropdown">
                    <a href="##" class="dropdown-toggle" data-toggle="dropdown">Hi，{{Auth::user()->email}}<b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li><a href="/logout">退出</a></li>
                    </ul>
                </li>
            </ul>
        </div><!--/.nav-collapse -->
    </div>
</div>