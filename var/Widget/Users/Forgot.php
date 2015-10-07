<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

// +----------------------------------------------------------------------
// | SISOME 找回密码
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.sisome.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 绛木子 <master@lixianhua.com>
// +----------------------------------------------------------------------

class Widget_Users_Forgot extends Widget_Abstract_Users implements Widget_Interface_Do
{
    
    public function sendForgotVerify(){

        $captcha = $this->request->get('captcha');
        
        if(empty($captcha) || !$this->widget('Util_Captcha')->check($captcha)){
            $this->widget('Widget_Notice')->set('验证码错误!','error');
            $this->response->goBack();
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
        //发送验证码
        Widget_Common::sendVerify($params);
        $this->widget('Widget_Notice')->set('验证邮件已发送，请注意查收!','success');
        $this->response->goBack();
    }
    
    public function resetPwd(){
        
        $token = $this->request->token;
        
        if(empty($token)){
            throw new Typecho_Widget_Exception(_t('请求的地址不存在'), 404);
        }
        
        $verify = $this->widget('Util_Verify')->setParams('type=reset')->check($token);
        
        if(empty($verify) || empty($verify['uid'])){
            throw new Typecho_Widget_Exception(_t('请求的地址不存在'), 404);
        }
        
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
        
        /** 更新数据 */
        $this->update(array('password' => $password),
            $this->db->sql()->where('uid = ?', $verify['uid']));
        
        /** 提示信息 */
        $this->widget('Widget_Notice')->set(_t('密码已经成功修改，请重新登录'), 'success');
        
        $this->widget('Util_Verify')->setParams('type=reset')->delete($token);
        
        if($this->user->hasLogin())
            $this->user->logout();
        /** 转向登录页面 */
        $this->response->redirect(Typecho_Common::url('login', $this->options->index));
    }
    public function action(){
        //验证用户
        $this->on($this->request->is('do=forgot'))->sendForgotVerify();
        //重置密码
        $this->on($this->request->is('do=reset'))->resetPwd();
        
    }
}
