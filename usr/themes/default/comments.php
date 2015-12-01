<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php if($this->allow('comment')): ?>
<div class="box" id="comments">
    <?php $this->comments()->to($comments); ?>
    <?php if ($comments->have()): ?>
	<div class="head"><?php $this->commentsNum(_t('%d 条回复')); ?>
	</div>
    
    <?php $comments->listComments(); ?>
    <div class="inner">
    <?php $comments->pageNav('&laquo;', '&raquo;'); ?>
    </div>
    <?php else:?>
        <div class="noreply">
        <p><?php _e('目前尚无回复');?></p>
        </div>
    <?php endif; ?>
</div>
<div class="box cell" id="<?php $this->respondId(); ?>">
    <div class="head">
        <?php _e('添加一条新回复'); ?>
        <?php $comments->cancelReply(); ?>
        <a class="fr back-top-btn" href="#"><strong>↑</strong><?php _e('回到顶部'); ?></a>
    </div>
	<form method="post" action="<?php $this->commentUrl() ?>" id="comment-form" role="form">
        <?php if($this->user->hasLogin()): ?>
    		<div class="cell">
                <textarea rows="6" cols="50" name="text" id="textarea" class="textarea" required ><?php $this->remember('text'); ?></textarea>
            </div>
            <div class="inner">
                <button type="submit" class="btn"><?php _e('提交评论'); ?></button>
                <span class="fr"><?php _e('请尽量让自己的回复能够对别人有帮助');?></span>
            </div>
        <?php else: ?>
        <p class="m20 aligncenter">
                        已注册用户请 <a class="btn" href="<?php $this->options->loginUrl();?>?redir=<?php echo $this->request->getRequestUrl();?>"><?php _e('登录');?></a> 或者 <a class="btn" href="<?php $this->options->registerUrl();?>"><?php _e('现在注册');?></a></p>
        <?php endif; ?>
	</form>
    
</div>
<?php endif; ?>