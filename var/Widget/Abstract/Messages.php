<?php
// +----------------------------------------------------------------------
// | SISOME 应用入口文件
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.sisome.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 绛木子 <master@lixianhua.com>
// +----------------------------------------------------------------------
class Widget_Abstract_Messages extends Widget_Abstract{
    
    
    /**
     * 通用过滤器
     *
     * @access public
     * @param array $value 需要过滤的行数据
     * @return array
     */
    public function filter(array $value){
        if($value['type']=='comment' || $value['type']=='at'){
			$comment = $this->widget('Some_Query_Comment@coid_'.$value['srcId'],array('coid'=>$value['srcId']));
			$value['author'] = $comment->poster;
			$value['title'] = $comment->parentContent['title'];
			$value['permalink'] = $comment->permalink;
			$value['content'] = $comment->content;
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
    public function select(){
        return $this->db->select()->from('table.messages');
    }
    
    /**
     * 增加记录方法
     *
     * @access public
     * @param array $rows 字段对应值
     * @return integer
     */
    public function insert(array $rows){
        return $this->db->query($this->db->insert('table.messages')->rows($rows));
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
        return $this->db->query($condition->update('table.messages')->rows($rows));
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
            ->select(array('COUNT(DISTINCT table.messages.id)' => 'num'))
            ->from('table.messages')
            ->cleanAttribute('group'))->num;
    }
	/* (non-PHPdoc)
     * @see Widget_Abstract::delete()
     */
    public function delete(Typecho_Db_Query $condition)
    {
        // TODO Auto-generated method stub
        
    }

}