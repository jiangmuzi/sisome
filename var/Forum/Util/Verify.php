<?php
// +----------------------------------------------------------------------
// | SISOME 验证系统
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.sisome.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 绛木子 <master@lixianhua.com>
// +----------------------------------------------------------------------
class Forum_Util_Verify extends Typecho_Widget{

    protected $options;
    protected $db;
    
    private $data = array();
    private $_error = '';
    private $_time;
    private $_type = array(
        'reset'=>'重设密码',
        'register'=>'帐号验证',
        'changemail'=>'邮箱验证',
    );
    public function __construct($request, $response, $params = NULL){
        parent::__construct($request, $response, $params);
        /** 初始化数据库 */
        $this->db = Typecho_Db::get();
        /** 初始化常用组件 */
        $this->options = $this->widget('Widget_Options');
        
        $this->_time = new Typecho_Date($this->options->gmtTime);
        
    }
    public function execute(){}
    
    public function setParams($params){
        $this->parameter->setDefault($params);
        if(!isset($this->_type[$this->parameter->type])){
            throw new Typecho_Widget_Exception(_t('验证方式不存在'), 404);
        }
        return $this;
    }
    
    private function setBody(){
        if(empty($this->parameter->confirm)){
            throw new Typecho_Widget_Exception(_t('收件人不存在'), 404);
        }
        $this->parameter->siteTitle = $this->options->title;
        $this->parameter->token = strtolower(Typecho_Common::randString(8));
        $this->parameter->subject = $this->_type[$this->parameter->type];
        switch ($this->parameter->type){
            case 'reset':
                $url = '/forgot';
                break;
            case 'register':
                $url = '/activate';
                break;
            default :
                $url = '';
                break;
        }
        if(!empty($url)){
            $this->parameter->url = Typecho_Common::url($url.'?token='.$this->parameter->token, $this->options->index);
        }else{
            $this->parameter->url = $this->parameter->token;
        }
        $this->parameter->body = $this->parseBody();
        return $this;
    }
    
    public function send(){
        $this->setBody();
        
        $data = array(
            'uid'=>$this->parameter->uid,
            'mail'=>$this->parameter->confirm,
            'name'=>$this->parameter->name,
            'subject'=>$this->parameter->subject,
            'body'=>$this->parameter->body
        );
        
        Forum_Mail::asyncSendMail($data);
        $this->insertData();
    }
    
    public function check($token){
        $this->parameter->token = $token;
        $verifies = $this->getData();
        if($verifies && !$verifies['status']){
            $this->db->query($this->db
                ->update('table.verifies')
                ->rows(array('status' => 1))
                ->where('id = ?', $verifies['id']));
        }
        return $verifies;
    }
    
    public function delete($token){
        return $this->db->query($this->db
            ->delete('table.verifies')
            ->where('type = ?',$this->parameter->type)
            ->where('token = ?', $token));
    }
    
    private function parseBody(){
        $tpl = dirname(__FILE__).'/verify/'.$this->parameter->type.'.html';
        $body = file_get_contents($tpl);
        $result = array();
        preg_match_all("/(?:\{)(.*)(?:\})/i",$body, $result);
        $search = $replace = array();
        foreach ($result[0] as $k=>$v){
            $search[] = $v;
            $replace[] = isset($this->parameter->{$result[1][$k]}) ? $this->parameter->{$result[1][$k]} : '';
        }
        return str_replace($search, $replace, $body);
    }
    private function insertData(){
        $rows = array(
            'uid'=>$this->parameter->uid,
            'type'=>$this->parameter->type,
            'token'=>$this->parameter->token,
            'confirm'=>$this->parameter->confirm,
            'created'=>$this->_time->timeStamp,
            'status'=>0
        );
        return $this->db->query($this->db->insert('table.verifies')->rows($rows));
    }
    
    private function getData(){
        $select = $this->db->select()
        ->from('table.verifies')
        ->where('type = ?', $this->parameter->type)
        ->where('token = ?', $this->parameter->token)
        ->limit(1);
        
        return $this->db->fetchRow($select);
    }
    
}
