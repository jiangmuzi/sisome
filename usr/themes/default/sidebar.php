<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<div id="sidebar">
    <?php $this->need('user/widget_user.php'); ?>
	
	 <section class="box">
        <div class="head"><span class="fade"><?php _e('今日热门主题'); ?></span><div class="fr"></div></div>
        <?php $this->widget('Widget_Contents_Post_List@hotPosts','sort=commentsNum&pageSize=10')
        ->parse('<div class="cell"><a href="{permalink}">{title}</a></div>'); ?>
    </section>
	
	<section class="box">
        <div class="head"><span class="fade"><?php _e('最热节点'); ?></span><div class="fr"></div></div>
        <div class="cell">
        <?php $this->widget('Widget_Metas_List@hotTags','ignoreZeroCount=0&type=tag')
            ->parse('<a class="tag" href="{permalink}">{name}</a>'); ?>
        </div>
    </section>
	
    <section class="box">
        <div class="head"><span class="fade"><?php _e('最新回复'); ?></span><div class="fr"></div></div>
        <?php $this->widget('Widget_Comments_List@newReplys')->to($newReplys); ?>
        <div id="lastCommentList">
        <?php while($newReplys->next()): ?>
            <div class="cell">
            <a href="<?php $newReplys->permalink(); ?>"><?php $newReplys->poster->avatar(24);?></a>
            <a href="<?php $newReplys->permalink(); ?>"><?php $newReplys->poster->name(); ?></a> : <?php $newReplys->excerpt(35, '...'); ?></div>
        <?php endwhile; ?>
        </div>
        <div class="hide" id="lastCommentTime" data-last="<?php $newReplys->created();?>"></div>
    </section>

    <?php $stat = $this->widget('Widget_Stat');?>
    <section class="box">
        <div class="head"><span class="fade"><?php _e('社区运行状况'); ?></span><div class="fr"></div></div>
		<div class="cell">注册会员：<?php $stat->usersNum();?></div>
		<div class="cell">主题：<?php $stat->publishedPostsNum();?></div>
		<div class="cell"> 回复：<?php $stat->publishedCommentsNum();?></div>
    </section>
</div><!-- end #sidebar -->
