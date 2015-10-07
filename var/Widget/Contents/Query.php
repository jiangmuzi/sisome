<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

// +----------------------------------------------------------------------
// | SISOME 根据内容ID获取内容
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.sisome.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 绛木子 <master@lixianhua.com>
// +----------------------------------------------------------------------

class Widget_Contents_Query extends Widget_Abstract_Contents
{
    /**
     * 执行函数,初始化数据
     *
     * @access public
     * @return void
     */
    public function execute()
    {
        if ($this->parameter->cid) {
            $this->db->fetchRow($this->select()
            ->where('table.contents.cid = ?', $this->parameter->cid)->limit(1), array($this, 'push'));
        }
    }
}
