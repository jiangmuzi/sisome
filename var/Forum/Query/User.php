<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
// +----------------------------------------------------------------------
// | SISOME 根据用户ID、用户名或者用户邮箱查找用户
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.sisome.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 绛木子 <master@lixianhua.com>
// +----------------------------------------------------------------------
class Forum_Query_User extends Widget_Abstract_Users
{
    /**
     * 执行函数,初始化数据
     *
     * @access public
     * @return void
     */
    public function execute()
    {
        if($this->parameter->uid){
            $this->db->fetchRow($this->select()
                ->where('table.users.uid = ?', $this->parameter->uid)->limit(1), array($this, 'push'));
        }else if ($this->parameter->name) {
            $this->db->fetchRow($this->select()
                ->where('table.users.name = ?', $this->parameter->name)->limit(1), array($this, 'push'));
        }else if ($this->parameter->mail) {
            $this->db->fetchRow($this->select()
                ->where('table.users.mail = ?', $this->parameter->mail)->limit(1), array($this, 'push'));
        }
    }
}
