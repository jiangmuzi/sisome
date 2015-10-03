<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('header.php'); ?>
<style>.user-page .page-title,.user-page footer{display:none;}</style>
<div id="sidebar">
    <?php $this->need('user/widget_user.php'); ?>
</div>
<div class="box user-form" id="main">
    <div class="head">
        <div class="location">
            <a href="<?php $this->options->siteUrl(); ?>"><?php $this->options->title();?></a> &nbsp;&nbsp;&raquo;&nbsp;&nbsp;
            <?php $this->metaTitle();?>
        </div>
    </div>
    <div class="cell">
        <form action="<?php $this->options->someAction('setting');?>" method="POST" enctype="multipart/form-data">
        <div class="field">
	          <label>当前头像</label>
	          <p class="m0">
					<img class="avatar vab mr10" width="96" src="<?php echo Forum_Common::avatar($this->user->uid,96);?>" align="default">
					<img class="avatar vab mr10" width="48" src="<?php echo Forum_Common::avatar($this->user->uid,48);?>" align="default">
					<img class="avatar vab mr10" width="32" src="<?php echo Forum_Common::avatar($this->user->uid,32);?>" align="default">
                </p>
	      </div>
	      <div class="field">
	          <label>选择一个图片文件</label>
	          <input class="field-ipt" type="file" name="avatar">
	      </div>
	      <div class="field">
	          <label>&nbsp;</label>
	          <p class="fade field-ipt">支持 2MB 以内的 PNG / JPG / GIF 文件</p>
	      </div>
	      <div class="field">
	          <label>&nbsp;</label>
	          <button class="btn field-ipt" name="do" value="avatar"><?php _e('开始上传');?></button>
	      </div>
	      </form>
   </div>
</div>
<?php $this->need('footer.php'); ?>
