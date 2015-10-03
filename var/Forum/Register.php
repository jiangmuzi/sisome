<?php
// +----------------------------------------------------------------------
// | SISOME 用户注册
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.sisome.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 绛木子 <master@lixianhua.com>
// +----------------------------------------------------------------------
class Forum_Register extends Widget_Abstract_Users{
    public function __construct($request, $response, $params = NULL){
        parent::__construct($request, $response, $params);
    }
    //用户注册
    public function render(){
		$this->current = 'register';
        // 如果已经登录
        if ($this->user->hasLogin()) {
            // 直接返回首页 
            $this->response->redirect($this->options->index);
        }
        if($this->request->isPost() || $this->request->isAjax()){
            $this->doRegister();
        }else{
            $this->_metaTitle = '注册';
            parent::render('user/register.php');
        }
    }
    protected function doRegister(){
		// protect
        $this->security->protect();

        /** 如果已经登录 */
        if ($this->user->hasLogin()) {
            /** 直接返回 */
            $this->response->redirect($this->options->index);
        }
		/** 如果未开启注册 */
        if (!$this->options->allowRegister) {
            /** 直接返回 */
			$this->widget('Widget_Notice')->set('未开启注册!','error');
            $this->response->redirect($this->options->index);
        }
        //用户操作类
        $users = $this->widget('Widget_Abstract_Users');
        /** 初始化验证类 */
        $validator = new Typecho_Validate();
        $validator->addRule('captcha', 'required', _t('必须填写验证码'));
        $validator->addRule('captcha', array($this, 'checkCaptcha'), _t('验证码错误'));
        $validator->addRule('name', 'required', _t('必须填写用户名称'));
        $validator->addRule('name', 'minLength', _t('用户名至少包含2个字符'), 2);
        $validator->addRule('name', 'maxLength', _t('用户名最多包含32个字符'), 32);
        $validator->addRule('name', 'xssCheck', _t('请不要在用户名中使用特殊字符'));
        $validator->addRule('name', array($users, 'nameExists'), _t('用户名已经存在'));
        $validator->addRule('mail', 'required', _t('必须填写电子邮箱'));
        $validator->addRule('mail', array($users, 'mailExists'), _t('电子邮箱地址已经存在'));
        $validator->addRule('mail', 'email', _t('电子邮箱格式错误'));
        $validator->addRule('mail', 'maxLength', _t('电子邮箱最多包含200个字符'), 200);

        /** 如果请求中有password */
        $validator->addRule('password', 'required', _t('必须填写密码'));
		$validator->addRule('password', 'minLength', _t('为了保证账户安全, 请输入至少六位的密码'), 6);
		$validator->addRule('password', 'maxLength', _t('为了便于记忆, 密码长度请不要超过十八位'), 18);
		$validator->addRule('confirm', 'confirm', _t('两次输入的密码不一致'), 'password');
		
        /** 截获验证异常 */
        if ($error = $validator->run($this->request->from('captcha','name', 'password', 'mail', 'confirm'))) {
            Typecho_Cookie::set('__typecho_remember_name', $this->request->name);
            Typecho_Cookie::set('__typecho_remember_mail', $this->request->mail);
            /** 设置提示信息 */
            $this->widget('Widget_Notice')->set($error,'error');
            $this->response->goBack();
        }

        $hasher = new PasswordHash(8, true);
        //$generatedPassword = Typecho_Common::randString(7);
        $extend = array();
        $inviter = Typecho_Cookie::get('__some_inviter');
        if(!empty($inviter)){
            $extend['inviter'] = $inviter;
        }
        $dataStruct = array(
            'name'      =>  $this->request->name,
            'mail'      =>  $this->request->mail,
            'screenName'=>  $this->request->name,
            'password'  =>  $hasher->HashPassword($this->request->password),
            'created'   =>  $this->options->gmtTime,
            'group'     =>  'subscriber',
            'extend'    =>  empty($extend)?'':serialize($extend),   //扩展字段，存储一些不常用的数据
        );
        
        $insertId = $users->insert($dataStruct);
        //$insertId = 4;
        $this->db->fetchRow($users->select()->where('uid = ?', $insertId)
        ->limit(1), array($users, 'push'));

        $this->user->login($this->request->name, $this->request->password);
 
		$params = array(
            'uid'=>$this->user->uid,
            'confirm'=>$this->user->mail,
            'name'=>$this->user->screenName,
            'type'=>'register'
        );
        //发送验证信息
        Forum_Common::sendVerify($params);
        //注册积分
		Forum_Common::credits('register');
        $this->widget('Widget_Notice')->set(_t('用户 <strong>%s</strong> 已经成功注册,请及时验证邮件', $users->screenName), 'success');
        $this->response->redirect($this->options->index);
    }
    
    public function checkCaptcha($captcha){
        return $this->widget('Forum_Util_Captcha')->check($captcha);
    }
}