<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('header.php'); ?>
<div id="sidebar">
    <?php if($this->user->hasLogin()):?>
    <?php $this->need('user/widget_user.php'); ?>
    <?php else:?>
    <?php $this->need('user/widget_login.php'); ?>
    <?php endif;?>
</div>
<div class="user-profile" id="main" role="main">
    <div class="box">
        <div class="head">
            <a href="<?php $this->options->siteUrl(); ?>"><?php $this->options->title();?></a> &nbsp;&nbsp;&raquo;&nbsp;&nbsp;
            <?php $this->metaTitle();?>
        </div>
		<div class="cell">
		  <p><?php _e('谢谢，你的电子邮件地址已经成功激活。现在可以发布主题了');?></p>
		  <a href="<?php $this->some->loginUrl();?>"><?php _e('现在登录');?></a> → 
		</div>
	</div>
</div><!-- end #main -->
<?php $this->need('footer.php'); ?>
