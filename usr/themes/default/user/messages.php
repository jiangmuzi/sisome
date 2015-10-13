<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('header.php'); ?>
<div id="sidebar">
    <?php $this->need('user/widget_user.php'); ?>
    <div class="box">
    <div class="head clearfix"><?php $this->options->title();_e('邀请注册');?></div>
    <div class="cell">
        <p>你可以在自己的个人网站上分享 V2EX 的链接，如果有新用户通过你的分享注册，那么你和新用户将各自得到：</p>
        <p><span class="credits-area">2000<img src="<?php $this->options->themeUrl('img/credits.png');?>" alt="Credits" align="absmiddle" border="0"></span></p>
        <input type="text" onclick="this.select();" value="<?php $this->options->index('?r='.$this->user->name);?>">
    </div>
</div>
</div>
<div class="user-profile" id="main" role="main">
    <div class="box">
        <div class="head">
            <a href="<?php $this->options->siteUrl(); ?>"><?php $this->options->title();?></a> &nbsp;&nbsp;&raquo;&nbsp;&nbsp;
            <?php $this->getMetaTitle();?>
        </div>
        <?php $this->widget('Widget_Messages_List')->to($messages);?>
        <?php if($messages->have()):?>
        <?php while ($messages->next()):?>
            <?php if($messages->type=='comment'):?>
                <div class="cell" style="background-color: #EDF3F5;">
                <a href="<?php $messages->author->ucenter();?>" target="_blank"><?php $messages->author->name();?></a>
                <?php _e('在'); $messages->dateWord();_e('回复了你的主题：');?>
                <a href="<?php $messages->permalink();?>" target="_blank"><?php $this->title();?></a>
                </div>
				<div class="cell">
				<?php $messages->content();?>
                </div>
            <?php endif;?>
			<?php if($messages->type=='at'):?>
                <div class="cell" style="background-color: #EDF3F5;">
                <a href="<?php $messages->author->ucenter();?>" target="_blank"><?php $messages->author->name();?></a>
                <?php _e('在'); $messages->dateWord();_e('回复中@你：');?>
				<a href="<?php $messages->permalink();?>" target="_blank"><?php _e('查看')?></a>
                </div>
				<div class="cell">
				<?php $messages->content();?>
                </div>
            <?php endif;?>
        <?php endwhile;?>
        <?php else:?>
		<div class="cell">
		  <p class="aligncenter fade"><?php _e('目前尚无任何提醒信息');?></p>
		</div>
		<?php endif;?>
	</div>
</div><!-- end #main -->
<?php $this->need('footer.php'); ?>
