<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<div class="user-meta box">
	<div class="cell flowhd">
	  <a href="<?php $this->ucenter()->ucenter();?>">
	   <img class="avatar fl" src="<?php $this->ucenter()->avatar96(); ?>">
	  </a>
	  <h1 class="user-title"><a href="<?php $this->options->someUrl('ucenter',array('u'=>$this->ucenter()->name));?>"><?php $this->ucenter()->screenName();?></a> </h1>
    <?php if($this->ucenter()->sign):?><p class="m0"><strong><?php $this->ucenter()->sign();?></strong></p><?php endif;?>
    <div>
    <?php
    $this->options->title();
    _e(_t('第 %s 位会员，',$this->ucenter()->uid));
    _e('加入于：'.date('Y-m-d H:i:s',$this->ucenter()->created));
    if($this->user->uid==$this->ucenter()->uid){
        if($this->ucenter()->logged!=0):
		  _e('，最后登录: %s', Typecho_I18n::dateWord($this->ucenter()->logged  + $this->options->timezone, $this->options->gmtTime + $this->options->timezone));
		  else:
		  _e('，第一次登录');
		  endif;
    }
		  ?>
    </div>
	</div>
	<div class="cell">
	  <?php if($this->ucenter()->url):?>
	  <a class="tag" href="<?php $this->ucenter()->url();?>" target="_blank"><i class="fa fa-home"></i> <?php $this->ucenter()->url();?></a>
	  <?php endif;?>
	  <?php if($this->ucenter()->location):?>
	  <a class="tag" href="http://www.google.com/maps?q=<?php $this->ucenter()->location();?>" target="_blank"><i class="fa fa-map-marker"></i> <?php $this->ucenter()->location();?></a>
	  <?php endif;?>
	</div>
	<?php if($this->ucenter()->intro):?>
	<div class="inner">
	   <?php $this->ucenter()->intro();?>
	</div>
	 <?php endif;?>
</div>
<div class="box" style="margin-bottom: 0;">
    <div class="cell-tabs">
        <strong><a href="<?php $this->ucenter()->ucenter();?>"><?php $this->ucenter()->screenName();?></a></strong>
        <?php if($this->user->uid==$this->ucenter()->uid):?>
            <a class="tab<?php if($this->is('ucenter_post')){ echo ' current';}?>" href="<?php $this->options->someUrl('ucenter_post',array('u'=>$this->user->name));?>"><?php _e('话题');?></a>
            <a class="tab<?php if($this->is('ucenter_reply')){ echo ' current';}?>" href="<?php $this->options->someUrl('ucenter_reply',array('u'=>$this->user->name));?>"><?php _e('回复');?></a>
        <?php else:?>
            <a class="tab<?php if($this->is('ucenter_post')){ echo ' current';}?>" href="<?php $this->options->someUrl('ucenter_post',array('u'=>$this->ucenter()->name));?>"><?php _e('话题');?></a>
            <a class="tab<?php if($this->is('ucenter_reply')){ echo ' current';}?>" href="<?php $this->options->someUrl('ucenter_reply',array('u'=>$this->ucenter()->name));?>"><?php _e('回复');?></a>
        <?php endif;?>
    </div>
</div>
