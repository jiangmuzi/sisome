<?php
/**
 * 社区首页
 *
 * @package custom
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('header.php'); ?>
<?php $this->need('sidebar.php'); ?>
<div id="main">
    <div class="box">
    <div class="head">
		<?php Typecho_Widget::widget('Widget_Metas_Category_List')->listNodes(array('node'=>'','cateCurrent'=>'active','cate'=>'<a class="tab {cateCurrent}" href="/?tab={slug}">{name}</a>')); ?>
        <?php if($this->user->hasLogin()):?>
			<a class="tab <?php if($this->request->tab=='nodes')echo 'active';?>" href="<?php $this->options->index('/?tab=nodes');?>"><?php _e('节点');?></a>
			<a class="tab <?php if($this->request->tab=='users')echo 'active';?>" href="<?php $this->options->index('/?tab=users');?>"><?php _e('关注');?></a>
		<?php endif;?>
		<a class="tab fr" href="<?php $this->options->index('/recent');?>"><?php _e('更多');?></a>
    </div>
    <?php $this->widget('Widget_Contents_Post_Home@indexRecent', 'sort=lastComment&limit=30')->to($archives);?>
    <?php if($archives->have()):?>
	<?php while($archives->next()): ?>
        <article class="cell post">
			<a class="post-avatar" href="<?php $archives->author->ucenter(); ?>"><?php $archives->author->avatar();?></a>
			<h2 class="post-title"><a href="<?php $archives->permalink() ?>"><?php $archives->title() ?></a></h2>
			<ul class="post-meta">
                <?php if($archives->category):?>
						<li><?php $archives->category(','); ?>&nbsp;&bull;&nbsp;</li>
					<?php else:?>
						<li><?php $archives->tags(','); ?>&nbsp;&bull;&nbsp;</li>
					<?php endif;?>
                <li><strong><a href="<?php $archives->author->ucenter(); ?>"><?php $archives->author->name(); ?></a></strong>&nbsp;&bull;&nbsp;</li>
				<li><span><?php $archives->lastWord(); ?></span></li>
				<?php if($archives->lastUid):?>
				<li>&nbsp;&bull;&nbsp;最后回复来自：<strong><a href="<?php $archives->lastAuthor->ucenter();?>"><?php $archives->lastAuthor->name();?></a></strong></li>
				<?php endif;?>
			</ul>
			<?php if($archives->commentsNum):?>
			<div class="post-reply"><a href="<?php $archives->permalink() ?>"><?php $archives->commentsNum('%d'); ?></a></div>
			<?php endif;?>
		</article>
	<?php endwhile; ?>
	
	<?php else: ?>
		<article class="cell">
		<p class="aligncenter fade">还没有内容!</p>
		</article>
	<?php endif;?>
    </div>
    <div class="box">
        <div class="head">
            <span class="fade"><?php $this->options->title();_e(' / 节点导航');?></span>
        </div>
		<?php Typecho_Widget::widget('Widget_Metas_Category_List')->listNodes(
			array(
			'node'=>'<a href="{permalink}">{name}</a>',
			'cateBefore'=>'<div class="cell nodes">',
			'cate'=>'<strong class="fade">{name}</strong><div class="nodes-item">',
			'cateAfter'=>'</div></div>',
			)
		);
		?>
        
    </div>
</div><!-- end #main-->
<?php $this->need('footer.php'); ?>
