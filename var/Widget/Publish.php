<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

// +----------------------------------------------------------------------
// | SISOME 发布主题
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.sisome.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 绛木子 <master@lixianhua.com>
// +----------------------------------------------------------------------

class Widget_Publish extends Widget_Abstract_Contents{
    
    /**
     * 风格目录
     *
     * @access private
     * @var string
     */
    private $_themeDir;
    
    /**
     * 本页标题
     *
     * @access private
     * @var string
     */
    private $_metaTitle;
    
	protected $currentTag=array();
	
	/**
	 * @return the $_description
	 */
	public function getMetaTitle($slug='')
	{
	    return empty($this->_metaTitle) ? '' : $this->_metaTitle . $slug;
	}
	
	
    public function __construct($request, $response, $params = NULL){
        parent::__construct($request, $response, $params);
        if(!$this->user->hasLogin()){
            $this->response->redirect($this->some->___loginUrl().'?redir='.$this->request->getRequestUrl());
        }
        
        /** 初始化皮肤路径 */
        $this->_themeDir = rtrim($this->options->themeFile($this->options->theme), '/') . '/';
        
    }
    
    /**
     * 执行函数
     *
     * @access public
     * @return void
     */
    public function execute(){
        
		/** 必须为贡献者以上权限 */
		$this->user->pass('contributor');
		
        /** 获取文章内容 */
        if (!empty($this->request->cid)) {
            $this->db->fetchRow($this->select()
            ->where('table.contents.type = ? OR table.contents.type = ?', 'post', 'post_draft')
            ->where('table.contents.cid = ?', $this->request->filter('int')->cid)
            ->limit(1), array($this, 'push'));
            
            if (!$this->have()) {
                throw new Typecho_Widget_Exception(_t('文章不存在'), 404);
            } else if ($this->have() && !$this->allow('edit')) {
                throw new Typecho_Widget_Exception(_t('没有编辑权限'), 403);
            } else if ($this->created+300<$this->options->gmtTime && !$this->user->pass('editor',true)){
                throw new Typecho_Widget_Exception(_t('已不允许编辑'), 403);
            }
        }
    }

    
    /**
     * 获取文章权限
     *
     * @access public
     * @param string $permission 权限
     * @return unknown
     */
    public function allow()
    {
        $permissions = func_get_args();
        $allow = true;
    
        foreach ($permissions as $permission) {
            $permission = strtolower($permission);
    
            if ('edit' == $permission) {
                $allow &= ($this->user->pass('editor', true) || $this->authorId == $this->user->uid);
            } else {
                $permission = 'allow' . ucfirst(strtolower($permission));
                $optionPermission = 'default' . ucfirst($permission);
                $allow &= (isset($this->{$permission}) ? $this->{$permission} : $this->options->{$optionPermission});
            }
        }
        return $allow;
    }

    /**
     * 设置默认的标签
     */
    protected function setCurrentTag(){
        $slug = $this->request->get('slug');
        if(!empty($slug)){
            $select = $this->db->select()->from('table.metas')
            ->where('slug = ?', $slug)->limit(1);
            
            $rs = $this->db->fetchRow($select);
            if(!empty($rs)){
                $this->currentTag = $rs;
            } 
        }elseif($this->have() && isset($this->categories[0])){
            $this->currentTag = $this->categories[0];
        }
    }
    /**
     * 显示主题发布界面
     * @see Widget_Abstract::render()
     */
    public function render(){
        if($this->request->isPost()){
            $this->doPublish();
        
            /** 设置提示信息 */
            $this->widget('Widget_Notice')->set('post' == $this->type ?
                _t('文章 "<a href="%s">%s</a>" 已经发布', $this->permalink, $this->title) :
                _t('文章 "%s" 等待审核', $this->title), 'success');
            $this->response->goBack();
        }else{
            $this->_metaTitle = '创作新主题';
            $this->setCurrentTag();
             
        }
        /** 输出模板 */
        require_once $this->_themeDir . 'publish.php';
    }

    /**
     * 获取主题文件
     *
     * @access public
     * @param string $fileName 主题文件
     * @return void
     */
    public function need($fileName)
    {
        require $this->_themeDir . $fileName;
    }
    
    public function header(){

        $html = <<<EOT
<link rel="stylesheet" href="{$this->options->themeUrl('codemirror/codemirror.css','default')}">
<link rel="stylesheet" href="{$this->options->themeUrl('codemirror/theme/neo.css','default')}">
EOT;
        echo $html;
    }
    public function footer(){
        $nodetags = Widget_Common::allNodeTags();
        $topicNode = isset($this->currentTag['parent']) ? (($this->currentTag['parent'] != 0) ? $this->currentTag['parent'] : $this->currentTag['mid'] ) : 0;
        $preview_url = Typecho_Common::url('action/publish', $this->options->index);
        $html = <<<EOT
<script src="{$this->options->themeUrl('codemirror/codemirror.js','default')}"></script>
<script src="{$this->options->themeUrl('codemirror/markdown.js','default')}"></script>
<script src="{$this->options->themeUrl('js/jquery.tagsinput.min.js','default')}"></script>
<script>
//编辑器
var pubEditor = CodeMirror.fromTextArea(document.getElementById("topic_content"), {
    lineNumbers: true,
    mode: "markdown",
    theme: "neo",
    indentUnit: 4,
    lineWrapping: true
});
//实时统计并控制内容字数
pubEditor.on('change', function(cm, change) {
    var text = cm.getValue();
    var max = 20000;
    var remaining = max - text.length;
    var r = $("#content_remaining");
    r.html(remaining);
});
//实时统计并控制标题字数
$("#topic_title").keyup(function(e) {
    var s = $("#topic_title");
    var text = s.val()
    var max = 120;
    var remaining = max - text.length;
    var r = $("#title_remaining");
    r.html(remaining);
});
//切换标签
$('#topic-node').on('change',function(){
	var that = $(this); mid = that.data('mid'),select = that.val();
	if(select == mid){
	    return false;
	}
	that.data('mid',select);
	setRecTags(select);
});
function setRecTags(mid){
	var nodes = {$nodetags},html='<strong>推荐标签：</strong>';
	if(nodes[mid]!==undefined){
		$.each(nodes[mid] ,function(id,tags){
		    html += '<a class="tag" href="#'+tags.slug+'">'+tags.name+'</a>';
		});
	}else{
		//html='<strong>暂无推荐</strong>'
	}
	$('#topic-hot-tags').html(html);
}
//设置默认
$('#topic-node').val({$topicNode});
setRecTags({$topicNode});

//标签
$('#tagsInput').tagsInput({
	width:'auto',
	height:'auto',
	defaultText : '请输入标签名'
});
$(document).on('click','#topic-hot-tags a',function(){
	var tag = $(this).text();
    $('#tagsInput').addTag(tag);
    return false;
});

function prevTopic(){
	var box = $("#topic_preview_box");
	var preview = $("#topic_preview");
	if (preview.length == 0) {
        box.append('<div class="inner" id="topic_preview"></div>');
        preview = $("#topic_preview");
    }
	var md = pubEditor.getValue();
	if(md=='') return false;
	$.post("{$preview_url}", { 'do':'preview','text' : md }, function( data ) {
        preview.html('<div class="post-content">' + data + '</div>');
    });
}
</script>
EOT;
        echo $html;
    }
}