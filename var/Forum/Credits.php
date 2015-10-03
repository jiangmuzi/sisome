<?php
// +----------------------------------------------------------------------
// | SISOME 用户积分
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.sisome.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 绛木子 <master@lixianhua.com>
// +----------------------------------------------------------------------
class Forum_Credits extends Forum_Abstract_Credits
{
    
    private $_total = false;
    
    private $_countSql;
    
    private $_pageNav;
    
    private $_currentPage;

    private $_nowTime;
    
    protected $_creditType=array(
        'register'=>array('title'=>'注册用户','credits'=>'2000','max'=>1),
        'login'=>array('title'=>'每日登录','credits'=>'10','max'=>1),
        'post'=>array('title'=>'创建主题','credits'=>'-10','max'=>10),
        'reply'=>array('title'=>'发表回复','credits'=>'15','max'=>15),
        'invite'=>array('title'=>'通过邀请注册','credits'=>'100','max'=>1),
        'inviter'=>array('title'=>'邀请注册','credits'=>'100','max'=>0),
    );
    /**
     * @return the $_currentPage
     */
    public function getCurrentPage()
    {
        return $this->_currentPage;
    }
    /**
     * @return the $_total
     */
    public function getTotal()
    {
        if (false === $this->_total) {
            $this->_total = $this->size($this->_countSql);
        }
    
        return $this->_total;
    }
    /**
     * 获取页数
     *
     * @return integer
     */
    public function getTotalPage(){
        return ceil($this->getTotal() / 10);
    }
    
    
    public function __construct($request, $response, $params = NULL){
        parent::__construct($request, $response, $params);
        if(!$this->user->hasLogin()){
            $this->response->redirect($this->options->someUrl('login',null,false).'?redir='.$this->request->getRequestUrl());
        }
        $this->_nowTime = new Typecho_Date($this->options->gmtTime);
    }
    
    public function execute(){
        $this->_currentPage = $this->request->get('page',1);
        
    }
    
    public function index(){
        $this->setMetaTitle('账户余额');
        if(!$this->have()){
            $this->indexHandle();
        }
        $this->render('user/credits.php');
    }
    
    /**
     * 前一页
     *
     * @access public
     * @param string $word 链接标题
     * @param string $page 页面链接
     * @return void
     */
    public function pageLink($word = '&laquo; Previous Entries', $page = 'prev'){
        if ($this->have()) {
            if (empty($this->_pageNav)) {
                $query = $this->request->makeUriByRequest('page={page}');
                /** 使用盒状分页 */
                $this->_pageNav = new Typecho_Widget_Helper_PageNavigator_Classic($this->getTotal(),
                    $this->_currentPage, 10, $query);
            }
    
            $this->_pageNav->{$page}($word);
        }
    }
    
    protected function indexHandle(){
        $creditsSelect = $this->db->select()
        ->from('table.creditslog')
        ->where('uid = ?', $this->user->uid);
        
        $this->_countSql = clone $creditsSelect;
        
        $creditsSelect->order('created',Typecho_Db::SORT_DESC)
        ->page($this->_currentPage, 10);
        $this->db->fetchAll($creditsSelect, array($this, 'push'));
    }
    
    protected function ___typeWord(){
        return $this->_creditType[$this->type]['title'];
    }
    protected function ___remarkWord(){
        $r = $this->amount>0 ? '奖励' : '扣掉';
        return $this->_creditType[$this->type]['title'].' '.$r;
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
        if($this->isSetMaxNum($uid, $type)){
            $data = array(
                'uid'=>$uid,
                'type'=>$type,
                'amount'=>$this->_creditType[$type]['credits'],
                'created'=>$this->_nowTime->timeStamp
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
