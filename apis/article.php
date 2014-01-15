<?php
/**
 * 文章详细页
 *
 * Created by Lane.
 * @Class Article
 * @Author: lane
 * @Mail lixuan868686@163.com
 * @Date: 14-1-10
 * @Time: 下午4:22
 */
class Article extends Controller{
	/**
	 * 构造函数
	 */
	public function __construct($param=array()){
		parent::__construct($param);
	}
    /**
     * @descrpition 单篇文章展示
     * @return Ambigous
     */
    public function main(){
    	$articleId = $this->param['aid'];
        if(empty($articleId)){
            return MsgCommon::returnErrMsg(MsgConstant::ERROR_ARTICLE_NOT_EXISTS, '文章ID为空');
        }

        //获取文章信息
        $article = ArticleBusiness::getArticle($articleId);
        $article['author'] = htmlspecialchars_decode($article['author']);
        $article['title'] = htmlspecialchars_decode($article['title']);
        $article['description'] = htmlspecialchars_decode($article['description']);
        $article['ctime'] = date('Y-m-d H:i:s', $article['ctime']);
        $article['tag'] = explode('|', $article['tag']);

        //获取该文章的评论
        $commentList = CommentBusiness::getCommentByAid($this->param['aid']);
        //获取该分类下热门文章
        $articleHotList = ArticleBusiness::getHotListByMid($article['mid']);
        foreach($articleHotList as $k=>$a){
            $articleHotList[$k]['title'] = mb_substr($a['title'], 0, 30, 'UTF-8') . '...';
        }

        //获取该分类下最新评论
        $commentNewList = CommentBusiness::getNewListByMid($article['mid']);
        foreach($commentNewList as $k=>$comment){
            $commentNewList[$k]['content'] = mb_substr($comment['content'], 0, 30, 'UTF-8') . '...';
        }

        View::assign('commentList', $commentList);
        View::assign('commentNewList', $commentNewList);
        View::assign('articleHotList', $articleHotList);
        View::assign('article', $article);
        View::showFrontTpl('article');
    }

    /**
     * @descrpition 添加评论
     */
    public function addcomment(){
        //判断验证码
        $captcha = Request::getSession('captcha');
        if($captcha != strtolower($this->param['captcha'])){
            return MsgCommon::returnErrMsg(MsgConstant::ERROR_CAPTCHA_ERROR, '验证码错误');
        }
        $jumpUrl = GAME_URL . 'article/main/aid-'.$this->param['aid'];

        $fields = array();
        $fields['aid'] = $this->param['aid'];
        $fields['mid'] = $this->param['mid'];
        $fields['nickname'] = $this->param['nickname'];
        $fields['email'] = $this->param['email'];
        $fields['website'] = $this->param['website'];
        $fields['ctime'] = time();
        $fields['content'] = $this->param['content'];
        CommentBusiness::setComment($fields);
    }
}