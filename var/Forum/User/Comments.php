<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
/**
 * Typecho Blog Platform
 *
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

/**
 * 后台评论输出组件
 *
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Forum_User_Comments extends Widget_Abstract_Comments
{
    private $_user;
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
     * 获取当前内容结构
     *
     * @access protected
     * @return array
     */
    protected function ___parentContent()
    {
        $cid = isset($this->request->cid) ? $this->request->filter('int')->cid : $this->cid;
        return $this->db->fetchRow($this->widget('Widget_Abstract_Contents')->select()
        ->where('table.contents.cid = ?', $cid)
        ->limit(1), array($this->widget('Widget_Abstract_Contents'), 'filter'));
    }

    /**
     * 执行函数
     *
     * @access public
     * @return void
     */
    public function execute(){
        $this->_user = $this->widget('Forum_User')->getUser();

        $select = $this->select();
        $this->parameter->setDefault('pageSize=20');
        $this->_currentPage = $this->request->get('page', 1);

        /** 过滤标题 */
        if (NULL != ($keywords = $this->request->filter('search')->keywords)) {
            $select->where('table.comments.text LIKE ?', '%' . $keywords . '%');
        }

        $select->where('table.comments.authorId = ?', $this->_user->uid);
        
        if (in_array($this->request->status, array('approved', 'waiting', 'spam'))) {
            $select->where('table.comments.status = ?', $this->request->status);
        } else if ('hold' == $this->request->status) {
            $select->where('table.comments.status <> ?', 'approved');
        } else {
            $select->where('table.comments.status = ?', 'approved');
        }

        //增加按文章归档功能
        if (isset($this->request->cid)) {
            $select->where('table.comments.cid = ?', $this->request->filter('int')->cid);
        }

        $this->_countSql = clone $select;

        $select->order('table.comments.coid', Typecho_Db::SORT_DESC)
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
    /**
     * 输出分页
     *
     * @access public
     * @return void
     */
    public function pageNav()
    {
        $query = $this->request->makeUriByRequest('page={page}');

        /** 使用盒状分页 */
        $nav = new Typecho_Widget_Helper_PageNavigator_Box($this->getTotal(),
        $this->_currentPage, $this->parameter->pageSize, $query);
        $nav->render(_t('&laquo;'), _t('&raquo;'));
    }
}
