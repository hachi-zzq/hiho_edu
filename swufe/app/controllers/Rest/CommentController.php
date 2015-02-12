<?php namespace HiHo\Edu\Controller\Rest;

/**
 * RestAPI 评论
 * @package news.hiho.com
 * @subpackage restapi
 * @version 1.0
 * @copyright AUTOTIMING INC PTE. LTD.
 * @author Luyu<luyu.zhang@autotiming.com>
 */
class CommentController extends BaseController
{

    /**
     * @return string
     * #查询所有评论。支持对视频和碎片刷选
     */
    public function index()
    {
        $inputData = \Input::only('video_id', 'user_id', 'fragment_id');
        $rules = array(
            'video_id' => array(),
            'user_id' => array(),
            'fragment_id' => array(),
        );
        $v = \Validator::make($inputData, $rules);

        ##check params
        if ($v->fails()) {
            $messages = $v->messages();
            return $this->encodeResult('20002', 'The input parameter is not correct.', array('requiredFields' => $messages->first()));
        }
        $where = '';
        if ($inputData['video_id']) {
            $where .= ' and video_id = ' . $inputData['video_id'];
        }

        if ($inputData['user_id']) {
            $where .= ' and user_id = ' . $inputData['user_id'];
        }

        if ($inputData['fragment_id']) {
            $where .= ' and fragment_id = ' . $inputData['fragment_id'];
        }


        $objComment = \Comment::whereRaw("1=1 $where")->get();
        return $this->encodeResult('11100', 'success', $objComment->toArray());
    }

    /**
     * return string
     * ##添加评论
     */
    public function addComment()
    {
        $inputData = \Input::only('token', 'video_id', 'content', 'fragment_id', 'reply_id', 'play_time');
        $rules = array(
            'token' => 'required',
            'video_id' => 'required',
            'content' => 'required'
        );
        $validator = \Validator::make($inputData, $rules);
        if ($validator->fails()) {
            $messages = $validator->messages();
            return $this->encodeResult('20002', 'The input parameter is not correct.', array('requiredFields' => $messages->first()));
        }
        ##check token
        $userId = $this->verifyToken($inputData['token']);
        if (!$userId) {
            return $this->encodeResult('20100', 'Token validation fails.');
        }

        ##check video
        if (!\Video::find($inputData['video_id'])) {
            return $this->encodeResult('21101', 'video is not exist');
        }
        ##check fragment
        if ($inputData['fragment_id'] && (!\Fragment::find())) {
            return $this->encodeResult('21102', 'fragment is not exist');
        }
        $objCommnet = new \Comment();
        $objCommnet->guid = \Uuid::v4();
        $objCommnet->user_id = $userId;
        $objCommnet->video_id = $inputData['video_id'];
        $objCommnet->fragment_id = $inputData['fragment_id'] ? $inputData['fragment_id'] : NULL;
        $objCommnet->playing_time = $inputData['play_time'] ? $inputData['play_time'] : NULL;
        $objCommnet->content = addslashes($inputData['content']);
        $objCommnet->reply_id = $inputData['reply_id'] ? $inputData['reply_id'] : NULL;
        $objCommnet->save();
        return $this->encodeResult('11102', 'success', array('comment_id' => $objCommnet->id));

    }

    /**
     * return string
     * #删除评论
     */
    public function delete()
    {
        $inputData = \Input::only('token', 'id');
        $rules = array(
            'token' => 'required',
            'id' => 'required'
        );
        ##check params
        $validator = \Validator::make($inputData, $rules);
        if ($validator->fails()) {
            $messages = $validator->messages();
            return $this->encodeResult('20002', 'The input parameter is not correct.', array('requiredFields' => $messages->first()));
        }

        ##check token
        $userId = $this->verifyToken($inputData['token']);
        if (!$userId) {
            return $this->encodeResult('20100', 'Token validation fails.');
        }

        ##check comment exist
        if (!$objComment = \Comment::find($inputData['id'])) {
            return $this->encodeResult('21107', 'the comment not exist');
        }

        ##check the comment is yous??
        if ($objComment->user_id != $userId) {
            return $this->encodeResult('21108', 'Permission denied');
        }
        ##all pass
        $objComment->delete();
        return $this->encodeResult('11103', 'comment delete success');

    }


    /**
     * @return string
     * #评论的详细信息
     */
    public function detail()
    {
        $inputData = \Input::only('comment_id');
        $rules = array(
            'comment_id' => 'required'
        );
        ##check params
        $validator = \Validator::make($inputData, $rules);
        if ($validator->fails()) {
            $messages = $validator->messages();
            return $this->encodeResult('20002', 'The input parameter is not correct.', array('requiredFields' => $messages->first()));
        }

        ##check comment exist
        if (!$objComment = \Comment::find($inputData['comment_id'])) {
            return $this->encodeResult('21109', 'the comment not exist');
        }
        ## all pass
        return $this->encodeResult('11104', 'success', $objComment->toArray());

    }

    /**修改评论内容
     * @return string
     */
    public function modify()
    {
        $inputData = \Input::only('token', 'comment_id', 'content');
        $rules = array(
            'token' => 'required',
            'comment_id' => 'required',
            'content' => 'required'
        );
        ##check params
        $validator = \Validator::make($inputData, $rules);
        if ($validator->fails()) {
            $messages = $validator->messages();
            return $this->encodeResult('20002', 'The input parameter is not correct.', array('requiredFields' => $messages->first()));
        }

        ##check token
        $userId = $this->verifyToken($inputData['token']);
        if (!$userId) {
            return $this->encodeResult('20100', 'Token validation fails.');
        }

        ##check comment exist
        if (!$objComment = \Comment::find($inputData['comment_id'])) {
            return $this->encodeResult('21110', 'the comment not exist');
        }

        ##check the comment is yous??
        if ($objComment->user_id != $userId) {
            return $this->encodeResult('21111', 'Permission denied');
        }
        $objComment->content = $inputData['content'];
        return $this->encodeResult('11105', 'success', $objComment->toArray());
    }
}