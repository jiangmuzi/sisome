<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('header.php'); ?>
<div id="sidebar">
    <?php $this->need('user/widget_user.php'); ?>
</div>
<div class="user-profile" id="main" role="main">
    <div class="box">
        <div class="head">
            <a href="<?php $this->options->siteUrl(); ?>"><?php $this->options->title();?></a> &nbsp;&nbsp;&raquo;&nbsp;&nbsp;
            <?php $this->metaTitle();?>
        </div>
        <?php $this->widget('Forum_User_Favorites','type=post')->to($posts);?>
        <?php if($posts->have()):?>
        <?php while ($posts->next()):?>
    		<article class="cell post">
    		  <a class="post-avatar" href="<?php $posts->content->author->ucenter(); ?>"><img class="avatar" src="<?php $posts->content->author->avatar();?>"></a>
    			<h2 class="post-title"><a href="<?php $posts->content->permalink() ?>"><?php $posts->content->title() ?></a></h2>
    			<ul class="post-meta">
                    <?php if($posts->content->category):?>
    						<li><?php $posts->content->category(','); ?>&nbsp;•&nbsp;</li>
    					<?php else:?>
    						<li><?php $posts->content->tags(','); ?>&nbsp;•&nbsp;</li>
    					<?php endif;?>
                    <li><a href="<?php $posts->content->author->ucenter(); ?>"><?php $posts->content->author->name(); ?></a>&nbsp;•&nbsp;</li>
    				<li><span><?php echo Forum_Common::formatTime($posts->content->created,'Y-m-d H:i:s'); ?></span></li>
    				<?php if($posts->content->lastUid):?>
    				<li>&nbsp;•&nbsp;最后回复来自：<strong><a href="<?php $posts->content->lastAuthor->ucenter();?>"><?php $posts->content->lastAuthor->name();?></a></strong></li>
    				<?php endif;?>
    			</ul>
    			<div class="post-reply"><a href="<?php $posts->content->permalink() ?>"><?php $posts->content->commentsNum('%d'); ?></a></div>
            </article>
        <?php endwhile; ?>
		<div class="inner pager">
            <?php $posts->pageLink('上一页','prev');?>
            <?php echo $posts->getCurrentPage().'/'.$posts->getTotalPage();?>
            <?php $posts->pageLink('下一页','next');?>
        </div>
        <?php else: ?>
		<div class="cell">
		  <p class="aligncenter fade"><?php _e('尚未收藏主题');?></p>
		</div>
		<?php endif; ?>
	</div>
</div><!-- end #main -->
<?php $this->need('footer.php'); ?>
