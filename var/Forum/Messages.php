<?php
// +----------------------------------------------------------------------
// | SISOME 用户消息
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.sisome.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 绛木子 <master@lixianhua.com>
// +----------------------------------------------------------------------
class Forum_Messages extends Forum_Abstract_Messages{
    
    
    public function __construct($request, $response, $params = NULL){
        parent::__construct($request, $response, $params);
        if(!$this->user->hasLogin()){
            $this->response->redirect($this->___loginUrl().'?redir='.$this->request->getRequestUrl());
        }
    }
    
    public function execute(){}
    /**
     * 消息提醒
     */
    public function render(){
        //获取未读消息
        $select = $this->select()->where('uid = ?',$this->user->uid);//->where('status = 0');
        $this->db->fetchAll($select->order('created',Typecho_Db::SORT_DESC),
            array($this, 'push')
        );
        //未读标为已读
        if($this->have())
            $this->update(array('status'=>1), $select);
        $this->setMetaTitle('消息提醒');
        parent::render('user/messages.php');
    }
    
    public function addMessage($uid,$srcId,$type='comment'){
        $nowTime = new Typecho_Date($this->options->gmtTime);
        $row = array(
            'uid'=>$uid,
            'type'=>$type,
            'srcId'=>$srcId,
            'created'=>$nowTime->timeStamp,
            'status'=>0
        );
        return $this->insert($row);
    }
}
