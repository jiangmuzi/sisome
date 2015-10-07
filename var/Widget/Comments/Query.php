<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

// +----------------------------------------------------------------------
// | SISOME 根据评论ID查找评论信息
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.sisome.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 绛木子 <master@lixianhua.com>
// +----------------------------------------------------------------------

class Widget_Comments_Query extends Widget_Abstract_Comments
{
    /**
     * 执行函数,初始化数据
     *
     * @access public
     * @return void
     */
    public function execute()
    {
        if ($this->parameter->coid) {
            $this->db->fetchRow($this->select()
            ->where('table.comments.coid = ?', $this->parameter->coid)->limit(1), array($this, 'push'));
        }
    }
}
