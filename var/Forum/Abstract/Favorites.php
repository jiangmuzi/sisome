<?php
// +----------------------------------------------------------------------
// | SISOME 收藏基类
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://sisome.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 绛木子 <master@lixianhua.com>
// +----------------------------------------------------------------------
class Forum_Abstract_Favorites extends Widget_Abstract{
	/**
     * 收藏是否已存在
     *
     * @access public
     * @param string $mail 电子邮件
     * @return boolean
     */
    public function favoriteExists($type,$srcId)
	{
        $select = $this->select()
        ->where('type = ?',$type)
		->where('srcId = ?',$srcId)
        ->limit(1);

        $favorite = $this->db->fetchRow($select);
        return $favorite;
    }
    /**
     * 通用过滤器
     *
     * @access public
     * @param array $value 需要过滤的行数据
     * @return array
     */
    public function filter(array $value){
        if($value['type']=='post'){
            $value['content'] = $this->widget('Forum_Query_Content@cid_'.$value['srcId'],'cid='.$value['srcId']);
        }
        if($value['type']=='tag'){
            $value['node'] = $this->widget('Forum_Query_Meta@mid_'.$value['srcId'],'mid='.$value['srcId']);
        }
        return $value;
    }
    /**
     * 将每行的值压入堆栈
     *
     * @access public
     * @param array $value 每行的值
     * @return array
     */
    public function push(array $value)
    {
        $value = $this->filter($value);
        return parent::push($value);
    }
    /**
     * 查询方法
     *
     * @access public
     * @return Typecho_Db_Query
     */
    public function select()
	{
        return $this->db->select()->from('table.favorites');
    }
    
    /**
     * 增加记录方法
     *
     * @access public
     * @param array $rows 字段对应值
     * @return integer
     */
    public function insert(array $rows)
	{
        return $this->db->query($this->db->insert('table.favorites')->rows($rows));
    }
    /**
     * 更新记录方法
     *
     * @access public
     * @param array $rows 字段对应值
     * @param Typecho_Db_Query $condition 查询对象
     * @return integer
     */
    public function update(array $rows, Typecho_Db_Query $condition)
    {
        return $this->db->query($condition->update('table.favorites')->rows($rows));
    }
    /**
     * 删除记录方法
     *
     * @access public
     * @param Typecho_Db_Query $condition 查询对象
     * @return integer
     */
    public function delete(Typecho_Db_Query $condition)
    {
        return $this->db->query($condition->delete('table.favorites'));
    }
    /**
     * 按照条件计算内容数量
     *
     * @access public
     * @param Typecho_Db_Query $condition 查询对象
     * @return integer
     */
    public function size(Typecho_Db_Query $condition)
    {
        return $this->db->fetchObject($condition
            ->select(array('COUNT(DISTINCT table.favorites.fid)' => 'num'))
            ->from('table.favorites')
            ->cleanAttribute('group'))->num;
    }
}