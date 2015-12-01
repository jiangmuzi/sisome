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
		<?php Typecho_Widget::widget('Widget_Metas_Category_List')->listNodes(array('node'=>'','cate'=>'<a class="tab" href="{permalink}">{name}</a>')); ?>
        <a class="tab fr" href="<?php $this->options->index('/recent');?>"><?php _e('更多');?></a>
    </div>
    <?php $this->widget('Widget_Contents_Post_List@indexRecent', 'sort=lastComment&limit=30')->to($archives);?>
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
			<div class="post-reply"><a href="<?php $archives->permalink() ?>"><?php $archives->commentsNum('%d'); ?></a></div>
        </article>
	<?php endwhile; ?>
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
