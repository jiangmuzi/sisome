<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
    </div>
</div><!-- end #body -->
<footer id="footer" role="contentinfo">
    <div class="wp">
        <div class="foot-nav">
            <?php $this->widget('Widget_Contents_Page_List')
            ->parse('<a href="{permalink}" title="{title}">{title}</a>'); ?>
        </div>
        <p> &copy; <?php echo date('Y');?> <a href="<?php $this->options->siteUrl(); ?>" target="_blank"> <?php $this->options->title() ?> </a>
            <?php _e(' / Powered by <a href="http://www.typecho.org" target="_blank">Typecho</a>'); ?>
            <?php if ($this->options->siteIcp): ?>
               / <a href="http://www.miitbeian.gov.cn/" target="blank"><?php $this->options->siteIcp(); ?></a>
            <?php endif; ?>
			<?php if ($this->options->siteStat): ?>
				<span style="display:none;"><?php $this->options->siteStat(); ?></span>
			<?php endif; ?>
    	</p> 
    </div>
</footer><!-- end #footer -->
<div class="fixed-btn">
    <a class="back-to-top" href="#" title="返回顶部"><i class="fa fa-chevron-up"></i></a>
     <?php if($this->is('post')): ?>
    <a class="go-comments" href="#comments" title="评论"><i class="fa fa-comments"></i></a>
    <?php endif; ?>
</div>
<script src="http://apps.bdimg.com/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="<?php $this->options->themeUrl('js/common.js'); ?>"></script>
<?php $this->footer(); ?>
<!-- 网站统计代码 -->
<?php if($this->options->siteStat):?>
<?php $this->options->siteStat();?>
<?php endif;?>
</body>
</html>
