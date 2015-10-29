<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

// +----------------------------------------------------------------------
// | SISOME 用户积分
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.sisome.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 绛木子 <master@lixianhua.com>
// +----------------------------------------------------------------------

class Widget_Credits_List extends Widget_Abstract_Credits
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
    
    
    public function __construct($request, $response, $params = NULL){
        parent::__construct($request, $response, $params);
        $this->parameter->setDefault($params);
    }
    /**
     * 执行查询
     * @see Typecho_Widget::execute()
     */
    public function execute(){
        $this->_currentPage = $this->request->get('page', 1);
        if(!$this->parameter->pageSize) $this->parameter->pageSize = 10;
        
        $creditsSelect = $this->db->select()
        ->from('table.creditslog')
        ->where('uid = ?', $this->user->uid);
        
        $this->_countSql = clone $creditsSelect;
        
        $creditsSelect->order('created',Typecho_Db::SORT_DESC)
        ->page($this->_currentPage, $this->parameter->pageSize);
        $this->db->fetchAll($creditsSelect, array($this, 'push'));
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
