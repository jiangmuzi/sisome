<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('header.php'); ?>
<div class="publish" id="sidebar">
    <section class="box">
        <div class="head"><?php _e('发帖提示'); ?></div>
        <div class="inner tips">
            <ul style="margin-top: 0px;">
                <li><p>主题标题</p>
                    <p>请在标题中描述内容要点。如果一件事情在标题的长度内就已经可以说清楚，那就没有必要写正文了。</p>
                </li>
                <li><p>正文</p>
                    <p>可以在正文中为你要发布的主题添加更多细节。<?php $this->options->title();?> 支持 <span style="font-family: Consolas, 'Panic Sans', mono"><a href="https://help.github.com/articles/github-flavored-markdown" target="_blank">GitHub Flavored Markdown</a></span> 文本标记语法。</p>
                    <p>在正式提交之前，你可以点击本页面左下角的“预览主题”来查看 Markdown 正文的实际渲染效果。</p>
                </li>
                <li><p>选择节点</p>
                    <p> 在最后，请为你的主题选择一个节点。恰当的归类会让你发布的信息更加有用。</p>
                    <p> 你可以在主题发布后 300 秒内，对标题或者正文进行编辑。同时，在 300 秒内，你可以重新为主题选择节点。</p>
                </li>
            </ul>
        </div>
    </section>
    <section class="box">
        <div class="head">
            <strong><?php _e('社区指导原则'); ?></strong>
        </div>
        <div class="inner tips">
            <ul style="margin-top: 0px;">
                <li><p>尊重原创</p>
                    <p>请不要在 <?php $this->options->title();?> 发布任何盗版下载链接，包括软件、音乐、电影等等。
                    <?php $this->options->title();?> 是创意工作者的社区，我们尊重原创。</p>
                </li>
                <li><p>友好互助</p>
                    <p>保持对陌生人的友善。用知识去帮助别人。</p>
                </li>
            </ul>
        </div>
    </section>
</div>
<div class="publish" id="main">
    <div class="box">
    <div class="head">
        <div class="location">
            <a href="<?php $this->options->siteUrl(); ?>"><?php $this->options->title();?></a> &nbsp;&nbsp;&raquo;&nbsp;&nbsp;
            <?php if(!empty($this->currentTag)):?>
                <a href="<?php $this->options->index('tag/'.$this->currentTag['slug']); ?>"><?php echo $this->currentTag['name'];?></a> &nbsp;&nbsp;&raquo;&nbsp;&nbsp;
            <?php endif;?>
    		<?php if($this->have()){_e('编辑主题');}else{_e('创作新主题');}?>
        </div>
    </div>
    <form action="<?php $this->options->someAction('publish');?>" method="POST">
    <input type="hidden" name="cid" value="<?php $this->cid();?>">
    <div class="cell">
        <div class="fr fade" id="title_remaining">120</div>
        <?php _e('主题标题');?>
    </div>
    <div class="cell p0">
        <textarea class="title" rows="1" maxlength="120" id="topic_title" name="title" autofocus="autofocus" placeholder="请输入主题标题，如果标题能够表达完整内容，则正文可以为空"><?php $this->title();?></textarea>
    </div>
    <div class="cell">
        <div class="fr fade" id="content_remaining">20000</div>
        <?php _e('正文');?>
    </div>
    <div class="cell p0">
        <textarea class="content" maxlength="20000" id="topic_content" name="text"><?php $this->text();?></textarea>
    </div>
    <div class="cell p0">
        <input class="tags" id="tagsInput" type="text" name="tags" value="<?php $this->tags(',',false);?>" placeholder="请输入主题标签">
    </div>
    <div class="cell">
        <select id="topic-node" class="topic-select-btn" name="category">
            <option value="0"><?php _e('请选择一个节点');?></option>
            <?php Typecho_Widget::widget('Widget_Metas_Category_List')->listNodes(
				array('node'=>'<option value="{mid}">{name}</option>','cateBefore'=>'<optgroup label="{name}">','cateAfter'=>'</optgroup>')); ?>
        </select>
    </div>
    <div class="cell">
        <button class="btn" type="button" onclick="prevTopic();" ><i class="fa fa-eye"></i> <?php _e('预览主题');?></button>
        <?php if($this->have()):?>
        <button class="btn fr" type="submit" name="do" value="save" ><i class="fa fa-paper-plane"></i> <?php _e('保存主题');?></button>
        <?php else:?>
        <button class="btn fr" type="submit" name="do" value="publish" ><i class="fa fa-paper-plane"></i> <?php _e('发布主题');?></button>
        <?php endif;?>
        
    </div>
    </form>
    <div id="topic_preview_box"></div>
</div><!-- end #main-->
<?php $this->need('footer.php'); ?>
