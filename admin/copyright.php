<?php if(!defined('__TYPECHO_ADMIN__')) exit; ?>
<div class="typecho-foot" role="contentinfo">
    <div class="copyright">
        <p><?php _e('由 <a href="http://www.sisome.com">%s</a> 强力驱动, 版本 %s (%s),', $options->software, $prefixVersion, $suffixVersion); ?>
			<?php _e('基于Typecho');?>
		</p>
    </div>
    <nav class="resource">
        <a href="http://docs.sisome.com"><?php _e('帮助文档'); ?></a> &bull;
        <a href="http://bbs.sisome.com"><?php _e('支持论坛'); ?></a> &bull;
        <a href="https://github.com/jiangmuzi/sisome/issues"><?php _e('报告错误'); ?></a> &bull;
        <a href="http://www.sisome.com"><?php _e('资源下载'); ?></a>
    </nav>
</div>
