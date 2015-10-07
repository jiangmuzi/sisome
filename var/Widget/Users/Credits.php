<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

// +----------------------------------------------------------------------
// | SISOME 用户积分
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.sisome.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 绛木子 <master@lixianhua.com>
// +----------------------------------------------------------------------

class Widget_Users_Credits extends Widget_Abstract_Credits
{
    /**
     * @return the $_currentPage
     */
    
    public function __construct($request, $response, $params = NULL){
        parent::__construct($request, $response, $params);
    }

    /**
     * 判断是否超出次数
     * @param unknown $uid
     * @param unknown $type
     * @return boolean
     */
    protected function isSetMaxNum($uid,$type){
        if($this->_creditType[$type]['max']==0) return true;
        $date = strtotime(date('Y-m-d'));
        $num = $this->size($this->db->select()->where('type = ? AND uid = ? AND created > ?', $type, $uid, $date));
        return $num < $this->_creditType[$type]['max'] ? true :false;
    }
    
    public function setUserCredits($uid,$type){
        $creditsName = 'credits'.ucfirst($type);
        if(!$this->options->$creditsName){
            return;
        }
        if($this->isSetMaxNum($uid, $type)){
            $data = array(
                'uid'=>$uid,
                'type'=>$type,
                'amount'=>$this->options->$creditsName,
                'created'=>$this->options->gmtTime
            );
            $this->saveCredits($data);
        }
		
		if($type=='invite'){
			$user = $this->widget('Widget_Users_Query@uid_'.$uid,'uid='.$uid);
			if(empty($user->extend) || empty($user->extend['inviter'])){
				return;
			}
			
			
			$inviter = $this->widget('Widget_Users_Query@name_'.$user->extend['inviter'],'name='.$user->extend['inviter']);
			
			if(!$this->isSetMaxNum($inviter->uid, 'inviter')){
				return;
			}
			$data = array(
                'uid'=>$inviter->uid,
                'type'=>'inviter',
                'amount'=>$this->options->$creditsName,
                'created'=>$this->options->gmtTime
            );
			$this->saveCredits($data);
		}
    }

    protected function saveCredits($data = array()){
        $user = $this->getUserCredits($data['uid']);
        $data['balance'] = $user['credits']+$data['amount'];
        $this->insert($data);
        $this->db->query($this->db->update('table.users')->rows(array('credits'=>$data['balance']))->where('uid = ?',$data['uid']));
    }
    
    protected function getUserCredits($uid){
        $user = $this->db->fetchRow($this->db->select('table.users.uid,table.users.credits')
            ->from('table.users')
            ->where('uid = ?', $uid)
            ->limit(1));
        return $user;
    }
    
}
