<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('header.php'); ?>
<div id="sidebar">
    <?php $this->need('user/widget_user.php'); ?>
</div>
<div id="main">
    <div class="user-form">
        <div class="box">
            <div class="head">
                <a href="<?php $this->options->siteUrl(); ?>"><?php $this->options->title();?></a> &nbsp;&nbsp;&raquo;&nbsp;&nbsp;
                <?php $this->getMetaTitle();?>
            </div>
    		<div class="cell">
    		  <form class="form" method="POST" action="<?php $this->options->someAction('setting');?>">
    		  <div class="field">
    				<label>用户名：</label>
    			     <input class="field-ipt" type="text" value="<?php if($this->user->name){$this->user->name();}else{ _e('还未填写');}?>" disabled/>
    			</div>
    			<div class="field">
    				<label>邮箱：</label>
    			     <input class="field-ipt" type="text" value="<?php $this->user->mail();?>" disabled/>
    			</div>
    			<?php if($this->user->token):?>
    			<div class="field">
    				<label>&nbsp;</label>
    			    <span class="field-ipt red">邮箱未验证</span>
    			</div>
    			<?php endif;?>
    			<div class="field">
    				<label for="screenName">昵称：</label>
    				<input class="field-ipt" id="screenName" type="text" name="screenName" value="<?php $this->user->screenName();?>">
    			</div>
    			<div class="field">
    				<label for="url">个人主页：</label>
    				<input class="field-ipt" id="url" type="text" name="url" value="<?php $this->user->url();?>">
    			</div>
    			<div class="field">
    				<label for="location">所在地：</label>
    				<input class="field-ipt" id="location" type="text" name="location" value="<?php $this->user->location();?>">
    			</div>
    			<div class="field">
    				<label for="sign">签名：</label>
    				<input class="field-ipt" id="sign" type="text" name="sign" value="<?php $this->user->sign();?>">
    			</div>
    			<div class="field">
    				<label for="intro">简介：</label>
    				<textarea class="field-ipt" id="intro" name="intro" rows="4"><?php $this->user->intro();?></textarea>
    			</div>
    			<div class="field">
    			<label>&nbsp;</label>
    			 <button class="btn field-ipt" type="submit" name="do" value="profile"><?php _e('保存设置');?></button>
    			</div>
    			<input type="hidden" name="uid" value="<?php $this->user->uid();?>">
    		</form>
    		</div>
        </div>
        <div class="box">
		  <div class="head"><?php _e('上传头像');?><a class="fade fr" href="#">取消当前头像</a></div>
		  <div class="cell">
		      <div class="field">
		          <label>当前头像</label>
		          <p class="m0">
    		          <img class="avatar vab mr10" width="96" src="<?php echo Widget_Common::avatar($this->user->uid,96);?>" align="default">
    		          <img class="avatar vab mr10" width="48" src="<?php echo Widget_Common::avatar($this->user->uid,48);?>" align="default">
    		          <img class="avatar vab mr10" width="24" src="<?php echo Widget_Common::avatar($this->user->uid,24);?>" align="default">
                    </p>
		      </div>
		      <div class="field">
		          <label>&nbsp;</label>
		          <p><a class="btn" href="<?php $this->options->someUrl('setting_avatar');?>"><?php _e('上传新头像');?></a></p>
		      </div>
		  </div>
		  <div class="inner">
		      <p class="m0">关于头像的规则</p>
		      <ul>
		          <li>禁止使用任何低俗或者敏感图片作为头像</li>
		          <li>如果你是男的，请不要用女人的照片作为头像，这样可能会对其他会员产生误导</li>
		      </ul>
		  </div>
		</div>
        <div class="box">
		  <div class="head"><?php _e('修改邮箱');?></div>
		  <div class="cell">
		      <form class="form" method="POST" action="<?php $this->options->someAction('setting');?>">
		          <div class="field">
    				<label for="mail">电子邮箱：</label>
    				<input class="field-ipt" id="mail" type="text" name="mail" value="<?php if($this->user->token){_e($this->user->mail);}?>" placeholder="填写常用的电子邮箱">
    			</div>
    			<div class="field">
    				<label>&nbsp;</label>
    				<button type="button" class="btn btn-sendverify" data-url="<?php $this->options->someAction('setting');?>" data-target="#mail"><?php _e('获取验证码');?></button>
    			</div>
    			<div class="field">
    				<label for="confirm">验证码：</label>
    				<input class="field-ipt" id="confirm" type="text" name="confirm" value="" placeholder="填写邮箱收到的验证码">
    			</div>
    			<div class="field">
    			<label>&nbsp;</label>
    			 <button class="btn field-ipt" type="submit" name="do" value="changemail">修改</button>
    			</div>
    			<input type="hidden" name="uid" value="<?php $this->user->uid();?>">
		      </form>
		  </div>
		</div>
		<div class="box">
		  <div class="head"><?php _e('修改密码');?><span class="fade fr">如果你不打算更改密码，请留空以下区域</span></div>
		  <div class="cell">
		      <form class="form" method="POST" action="<?php $this->options->someAction('setting');?>">
		          <div class="field">
    				<label for="password">当前登录密码：</label>
    				<input class="field-ipt" id="password" type="password" name="password" value="" placeholder="当前登录密码">
    			</div>
    			<div class="field">
    				<label for="newpassword">新密码：</label>
    				<input class="field-ipt" id="newpassword" type="password" name="newpassword" value="" placeholder="要设置的新密码">
    			</div>
    			<div class="field">
    				<label for="confirm">确认新密码：</label>
    				<input class="field-ipt" id="confirm" type="password" name="confirm" value="" placeholder="重复新密码">
    			</div>
    			<div class="field">
    			<label>&nbsp;</label>
    			 <button class="btn field-ipt" type="submit" name="do" value="changepass">修改</button>
    			</div>
    			<input type="hidden" name="uid" value="<?php $this->user->uid();?>">
		      </form>
		  </div>
		</div>
    </div>
</div><!-- end #main -->
<?php $this->need('footer.php'); ?>
