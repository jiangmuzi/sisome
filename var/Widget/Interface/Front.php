<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

// +----------------------------------------------------------------------
// | SISOME 显示页面
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.sisome.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 绛木子 <master@lixianhua.com>
// +----------------------------------------------------------------------

class Widget_Interface_Front extends Typecho_Widget{
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
     * 模版目录
     * @var string
     */
    private $_themeDir;
    
    /**
     * 模版文件
     * @var string
     */
    private $_themeFile;
    
    /**
     * 本页关键字
     *
     * @access private
     * @var string
     */
    private $_keywords;
    
    /**
     * 本页描述
     *
     * @access private
     * @var string
     */
    private $_description;
    
    public function __construct($request, $response, $params = NULL){
        parent::__construct($request, $response, $params);
        $this->parameter->setDefault(array(
            'type'              =>  NULL,
        ));
        
        /** 初始化常用组件 */
        $this->options = $this->widget('Widget_Options');
        $this->user = $this->widget('Widget_User');
        
        /** 初始化皮肤路径 */
        $this->_themeDir = rtrim($this->options->themeFile($this->options->theme), '/') . '/';
        
        if (NULL == $this->parameter->type) {
            $this->parameter->type = Typecho_Router::$current;
        }
        
        
    }
    
    
    public function execute(){
    
        $handles = array(
            'login'                 =>  'loginHandle',
            'register'              =>  'registerHandle',
            'activate'              =>  'activateHandle',
            'setting'               =>  'settingHandle',
            'setting_avatar'        =>  'settingAvatarHandle',
            'message'               =>  'messageHandle',
            'credits'               =>  'creditsHandle',
            'forgot'                =>  'forgotHandle',
            'favorite_nodes'        =>  'favoriteHandle',
            'favorite_posts'        =>  'favoriteHandle',
        );
        
        if(isset($handles[$this->parameter->type])){
            $handle = $handles[$this->parameter->type];
            $this->$handle();
        }
    }
    /**
     * 登录
     */
    public function loginHandle(){
        $this->setMetaTitle('登录');
        $this->setThemeFile('user/login.php');
    }
    /**
     * 注册
     */
    public function registerHandle(){
        $this->setMetaTitle('注册');
        $this->setThemeFile('user/register.php');
    }
    
    public function messageHandle(){
        $this->setMetaTitle('消息提醒');
        $this->setThemeFile('user/messages.php');
    }
    /**
     * 设置
     */
    public function settingHandle(){
        $this->setMetaTitle('设置');
        $this->setThemeFile('user/setting.php');
    }
    /**
     * 设置头像
     */
    public function settingAvatarHandle(){
        $this->setMetaTitle('上传头像');
        $this->setThemeFile('user/setting_avatar.php');
    }
    /**
     * 忘记密码
     */
    public function forgotHandle(){
        $this->setMetaTitle('忘记密码');
        $this->setThemeFile('user/forgot.php');
    }
    
    
    public function creditsHandle(){
        $this->setMetaTitle('账户积分');
        $this->setThemeFile('user/credits.php');
    }
    
    /**
     * 用户收藏
     */
    public function favoriteHandle(){
        $this->setMetaTitle('我收藏的'.($this->parameter->type == 'favorite_nodes' ? '节点' : '主题'));
        $this->setThemeFile('user/'.$this->parameter->type.'.php');
    }
    
    public function metaTitle($slug=null){
        echo empty($this->_metaTitle) ? '' : $this->_metaTitle . $slug;
    }
    public function setMetaTitle($metaTitle){
        $this->_metaTitle = $metaTitle;
    }
    public function setThemeFile($file){
        $this->_themeFile = $file;
    }
    public function render(){
        
        
        /** 初始化皮肤函数 */
        $functionsFile = $this->_themeDir . 'functions.php';
        if (file_exists($functionsFile)) {
            require_once $functionsFile;
            if (function_exists('themeInit')) {
                themeInit($this);
            }
        }
        
        /** 文件不存在 */
        if (!file_exists($this->_themeDir . $this->_themeFile)) {
            Typecho_Common::error(500);
        }
    
        /** 输出模板 */
        require_once $this->_themeDir . $this->_themeFile;
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
    public function header(){
        
        if(empty($this->_keywords)){
            $this->_keywords = $this->options->keywords;
        }
        if(empty($this->_description)){
            $this->_description = $this->options->description;
        }
        
        $allows = array(
            'description'   =>  htmlspecialchars($this->_description),
            'keywords'      =>  htmlspecialchars($this->_keywords),
            'generator'     =>  $this->options->generator,
            'template'      =>  $this->options->theme,
        );
        $header = '';
        if (!empty($allows['description'])) {
            $header .= '<meta name="description" content="' . $allows['description'] . '" />' . "\n";
        }
        
        if (!empty($allows['keywords'])) {
            $header .= '<meta name="keywords" content="' . $allows['keywords'] . '" />' . "\n";
        }
        
        if (!empty($allows['generator'])) {
            $header .= '<meta name="generator" content="' . $allows['generator'] . '" />' . "\n";
        }
        
        if (!empty($allows['template'])) {
            $header .= '<meta name="template" content="' . $allows['template'] . '" />' . "\n";
        }
        echo $header;
    }
    public function footer(){}
}
