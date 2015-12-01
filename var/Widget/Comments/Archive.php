<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
/**
 * 评论归档
 *
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/**
 * 评论归档组件
 *
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Widget_Comments_Archive extends Widget_Abstract_Comments
{
    /**
     * 当前页
     *
     * @access private
     * @var integer
     */
    private $_currentPage;

	/**
     * 分页计算对象
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

    /**
     * 子父级评论关系
     *
     * @access private
     * @var array
     */
    private $_threadedComments = array();
    
    /**
     * 多级评论回调函数
     * 
     * @access private
     * @var mixed
     */
    private $_customThreadedCommentsCallback = false;

    /**
     * _singleCommentOptions  
     * 
     * @var mixed
     * @access private
     */
    private $_singleCommentOptions = NULL;

	/**
     * @return the $_total
     */
    public function getTotal(){
        if (false === $this->_total) {
            $this->_total = $this->size($this->_countSql);
        }
        return $this->_total;
    }
    /**
     * 构造函数,初始化组件
     *
     * @access public
     * @param mixed $request request对象
     * @param mixed $response response对象
     * @param mixed $params 参数列表
     * @return void
     */
    public function __construct($request, $response, $params = NULL)
    {
        parent::__construct($request, $response, $params);
        $this->parameter->setDefault('parentId=0&commentPage=0&commentsNum=0&allowComment=1');
        
        /** 初始化回调函数 */
        if (function_exists('threadedComments')) {
            $this->_customThreadedCommentsCallback = true;
        }
    }
	/**
     * 评论回调函数
     * 
     * @access private
     * @return void
     */
    private function threadedCommentsCallback(){
		
		$singleCommentOptions = $this->_singleCommentOptions;
        if ($this->_customThreadedCommentsCallback) {
            return threadedComments($this, $singleCommentOptions);
        }
		$this->realAuthorUrl = $this->authorId ?  $this->poster->ucenter : 'javascript:;';
		$commentClass = '';
		if ($this->authorId && $this->authorId == $this->ownerId) {
			$commentClass = ' reply-by-author';
		}
		echo "<div id=\"{$this->theId}\" class=\"cell{$commentClass}\"><div class=\"reply-avatar fl\">";
		if($this->options->commentsAvatar){
			$this->poster->avatar(48);
		}
		
		echo '</div><div class="fr">';
		if($this->user->hasLogin()){
			echo '<a href="javascript:replyAt(\''.$this->poster->name.'\');">' . $singleCommentOptions->replyWord . '</a>';
		}
		if(!$this->options->commentsPageBreak){
			$no = $this->sequence;
		}else{
			$no = ( ($this->_currentPage - 1) * $this->options->commentsPageSize ) + $this->sequence;
		}
		
		if('DESC' == $this->options->commentsOrder){
			$no = ($this->getTotal() - $no)+1;
		}
		echo '<span class="no">'.$no.'</span>';
		echo '</div><p><a href="'.$this->realAuthorUrl.'">'.$this->poster->name.'</a>';
		echo '<span class="reply-time">';
		$this->dateWord();
		echo '</span>';
		if('waiting' == $this->status){
			echo '<span class="reply-waiting">'.$singleCommentOptions->commentStatus.'</span>';
		}
		echo '</p><div class="reply-content">';
		$this->content();
		echo '</div></div>';
	}
    
    /**
     * 获取当前评论链接
     *
     * @access protected
     * @return string
     */
    protected function ___permalink()
    {

        if ($this->options->commentsPageBreak) {            
            $pageRow = array('permalink' => $this->parentContent['pathinfo'], 'commentPage' => $this->_currentPage);
            return Typecho_Router::url('comment_page',
                        $pageRow, $this->options->index) . '#' . $this->theId;
        }
        
        return $this->parentContent['permalink'] . '#' . $this->theId;
    }

    /**
     * 重载内容获取
     *
     * @access protected
     * @return void
     */
    protected function ___parentContent()
    {
        return $this->parameter->parentContent;
    }

    /**
     * 输出文章评论数
     *
     * @access public
     * @param string $string 评论数格式化数据
     * @return void
     */
    public function num()
    {
        $args = func_get_args();
        if (!$args) {
            $args[] = '%d';
        }

        $num = intval($this->getTotal());

        echo sprintf(isset($args[$num]) ? $args[$num] : array_pop($args), $num);
    }

    /**
     * 执行函数
     *
     * @access public
     * @return void
     */
    public function execute()
    {
        if (!$this->parameter->parentId) {
            return;
        }

        $select = $this->select()->where('table.comments.cid = ?', $this->parameter->parentId)
        ->where('table.comments.type = ?', 'comment');
		
        if($this->user->hasLogin()){
			$select->where('table.comments.status = ? OR (table.comments.authorId = ? AND table.comments.status = ?)', 'approved', $this->user->uid, 'waiting');
		}else{
			$select->where('table.comments.status = ?', 'approved');
		}
        
        $this->_countSql = clone $select;

        /** 评论排序 */
        if ('DESC' == $this->options->commentsOrder) {
			$select->order('table.comments.coid', 'DESC');
        }else{
			$select->order('table.comments.coid', 'ASC');
		}
        
        /** 对评论进行分页 */
        if ($this->options->commentsPageBreak && ($this->getTotal() > $this->options->commentsPageSize)) {
			
            $this->_currentPage = $this->parameter->commentPage ? $this->parameter->commentPage : 1;
            
            /** 截取评论 */
            $this->stack = array_slice($this->stack,
                ($this->_currentPage - 1) * $this->options->commentsPageSize, $this->options->commentsPageSize);
            
			$select->page($this->_currentPage, $this->options->commentsPageSize);
			
        }

		$this->db->fetchAll($select, array($this, 'push'));

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
		//将行数据按顺序置位
        $this->row = $value;

		$this->stack[$value['coid']] = $value;
        $this->length ++;
		
        return $value;
    }

    /**
     * 输出分页
     *
     * @access public
     * @param string $prev 上一页文字
     * @param string $next 下一页文字
     * @param int $splitPage 分割范围
     * @param string $splitWord 分割字符
     * @param string $template 展现配置信息
     * @return void
     */
    public function pageNav($prev = '&laquo;', $next = '&raquo;', $splitPage = 3, $splitWord = '...', $template = '')
    {
        if ($this->options->commentsPageBreak && $this->getTotal() > $this->options->commentsPageSize) {
            $default = array(
                'wrapTag'       =>  'ol',
                'wrapClass'     =>  'page-navigator'
            );

            if (is_string($template)) {
                parse_str($template, $config);
            } else {
                $config = $template;
            }

            $template = array_merge($default, $config);

            $pageRow = $this->parameter->parentContent;
            $pageRow['permalink'] = $pageRow['pathinfo'];

            $query = Typecho_Router::url('comment_page', $pageRow, $this->options->index);

            /** 使用盒状分页 */
            $nav = new Typecho_Widget_Helper_PageNavigator_Box($this->_total,
                $this->_currentPage, $this->options->commentsPageSize, $query);
            $nav->setPageHolder('commentPage');
            $nav->setAnchor('comments');
            
            echo '<' . $template['wrapTag'] . (empty($template['wrapClass']) 
                    ? '' : ' class="' . $template['wrapClass'] . '"') . '>';
            $nav->render($prev, $next, $splitPage, $splitWord, $template);
            echo '</' . $template['wrapTag'] . '>';
        }
    }
    
    /**
     * 列出评论
     * 
     * @access private
     * @param mixed $singleCommentOptions 单个评论自定义选项
     * @return void
     */
    public function listComments($singleCommentOptions = NULL)
    {
		
        //初始化一些变量
        $this->_singleCommentOptions = Typecho_Config::factory($singleCommentOptions);
        $this->_singleCommentOptions->setDefault(array(
            'before'        =>  '',
            'after'         =>  '',
            'beforeAuthor'  =>  '',
            'afterAuthor'   =>  '',
            'beforeDate'    =>  '',
            'afterDate'     =>  '',
            'dateFormat'    =>  $this->options->commentDateFormat,
            'replyWord'     =>  _t('<i class="fa fa-reply"></i>'),
            'commentStatus' =>  _t('回复待审核'),
            'avatarSize'    =>  32,
            'defaultAvatar' =>  NULL
        ));
        $this->pluginHandle()->trigger($plugged)->listComments($this->_singleCommentOptions, $this);

        if (!$plugged) {
            if ($this->have()) { 
                echo $this->_singleCommentOptions->before;
            
                while ($this->next()) {
                    $this->threadedCommentsCallback();
                }
            
                echo $this->_singleCommentOptions->after;
            }
        }
    }
}
