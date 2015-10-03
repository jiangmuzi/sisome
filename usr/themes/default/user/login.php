<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('header.php'); ?>
<style>.user-page .page-title,.user-page footer{display:none;}</style>
<div id="sidebar">
    <?php $this->need('user/widget_login.php'); ?>
</div>
<div class="box" id="main">
    <div class="head">
        <div class="location">
            <a href="<?php $this->options->siteUrl(); ?>"><?php $this->options->title();?></a> &nbsp;&nbsp;&raquo;&nbsp;&nbsp;
            <?php $this->metaTitle();?>
        </div>
    </div>
    <div class="cell">
   <form accept-charset="UTF-8" action="<?php $this->options->someAction('login'); ?>" class="form user-form" method="post">
    <div id="error-dialog"></div>
    <div class="field">
		<label for="name">用户名</label>
        <input class="field-ipt" id="name" name="name" placeholder="请输入用户名或邮箱" size="30" type="text" /> 
    </div> 
    <div class="field">
	<label for="password">密码</label>
    <input class="field-ipt" id="password" name="password" placeholder="输入登录密码" size="30" type="password" /> 
    </div> 
    <div class="field">
        <label class="remember" for="remember"><input id="remember" name="remember" type="checkbox" value="1" /> 记住我</label>
     </div> 
	 <div class="field">
	 <label>&nbsp;</label>
     <button class="btn field-ipt" type="submit">登录</button>
    </div>
    <div class="field">
	 <label>&nbsp;</label>
     <p><a href="<?php $this->options->someUrl('forgot');?>">忘记密码?</a> </p>
    </div>
	<input name="redir" value="<?php echo $this->request->get('redir');?>" type="hidden" />
   </form>
   </div>
</div>
<?php $this->need('footer.php'); ?>
