<?php
// +----------------------------------------------------------------------
// | SISOME SNS授权
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.sisome.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 绛木子 <master@lixianhua.com>
// +----------------------------------------------------------------------
class Forum_Oauth extends Widget_Abstract_Users{

	private $current;    //当前所处页面
    
	private $auth;
	
	private $sns = array(
        'qq'=>'QQ',
        'weibo'=>'新浪微博',
        'wechat'=>'微信',
        'douban'=>'豆瓣',
        'baidu'=>'百度',
        'renren'=>'人人网',
        'kaixin'=>'开心网',
        'taobao'=>'淘宝网',
        'google'=>'Google',
        'github'=>'GitHub',
    );
	
    public function __construct($request, $response, $params = NULL){
        parent::__construct($request, $response, $params);
    }
    /**
     * 构造oauth链接
     */
    public function oauth(){
        $this->auth['type'] = $this->request->get('type');
        $this->auth['code'] = $this->request->get('code');
        if(!empty($this->auth['code'])){
            $this->callback();
        }else{
            if(is_null($this->auth['type'])){
                $this->widget('Widget_Notice')->set(array('请选择登录方式!'),'error');
                $this->response->redirect($this->___loginUrl());
            }
            
            //是否允许使用
            if(!$this->allowConnect($this->auth['type'])){
                $this->widget('Widget_Notice')->set(array('暂不支持该登录方式!'),'error');
                $this->response->goBack();
            }
            
            $callback_url = Typecho_Common::url('/user/oauth?type='.$this->auth['type'], $this->options->index);
            
            require_once 'Connect.php';
            $this->response->redirect(Connect::getLoginUrl($this->auth['type'], $callback_url));
        }
    }
    /**
     * 处理callback
     */
    protected function callback(){
        //不在开启的登陆方式内直接返回
        if(!$this->allowConnect($this->auth['type'])){
            $this->response->redirect(Typecho_Common::url('/login', $this->options->index));
        }
        if(empty($this->auth['code'])){
            $this->response->redirect($this->options->index);
        }
        
        $callback_url = Typecho_Common::url('/user/oauth?type='.$this->auth['type'], $this->options->index);
        
        $this->auth['openid'] = '';
        
        require_once 'Connect.php';
        //换取access_token
        $this->auth['token'] = Connect::getToken($this->auth['type'], $callback_url, $this->auth['code']);
        
        if(empty($this->auth['token'])){
            $this->response->redirect($this->options->index);
        }
        
        //获取openid
        $this->auth['openid'] = Connect::getOpenId($this->auth['type']);
        
        if(empty($this->auth['openid'])){
            $this->response->redirect($this->options->index);
        }
        
        //使用openid登录
        $this->autoLogin($this->auth['openid'],$this->auth['type']);
        
        //获取用户昵称
        $this->auth['nickname'] = Connect::getNickName($this->auth['type'], $this->auth['openid']);
        
        if(empty($this->auth['nickname'])){
            $this->auth['nickname'] = '关注者'.substr(str_shuffle($this->auth['openid']),0,4);
        }
        Typecho_Cookie::set('__user_auth', serialize($this->auth));
        $this->response->redirect($this->___bindUrl());
    }
    //帐号绑定、解绑
    public function bind(){
        $type = $this->request->get('type');
        if(!empty($type) && $this->user->hasLogin()){
            $this->unbind($type);exit;
        }
        $this->current = 'bind';
        $this->auth = Typecho_Cookie::get('__user_auth');
        $this->auth = unserialize($this->auth);
        if(empty($this->auth['openid']) || empty($this->auth['type'])){
            $this->widget('Widget_Notice')->set(array('请重新登录!'),'error');
            $this->response->redirect($this->___loginUrl());
        }
        if($this->request->isPost()){
            $do = $this->request->get('do');
            if($do=='register'){
                $this->doBindRegister();
            }elseif($do=='login'){
                $this->doBindLogin();
            }else{
                Typecho_Cookie::delete('__user_auth');
                $this->widget('Widget_Notice')->set(array('请重新登录!'),'error');
                $this->response->redirect($this->___loginUrl());
            }
        }else{
            $this->_title = '绑定帐号';
            $this->render('user/bind.php');
        }
    }
    //绑定已有用户
    protected function doBindLogin(){
        
        /** 初始化验证类 */
        $validator = new Typecho_Validate();
        $validator->addRule('name', 'required', _t('请输入用户名'));
        $validator->addRule('password', 'required', _t('请输入密码'));
        
        /** 截获验证异常 */
        if ($error = $validator->run($this->request->from('name', 'password'))) {
            /** 设置提示信息 */
            $this->widget('Widget_Notice')->set($error,'error');
            $this->response->goBack();
        }
        
        $name = $this->request->get('name');
        $password = $this->request->get('password');
        /** 开始验证用户 **/
        $valid = $this->user->login($this->request->name, $this->request->password,
            false, $this->options->gmtTime + $this->options->timezone + 30*24*3600);
        if(!$valid){
            $this->widget('Widget_Notice')->set(_t('用户名或密码无效'), 'error');
            $this->response->goBack();
        }
        $this->bindAuthUser($this->auth['openid'],$this->auth['type'],$this->user->uid);
        $this->widget('Widget_Notice')->set(_t('成功绑定!'));
        Typecho_Cookie::delete('__user_auth');
        $this->response->redirect($this->options->index);
    }
    //绑定新用户
    protected function doBindRegister(){
        
        $validator = new Typecho_Validate();
        $validator->addRule('mail', 'required', _t('必须填写电子邮箱'));
        $validator->addRule('mail', array($this, 'mailExists'), _t('电子邮箱地址已经存在'));
        $validator->addRule('mail', 'email', _t('电子邮箱格式错误'));
        $validator->addRule('mail', 'maxLength', _t('电子邮箱最多包含200个字符'), 200);
        
        $validator->addRule('nickname', 'required', _t('必须填写昵称'));
        $validator->addRule('nickname', 'xssCheck', _t('请不要在昵称中使用特殊字符'));
        $validator->addRule('nickname', array($this, 'screenNameExists'), _t('昵称已经存在'));
        /** 截获验证异常 */
        if ($error = $validator->run($this->request->from('mail', 'nickname'))) {
            /** 设置提示信息 */
            $this->widget('Widget_Notice')->set($error,'error');
            $this->response->goBack();
        }
        $mail = $this->request->get('mail');
        $nickname = $this->request->get('nickname');
        $data = array(
            'mail'=>$mail,
            'screenName'=>$nickname,
            'created'   =>  $this->options->gmtTime,
            'group'     =>  'subscriber',
        );
        
        $uid = $this->insert($data);
        $this->bindAuthUser($this->auth['openid'],$this->auth['type'],$uid);
        Typecho_Cookie::delete('__user_auth');
        $this->autoLogin($this->auth['openid'],$this->auth['type']);
    }
    protected function unbind($type,$uid=null){
        $uid = empty($uid) ? $this->user->uid : $uid;
        $rows = array($type.'Id'=>'');
        $rs = $this->db->query($this->db->update('table.oauth')->rows($rows)->where('uid = ?',$uid));
        if($rs){
            $return = array('status'=>1);
            $this->widget('Widget_Notice')->set(array('已解除绑定!'));
        }else{
            $return = array('status'=>0);
            $this->widget('Widget_Notice')->set(array('解除绑定出错，请重试!'),'error');
        }
        $this->response->throwJson($return);
    }
    //自动登录
    protected function autoLogin($openid,$type){
        //查找绑定的用户
        $user = $this->findAuthUser($openid,$type);
    
        /** 如果已经登录 */
        if ($this->user->hasLogin()) {
            //已经绑定
            if(isset($user['uid']) && $user['uid']){
                $this->widget('Widget_Notice')->set(array('已绑定账号，不需要重复绑定!'));
                $this->response->redirect($this->options->index);
            }
            //绑定用户
            $this->bindAuthUser($openid,$type,$this->user->uid);
            //提示绑定成功，并跳转
            // add 跳转提示
            $this->widget('Widget_Notice')->set(array('成功绑定账号!'));
            $this->response->redirect($this->options->index);
            //未登录
        }else{
            if(isset($user['uid']) && $user['uid']){
                //已经绑定，直接登陆并跳转
                $this->authLogin($user['uid']);
                // add 跳转提示
                $this->widget('Widget_Notice')->set(array('已成功登陆!'));
                $this->response->redirect($this->options->index);
            }
        }
    }
    //绑定用户
    protected function bindAuthUser($openid,$type,$uid){
        $rows = array($type.'Id'=>$openid);
        //是否已经存在
        $user = $this->db->fetchRow($this->db->select()
            ->from('table.oauth')
            ->where('uid = ?', $uid)
            ->limit(1));
        if(empty($user)){
            $rows['uid'] = $uid;
            return $this->db->query($this->db->insert('table.oauth')->rows($rows));
        }else{
            return $this->db->query($this->db->update('table.oauth')->rows($rows)->where('uid = ?',$uid));
        }
    }
    //查找已绑定用户
    protected function findAuthUser($openid,$type){
        if(empty($openid)) return 0;
        $user = $this->db->fetchRow($this->db->select()
            ->from('table.oauth')
            ->where($type.'Id = ?', $openid)
            ->limit(1));
    
        return empty($user)? 0 : $user;
    }
    //绑定用户登录
    protected function authLogin($uid,$expire = 0){
        $authCode = function_exists('openssl_random_pseudo_bytes') ?
        bin2hex(openssl_random_pseudo_bytes(16)) : sha1(Typecho_Common::randString(20));
    
        Typecho_Cookie::set('__typecho_uid', $uid, $expire);
        Typecho_Cookie::set('__typecho_authCode', Typecho_Common::hash($authCode), $expire);
    
        //更新最后登录时间以及验证码
        $this->db->query($this->db
            ->update('table.users')
            ->expression('logged', 'activated')
            ->rows(array('authCode' => $authCode))
            ->where('uid = ?', $uid));
    
    }
    //允许使用的登录方式
    protected function allowConnect($type){
        if(!empty($type) && !empty($this->options->$type)){
            return true;
        }
        return false;
    }
    public function getSnsOptions(){
        $options = array();
        foreach($this->sns as $type=>$title){
            if(!empty($this->options->$type)){
                $tmp = explode(',',$this->options->$type);
                if(isset($tmp[1])){
                    $options[$type]=array('id'=>$tmp[0],'key'=>$tmp[1]);
                }  
            }
        }
        return $options;
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
    public function parseActiveSns($parse='<a class="btn btn-{type}" href="{url}">{title}</a>'){
        $html = '';
        foreach($this->sns as $type=>$title){
            if(!empty($this->options->$type)){
                $url = Typecho_Common::url('?type='.$type, $this->___oauthUrl());
                $html .= str_replace(array('{type}','{title}','{url}'), 
                        array($type,$title,$url), $parse);
            }
        }
        echo $html;
    }

    /**
     * 判断当前位置
     * @param stirng $user
     * @param string $current
     * @return boolean
     */
    public function is($widget,$current=null){
        return (($widget=='oauth') && (empty($current)? true : (strtolower($current)==$this->current))) ? true : false;
    }
}