<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('header.php'); ?>
<div id="sidebar">
    <?php $this->need('user/widget_user.php'); ?>
</div>
<div class="user-profile" id="main" role="main">
    <div class="box">
        <div class="head">
            <a href="<?php $this->options->siteUrl(); ?>"><?php $this->options->title();?></a> &nbsp;&nbsp;&raquo;&nbsp;&nbsp;
            <?php $this->getMetaTitle();?>
        </div>
		<?php if($this->parameter->type=='favorite_posts'):?>
			<?php if($this->favorites->have()):?>
			<?php while ($this->favorites->next()):?>
				<article class="cell post">
				  <a class="post-avatar" href="<?php $this->favorites->content->author->ucenter(); ?>"><?php $this->favorites->content->author->avatar();?></a>
					<h2 class="post-title"><a href="<?php $this->favorites->content->permalink() ?>"><?php $this->favorites->content->title() ?></a></h2>
					<ul class="post-meta">
						<?php if($this->favorites->content->category):?>
								<li><?php $this->favorites->content->category(','); ?>&nbsp;•&nbsp;</li>
							<?php else:?>
								<li><?php $this->favorites->content->tags(','); ?>&nbsp;•&nbsp;</li>
							<?php endif;?>
						<li><a href="<?php $this->favorites->content->author->ucenter(); ?>"><?php $this->favorites->content->author->name(); ?></a>&nbsp;•&nbsp;</li>
						<li><span><?php $this->favorites->content->dateWord(); ?></span></li>
						<?php if($this->favorites->content->lastUid):?>
						<li>&nbsp;•&nbsp;最后回复来自：<strong><a href="<?php $this->favorites->content->lastAuthor->ucenter();?>"><?php $this->favorites->content->lastAuthor->name();?></a></strong></li>
						<?php endif;?>
					</ul>
					<div class="post-reply"><a href="<?php $this->favorites->content->permalink() ?>"><?php $this->favorites->content->commentsNum('%d'); ?></a></div>
				</article>
			<?php endwhile; ?>
			<div class="inner pager">
				<?php $this->favorites->pageLink('上一页','prev');?>
				<?php echo $this->favorites->getCurrentPage().'/'.$this->favorites->getTotalPage();?>
				<?php $this->favorites->pageLink('下一页','next');?>
			</div>
			<?php else: ?>
			<div class="cell">
			  <p class="aligncenter fade"><?php _e('尚未收藏主题');?></p>
			</div>
			<?php endif; ?>
		<?php endif; ?>
		
		<?php if($this->parameter->type=='favorite_nodes'):?>
			<?php if($this->favorites->have()):?>
			<div class="cell-gird">
			<?php while ($this->favorites->next()):?>
			   <a class="gird-item" href="<?php $this->favorites->node->permalink();?>"><?php $this->favorites->node->name();?>
				   <span class="fade"><i class="fa fa-comments"></i> <?php $this->favorites->node->count();?></span>
			   </a>
			<?php endwhile; ?>
			</div>
			<?php else: ?>
			<div class="cell">
			  <p class="aligncenter fade"><?php _e('尚未收藏节点');?></p>
			</div>
			<?php endif; ?>
		<?php endif; ?>
	</div>
</div><!-- end #main -->
<?php $this->need('footer.php'); ?>
