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
        <?php $this->widget('Widget_Metas_Category_List')->to($categories);?>
        <?php if ($categories->have()): ?>
            <?php while($categories->next()): ?>
            <a class="tab" href="<?php $categories->permalink();?>"><?php $categories->name();?></a>
            <?php endwhile; ?>
        <?php endif; ?>
        <a class="tab fr" href="<?php $this->options->index('/recent');?>"><?php _e('更多');?></a>
    </div>
    <?php $this->widget('Widget_Contents_Post_List@indexRecent', 'limit=30')->to($archives);?>
    <?php if($archives->have()):?>
	<?php while($archives->next()): ?>
        <article class="cell post">
			<a class="post-avatar" href="<?php $archives->author->ucenter(); ?>"><?php $archives->author->avatar();?></a>
			<h2 class="post-title"><a href="<?php $archives->permalink() ?>"><?php $archives->title() ?></a></h2>
			<ul class="post-meta">
                <?php if($archives->category):?>
						<li><?php $archives->category(','); ?>&nbsp;•&nbsp;</li>
					<?php else:?>
						<li><?php $archives->tags(','); ?>&nbsp;•&nbsp;</li>
					<?php endif;?>
                <li><a href="<?php $archives->author->ucenter(); ?>"><?php $archives->author->name(); ?></a>&nbsp;•&nbsp;</li>
				<li><span><?php echo Widget_Common::formatTime($archives->created,'Y-m-d H:i:s'); ?></span></li>
				<?php if($archives->lastUid):?>
				<li>&nbsp;•&nbsp;最后回复来自：<strong><a href="<?php $archives->lastAuthor->ucenter();?>"><?php $archives->lastAuthor->name();?></a></strong></li>
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
        <?php $this->widget('Widget_Metas_Category_List')->to($categories);?>
            <?php if ($categories->have()): ?>
                <?php while($categories->next()): ?>
                    <div class="cell nodes">
                      <strong class="fade"><?php $categories->name();?></strong>
                      <?php Typecho_Widget::widget('Widget_Metas_List@Tags_'.$categories->mid, 'sort=count&ignoreZeroCount=0&desc=count&limit=20&parent='.$categories->mid)->to($catTags); ?>
                      <div class="nodes-item">
                      <?php if($catTags->have()):?>
                          <?php while ($catTags->next()):?>
                          <a href="<?php $catTags->permalink();?>"><?php $catTags->name();?></a>
                          <?php endwhile; ?>
                      <?php endif; ?>
                      </div>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>
    </div>
</div><!-- end #main-->
<?php $this->need('footer.php'); ?>
