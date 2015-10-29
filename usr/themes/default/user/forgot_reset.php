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
            <?php $this->getMetaTitle();?>
        </div>
    </div>
    <div class="cell">
   <form accept-charset="UTF-8" action="<?php $this->options->someAction('forgot'); ?>" class="form" method="post">
    <div class="field">
		<label for="password">新密码</label>
        <input class="field-ipt" id="password" name="password" placeholder="请输入新的密码" size="30" type="password" /> 
    </div>
    <div class="field">
		<label for="confirm">确认新的密码</label>
        <input class="field-ipt" id="confirm" name="confirm" placeholder="确认新的密码" size="30" type="password" /> 
    </div>
    <div class="field">
        <label>&nbsp;</label>
        <button class="btn fieid-ipt" type="submit" name="do" value="reset">修改密码</button>
     </div>
     <input name="token" value="<?php echo $this->request->get('token');?>" type="hidden" />
   </form>
   </div>
</div>
<?php $this->need('footer.php'); ?>
