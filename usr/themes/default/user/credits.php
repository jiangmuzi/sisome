<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('header.php'); ?>
<div id="sidebar">
    <?php $this->need('user/widget_user.php'); ?>
    <div class="box" style="display:none;">
    <div class="head clearfix"><?php $this->options->title();_e('邀请注册');?></div>
    <div class="cell">
        <p>你可以在自己的个人网站上分享 <?php $this->options->title();?> 的链接，如果有新用户通过你的分享注册，那么你和新用户将各自得到：</p>
        <p><span class="credits-area">200<img src="<?php $this->options->themeUrl('img/credits.png');?>" alt="Credits" align="absmiddle" border="0"></span></p>
        <input type="text" onclick="this.select();" value="<?php $this->options->index('?i='.$this->user->name);?>">
    </div>
</div>
</div>
<div class="user-profile" id="main" role="main">
    <div class="box">
        <div class="head">
            <a href="<?php $this->options->siteUrl(); ?>"><?php $this->options->title();?></a> &nbsp;&nbsp;&raquo;&nbsp;&nbsp;
            <?php $this->metaTitle();?>
        </div>
		<div class="cell">
		<p><?php _e('当前账户余额');?></p>
		<p><span class="credits-area"><?php $this->user->credits();?> <img src="<?php $this->options->themeUrl('img/credits.png');?>" alt="Credits" align="absmiddle" border="0"></span></p>
		</div>
	</div>
	<div class="box">
        <div class="head"><?php _e('积分记录');?></div>
		<div class="cell p0">
		<table class="table">
		  <thead>
		      <tr>
		          <th width="130">时间</th>
		          <th width="80">类型</th>
		          <th width="60">数额</th>
		          <th width="80">余额</th>
		          <th>描述</th>
		      </tr>
		  </thead>
		  <tbody>
		      <?php if($this->have()):?>
		      <?php while ($this->next()):?>
		      <tr>
                <td><small class="gray"><?php echo date('Y-m-d H:i:s',$this->created);?></small></td>
                <td><?php $this->typeWord();?></td>
                <td><span class="positive"><strong><?php $this->amount();?></strong></span></td>
                <td><?php $this->balance();?></td>
                <td class="d" style="border-right: none;"><span class="gray"><?php $this->remarkWord();?> <strong class="positive"><?php echo abs($this->amount);?></span></strong></td>
            </tr>
            <?php endwhile;?>
            <?php endif;?>
		  </tbody>
		</table>
		</div>
		<div class="inner pager">
		  <?php $this->pageLink('上一页','prev');?>
		  <?php echo $this->getCurrentPage();?>/<?php echo $this->getTotalPage();?>
		  <?php $this->pageLink('下一页','next');?>
		</div>
	</div>
</div><!-- end #main -->
<?php $this->need('footer.php'); ?>
