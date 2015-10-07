<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

// +----------------------------------------------------------------------
// | SISOME 社区设置
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://sisome.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 绛木子 <master@lixianhua.com>
// +----------------------------------------------------------------------

class Widget_Options_Forum extends Widget_Abstract_Options implements Widget_Interface_Do{
    
    public function form(){
        /** 构建表格 */
        $form = new Typecho_Widget_Helper_Form($this->security->getIndex('/action/options-forum'),
        Typecho_Widget_Helper_Form::POST_METHOD);
        
		$siteIcp = new Typecho_Widget_Helper_Form_Element_Text('siteIcp', NULL, $this->options->siteIcp, _t('网站备案号'), _t('在这里填入网站备案号'));
		$siteIcp->input->setAttribute('class', 'w-60');
		$form->addInput($siteIcp);
    
		$siteStat = new Typecho_Widget_Helper_Form_Element_Textarea('siteStat', NULL, $this->options->siteStat, _t('统计代码'), _t('在这里填入网站统计代码'));
		$form->addInput($siteStat);
		
        $smtp_host = new Typecho_Widget_Helper_Form_Element_Text('smtpHost', NULL, $this->options->smtpHost, _t('SMTP地址'), _t('请填写 SMTP 服务器地址'));
        $smtp_host->input->setAttribute('class', 'w-60');
		$form->addInput($smtp_host);
        
        $smtp_port = new Typecho_Widget_Helper_Form_Element_Text('smtpPort', NULL, $this->options->smtpPort, _t('SMTP端口'), _t('SMTP服务端口,一般为25。'));
        $smtp_port->input->setAttribute('class', 'w-60');
		$form->addInput($smtp_port);
        
        $smtp_user = new Typecho_Widget_Helper_Form_Element_Text('smtpUser', NULL, $this->options->smtpUser, _t('SMTP用户'), _t('SMTP服务验证用户名,一般为邮箱名如：youname@domain.com'));
        $smtp_user->input->setAttribute('class', 'w-60');
		$form->addInput($smtp_user);
        
        $smtp_pass = new Typecho_Widget_Helper_Form_Element_Password('smtpPass', NULL, $this->options->smtpPass, _t('SMTP密码'));
        $smtp_pass->input->setAttribute('class', 'w-60');
		$form->addInput($smtp_pass);
        
        $from_mail = new Typecho_Widget_Helper_Form_Element_Text('smtpMail', NULL, $this->options->smtpMail, _t('发件人EMAIL'));
		$from_mail->input->setAttribute('class', 'w-60');
        $form->addInput($from_mail->addRule('email', _t('电子邮箱格式错误')));
        $from_name = new Typecho_Widget_Helper_Form_Element_Text('smtpName', NULL, $this->options->smtpName, _t('发件人名称'));
		$from_name->input->setAttribute('class', 'w-60');
        $form->addInput($from_name);
        
		
		//用户积分
		$credits_register = new Typecho_Widget_Helper_Form_Element_Text('creditsRegister', NULL, $this->options->creditsRegister, _t('注册积分'),_t('用户注册后默认的积分'));
		$credits_register->input->setAttribute('class', 'w-60');
        $form->addInput($credits_register);
		
		$credits_login = new Typecho_Widget_Helper_Form_Element_Text('creditsLogin', NULL, $this->options->creditsLogin, _t('登录积分'),_t('每日登录获取的积分'));
		$credits_login->input->setAttribute('class', 'w-60');
        $form->addInput($credits_login);
		
		$credits_publish = new Typecho_Widget_Helper_Form_Element_Text('creditsPublish', NULL, $this->options->creditsPublish, _t('发布主题'),_t('用户发布主题加上或减少的积分'));
		$credits_publish->input->setAttribute('class', 'w-60');
        $form->addInput($credits_publish);
		
		$credits_reply = new Typecho_Widget_Helper_Form_Element_Text('creditsReply', NULL, $this->options->creditsReply, _t('发表回复'),_t('用户发表回复加上或减少的积分'));
		$credits_reply->input->setAttribute('class', 'w-60');
        $form->addInput($credits_reply);
		
		$credits_invite = new Typecho_Widget_Helper_Form_Element_Text('creditsInvite', NULL, $this->options->creditsInvite, _t('邀请注册'),_t('邀请者和被邀请者所奖励的积分'));
		$credits_invite->input->setAttribute('class', 'w-60');
        $form->addInput($credits_invite);
		
        /** 提交按钮 */
        $submit = new Typecho_Widget_Helper_Form_Element_Submit('submit', NULL, _t('保存设置'));
        $submit->input->setAttribute('class', 'btn primary');
        $form->addItem($submit);
        
        return $form;
    }
	/**
     * 执行更新动作
     *
     * @access public
     * @return void
     */
    public function updateForumSettings(){
        /** 验证格式 */
        if ($this->form()->validate()) {
            $this->response->goBack();
        }
        $settings = $this->request->from('siteIcp','siteStat','smtpHost','smtpPort', 'smtpUser', 'smtpPass', 'smtpMail', 'smtpName','creditsRegister','creditsLogin','creditsPublish','creditsReply','creditsInvite');
        
        foreach ($settings as $name => $value) {
            $this->update(array('value' => $value), $this->db->sql()->where('name = ?', $name));
        }
        
        $this->widget('Widget_Notice')->set(_t("设置已经保存"), 'success');
        $this->response->goBack();
    }
    /**
     * 绑定动作
     *
     * @access public
     * @return void
     */
    public function action()
    {
        $this->user->pass('administrator');
        $this->security->protect();
        $this->on($this->request->isPost())->updateForumSettings();
        $this->response->redirect($this->options->adminUrl);
    }
}