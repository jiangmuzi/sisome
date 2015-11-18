<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('header.php'); ?>
<style>.user-page .page-title,.user-page footer{display:none;}</style>
<?php
	$rememberName = htmlspecialchars(Typecho_Cookie::get('__some_remember_name'));
	$rememberMail = htmlspecialchars(Typecho_Cookie::get('__some_remember_mail'));
	$notice = Typecho_Cookie::get('__some_notice');
	if(!empty($notice)) {
		$notice = json_decode($notice,true);
	}
	Typecho_Cookie::delete('__some_remember_name');
	Typecho_Cookie::delete('__some_remember_mail');
?>
<div id="sidebar">
    <?php $this->need('user/widget_login.php'); ?>
</div>
<div class="box" id="main">
    <div class="head">
        <div class="location">
            <a href="<?php $this->options->siteUrl(); ?>"><?php $this->options->title();?></a>  &nbsp;&nbsp;&raquo;&nbsp;&nbsp;
            <?php $this->getMetaTitle();?>
        </div>
    </div>
    <div class="cell">
   <?php if($this->options->allowRegister==1): ?>
       <form accept-charset="UTF-8" action="<?php $this->options->someAction('register'); ?>" class="form user-form" method="post">
        <div class="field">
    		<label for="name">用户名</label>
            <input class="field-ipt" id="name" name="name" placeholder="请输入用户名" size="30" type="text" /> 
        </div>
    	<div class="field">
    		<label for="mail">邮箱</label>
         <input class="field-ipt" id="mail" name="mail" placeholder="请输入邮箱" size="30" type="text" /> 
        </div>
    	<div class="field">
        	<label for="password">密码</label>
            <input class="field-ipt" id="password" name="password" placeholder="密码不少于6位" size="30" type="password" /> 
        </div>
    	<div class="field">
        	<label for="confirm">确认密码</label>
            <input class="field-ipt" id="confirm" name="confirm" placeholder="确认密码" size="30" type="password" /> 
        </div>
        <div class="field">
    	   <label>你是机器人么？</label>
            <p class="field-ipt" style="margin:0;"><img class="captcha" src="<?php $this->options->someUrl('captcha');?>"></p> 
        </div>
        <div class="field">
    	   <label>&nbsp;</label>
            <input class="field-ipt" id="captcha" name="captcha" placeholder="输入验证码" size="30" type="text" /> 
        </div> 
        <div class="field">
            <label>&nbsp;</label>
    	   <button class="btn field-ipt" type="submit">注册</button>	 
        </div>  
       </form>
   <?php else:?>
   <div id="accounts-form">
        <p>社交帐号登录</p>
        <div class="sns-btn">
            <?php Typecho_Widget::widget('Widget_Users_Oauth')->parseActiveSns();?>
        </div>
    </div>
   <?php endif; ?>
  </div>
</div>
<?php $this->need('footer.php'); ?>
