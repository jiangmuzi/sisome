<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('header.php'); ?>
<div id="sidebar">
<?php $this->need('user/widget_user.php'); ?>
</div>
<div id="main">
    <div class="box">
    <div class="head">
        <div class="location">
            <a href="<?php $this->options->siteUrl(); ?>"><?php $this->options->title();?></a> &nbsp;&nbsp;&raquo;&nbsp;&nbsp;
    		<?php if ($this->is('index')): ?><!-- 页面为首页时 -->
    			<?php _e('最近的主题');?>
    		<?php elseif ($this->is('post')): ?><!-- 页面为文章单页时 -->
    			<?php $this->category(); ?> &raquo; <?php $this->title(); ?>
    		<?php else: ?><!-- 页面为其他页时 -->
    			<?php $this->archiveTitle(' &raquo; ','',''); ?>
    		<?php endif; ?>
        </div>
    </div>
    <article class="cell post page" itemscope itemtype="http://schema.org/BlogPosting">
        <h1 class="post-title"><?php $this->title() ?></h1>
        <div class="post-content">
            <?php $this->content(); ?>
        </div>
    </article>
    <div class="inner"><?php _e('最后更新：');echo Forum_Common::formatTime($this->modified,'Y-m-d H:i:s')?></div>
    </div>
</div><!-- end #main-->
<?php $this->need('footer.php'); ?>
