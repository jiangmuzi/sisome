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
        <?php $this->widget('Forum_User_Favorites','type=tag')->to($nodes);?>
        <?php if($nodes->have()):?>
        <div class="cell-gird">
        <?php while ($nodes->next()):?>
    	   <a class="gird-item" href="<?php $nodes->node->permalink();?>"><?php $nodes->node->name();?>
    	       <span class="fade"><i class="fa fa-comments"></i> <?php $nodes->node->count();?></span>
    	   </a>
        <?php endwhile; ?>
        </div>
        <?php else: ?>
		<div class="cell">
		  <p class="aligncenter fade"><?php _e('尚未收藏节点');?></p>
		</div>
		<?php endif; ?>
	</div>
</div><!-- end #main -->
<?php $this->need('footer.php'); ?>
