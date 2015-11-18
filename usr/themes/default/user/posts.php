<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('header.php'); ?>
<div id="sidebar">
    <?php $this->need('user/widget_user.php'); ?>
</div>
<div class="user-profile" id="main" role="main">
    <?php $this->need('user/widget_info.php'); ?>
    <div class="box">
        <?php if($this->posts->have()): ?>
        <?php while($this->posts->next()): ?>
            <article class="cell post">
    			<a class="post-avatar" href="<?php $this->posts->author->ucenter(); ?>"><?php $this->posts->author->avatar();?></a>
    			<h2 class="post-title"><a href="<?php $this->posts->permalink() ?>"><?php $this->posts->title() ?></a></h2>
    			<ul class="post-meta">
                    <?php if($this->posts->category):?>
    						<li><?php $this->posts->category(','); ?>&nbsp;•&nbsp;</li>
    					<?php else:?>
    						<li><?php $this->posts->tags(','); ?>&nbsp;•&nbsp;</li>
    					<?php endif;?>
                    <li><a href="<?php $this->posts->author->ucenter(); ?>"><?php $this->posts->author->name(); ?></a>&nbsp;•&nbsp;</li>
    				<li><span><?php $this->posts->dateWord(); ?></span></li>
    				<?php if($this->posts->lastUid):?>
    				<li>&nbsp;•&nbsp;最后回复来自：<strong><a href="<?php $this->posts->lastAuthor->ucenter();?>"><?php $this->posts->lastAuthor->name();?></a></strong></li>
    				<?php endif;?>
    			</ul>
    			<div class="post-reply"><a href="<?php $this->posts->permalink() ?>"><?php $this->posts->commentsNum('%d'); ?></a></div>
            </article>
        <?php endwhile; ?>
        <div class="inner pager">
            <?php $this->posts->pageLink('上一页','prev');?>
            <?php echo $this->posts->getCurrentPage().'/'.$this->posts->getTotalPage();?>
            <?php $this->posts->pageLink('下一页','next');?>
        </div>
        <?php else: ?>
		<div class="cell">
		  <p class="aligncenter fade"><?php _e('目前尚未发布主题');?></p>
		</div>
		<?php endif; ?>
	</div>
</div><!-- end #main -->
<?php $this->need('footer.php'); ?>
