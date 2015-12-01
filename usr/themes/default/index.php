<?php
/**
 * 这是 Typecho 0.9 系统的一套默认皮肤
 * 
 * @package Typecho Replica Theme 
 * @author Typecho Team
 * @version 1.2
 * @link http://typecho.org
 */

if (!defined('__TYPECHO_ROOT_DIR__')) exit;
 $this->need('header.php');
 ?>
<?php $this->need('sidebar.php'); ?>
<div id="main">
	<div class="box">
    <div class="head">
        <a href="<?php $this->options->siteUrl(); ?>"><?php $this->options->title();?></a> &nbsp;&nbsp;&raquo;&nbsp;&nbsp;
		<?php if ($this->is('index') || $this->is('front')): ?><!-- 页面为首页时 -->
			<?php _e('最新发布的主题');?>
		<?php elseif ($this->is('post')): ?><!-- 页面为文章单页时 -->
			<?php $this->category(); ?> &raquo; <?php $this->title(); ?>
		<?php else: ?><!-- 页面为其他页时 -->
			<?php $this->archiveTitle(' &raquo; ','',''); ?>
		<?php endif; ?>
		<div class="fr f12">
		  <span class="fade"><?php _e('主题总数'); ?></span>
		  <strong class="gray"><?php echo $this->getTotal();?></strong>
		  
			<?php if($this->is('tag') && $this->user->hasLogin()):?>
			     <span class="snow">&nbsp;•&nbsp;</span>
				<a class="add_favorite" <?php if($this->parameter->isFavorite):?>data-fid="<?php $this->parameter->isFavorite();?>"<?php endif;?> data-type="tag" data-slug="<?php echo $this->getArchiveSlug();?>" href="javascript:;"><?php if($this->parameter->isFavorite){_e('取消收藏');}else{_e('加入收藏');}?></a>
			<?php endif;?>
		</div>
    </div>
	<?php if($this->is('category') && $this->getArchiveMid()):?>
	<?php Typecho_Widget::widget('Widget_Metas_List@ChildTags_'.$this->getArchiveMid(), 'sort=count&limit=10&parent='.$this->getArchiveMid())->to($tags); ?>
		<?php if($tags->have()):?>
		<div class="cell" style="background-color: #f9f9f9; padding: 5px 10px;">
		  <div class="nodes-item">
			  <?php while ($tags->next()):?>
			  <a href="<?php $tags->permalink();?>"><?php $tags->name();?></a>
			  <?php endwhile; ?>
		  </div>
		</div>
		<?php endif; ?>
	<?php endif;?>
    <div class="cell">
        <?php if($this->getArchiveType()!='search'):?>
        <p class="m0"><?php echo $this->getDescription();?></p>
        <?php endif;?>
        <?php if($this->user->hasLogin()):?>
        <a class="btn" href="<?php $this->options->index('/publish/'.$this->getArchiveSlug());?>"><?php _e('创建新主题');?></a>
        <?php endif;?>
    </div>
    <?php if($this->have()):?>
    	<?php while($this->next()): ?>
            <article class="cell post">
    			<a class="post-avatar" href="<?php $this->author->ucenter(); ?>"><?php $this->author->avatar(48,'avatar');?></a>
    			<h2 class="post-title"><a href="<?php $this->permalink() ?>"><?php $this->title() ?></a></h2>
    			<ul class="post-meta">
					<?php if($this->category):?>
						<li><?php $this->category(','); ?>&nbsp;&bull;&nbsp;</li>
					<?php else:?>
						<li><?php $this->tags(','); ?>&nbsp;&bull;&nbsp;</li>
					<?php endif;?>
                    
                    <li><strong><a href="<?php $this->author->ucenter(); ?>"><?php $this->author->name(); ?></a><strong>&nbsp;&bull;&nbsp;</li>
    				<li><span><?php $this->lastWord(); ?></span></li>
					<?php if($this->lastUid):?>
					<li>&nbsp;&bull;&nbsp;最后回复来自：<strong><a href="<?php $this->lastAuthor->ucenter();?>"><?php $this->lastAuthor->name();?></a></strong></li>
					<?php endif;?>
    			</ul>
    			<div class="post-reply"><a href="<?php $this->permalink() ?>"><?php $this->commentsNum('%d'); ?></a></div>
            </article>
    	<?php endwhile; ?>
	<?php else:?>
	   <div class="cell">
	       <?php _e('暂无话题!');?>
	   </div>
	<?php endif;?>
    <div class="inner pager">
        <?php $this->pageLink('上一页','prev');?>
        <?php echo $this->request->get('page',1).'/'.$this->getTotalPage();?>
        <?php $this->pageLink('下一页','next');?>
    </div>
    </div>
</div><!-- end #main-->


<?php $this->need('footer.php'); ?>
