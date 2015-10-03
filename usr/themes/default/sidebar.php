<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<div id="sidebar">
    <?php if($this->user->hasLogin()):?>
    <?php $this->need('user/widget_user.php'); ?>
    <?php endif;?>
    
    <section class="box">
        <div class="head"><span class="fade"><?php _e('最新回复'); ?></span><div class="fr"></div></div>
        <?php $this->widget('Widget_Comments_Recent')->to($comments); ?>
        <div id="lastCommentList">
        <?php while($comments->next()): ?>
            <div class="cell">
            <a href="<?php $comments->permalink(); ?>"><img class="avatar" src="<?php $comments->poster->avatar();?>" width="32"></a>
            <a href="<?php $comments->permalink(); ?>"><?php $comments->poster->name(); ?></a> : <?php $comments->excerpt(35, '...'); ?></div>
        <?php endwhile; ?>
        </div>
        <div class="hide" id="lastCommentTime" data-last="<?php $comments->created();?>"></div>
    </section>
    
    <section class="box">
        <div class="head"><span class="fade"><?php _e('今日热门主题'); ?></span><div class="fr"></div></div>
        <?php $this->widget('Widget_Contents_Post_Recent@hotPosts','sort=commentsNum')
        ->parse('<div class="cell"><a href="{permalink}">{title}</a></div>'); ?>
    </section>
    
    <section class="box">
        <div class="head"><span class="fade"><?php _e('最热节点'); ?></span><div class="fr"></div></div>
        <div class="cell">
        <?php $this->widget('Widget_Metas_Tag_Cloud@hotTags','ignoreZeroCount=1')->to($hotTags); ?>
        <?php while($hotTags->next()): ?>
            <a class="tag" href="<?php $hotTags->permalink(); ?>"><?php $hotTags->name();?></a>
        <?php endwhile; ?>
        </div>
    </section>
    <?php $stat = $this->widget('Widget_Stat');?>
    <section class="box">
        <div class="head"><span class="fade"><?php _e('社区运行状况'); ?></span><div class="fr"></div></div>
		<div class="cell">注册会员：<?php $stat->usersNum();?></div>
		<div class="cell">主题：<?php $stat->publishedPostsNum();?></div>
		<div class="cell"> 回复：<?php $stat->publishedCommentsNum();?></div>
    </section>
</div><!-- end #sidebar -->
