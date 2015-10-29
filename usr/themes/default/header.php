<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<!DOCTYPE HTML>
<html class="no-js">
<head>
    <meta charset="<?php $this->options->charset(); ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1">
    <meta name="renderer" content="webkit">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title><?php $this->getMetaTitle(' - ');$this->options->title(); ?></title>

    <!-- 使用url函数转换相关路径 -->
    <link rel="stylesheet" href="http://apps.bdimg.com/libs/fontawesome/4.2.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="<?php $this->options->themeUrl('css/style.css'); ?>">

    <!--[if lt IE 9]>
    <script src="//cdn.staticfile.org/html5shiv/r29/html5.min.js"></script>
    <script src="//cdn.staticfile.org/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
    <!-- 通过自有函数输出HTML头部信息 -->
    <?php $this->header(); ?>
    <script>
    window.notice = <?php Widget_Common::getNotice();?>;
	window.siteUrl = '<?php $this->options->siteUrl();?>';
    </script>
</head>
<body>
<!--[if lt IE 8]>
    <div class="browsehappy" role="dialog"><?php _e('当前网页 <strong>不支持</strong> 你正在使用的浏览器. 为了正常的访问, 请 <a href="http://browsehappy.com/">升级你的浏览器</a>'); ?>.</div>
<![endif]-->
<div id="top-bar">
    <div class="wp clearfix">
        <div class="fl">
            <a class="top-logo fl" href="<?php $this->options->siteUrl(); ?>"><?php _e('首页'); ?></a>
            <div class="top-srh fl">
                <form id="search" method="post" action="<?php $this->options->index();?>" role="search">
                    <input type="text" name="s" class="text" placeholder="<?php _e('输入关键字搜索'); ?>" />
                    <button type="submit" class="submit"><?php _e('搜索'); ?></button>
                </form>
            </div>
        </div>
        <div class="fr top-user">
            <a href="<?php $this->options->siteUrl(); ?>"><?php _e('首页'); ?></a>
             <?php if($this->user->hasLogin()): ?>
				<a href="<?php $this->options->someUrl('ucenter',array('u'=>$this->user->name)); ?>"><?php $this->user->name(); ?></a>
				<a href="<?php $this->options->someUrl('setting'); ?>"><?php _e('设置'); ?></a>
                <a href="<?php $this->options->logoutUrl(); ?>"><?php _e('退出'); ?></a>
            <?php else: ?>
                <a href="<?php $this->options->someUrl('login'); ?>"><?php _e('登录'); ?></a>
                <a href="<?php $this->options->someUrl('register'); ?>"><?php _e('注册'); ?></a>
            <?php endif; ?>
        </div>
    </div>
</div>
<div id="body">
    <div class="wp clearfix">

    
    
