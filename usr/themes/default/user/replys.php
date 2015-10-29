<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('header.php'); ?>
<div id="sidebar">
    <?php $this->need('user/widget_user.php'); ?>
</div>
<div class="user-profile" id="main" role="main">
    <?php $this->need('user/widget_info.php'); ?>
    <div class="box">
        <?php $this->widget('Widget_Comments_List@UserRecentReply','uid='.$this->ucenter()->uid)->to($comments); ?>
        <?php if($comments->have()): ?>
        <?php while($comments->next()): ?>
            <div class="cell" style="background-color: #EDF3F5;"><?php _e('回复了');?> <a href="<?php $comments->permalink(); ?>"><?php $comments->title(); ?></a>
                <span class="fr"><?php $comments->dateWord(); ?></span>
            </div>
            <div class="cell"><?php $comments->content(); ?></div>
        <?php endwhile; ?>
        <div class="inner pager">
            <?php $comments->pageLink('上一页','prev');?>
            <?php echo $comments->getCurrentPage().'/'.$comments->getTotalPage();?>
            <?php $comments->pageLink('下一页','next');?>
        </div>
        <?php else: ?>
		<div class="cell">
		  <p class="aligncenter fade"><?php _e('目前尚未发表回复');?></p>
		</div>
		<?php endif; ?>
		
	</div>
</div><!-- end #main -->
<?php $this->need('footer.php'); ?>