<?php
// +----------------------------------------------------------------------
// | SISOME 积分基类
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://sisome.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 绛木子 <master@lixianhua.com>
// +----------------------------------------------------------------------
class Forum_Abstract_Credits extends Widget_Abstract{
	/* (non-PHPdoc)
     * @see Widget_Abstract::select()
     */
    public function select()
    {
        return $this->db->select()->from('table.creditslog');
        
    }

	/* (non-PHPdoc)
     * @see Widget_Abstract::insert()
     */
    public function insert(array $rows)
    {
        return $this->db->query($this->db->insert('table.creditslog')->rows($rows));
        
    }

	/* (non-PHPdoc)
     * @see Widget_Abstract::update()
     */
    public function update(array $rows, Typecho_Db_Query $condition)
    {
        return $this->db->query($condition->update('table.creditslog')->rows($rows));
    }

	/* (non-PHPdoc)
     * @see Widget_Abstract::delete()
     */
    public function delete(Typecho_Db_Query $condition)
    {
        return $this->db->query($condition->delete('table.creditslog'));
        
    }

    /**
     * 获得所有记录数
     *
     * @access public
     * @param Typecho_Db_Query $condition 查询对象
     * @return integer
     */
    public function size(Typecho_Db_Query $condition){
        return $this->db->fetchObject($condition->select(array('COUNT(id)' => 'num'))->from('table.creditslog'))->num;
    }
    
}