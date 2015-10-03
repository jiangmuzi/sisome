<?php
// +----------------------------------------------------------------------
// | SISOME 找回密码
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.sisome.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 绛木子 <master@lixianhua.com>
// +----------------------------------------------------------------------
class Forum_Forgot extends Widget_Abstract_Users{
    
    private $token;

    public function __construct($request, $response, $params = NULL){
        parent::__construct($request, $response, $params);
    }
    
    public function execute(){
        $this->token = $this->request->get('token');
        if(!empty($this->token)){
            $verify = $this->widget('Some_Util_Verify')->setParams('type=reset')->check($this->token);
            if(empty($verify)){
                $this->widget('Widget_Archive@404', 'type=404')->render();
                exit;
            }
            $this->_user = $this->db->fetchRow($this->db->select()
                ->from('table.users')
                ->where('uid = ?', $verify['uid'])
                ->limit(1));
            $this->screenName = $this->_user['name'];
        }
        if($this->request->isPost()){
            $this->security->protect();
            if(!empty($this->token)){
                $this->doReset();
            }else{
                $this->doForgot();
            }
        }
    }
    
    public function render(){
        if(empty($this->token)){
            $this->setMetaTitle('通过电子邮件重设密码');
            if($this->user->hasLogin()){
                $this->response->redirect($this->___settingUrl());
            }
            parent::render('user/forgot.php');
        }else{
            $this->setMetaTitle('重设密码');
            parent::render('user/forgot_reset.php');
        }
    }
    
    protected function doForgot(){
        
        
        $captcha = $this->request->get('captcha');
        
        if(empty($captcha) || !$this->widget('Some_Util_Captcha')->check($captcha)){
            $this->widget('Widget_Notice')->set('验证码错误!','error');
            //$this->response->goBack();
        }
        
        $name = $this->request->get('name');
        $mail = $this->request->get('mail');
        if(empty($name) || empty($mail)){
            $this->widget('Widget_Notice')->set('用户名或邮箱不能为空!','error');
            $this->response->goBack();
        }
        $user = $this->db->fetchRow($this->db->select()
            ->from('table.users')
            ->where('name = ?', $name)
            ->where('mail = ?', $mail)
            ->limit(1)
            );
        if(empty($user)){
            $this->widget('Widget_Notice')->set('用户不存在!','error');
            $this->response->goBack();
        }
        $params = array(
            'uid'=>$user['uid'],
            'confirm'=>$user['mail'],
            'name'=>$user['screenName'],
            'type'=>'reset'
        );
        $this->widget('Some_Util_Verify',$params)->send();
        $this->render('user/forgot.php');
    }
    
    protected function doReset(){
        $validator = new Typecho_Validate();
        
        $validator->addRule('password', 'required', _t('必须填写密码'));
        $validator->addRule('password', 'minLength', _t('为了保证账户安全, 请输入至少六位的密码'), 6);
        $validator->addRule('password', 'maxLength', _t('为了便于记忆, 密码长度请不要超过十八位'), 18);
        $validator->addRule('confirm', 'confirm', _t('两次输入的密码不一致'), 'password');
        
        $error = $validator->run($this->request->from('password','confirm'));
        
        /** 截获验证异常 */
        if ($error) {
            /** 设置提示信息 */
            $this->widget('Widget_Notice')->set($error,'error');
            $this->response->goBack();
        }
        $password = $this->request->get('password');
        
        $hasher = new PasswordHash(8, true);

        $password = $hasher->HashPassword($password);
        //用户操作类
        $users = $this->widget('Widget_Abstract_Users');
        /** 更新数据 */
        $users->update(array('password' => $password),
            $this->db->sql()->where('uid = ?', $this->_user['uid']));
        
        /** 提示信息 */
        $this->widget('Widget_Notice')->set(_t('密码已经成功修改'), 'success');
        
        $this->widget('Some_Util_Verify')->setParams('type=reset')->delete($this->token);
        /** 转向登录页面 */
        $this->response->redirect($this->___loginUrl());
    }
}
