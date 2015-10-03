<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<div class="user-meta box">
	<div class="cell flowhd">
	  <a href="<?php $this->options->someUrl('ucenter',array('u'=>$this->_user->name));?>">
	   <img class="avatar fl" src="<?php $this->_user->avatar96(); ?>">
	  </a>
	  <h1 class="user-title"><a href="<?php $this->options->someUrl('ucenter',array('u'=>$this->_user->name));?>"><?php $this->_user->screenName();?></a> </h1>
    <?php if($this->_user->sign):?><p class="m0"><strong><?php $this->_user->sign();?></strong></p><?php endif;?>
    <div>
    <?php
    $this->options->title();
    _e(_t('第 %s 位会员，',$this->_user->uid));
    _e('加入于：'.date('Y-m-d H:i:s',$this->_user->created));
    if($this->user->uid==$this->_user->uid){
        if($this->_user->logged!=0):
		  _e('，最后登录: %s', Typecho_I18n::dateWord($this->_user->logged  + $this->options->timezone, $this->options->gmtTime + $this->options->timezone));
		  else:
		  _e('，第一次登录');
		  endif;
    }
		  ?>
    </div>
	</div>
	<div class="cell">
	  <?php if($this->_user->url):?>
	  <a class="tag" href="<?php $this->_user->url();?>" target="_blank"><i class="fa fa-home"></i> <?php $this->_user->url();?></a>
	  <?php endif;?>
	  <?php if($this->_user->location):?>
	  <a class="tag" href="http://www.google.com/maps?q=<?php $this->_user->location();?>" target="_blank"><i class="fa fa-map-marker"></i> <?php $this->_user->location();?></a>
	  <?php endif;?>
	</div>
	<?php if($this->_user->intro):?>
	<div class="inner">
	   <?php $this->_user->intro();?>
	</div>
	 <?php endif;?>
</div>
<div class="box" style="margin-bottom: 0;">
    <div class="cell-tabs">
        <strong><?php $this->_user->screenName();?></strong>
        <?php if($this->user->uid==$this->_user->uid):?>
            <a class="tab<?php if($this->is('user_posts')){ echo ' current';}?>" href="<?php $this->options->someUrl('user_posts',array('u'=>$this->user->name));?>"><?php _e('话题');?></a>
            <a class="tab<?php if($this->is('user_replys')){ echo ' current';}?>" href="<?php $this->options->someUrl('user_replys',array('u'=>$this->user->name));?>"><?php _e('回复');?></a>
        <?php else:?>
            <a class="tab<?php if($this->is('user_posts')){ echo ' current';}?>" href="<?php $this->options->someUrl('user_posts',array('u'=>$this->_user->name));?>"><?php _e('话题');?></a>
            <a class="tab<?php if($this->is('user_replys')){ echo ' current';}?>" href="<?php $this->options->someUrl('user_replys',array('u'=>$this->_user->name));?>"><?php _e('回复');?></a>
        <?php endif;?>
    </div>
</div>
