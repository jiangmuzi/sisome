<?php
// +----------------------------------------------------------------------
// | SISOME 用户登录
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.sisome.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 绛木子 <master@lixianhua.com>
// +----------------------------------------------------------------------

class Widget_Users_Login extends Widget_Abstract_Users implements Widget_Interface_Do
{
    //用户登录
    public function action(){
		// protect
        $this->security->protect();
		
		/** 如果已经登录 */
        if ($this->user->hasLogin()) {
            /** 直接返回 */
            $this->response->redirect($this->options->index);
        }
		
		/** 初始化验证类 */
        $validator = new Typecho_Validate();
        $validator->addRule('name', 'required', _t('请输入用户名'));
        $validator->addRule('password', 'required', _t('请输入密码'));
		
		/** 截获验证异常 */
        if ($error = $validator->run($this->request->from('name', 'password'))) {
            Typecho_Cookie::set('__some_remember_name', $this->request->name);

            /** 设置提示信息 */
            $this->widget('Widget_Notice')->set($error);
            $this->response->goBack();
        }

        /** 开始验证用户 **/
        $valid = $this->user->login($this->request->name, $this->request->password,
        false, 1 == $this->request->remember ? $this->options->gmtTime + $this->options->timezone + 30*24*3600 : 0);

        /** 比对密码 */
        if (!$valid) {
            /** 防止穷举,休眠3秒 */
            sleep(3);

            Typecho_Cookie::set('__some_remember_name', $this->request->name);
            $this->widget('Widget_Notice')->set(_t('用户名或密码无效'), 'error');
            $this->response->goBack('?referer=' . urlencode($this->request->referer));
        }
		$this->widget('Widget_Notice')->set('已成功登录!','notice');
		//登录积分
		Widget_Common::credits('login');
        /** 跳转验证后地址 */
        $this->response->redirect($this->request->get('redir',$this->options->index));
    }
}