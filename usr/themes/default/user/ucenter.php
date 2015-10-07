<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('header.php'); ?>
<div id="sidebar">
    <?php $this->need('user/widget_user.php'); ?>
</div>
<div class="user-profile" id="main" role="main">
    <?php $this->need('user/widget_info.php'); ?>
    <div class="box">
        <div class="head"><span class="fade"><?php _e($this->ucenter()->screenName.'最近的主题');?></span></div>
        <?php $this->widget('Widget_Contents_Post_List@UserRecentPost','uid='.$this->ucenter()->uid)->to($posts); ?>
        <?php if($posts->have()): ?>
        <?php while($posts->next()): ?>
            <article class="cell post">
    			<a class="post-avatar" href="<?php $posts->author->ucenter(); ?>"><img class="avatar" src="<?php $posts->author->avatar();?>"></a>
    			<h2 class="post-title"><a href="<?php $posts->permalink() ?>"><?php $posts->title() ?></a></h2>
    			<ul class="post-meta">
                    <?php if($posts->category):?>
    						<li><?php $posts->category(','); ?>&nbsp;•&nbsp;</li>
    					<?php else:?>
    						<li><?php $posts->tags(','); ?>&nbsp;•&nbsp;</li>
    					<?php endif;?>
                    <li><a href="<?php $posts->author->ucenter(); ?>"><?php $posts->author->name(); ?></a>&nbsp;•&nbsp;</li>
    				<li><span><?php $posts->dateWord(); ?></span></li>
    				<?php if($posts->lastUid):?>
    				<li>&nbsp;•&nbsp;最后回复来自：<strong><a href="<?php $posts->lastAuthor->ucenter();?>"><?php $posts->lastAuthor->name();?></a></strong></li>
    				<?php endif;?>
    			</ul>
    			<div class="post-reply"><a href="<?php $posts->permalink() ?>"><?php $posts->commentsNum('%d'); ?></a></div>
            </article>
        <?php endwhile; ?>
        <?php else: ?>
		<div class="cell">
		  <p class="aligncenter fade"><?php _e('目前尚未发布主题');?></p>
		</div>
		<?php endif; ?>
	</div>
	
	<div class="box">
	   <div class="head"><span class="fade"><?php _e($this->ucenter()->screenName.'最近的回复');?></span></div>
        <?php $this->widget('Widget_Comments_List@UserRecentReply','uid='.$this->ucenter()->uid)->to($comments); ?>
        <?php if($comments->have()): ?>
        <?php while($comments->next()): ?>
            <div class="cell" style="background-color: #EDF3F5;"><?php _e('回复了');?> <a href="<?php $comments->permalink(); ?>"><?php $comments->title(); ?></a>
                <span class="fr"><?php $comments->dateWord(); ?></span>
            </div>
            <div class="cell"><?php $comments->content(); ?></div>
        <?php endwhile; ?>
        <?php else: ?>
		<div class="cell">
		  <p class="aligncenter fade"><?php _e('目前尚未发表回复');?></p>
		</div>
		<?php endif; ?>
	</div>
</div><!-- end #main -->
<?php $this->need('footer.php'); ?>
