<?php namespace HihoEdu\Controller\Admin;

use HihoEdu\Controller\Admin\AdminBaseController;

class FavoriteController extends AdminBaseController
{

    /**
     *
     * @return mixed
     * @author zhuzhengqian
     */
    public function index()
    {
        return \View::make('admin.favorite.index');
    }


}
