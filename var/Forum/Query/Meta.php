<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
// +----------------------------------------------------------------------
// | SISOME 根据MetaID获取节点
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.sisome.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 绛木子 <master@lixianhua.com>
// +----------------------------------------------------------------------
class Forum_Query_Meta extends Widget_Abstract_Metas
{
    /**
     * 执行函数,初始化数据
     *
     * @access public
     * @return void
     */
    public function execute()
    {
        if ($this->parameter->mid) {
            $this->db->fetchRow($this->select()
            ->where('table.metas.mid = ?', $this->parameter->mid)->limit(1), array($this, 'push'));
        }
    }
}
