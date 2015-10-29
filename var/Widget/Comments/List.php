<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

// +----------------------------------------------------------------------
// | SISOME 评论列表
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.sisome.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 绛木子 <master@lixianhua.com>
// +----------------------------------------------------------------------

class Widget_Comments_List extends Widget_Abstract_Comments
{
    /**
     * 分页计算对象
     *
     * @access private
     * @var Typecho_Db_Query
     */
    private $_countSql;
    
    /**
     * 当前页
     *
     * @access private
     * @var integer
     */
    private $_currentPage;
    
    /**
     * 所有文章个数
     *
     * @access private
     * @var integer
     */
    private $_total = false;
    
    /**
     * 分页对象
     * @var Typecho_Widget_Helper_PageNavigator_Classic
     */
    private $_pageNav;
    
    /**
     * @return the $_currentPage
     */
    public function getCurrentPage()
    {
        return $this->_currentPage;
    }
    /**
     * @return the $_total
     */
    public function getTotal()
    {
        if (false === $this->_total) {
            $this->_total = $this->size($this->_countSql);
        }
        return $this->_total;
    }
    /**
     * 获取页数
     *
     * @return integer
     */
    public function getTotalPage(){
        return ceil($this->getTotal() / $this->parameter->pageSize);
    }
    
    /**
     * 执行函数
     *
     * @access public
     * @return void
     */
    public function execute(){
        $select = $this->select();
        
        $this->parameter->setDefault(
            array(
                'sort'=>'created',  //排序字段
                'desc'=>true,		//顺序、逆序
                'pageSize'=>10,		//获取内容条数
                'last'=>0,           //最后回复时间
                'uid'=>0            //获取某用户回复
            ));
        
        $this->_currentPage = $this->request->get('page', 1);
        $desc = $this->parameter->desc ? Typecho_Db::SORT_DESC : Typecho_Db::SORT_ASC ;
        
        if($this->parameter->last){
            $select->where('table.comments.created > ?', $this->parameter->last);
        }
        if($this->parameter->uid){
            $select->where('table.comments.authorId = ?', $this->parameter->uid);
        }
        
        $select->where('table.comments.status = ?', 'approved');

        $this->_countSql = clone $select;
        
        $select->order('table.comments.'.$this->parameter->sort, $desc)
            ->page($this->_currentPage, $this->parameter->pageSize);

        $this->db->fetchAll($select, array($this, 'push'));
    }
    
    /**
     * 前一页
     *
     * @access public
     * @param string $word 链接标题
     * @param string $page 页面链接
     * @return void
     */
    public function pageLink($word = '&laquo; Previous Entries', $page = 'prev'){
        if ($this->have()) {
            if (empty($this->_pageNav)) {
                $query = $this->request->makeUriByRequest('page={page}');
                /** 使用盒状分页 */
                $this->_pageNav = new Typecho_Widget_Helper_PageNavigator_Classic($this->getTotal(),
                    $this->_currentPage, $this->parameter->pageSize, $query);
            }
            $this->_pageNav->{$page}($word);
        }
    }
}
