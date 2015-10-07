<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

// +----------------------------------------------------------------------
// | SISOME 积分基类
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://sisome.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 绛木子 <master@lixianhua.com>
// +----------------------------------------------------------------------

class Widget_Abstract_Credits extends Widget_Abstract{
    /**
     * 积分的种类
     * @var unknown
     */
    protected $_creditType=array(
        'register'=>array('title'=>'注册用户','max'=>1),
        'login'=>array('title'=>'每日登录','max'=>1),
        'publish'=>array('title'=>'创建主题','max'=>0),
        'reply'=>array('title'=>'发表回复','max'=>0),
        'invite'=>array('title'=>'通过邀请注册','max'=>1),
		'inviter'=>array('title'=>'邀请用户注册','max'=>10),
    );
    
    /**
     * 输出词义化日期
     *
     * @access protected
     * @return string
     */
    protected function ___dateWord()
    {
        return $this->date->word();
    }
    
    /**
     * 通用过滤器
     *
     * @access public
     * @param array $value 需要过滤的行数据
     * @return array
     */
    public function filter(array $value)
    {
        $value['date'] = new Typecho_Date($value['created']);
        $value['name'] = $this->_creditType[$value['type']]['title'];
        $r = $value['amount']>0 ? '奖励' : '扣掉';
        $value['remark'] = $this->_creditType[$value['type']]['title'].' '.$r;
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
    
    /**
     * 输出文章发布日期
     *
     * @access public
     * @param string $format 日期格式
     * @return void
     */
    public function date($format = NULL)
    {
        echo $this->date->format(empty($format) ? $this->options->commentDateFormat : $format);
    }
}