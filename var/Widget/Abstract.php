<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
/**
 * 纯数据抽象组件
 *
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/**
 * 纯数据抽象组件
 *
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
abstract class Widget_Abstract extends Typecho_Widget
{
    /**
     * 全局选项
     *
     * @access protected
     * @var Widget_Options
     */
    protected $options;

    /**
     * 用户对象
     *
     * @access protected
     * @var Widget_User
     */
    protected $user;

    /**
     * 安全模块
     *
     * @var Widget_Security
     */
    protected $security;

    /**
     * 数据库对象
     *
     * @access protected
     * @var Typecho_Db
     */
    protected $db;

    /**
     * 模版目录
     * @var string
     */
    private $_themeDir;
    /**
     * 构造函数,初始化组件
     *
     * @access public
     * @param mixed $request request对象
     * @param mixed $response response对象
     * @param mixed $params 参数列表
     */
    public function __construct($request, $response, $params = NULL)
    {
        parent::__construct($request, $response, $params);

        /** 初始化数据库 */
        $this->db = Typecho_Db::get();

        /** 初始化常用组件 */
        $this->options = $this->widget('Widget_Options');
        $this->user = $this->widget('Widget_User');
        $this->security = $this->widget('Widget_Security');
        
        $this->_themeDir = rtrim($this->options->themeFile($this->options->theme), '/') . '/';
        
        /** 加载皮肤函数 */
        $functionsFile = $this->_themeDir . 'functions.php';
        if (!$this->_invokeFromOutside && file_exists($functionsFile)) {
            require_once $functionsFile;
        }
    }

    /**
     * 查询方法
     *
     * @access public
     * @return Typecho_Db_Query
     */
    abstract public function select();

    /**
     * 获得所有记录数
     *
     * @access public
     * @param Typecho_Db_Query $condition 查询对象
     * @return integer
     */
    abstract public function size(Typecho_Db_Query $condition);

    /**
     * 增加记录方法
     *
     * @access public
     * @param array $rows 字段对应值
     * @return integer
     */
    abstract public function insert(array $rows);

    /**
     * 更新记录方法
     *
     * @access public
     * @param array $rows 字段对应值
     * @param Typecho_Db_Query $condition 查询对象
     * @return integer
     */
    abstract public function update(array $rows, Typecho_Db_Query $condition);

    /**
     * 删除记录方法
     *
     * @access public
     * @param Typecho_Db_Query $condition 查询对象
     * @return integer
     */
    abstract public function delete(Typecho_Db_Query $condition);
    
    public function metaTitle($slug=null){
        echo empty($this->_metaTitle) ? '' : $this->_metaTitle . $slug;
    }
    protected function setMetaTitle($metaTitle){
        $this->_metaTitle = $metaTitle;
    }
    public function render($themeFile){
        /** 文件不存在 */
        if (!file_exists($this->_themeDir . $themeFile)) {
            Typecho_Common::error(500);
        }
    
        /** 输出模板 */
        require_once $this->_themeDir . $themeFile;
    }
    /**
     * 获取主题文件
     *
     * @access public
     * @param string $fileName 主题文件
     * @return void
     */
    public function need($fileName){
        /** 输出模板 */
        require_once $this->_themeDir . $fileName;
    }
}
