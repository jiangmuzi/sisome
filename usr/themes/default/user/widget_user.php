<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php if($this->user->hasLogin()):?>
<div class="user-widget box">
    <div class="head clearfix">
        <a class="fl" href="<?php $this->options->index('u/'.$this->user->name);?>"><img class="avatar" width="48" src="<?php echo Forum_Common::avatar($this->user->uid,48); ?>"></a>
        <a class="user-nickname" href="<?php $this->options->index('u/'.$this->user->name);?>"><?php $this->user->name();?></a>
    </div>
    <?php $stat = $this->widget('Widget_Stat');?>
    <div class="cell user-stat aligncenter">
        <a class="stat-item" href="<?php $this->options->someUrl('favorite_nodes');?>" title="<?php _e('收藏的节点');?>">
            <span><?php $stat->favoriteNodesNum();?></span>
            <?php _e('节点收藏');?>
        </a>
        <a class="stat-item" href="<?php $this->options->someUrl('favorite_posts');?>" title="<?php _e('收藏的主题');?>">
            <span><?php $stat->favoritePostsNum();?></span>
            <?php _e('主题收藏');?>
        </a>
        <a href="<?php $this->options->someUrl('user_posts',array('u'=>$this->user->name));?>" title="<?php _e('发布的主题');?>">
            <span><?php $stat->myPublishedPostsNum();?></span>
            <?php _e('主题');?>
        </a>
        <a class="right" href="<?php $this->options->someUrl('user_replys',array('u'=>$this->user->name));?>" title="<?php _e('发表的回复');?>">
            <span><?php $stat->myPublishedCommentsNum();?></span>
            <?php _e('回复');?>
        </a>
    </div>
    <div class="cell">
        <a href="<?php $this->options->someUrl('publish');?>"><i class="fa fa-pencil"></i> <?php _e('创作新主题');?></a>
    </div>
    <div class="inner">
        <a href="<?php $this->options->someUrl('message');?>"><?php $stat->unReadMessages();?> <?php _e('条未读消息');?></a>
        <a class="credits-area fr" href="<?php $this->options->someUrl('credits');?>"><?php $this->user->credits();?> <img src="<?php $this->options->themeUrl('img/credits.png');?>" alt="Credits" align="absmiddle" border="0"></a>
    </div>
</div>
<?php endif;?>
