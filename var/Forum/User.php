<?php
// +----------------------------------------------------------------------
// | SISOME 社区用户
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.sisome.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 绛木子 <master@lixianhua.com>
// +----------------------------------------------------------------------
class Forum_User extends Widget_Abstract_Users{
    
	public $_user;
    public $data;
    public function __construct($request, $response, $params = NULL){
        parent::__construct($request, $response, $params);
        $this->parameter->setDefault(array(
            'type'              =>  NULL,
        ));
        
        /** 用于判断是路由调用还是外部调用 */
        if (NULL == $this->parameter->type) {
            $this->parameter->type = Typecho_Router::$current;
        } else {
            $this->_invokeFromOutside = true;
        }
    }
    
    public function execute(){
        $this->initGuestUser();
    }
    public function getUser(){
        return $this->_user;
    }
    /**
     * 初始化函数
     *
     * @access public
     * @return void
     */
    public function indexHandle(){
        $this->current = 'index';
        $this->_metaTitle = $this->_user->screenName;
        $this->render('user/ucenter.php');
    }
    public function favoriteHandle(){
        $this->current = $this->parameter->type;
        $this->_metaTitle = '我收藏的'.($this->parameter->type == 'favorite_nodes' ? '节点' : '主题');

        $this->render('user/'.$this->current.'.php');
    }
    public function activateHandle(){
        $this->_metaTitle = '激活邮箱';
        $token = $this->request->get('token');
        if(empty($token)){
            $this->widget('Widget_Archive@404', 'type=404')->render();
            exit;
        }
        $verify = $this->widget('Forum_Util_Verify')->setParams('type=register')->check($token);
        if(!empty($verify)){
            $row['group'] = 'contributor';
            $this->db->query($this->db
                ->update('table.users')
                ->rows($row)
                ->where('uid = ?', $verify['uid']));
            $verify = $this->widget('Forum_Util_Verify')->setParams('type=register')->delete($token);
        }
        $this->render('user/activate.php');
    }
    
    public function postHandle(){
        $this->_metaTitle = '发布的主题';
        $this->render('user/posts.php');
    }
    
    public function replyHandle(){
        $this->_metaTitle = '发表的回复';
        $this->render('user/replys.php');
    }
    
    public function getActiveAuth(){
        if(!$this->user->hasLogin()){
            return null;
        }
        $auth = $this->db->fetchRow($this->db->select()
            ->from('table.oauth')
            ->where('uid = ?', $this->user->uid)
            ->limit(1));
        if(empty($auth)){
            return null;
        }else{
            $actived =array();
            foreach($auth as $k=>$v){
                if($k!='uid' && !empty($v)){
                    $actived[] = substr($k, 0,-2);
                }
            }
            return $actived;
        }
    }
    /**
     * 
     */
    private function initGuestUser(){
        $this->_name = $this->request->get('u');
        if(empty($this->_user) && !empty($this->_name)){
            $this->_user = $this->widget('Forum_Query_User@name_'.$this->_name,array('name'=>$this->_name));
        }
        if(empty($this->_user)){
            if($this->_invokeFromOutside){
                $this->widget('Widget_Archive@404', 'type=404')->render();
                exit;
            }
            if($this->user->hasLogin()){
                $this->_user = clone $this->user;
            }
        }
    }
    /**
     * 权限
     * @return boolean
     */
    protected function checkAccess(){
        if(!$this->user->hasLogin()){
            $this->response->redirect($this->___loginUrl().'?redir='.$this->request->getRequestUrl());
        }
        
    }
    /**
     * 判断当前位置
     * @param stirng $user
     * @param string $current
     * @return boolean
     */
    public function is($widget,$current=null){
        return ($widget==$this->parameter->type) ? true : false;
    }
}