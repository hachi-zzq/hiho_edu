<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', 'HomeController@index');
Route::any('/search', 'SearchController@getSearch');
Route::any('/search/getSingleSubtitleFt', 'SearchController@getSingleSubtitleFt');
Route::get('/app', 'HomeController@app');

/**
 * 通用播放
 */
Route::any('/play/{playid}', 'PlayController@playAnything');
Route::get('/play/review', 'PlayController@review');

/**
 * 一级分类
 */
Route::any('videos', 'CategoryController@index'); // 西南财大视频资源

Route::get('review', 'VideoController@review');

/**
 * 碎片
 */
Route::get('/clips', 'FragmentController@index');
Route::get('/fragment/getWithPlayIdAndStEt', 'FragmentController@getCreateWithPlayId');

/**
 *附件下载
 */
Route::get('/download/attachment/{id}', 'PlayController@downloadAttachment');

/**
 * 笔记
 */
Route::group(array('prefix' => 'note'), function () {
    Route::get('/', 'PlaylistController@getIndex');
    Route::get('getMyNoteList', 'PlaylistController@getMyNoteList');
    Route::get('{playlist_id}', 'PlaylistController@detail')->where('playlist_id', '[\d]+');
    Route::get('getFragmentPlayUrl/{playid}', 'PlaylistController@getFragmentPlayUrl');
    Route::get('create', 'PlaylistController@create');
    Route::post('create', 'PlaylistController@createPost');
    Route::post('delete', 'PlaylistController@deletePost');
    Route::post('deleteFragment', 'PlaylistController@deleteFragmentPost');
    Route::get('add', 'PlaylistController@add');

    Route::get('edit', 'PlaylistController@edit');
    Route::post('edit', 'PlaylistController@editPost');
    Route::post('sort', 'PlaylistController@sort');

    Route::post('editTitle', 'PlaylistController@editTitlePost');
    Route::post('editFragmentTitle', 'PlaylistController@editFragmentTitlePost');
    Route::post('editFragmentComment', 'PlaylistController@editFragmentCommentPost');

    Route::any('copyToMine', 'PlaylistController@copyToMine');
});

/**
 * 需要验证登录
 */
Route::group(array('prefix' => 'my', 'before' => 'auth'), function () {
    Route::get('note', 'PlaylistController@my');
    Route::any('note/addInMyPlaylist', 'PlaylistController@addInMyPlaylist');
    Route::get('share', 'FavoriteController@share');
    Route::get('profile', 'UserController@profile');
    Route::post('profile', 'UserController@profilePost');
});

// Route::get('/videos', 'VideoController@index');

/**
 * 收藏
 */
Route::post('favorite/add', 'FavoriteController@addPost');
Route::post('favorite/addFragment', 'FavoriteController@addFragmentPost');
Route::post('favorite/addPlaylist', 'FavoriteController@addPlaylist');
Route::group(array('prefix' => 'favorite', 'before' => 'auth'), function () {
    Route::get('videos', 'FavoriteController@videos');
    Route::get('notes', 'FavoriteController@playlists');
    Route::get('clips', 'FavoriteController@clips');
    Route::post('delete', 'FavoriteController@deletePost');
});

/**
 * 评论
 */
Route::group(array('prefix' => 'comment'), function () {
    Route::post('add', 'CommentController@addPost');
    Route::post('reply', 'CommentController@replyPost');
});

Route::get('vi/notfound', 'VideoImageController@getNotFound');
Route::get('vi/notfound/{norms}', 'VideoImageController@getNotFound');
Route::get('vi/{video_guid}', 'VideoImageController@getVideoImage');
Route::get('vi/{video_guid}/{st}-{et}/{norms?}', 'VideoImageController@getFragmentImageWithVideoGuid');

Route::get('/loadComment', 'VideoController@loadComment');
Route::get('subtitle/{video_guid}/{lang}.{type}', 'SubtitleController@getSubtitle');
Route::get('subtitle', 'SubtitleController@getSubtitleV2');
// Route::get('subtitle/{video_guid}/{st}-{et}/{lang}.{returntype}', 'SubtitleController@getFragmentSubtitleV2');
Route::get('subtitle/fragment', 'SubtitleController@getFragmentSubtitleV2');

Route::group(array('prefix' => 'signup'), function () {
    Route::get('phone', 'UserController@signupPhone');
    Route::post('phone', 'UserController@signupPhonePost');
    Route::get('phone_step2', 'UserController@signupPhoneS2');
    Route::post('phone_step2', 'UserController@signupPhoneS2Post');
    Route::get('phone/success', 'UserController@signupPhoneSuccess');
    Route::get('email', 'UserController@signupEmail');
    Route::post('email', 'UserController@signupEmailPost');
    Route::get('email_step2', 'UserController@signupEmailS2');
    Route::post('email_step2', 'UserController@signupEmailS2Post');
    Route::get('email/success', 'UserController@signupEmailSuccess');
});

Route::group(array('prefix' => 'reset'), function () {
    Route::get('/', 'UserController@reset');
    Route::post('/', 'UserController@resetPost');
    Route::get('mobile', 'UserController@resetByPhone');
    Route::post('mobile', 'UserController@resetByPhonePost');
    Route::get('email_sent', 'UserController@resetEmailSent');
    Route::get('email/{token}', 'UserController@resetByEmail');
    Route::post('email', 'UserController@resetByEmailPost');
    Route::get('success', 'UserController@resetPwSuccess');
});

Route::get('captcha', 'BaseController@getCaptcha');

Route::get('/login', 'UserController@login');
Route::post('/login', 'UserController@loginPost');
Route::get('/weiboCallBack', 'UserController@weiboCallBack');
Route::get('/fbCallBack', 'UserController@fbCallBack');
Route::get('/logout', 'UserController@logout');
Route::get('/forgot', 'UserController@forgot');
Route::post('/forgot', 'UserController@forgotPost');
Route::get('/resetSendSuccess', 'UserController@resetSendSuccess');
Route::get('/resetPassword/{token}', 'UserController@resetPassword');
Route::post('/resetPassword', 'UserController@resetPasswordPost');
Route::get('/resetSuccess/{guid}', 'UserController@resetSuccess');
Route::get('/activate/{token}', 'UserController@getActivate');
Route::post('/sendSms', 'UserController@sendSMS');

Route::get('/departments', 'DepartmentController@index');
Route::get('/department/{department_id}', 'DepartmentController@detail');
Route::get('/teacher/{teacher_id}', 'TeacherController@detail');
//test
Route::get('/test', 'HomeController@test');
Route::any('/blank', 'HomeController@blank');

##get subtitle by zhuzhengqian
Route::any('/course/getSubtitle', array('as' => 'getCourseSubtitle', 'uses' => 'HihoEdu\Controller\Admin\CourseController@getSubtitle'));

Route::any('app/download','AppController@appDownload');

/**
 * 后台管理
 * Admin
 * @author Zhengqian, Luyu
 */
Route::group(array('prefix' => 'admin', 'before' => 'auth.admin'), function () {

    // 后台首页
    Route::get('/', array('as' => 'adminHome', 'uses' => 'HihoEdu\Controller\Admin\HomeController@index'));
    Route::get('/dashboard', array('as' => 'adminHome', 'uses' => 'HihoEdu\Controller\Admin\HomeController@index'));

    // 用户
    Route::get('/users', array('as' => 'adminUserList', 'uses' => 'HihoEdu\Controller\Admin\UserController@getIndex'));
    Route::get('/users/modify/{user_id}', array('as' => 'adminUserModify', 'uses' => 'HihoEdu\Controller\Admin\UserController@getModify'))->where('user_id', '[\d]+');
    Route::post('/users/modify/{user_id}', array('as' => 'adminUserModifyAction', 'uses' => 'HihoEdu\Controller\Admin\UserController@postmodify'))->where('user_id', '[\d]+');
    Route::get('/users/create', array('as' => 'adminUserAdd', 'uses' => 'HihoEdu\Controller\Admin\UserController@getCreate'));
    Route::post('/users/create', array('as' => 'adminUserAdd', 'uses' => 'HihoEdu\Controller\Admin\UserController@postCreate'));
    Route::any('/users/destroy/{user_id}', array('as' => 'adminUserDelete', 'uses' => 'HihoEdu\Controller\Admin\UserController@getDestroy'))->where('user_id', '[\d]+');
    Route::any('/users/unlock/{user_id}', array('as' => 'adminUserUnlock', 'uses' => 'HihoEdu\Controller\Admin\UserController@unlockUser'))->where('user_id', '[\d]+');
    Route::any('/users/loginSession/{user_id}', 'HihoEdu\Controller\Admin\UserController@getSessionLogin')->where('user_id', '[\d]+');
    Route::get('/users/logout', array('as' => 'adminUserLogout', 'uses' => 'HihoEdu\Controller\Admin\UserController@adminLogout'));
    Route::any('/users/find', array('as' => 'adminUserFind', 'uses' => 'HihoEdu\Controller\Admin\UserController@find'));

    //角色
    Route::get('/roles', array('as' => 'adminRoleList', 'uses' => 'HihoEdu\Controller\Admin\RoleController@index'));
    Route::get('/roles/modify/{role_id}', array('as' => 'adminRoleModify', 'uses' => 'HihoEdu\Controller\Admin\RoleController@getModify'))->where('role_id', '[\d]+');
    Route::post('/roles/modify/{role_id}', array('as' => 'adminRoleModifyAction', 'uses' => 'HihoEdu\Controller\Admin\RoleController@postModify'))->where('role_id', '[\d]+');
    Route::get('/roles/create', array('as' => 'adminRoleAdd', 'uses' => 'HihoEdu\Controller\Admin\RoleController@getCreate'));
    Route::post('/roles/create', array('as' => 'adminRoleAdd', 'uses' => 'HihoEdu\Controller\Admin\RoleController@postCreate'));
    Route::any('/roles/destroy/{role_id}', array('as' => 'adminRoleDelete', 'uses' => 'HihoEdu\Controller\Admin\RoleController@getDestroy'))->where('role_id', '[\d]+');

    //上传管理
    Route::get('/videos/uploadList/{type?}', array('as' => 'adminUploadList', 'uses' => 'HihoEdu\Controller\Admin\VideoController@uploadList'));
    Route::get('/video/upload', array('as' => 'adminVideoUpload', 'uses' => 'HihoEdu\Controller\Admin\VideoController@videoAdd'));
    Route::get('/video/uploadDelete/{video_id}', array('as' => 'adminVideoUploadDelete', 'uses' => 'HihoEdu\Controller\Admin\VideoController@videoUploadDelete'))->where('video_id', '[\d]+');
    Route::post('/video/doUpload', array('as' => 'adminVideoDoUpload', 'uses' => 'HihoEdu\Controller\Admin\VideoController@doUpload'));

    //视频管理
    Route::get('/video/list', array('as' => 'adminVideoList', 'uses' => 'HihoEdu\Controller\Admin\VideoController@videoList'));
    Route::get('/video/videoDelete/{video_id}', array('as' => 'adminVideoDelete', 'uses' => 'HihoEdu\Controller\Admin\VideoController@videoDelete'))->where('video_id', '[\d]+');
    Route::get('/video/modify/{video_id}', array('as' => 'adminVideoModify', 'uses' => 'HihoEdu\Controller\Admin\VideoController@modify'))->where('video_id', '[\d]+');
    Route::post('/video/modifyPost', array('as' => 'adminVideoModifyPost', 'uses' => 'HihoEdu\Controller\Admin\VideoController@modifyPost'));
    Route::any('/video/bindInfo', array('as' => 'adminVideoBind', 'uses' => 'HihoEdu\Controller\Admin\VideoController@ajaxBindVideoInfo'));

    // 字幕
    Route::get('/video/addSubtitle/{video_id}', array('as' => 'adminSubtitleAdd', 'uses' => 'HihoEdu\Controller\Admin\SubtitleController@add'))->where('video_id', '[\d]+');
    Route::post('/video/addSubtitle/{video_id?}', array('as' => 'adminSubtitleAddAction', 'uses' => 'HihoEdu\Controller\Admin\SubtitleController@postAdd'))->where('video_id', '[\d]+');
    Route::any('/video/subtitleConvert', array('as' => 'adminSubtitleConvert', 'uses' => 'HihoEdu\Controller\Admin\SubtitleController@srt2Txt'));
    //subtitle V2 (结构化课程字幕)
    Route::get('/getCourseSubtitle', array('as' => 'adminGetCourseSubtitle', 'uses' => 'HihoEdu\Controller\Admin\SubtitleController@getCourseSubtitle'));

    // 短视频, 碎片,
    Route::get('/fragments/index', array('as' => 'adminFragments', 'uses' => 'HihoEdu\Controller\Admin\FragmentController@index'));
    Route::get('/fragment/delete/{id}', array('as' => 'adminFragmentDelete', 'uses' => 'HihoEdu\Controller\Admin\FragmentController@delete'))->where('id', '[\d]+');

    // 评论, by zhuzhengqian
    Route::get('/comments/index', array('as' => 'adminCommentList', 'uses' => 'HihoEdu\Controller\Admin\CommentController@index'));
    Route::get('/comment/delete/{id}', array('as' => 'adminCommentDelete', 'uses' => 'HihoEdu\Controller\Admin\CommentController@delete'))->where('id', '[\d]+');
    Route::any('/comment/modify/{id?}', array('as' => 'adminCommentModify', 'uses' => 'HihoEdu\Controller\Admin\CommentController@modify'))->where('id', '[\d]+');

    // 笔记, 播放列表
    Route::get('/playlists/index', array('as' => 'adminPlayLists', 'uses' => 'HihoEdu\Controller\Admin\PlayListController@index'));
    Route::get('/playlist/check/{id}', array('as' => 'adminPlaylistCheck', 'uses' => 'HihoEdu\Controller\Admin\PlayListController@check'))->where('id', '[\d]+');
    Route::get('/playlist/destory/{id}', array('as' => 'adminPlaylistDestory', 'uses' => 'HihoEdu\Controller\Admin\PlayListController@destory'))->where('id', '[\d]+');

    // 分类,
    Route::get('/categories', array('as' => 'adminCategories', 'uses' => 'HihoEdu\Controller\Admin\CategoryController@index'));
    Route::get('/category/add', array('as' => 'adminCategoryAdd', 'uses' => 'HihoEdu\Controller\Admin\CategoryController@addShow'));
    Route::post('/category/add', array('as' => 'adminCategoryAddPost', 'uses' => 'HihoEdu\Controller\Admin\CategoryController@addPost'));
    Route::get('/category/delete/{id}', array('as' => 'adminCategoryDelete', 'uses' => 'HihoEdu\Controller\Admin\CategoryController@delete'))->where('id', '[\d]+');
    Route::any('/category/modify/{id?}', array('as' => 'adminCategoryModify', 'uses' => 'HihoEdu\Controller\Admin\CategoryController@modify'))->where('id', '[\d]+');
    Route::get('/category/slave/{id}', array('as' => 'adminCategorySlave', 'uses' => 'HihoEdu\Controller\Admin\CategoryController@slaveCategoryIndex'));

    Route::get('/specialities', array('as' => 'adminSpecialities', 'uses' => 'HihoEdu\Controller\Admin\SpecialityController@index'));
    Route::get('/speciality/add', array('as' => 'adminSpecialityAdd', 'uses' => 'HihoEdu\Controller\Admin\SpecialityController@addShow'));
    Route::post('/speciality/add', array('as' => 'adminSpecialityAddPost', 'uses' => 'HihoEdu\Controller\Admin\SpecialityController@addPost'));
    Route::get('/speciality/delete/{id}', array('as' => 'adminSpecialityDelete', 'uses' => 'HihoEdu\Controller\Admin\SpecialityController@delete'))->where('id', '[\d]+');
    Route::any('/speciality/modify/{id?}', array('as' => 'adminSpecialityModify', 'uses' => 'HihoEdu\Controller\Admin\SpecialityController@modify'))->where('id', '[\d]+');
//    Route::get('/speciality/slave/{id}', array('as' => 'adminSpecialitySlave', 'uses' => 'HihoEdu\Controller\Admin\SpecialityController@slaveSpecialityIndex'));

    // 院系机构,
    Route::get('/departments', array('as' => 'adminDepartments', 'uses' => 'HihoEdu\Controller\Admin\DepartmentController@getIndex'));
    Route::get('/departments/slave/{id}', array('as' => 'adminDepartmentSlave', 'uses' => 'HihoEdu\Controller\Admin\DepartmentController@slaveDepartmentIndex'));
    Route::get('/departments/create', array('as' => 'adminDepartmentCreate', 'uses' => 'HihoEdu\Controller\Admin\DepartmentController@getCreate'));
    Route::post('/departments/create', array('as' => 'adminDepartmentCreatePost', 'uses' => 'HihoEdu\Controller\Admin\DepartmentController@postCreate'));
    Route::get('/departments/destroy/{id}', array('as' => 'adminDepartmentDelete', 'uses' => 'HihoEdu\Controller\Admin\DepartmentController@getDestroy'))->where('id', '[\d]+');;
    Route::any('/departments/modify/{id?}', array('as' => 'adminDepartmentModify', 'uses' => 'HihoEdu\Controller\Admin\DepartmentController@modify'))->where('id', '[\d]+');;

    // 标签管理,
    Route::get('/tags/index', array('as' => 'adminTags', 'uses' => 'HihoEdu\Controller\Admin\TagController@index'));
    Route::get('/tag/add', array('as' => 'adminTagAdd', 'uses' => 'HihoEdu\Controller\Admin\TagController@addShow'));
    Route::post('/tag/add', array('as' => 'adminTagAdd', 'uses' => 'HihoEdu\Controller\Admin\TagController@add'));
    Route::get('/tag/delete/{id}', array('as' => 'adminTagDelete', 'uses' => 'HihoEdu\Controller\Admin\TagController@delete'))->where('id', '[\d]+');
    Route::any('/tag/modify/{id?}', array('as' => 'adminTagModfy', 'uses' => 'HihoEdu\Controller\Admin\TagController@modify'));

    //主题
    Route::get('/topics/index', array('as' => 'adminTopics', 'uses' => 'HihoEdu\Controller\Admin\TopicController@index'));
    Route::any('/topics/create', array('as' => 'adminTopicsCreate', 'uses' => 'HihoEdu\Controller\Admin\TopicController@create'));
    Route::any('/topics/modify/{id?}', array('as' => 'adminTopicsModify', 'uses' => 'HihoEdu\Controller\Admin\TopicController@modify'));
    Route::get('/topics/delete/{id}', array('as' => 'adminTopicsDelete', 'uses' => 'HihoEdu\Controller\Admin\TopicController@delete'));


    // 教师管理,
    Route::get('/teachers', array('as' => 'adminTeacherList', 'uses' => 'HihoEdu\Controller\Admin\TeacherController@getIndex'));
    Route::get('/teachers/create', array('as' => 'adminTearchCreate', 'uses' => 'HihoEdu\Controller\Admin\TeacherController@getCreate'));
    Route::post('/teachers/create', array('as' => 'adminTearchCreatePost', 'uses' => 'HihoEdu\Controller\Admin\TeacherController@postCreate'));
    Route::get('/teachers/destroy/{id}', array('as' => 'adminTeacherDelete', 'uses' => 'HihoEdu\Controller\Admin\TeacherController@getDestroy'))->where('id', '[\d]+');;
    Route::get('/teachers/modify/{id}', array('as' => 'adminTeacherModify', 'uses' => 'HihoEdu\Controller\Admin\TeacherController@modify'))->where('id', '[\d]+');;
    Route::post('/teachers/modify/', array('as' => 'adminTeacherModifyAction', 'uses' => 'HihoEdu\Controller\Admin\TeacherController@modifyPost'));
    Route::get('/teachers/getSubDepartment', array('as' => 'adminTeacherGetSub', 'uses' => 'HihoEdu\Controller\Admin\TeacherController@getSubDepartment'));
    Route::get('/teachers/getDepartmentTeacher', array('as' => 'adminDepartmentTeacher', 'uses' => 'HihoEdu\Controller\Admin\TeacherController@getDepartmentTeacher'));
    Route::any('/teachers/recommend', array('as' => 'adminTeacherRecommend', 'uses' => 'HihoEdu\Controller\Admin\TeacherController@indexRecommend'));

    //favorite
    //Route::get('/favorites/index', array('as' => 'adminFavoriteList', 'uses' => 'HihoEdu\Controller\Admin\FavoriteController@index'));

    //system
    Route::get('/system/index', array('as' => 'adminSystemSetting', 'uses' => 'HihoEdu\Controller\Admin\SystemController@index'));
    Route::post('/setting/website', array('as' => 'adminUpdateWebSetting', 'uses' => 'HihoEdu\Controller\Admin\SystemController@webSetting'));
    Route::post('/setting/register', array('as' => 'adminRegisterSetting', 'uses' => 'HihoEdu\Controller\Admin\SystemController@registerSetting'));
    Route::post('/setting/email', array('as' => 'adminEmailSetting', 'uses' => 'HihoEdu\Controller\Admin\SystemController@emailSetting'));
    Route::post('/setting/school', array('as' => 'adminSchoolSetting', 'uses' => 'HihoEdu\Controller\Admin\SystemController@schoolSetting'));
    Route::post('/setting/message', array('as' => 'adminMsgSetting', 'uses' => 'HihoEdu\Controller\Admin\SystemController@messageSetting'));

    //推荐位
    Route::get('/positions/recommend/index', array('as' => 'adminPositionIndex', 'uses' => 'HihoEdu\Controller\Admin\PositionController@recommendIndex'));
    Route::any('/positions/recommend/create', array('as' => 'adminPositionCreate', 'uses' => 'HihoEdu\Controller\Admin\PositionController@recommendCreate'));
    Route::any('/position/recommend/modify/{id}', array('as' => 'adminPositionRecommendModify', 'uses' => 'HihoEdu\Controller\Admin\PositionController@recommendModify'));
    Route::get('/position/recommend/destroy/{id}', array('as' => 'adminPositionRecommendDestroy', 'uses' => 'HihoEdu\Controller\Admin\PositionController@recommendDestroy'));
    Route::get('/position/recommend/{id}/showRecommends', array('as' => 'adminPositionRecommendDetail', 'uses' => 'HihoEdu\Controller\Admin\PositionController@showRecommends'));

    //推荐
    Route::get('/recommend/teacher/list', array('as' => 'adminRecommendTeacherList', 'uses' => 'HihoEdu\Controller\Admin\RecommendController@teacherIndex'));
    Route::get('/recommend/video/list', array('as' => 'adminRecommendVideoList', 'uses' => 'HihoEdu\Controller\Admin\RecommendController@videoIndex'));
    Route::post('/recommend/create', array('as' => 'adminRecommendCreate', 'uses' => 'HihoEdu\Controller\Admin\RecommendController@create'));
    Route::get('/recommend/unRecommend/{id}', array('as' => 'adminNoRecommend', 'uses' => 'HihoEdu\Controller\Admin\RecommendController@unRecommend'));

    //广告位

    Route::get('/positions/advertisement/index', array('as' => 'adminPositionAd', 'uses' => 'HihoEdu\Controller\Admin\PositionController@advertisementIndex'));
    Route::any('/positions/advertisement/create', array('as' => 'adminPositionAdCreate', 'uses' => 'HihoEdu\Controller\Admin\PositionController@advertisementCreate'));
    Route::any('/positions/advertisement/modify/{id}', array('as' => 'adminPositionAdModify', 'uses' => 'HihoEdu\Controller\Admin\PositionController@advertisementModify'));
    Route::get('/positions/advertisement/status/{id}', array('as' => 'adminPositionAdStatus', 'uses' => 'HihoEdu\Controller\Admin\PositionController@advertisementStatus'));
    Route::get('/positions/advertisement/destroy/{id}', array('as' => 'adminPositionAdDestroy', 'uses' => 'HihoEdu\Controller\Admin\PositionController@advertisementDestroy'));

    Route::get('/positions/advertisement/{id}/showAds', array('as' => 'adminPositionAds', 'uses' => 'HihoEdu\Controller\Admin\PositionController@showAds'));

    //广告
    Route::any('/advertisement/{position_id}/create', array('as' => 'adminAdCreate', 'uses' => 'HihoEdu\Controller\Admin\AdvertisementController@create'));
    Route::get('/advertisement/destroy/{id}', array('as' => 'adminAdDestroy', 'uses' => 'HihoEdu\Controller\Admin\AdvertisementController@destroy'));
    Route::any('/advertisement/modify/{id?}', array('as' => 'adminAdModify', 'uses' => 'HihoEdu\Controller\Admin\AdvertisementController@modify'));
    Route::get('/advertisement/status/{id}', array('as' => 'adminAdStatus', 'uses' => 'HihoEdu\Controller\Admin\AdvertisementController@status'));
    Route::post('/advertisement/sort/{position_id}', array('as' => 'adminAdSort', 'uses' => 'HihoEdu\Controller\Admin\AdvertisementController@sort'));

    //结构化课程
    ##视频重点片段（ajax）
    Route::any('/course/highlightsCreate', array('as' => 'adminHighlightCreate', 'uses' => 'HihoEdu\Controller\Admin\CourseController@highlightCreate'));
    Route::any('/course/highlight/detail', array('as' => 'adminHighlightDetail', 'uses' => 'HihoEdu\Controller\Admin\CourseController@highlightDetail'));
    Route::any('/course/highlightsDestroy', array('as' => 'adminHighlightDestroy', 'uses' => 'HihoEdu\Controller\Admin\CourseController@highlightDestroy'));
    Route::any('/course/highlightsModify', array('as' => 'adminHighlightModify', 'uses' => 'HihoEdu\Controller\Admin\CourseController@highlightModify'));

    ##添加问题（ajax）
    Route::any('/course/questionCreate', array('as' => 'adminQuestionCreate', 'uses' => 'HihoEdu\Controller\Admin\CourseController@questionCreate'));
    Route::get('/course/question/detail', array('as' => 'adminQuestionDetail', 'uses' => 'HihoEdu\Controller\Admin\CourseController@questionDetail'));
    Route::any('/course/questionDestroy', array('as' => 'adminQuestionDestroy', 'uses' => 'HihoEdu\Controller\Admin\CourseController@questionsDestroy'));
    Route::any('/course/questionModify', array('as' => 'adminQuestionModify', 'uses' => 'HihoEdu\Controller\Admin\CourseController@questionsModify'));

    ##视频注释(ajax)
    Route::any('/course/annotationCreate', array('as' => 'adminAnnotationCreate', 'uses' => 'HihoEdu\Controller\Admin\CourseController@annotationCreate'));
    Route::get('/course/annotation/detail', array('as' => 'adminAnnotationDetail', 'uses' => 'HihoEdu\Controller\Admin\CourseController@annotationDetail'));
    Route::any('/course/annotationDestroy', array('as' => 'adminAnnotationDestroy', 'uses' => 'HihoEdu\Controller\Admin\CourseController@annotationDestroy'));
    Route::any('/course/annotationModify', array('as' => 'adminAnnotationModify', 'uses' => 'HihoEdu\Controller\Admin\CourseController@annotationModify'));


    ##视频附录
    Route::get('/course/appendixs/create/{id}', array('as' => 'adminAppendixsGetCreate', 'uses' => 'HihoEdu\Controller\Admin\CourseController@appendixsCreate'));
    Route::post('/course/appendixs/postCreate', array('as' => 'adminAppendixsCreate', 'uses' => 'HihoEdu\Controller\Admin\CourseController@postAppendixsCreate'));
    Route::any('/imgUpload',array('as'=>'adminImgUpload','uses'=>'HihoEdu\Controller\Admin\CourseController@imgUpload'));

    ##问题和重点
    Route::get('/course/questionsHighlights/{id}', array('as' => 'adminQuestionHighLight', 'uses' => 'HihoEdu\Controller\Admin\CourseController@questionsHighlights'));
    Route::get('/course/annotationsLinks/{id}', array('as' => 'adminAnnotationsLinks', 'uses' => 'HihoEdu\Controller\Admin\CourseController@annotationsLinks'));

    ##附件
    Route::get('/course/uploadAttachmentShow/{id}', array('as' => 'adminUpdateAttachmentShow', 'uses' => 'HihoEdu\Controller\Admin\CourseController@uploadAttachmentShow'));
    Route::post('/course/uploadAttachment/{id}', array('as' => '', 'uses' => 'HihoEdu\Controller\Admin\CourseController@uploadAttachment'));
    Route::get('/course/attachmentDestroy/{id}', array('as' => 'adminAttachmentDestroy', 'uses' => 'HihoEdu\Controller\Admin\CourseController@attachmentDestroy'));

    //App
    Route::any('/App/download', array('as' => 'adminAppDownload', 'uses' => 'HihoEdu\Controller\Admin\AppController@index'));
});


/**
 * Rest 接口
 */
Route::group(array('prefix' => 'rest'), function () {
    Route::group(array('prefix' => 'v1'), function () {

        Route::any('/', 'HiHo\Edu\Controller\Rest\BaseController@index');

        # 系统
        Route::controller('system', 'HiHo\Edu\Controller\Rest\SystemController');

        # 用户登录、注册、Token
        Route::any('/passport/login', 'HiHo\Edu\Controller\Rest\PassportController@login');
        Route::any('/passport/register', 'HiHo\Edu\Controller\Rest\PassportController@register');
        Route::any('/passport/showMyProfile', 'HiHo\Edu\Controller\Rest\PassportController@showMyProfile');
        Route::any('/passport/showProfile', 'HiHo\Edu\Controller\Rest\PassportController@showProfile');
        Route::any('/passport/logout', 'HiHo\Edu\Controller\Rest\PassportController@logout');
        Route::any('/passport/verifyEmail', 'HiHo\Edu\Controller\Rest\PassportController@verifyEmail');
        Route::any('/passport/queryTokenInfo', 'HiHo\Edu\Controller\Rest\PassportController@queryTokenInfo');
        Route::any('/passport/modify', 'HiHo\Edu\Controller\Rest\PassportController@modify');
        Route::any('/passport/passwordRest', 'HiHo\Edu\Controller\Rest\PassportController@modifyPassword');

        # 讲师
        Route::any('/teacher/index', 'HiHo\Edu\Controller\Rest\TeacherController@getIndex');

        # 院系机构
        Route::any('/department/index', 'HiHo\Edu\Controller\Rest\DepartmentController@getIndex');

        # 笔记
        Route::any('playlist', 'HiHo\Edu\Controller\Rest\PlaylistController@getIndex');
        Route::any('playlist/index', 'HiHo\Edu\Controller\Rest\PlaylistController@getIndex');
        Route::any('playlist/show', 'HiHo\Edu\Controller\Rest\PlaylistController@getFragments');
        Route::any('playlist/destroy', 'HiHo\Edu\Controller\Rest\PlaylistController@postDestroy');

        # 视频, 参照 HiHo
        Route::get('/video', 'HiHo\Edu\Controller\Rest\VideoController@getIndex');
        Route::get('/video/index', 'HiHo\Edu\Controller\Rest\VideoController@getIndex');
        Route::any('/video/show', 'HiHo\Edu\Controller\Rest\VideoController@getShow');
        Route::get('/video/search', 'HiHo\Edu\Controller\Rest\VideoController@getSearch');
        Route::any('/video/searchSubtitleResultWithKeywordsAndGuid', 'HiHo\Edu\Controller\Rest\VideoController@getSearchSubtitleResultWithKeywordsAndGuid');

        # 碎片, 参照 HiHo
        Route::any('/fragment', 'HiHo\Edu\Controller\Rest\FragmentController@getIndexV2');
        Route::any('/fragment/indexV2', 'HiHo\Edu\Controller\Rest\FragmentController@getIndexV2');
        Route::any('/fragment/show', 'HiHo\Edu\Controller\Rest\FragmentController@getShow');
        Route::any('/fragment/showWithVideoGuid', 'HiHo\Edu\Controller\Rest\FragmentController@getShowWithVideoGuid');
        Route::any('/fragment/showWithGuid', 'HiHo\Edu\Controller\Rest\FragmentController@getShowWithGuid');
        Route::any('/fragment/getPictureWithGuid', 'HiHo\Edu\Controller\Rest\FragmentController@getPictureWithGuid');
        Route::any('/fragment/destroy', 'HiHo\Edu\Controller\Rest\FragmentController@postDestroy');

        # 主题 topic
        Route::any('topics/index', 'HiHo\Edu\Controller\Rest\TopicController@getIndex');

        ##专业
        Route::any('specialities/index','HiHo\Edu\Controller\Rest\SpecialityController@getIndex');

        # 收藏, 参照 HiHo
        Route::controller('favorite', 'HiHo\Edu\Controller\Rest\FavoriteController');

        # 评论
        Route::any('/comment/index', 'HiHo\Edu\Controller\Rest\CommentController@index');
        Route::any('/comment/create', 'HiHo\Edu\Controller\Rest\CommentController@addComment');
        Route::any('/comment/destroy', 'HiHo\Edu\Controller\Rest\CommentController@delete');
        Route::any('/comment/show', 'HiHo\Edu\Controller\Rest\CommentController@detail');
        Route::any('/comment/modify', 'HiHo\Edu\Controller\Rest\CommentController@modify');

        # 分类
        Route::controller('category', 'HiHo\Edu\Controller\Rest\CategoryController');

        # 关键词
        Route::controller('keyword', 'HiHo\Edu\Controller\Rest\KeywordController');

        # 国家
        Route::controller('country', 'HiHo\Edu\Controller\Rest\CountryController');

        # 语言
        Route::controller('language', 'HiHo\Edu\Controller\Rest\LanguageController');

        # 时区
        Route::controller('timezone', 'HiHo\Edu\Controller\Rest\TimezoneController');
    });
});

/**
 * Push 接口
 */
Route::group(array('prefix' => 'push'), function () {
    Route::group(array('prefix' => 'v1'), function () {
        Route::any('/', 'HiHo\Controller\Rest\BaseController@index');

        Route::any('/video', 'HiHo\Controller\Rest\BaseController@index');

        Route::any('/video/create', 'HiHo\Controller\Push\VideoController@create');
        Route::any('/video/modify', 'HiHo\Controller\Push\VideoController@modify');
        Route::any('/video/delete', 'HiHo\Controller\Push\VideoController@delete');
        Route::any('/video/dojson', 'HiHo\Controller\Push\VideoController@dojson');

        Route::any('/tvs/create', 'HiHo\Controller\Push\TvInfoController@getData');

    });
});