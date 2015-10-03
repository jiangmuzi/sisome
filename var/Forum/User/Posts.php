<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
/**
 * 文章管理列表
 *
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/**
 * 文章管理列表组件
 *
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Forum_User_Posts extends Widget_Abstract_Contents
{
    /**
     * 用于计算数值的语句对象
     *
     * @access private
     * @var Typecho_Db_Query
     */
    private $_countSql;

    /**
     * 所有文章个数
     *
     * @access private
     * @var integer
     */
    private $_total = false;

    private $_user;
    /**
     * 当前页
     *
     * @access private
     * @var integer
     */
    private $_currentPage;

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
     * 当前文章的草稿
     *
     * @access protected
     * @return array
     */
    protected function ___hasSaved()
    {
        $savedPost = $this->db->fetchRow($this->db->select('cid', 'modified')
        ->from('table.contents')
        ->where('table.contents.parent = ? AND (table.contents.type = ? OR table.contents.type = ?)',
            $this->cid, 'post_draft', 'page_draft')
        ->limit(1));

        if ($savedPost) {
            $this->modified = $savedPost['modified'];
            return true;
        }

        return false;
    }

    /**
     * 执行函数
     *
     * @access public
     * @return void
     */
    public function execute(){
        $this->_user = $this->widget('Forum_User')->getUser();
        $this->parameter->setDefault('pageSize=10');
        $this->_currentPage = $this->request->get('page', 1);

        /** 构建基础查询 */
        $select = $this->select()->where('table.contents.type = ? OR (table.contents.type = ? AND table.contents.parent = ?)', 'post', 'post_draft', 0);

        /** 过滤分类 */
        if (NULL != ($category = $this->request->category)) {
            $select->join('table.relationships', 'table.contents.cid = table.relationships.cid')
            ->where('table.relationships.mid = ?', $category);
        }

        /** 如果具有编辑以上权限,可以查看所有文章,反之只能查看自己的文章 */
        /*if (!$this->user->pass('editor', true)) {
            $select->where('table.contents.authorId = ?', $this->user->uid);
        } else if (isset($this->request->uid)) {
            $select->where('table.contents.authorId = ?', $this->request->filter('int')->uid);
        }*/

        $select->where('table.contents.authorId = ?', $this->_user->uid);
        
        /** 过滤标题 */
        if (NULL != ($keywords = $this->request->filter('search')->keywords)) {
            $args = array();
            $keywordsList = explode(' ', $keywords);
            $args[] = implode(' OR ', array_fill(0, count($keywordsList), 'table.contents.title LIKE ?'));

            foreach ($keywordsList as $keyword) {
                $args[] = '%' . $keyword . '%';
            }

            call_user_func_array(array($select, 'where'), $args);
        }

        /** 给计算数目对象赋值,克隆对象 */
        $this->_countSql = clone $select;

        /** 提交查询 */
        $select->order('table.contents.created', Typecho_Db::SORT_DESC)
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

