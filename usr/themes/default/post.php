<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('header.php'); ?>
<?php $this->need('sidebar.php'); ?>
<div id="main">
    <div class="box">
    <div class="head">
        <div class="location">
            <a href="<?php $this->options->siteUrl(); ?>"><?php $this->options->title();?></a> &nbsp;&nbsp;&raquo;&nbsp;&nbsp;
    		<?php $this->category(); ?>
        </div>
		<div class="fr">
		  <a class="post-avatar" href="<?php $this->author->ucenter(); ?>">
		      <?php $this->author->avatar(96);?></a>
		</div>
		<h1 class="post-title"><?php $this->title() ?></h1>
        <ul class="post-meta">
            <li><a href="<?php $this->author->ucenter(); ?>"><?php $this->author(); ?></a>&nbsp;·&nbsp;</li>
    		<li><span><?php $this->dateWord(); ?></span>&nbsp;·&nbsp;</li>
    		<li><span><?php $this->viewsNum(); _e('次点击');?></span></li>
    		<?php if($this->user->hasLogin() && $this->user->uid==$this->authorId):?>
    		  <li>&nbsp;·&nbsp;<span><a href="<?php $this->options->index('publish?cid='.$this->cid);?>"><?php _e('编辑');?></a></span></li>
    	   <?php endif;?>
    	   <?php if($this->user->hasLogin() && $this->authorId != $this->user->uid):?>
    	       <li>&nbsp;·&nbsp;<a class="add_favorite" <?php if($this->isFavorite):?>data-fid="<?php $this->isFavorite();?>"<?php endif;?> data-type="post" data-slug="<?php $this->cid();?>" href="javascript:;"><?php if($this->isFavorite){_e('取消收藏');}else{_e('加入收藏');}?></a></li>
    	   <?php endif;?>
    	</ul>
    </div>
	<?php if($this->text):?>
    <article class="cell post">
        <div class="post-content">
            <?php $this->content(); ?>
        </div>
    </article>
	<?php endif;?>
    <ul class="inner">
        <?php foreach ($this->tags as $tag):?>
            <a class="tag" href="<?php echo $tag['permalink'];?>"><i class="fa fa-tag"></i> <?php echo $tag['name']?></a>
        <?php endforeach;?>
    </ul>
    </div>
    <?php $this->need('comments.php'); ?>

    
</div><!-- end #main-->

<?php $this->need('footer.php'); ?>

