<?php
// +----------------------------------------------------------------------
// | SISOME 
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://sisome.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 绛木子 <master@lixianhua.com>
// +----------------------------------------------------------------------
class Forum_Action extends Typecho_Widget implements Widget_Interface_Do{
	public function options(){
		$this->widget('Widget_User')->pass('administrator');
		if(!TeForum_Plugin::configForm('update')->validate()){
			$this->widget('Widget_Notice')->set(_t('设置出错'),NULL,'error');
			$this->response->goBack();
		}
		$this->widget('Widget_Notice')->set(_t('设置已保存'),NULL,'success');
		$this->response->goBack();
	}
	//收藏
	public function favorite(){
		$user = $this->widget('Widget_User');
		if(!$user->hasLogin()){
			$this->response->throwJson(array('status'=>0,'msg'=>_t('还未登录不能进行此操作!')));
		}
		$favorites = $this->widget('Forum_Favorites');
		if(!empty($this->request->fid)){
		    $db = Typecho_Db::get();
		    $favorites->delete($db->select()->where('fid = ?',$this->request->fid));
		    $this->response->throwJson(array('status'=>1,'msg'=>_t('已取消收藏!')));
		}
		//数据是否存在
		if($favorites->dataExists()){
			$this->response->throwJson(array('status'=>0,'msg'=>_t('收藏的内容不存在!')));
		}
		//是否已经收藏
		if($favorites->favoriteExists($favorites->parameter->type,$favorites->getSrcId())){
			$this->response->throwJson(array('status'=>0,'msg'=>_t($favorites->getTitle().'已收藏!')));
		}
		$fid = $favorites->addFavorite();
		if($fid){
			$this->response->throwJson(array('status'=>1,'fid'=>$fid,'msg'=>_t($favorites->getTitle().'已成功收藏!')));
		}else{
			$this->response->throwJson(array('status'=>0,'msg'=>_t('收藏出错!')));
		}
	}
	public function loadcomments(){
	    $t = $this->request->filter('int')->t;
	    if(empty($t)){
	        $this->response->throwJson(array('status'=>0));
	    }
	    $comments = $this->widget('Forum_List_Comment','desc=0&limit=1&last='.$t);
	    $data = array();
	    if($comments->have()){
	        while ($comments->next()){
	            $data[] = array(
	                'permalink'=>$comments->permalink,
	                'authorAvatar'=>$comments->poster->avatar,
	                'authorName'=>$comments->poster->name,
	                'content'=>Typecho_Common::subStr(strip_tags($comments->content), 0, 35)
	            );
	        }
	    }
	    if(empty($data)){
	        $this->response->throwJson(array('status'=>0));
	    }
	    $this->response->throwJson(array('status'=>1,'data'=>$data,'last'=>$comments->created));
	}
	public function sendMail(){
	    $name = $this->request->get('name');
	    if(!empty($name)){
	        Forum_Mail::send($name);
	    }
	}
    /**
     * 绑定动作
     *
     * @access public
     * @return void
     */
    public function action(){
		$this->on($this->request->is('do=sendmail'))->sendMail();
        $this->on($this->request->is('do=favorite'))->favorite();
        $this->on($this->request->is('do=loadcomments'))->loadcomments();
    }
}