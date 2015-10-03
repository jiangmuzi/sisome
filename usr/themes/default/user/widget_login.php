<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<section class="box">
    <div class="head">
        <strong><?php $this->options->title(); ?></strong>
        <span class="fade"><?php $this->options->description(); ?></span>
    </div>
    <div class="cell aligncenter">
        <a class="btn" href="<?php $this->options->someUrl('register');?>"><?php _e('现在注册');?></a>
        <p>已注册用户请 <a class="btn" href="<?php $this->options->someUrl('login');;?>"><?php _e('登录');?></a></p>
    </div>
</section>
<section class="box">
    <div class="head">
        <strong><?php _e('社交帐号登录'); ?></strong>
    </div>
    <div class="cell sns-btn">
        <?php Typecho_Widget::widget('Forum_Oauth')->parseActiveSns();?>
    </div>
</section>
