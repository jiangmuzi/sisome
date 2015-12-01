<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('header.php'); ?>
<style>.user-page .page-title,.user-page footer{display:none;}</style>
<div id="sidebar">
    <?php $this->need('user/widget_login.php'); ?>
</div>
<div class="box user-form" id="main">
    <div class="head">
        <div class="location">
            <a href="<?php $this->options->siteUrl(); ?>"><?php $this->options->title();?></a> &nbsp;&nbsp;&raquo;&nbsp;&nbsp;
            <?php echo $this->getMetaTitle();?>
        </div>
    </div>
    <div class="cell">
    <?php if($this->request->isPost()):?>
        <p><?php _e('密码重置链接已发送到你的邮箱，请及时查收并修改自己的密码');?></p>
		  <a href="<?php $this->some->loginUrl();?>"><?php _e('现在登录');?></a> → 
    <?php else:?>
   <form accept-charset="UTF-8" action="<?php $this->options->someAction('forgot'); ?>" class="form" method="post">
    <div id="error-dialog"></div>
    <div class="field">
		<label for="name">用户名</label>
        <input class="field-ipt" id="name" name="name" placeholder="请输入用户名" size="30" type="text" /> 
    </div>
    <div class="field">
		<label for="name">邮箱</label>
        <input class="field-ipt" id="name" name="mail" placeholder="请输入邮箱" size="30" type="text" /> 
    </div>
    <div class="field">
	   <label>验证码</label>
        <p class="field-ipt" style="margin:0;"><img class="captcha" src="<?php $this->options->someUrl('captcha');?>"></p> 
    </div> 
    <div class="field">
	<label>&nbsp;</label>
    <input class="field-ipt" id="captcha" name="captcha" placeholder="请输入验证码" size="30" type="text" /> 
    </div> 
    <div class="field">
        <label>&nbsp;</label>
        <button class="btn fieid-ipt" type="submit" name="do" value="forgot">继续</button>
     </div>
   </form>
   <?php endif;?>
   </div>
</div>
<?php $this->need('footer.php'); ?>
