<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<section class="box">
    <div class="head">
        <strong><?php $this->options->title(); ?></strong>
        <div class="fade"><?php $this->options->description(); ?></div>
    </div>
    <div class="cell aligncenter">
        <a class="btn" href="<?php $this->options->someUrl('register');?>"><?php _e('现在注册');?></a>
        <p>已注册用户请 <a class="btn" href="<?php $this->options->someUrl('login');;?>"><?php _e('登录');?></a></p>
    </div>
</section>
