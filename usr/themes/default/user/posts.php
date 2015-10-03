<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('header.php'); ?>
<div id="sidebar">
    <?php $this->need('user/widget_user.php'); ?>
</div>
<div class="user-profile" id="main" role="main">
    <?php $this->need('user/widget_info.php'); ?>
    <div class="box">
        <?php Typecho_Widget::widget('Forum_User_Posts')->to($posts); ?>
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
    				<li><span><?php echo Forum_Common::formatTime($posts->created,'Y-m-d H:i:s'); ?></span></li>
    				<?php if($posts->lastUid):?>
    				<li>&nbsp;•&nbsp;最后回复来自：<strong><a href="<?php $posts->lastAuthor->ucenter();?>"><?php $posts->lastAuthor->name();?></a></strong></li>
    				<?php endif;?>
    			</ul>
    			<div class="post-reply"><a href="<?php $posts->permalink() ?>"><?php $posts->commentsNum('%d'); ?></a></div>
            </article>
        <?php endwhile; ?>
        <div class="inner pager">
            <?php $posts->pageLink('上一页','prev');?>
            <?php echo $posts->getCurrentPage().'/'.$posts->getTotalPage();?>
            <?php $posts->pageLink('下一页','next');?>
        </div>
        <?php else: ?>
		<div class="cell">
		  <p class="aligncenter fade"><?php _e('目前尚未发布主题');?></p>
		</div>
		<?php endif; ?>
	</div>
</div><!-- end #main -->
<?php $this->need('footer.php'); ?>
