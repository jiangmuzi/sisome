<?php
// +----------------------------------------------------------------------
// | SISOME 发布主题
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.sisome.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 绛木子 <master@lixianhua.com>
// +----------------------------------------------------------------------
class Forum_Publish extends Widget_Abstract_Contents{
	protected $currentTag=array();
	
    public function __construct($request, $response, $params = NULL){
        parent::__construct($request, $response, $params);
        if(!$this->user->hasLogin()){
            $this->response->redirect($this->some->___loginUrl().'?redir='.$this->request->getRequestUrl());
        }
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
        if (!empty($this->request->cid) && 'delete' != $this->request->do) {
            $this->db->fetchRow($this->select()
            ->where('table.contents.type = ? OR table.contents.type = ?', 'post', 'post_draft')
            ->where('table.contents.cid = ?', $this->request->filter('int')->cid)
            ->limit(1), array($this, 'push'));
            $nowTime = new Typecho_Date($this->options->gmtTime);
            if (!$this->have()) {
                throw new Typecho_Widget_Exception(_t('文章不存在'), 404);
            } else if ($this->have() && !$this->allow('edit')) {
                throw new Typecho_Widget_Exception(_t('没有编辑权限'), 403);
            } else if ($this->created+300<$nowTime->timeStamp && !$this->user->pass('editor')){
                throw new Typecho_Widget_Exception(_t('已不允许编辑'), 403);
            }
        }
    }
    
    
    public function preview(){
        $md = $this->request->get('md');
        
        if(!empty($md)){
            $html = Markdown::convert($md);
            echo $html;
        }
        echo '';
        exit;
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
    
    protected function doPublish(){
        $this->security->protect();

        $contents = $this->request->from('title','text','category','tags');
        if(!empty($contents['text'])){
            $contents['text'] = '<!--markdown-->' . $contents['text'];
        }
        if(!empty($contents['category']))
            $contents['category'] = array($contents['category']);
        $contents['slug'] = NULL;
        $contents['created'] = $this->getCreated();
        $contents['type'] = 'post';
        $contents['allowComment'] = 1;
        $contents['allowPing'] = 1;
        $contents['allowFeed'] = 1;
        /** 发布内容, 检查是否具有直接发布的权限 */
        if ($this->user->pass('editor', true)) {
            if (empty($contents['visibility'])) {
                $contents['status'] = 'publish';
            } else if ('password' == $contents['visibility'] || !in_array($contents['visibility'], array('private', 'waiting', 'publish', 'hidden'))) {
                if (empty($contents['password']) || 'password' != $contents['visibility']) {
                    $contents['password'] = '';
                }
                $contents['status'] = 'publish';
            } else {
                $contents['status'] = $contents['visibility'];
                $contents['password'] = '';
            }
        } else {
            $contents['status'] = 'publish';
            $contents['password'] = '';
        }
        
        /** 真实的内容id */
        $realId = 0;
        
        /** 是否是从草稿状态发布 */
        $isDraftToPublish = ('post_draft' == $this->type);
        
        $isBeforePublish = ('publish' == $this->status);
        $isAfterPublish = ('publish' == $contents['status']);

        /** 重新发布现有内容 */
        if ($this->have()) {
        
            /** 如果它本身不是草稿, 需要删除其草稿 */
            if (!$isDraftToPublish && $this->draft) {
                $this->deleteDraft($this->draft['cid']);
                $this->deleteFields($this->draft['cid']);
            }
        
            /** 直接将草稿状态更改 */
            if ($this->update($contents, $this->db->sql()->where('cid = ?', $this->cid))) {
                $realId = $this->cid;
            }
        
        } else {
            /** 发布一个新内容 */
            $realId = $this->insert($contents);
            if($realId>0){
                Forum_Common::credits('post');
            }
        }
        
        if ($realId > 0) {
            /** 插入分类 */
            if (array_key_exists('category', $contents)) {
                $this->setCategories($realId, !empty($contents['category']) && is_array($contents['category']) ?
                    $contents['category'] : array($this->options->defaultCategory), !$isDraftToPublish && $isBeforePublish, $isAfterPublish);
            }
        
            /** 插入标签 */
            if (array_key_exists('tags', $contents)) {
                $this->setTags($realId, $contents['tags'], !$isDraftToPublish && $isBeforePublish, $isAfterPublish);
            }
        
            /** 同步附件 */
            //$this->attach($realId);
        
            /** 保存自定义字段 */
            //$this->applyFields($this->getFields(), $realId);
        
            $this->db->fetchRow($this->select()->where('table.contents.cid = ?', $realId)->limit(1), array($this, 'push'));
        }
    }
    
    /**
     * 设置内容标签
     *
     * @access protected
     * @param integer $cid
     * @param string $tags
     * @param boolean $count 是否参与计数
     * @return string
     */
    protected function setTags($cid, $tags, $beforeCount = true, $afterCount = true)
    {
        $tags = str_replace('，', ',', $tags);
        $tags = array_unique(array_map('trim', explode(',', $tags)));
        $tags = array_filter($tags, array('Typecho_Validate', 'xssCheck'));
    
        /** 取出已有tag */
        $existTags = Typecho_Common::arrayFlatten($this->db->fetchAll(
            $this->db->select('table.metas.mid')
            ->from('table.metas')
            ->join('table.relationships', 'table.relationships.mid = table.metas.mid')
            ->where('table.relationships.cid = ?', $cid)
            ->where('table.metas.type = ?', 'tag')), 'mid');
    
        /** 删除已有tag */
        if ($existTags) {
            foreach ($existTags as $tag) {
                if (0 == strlen($tag)) {
                    continue;
                }
    
                $this->db->query($this->db->delete('table.relationships')
                    ->where('cid = ?', $cid)
                    ->where('mid = ?', $tag));
    
                if ($beforeCount) {
                    $this->db->query($this->db->update('table.metas')
                        ->expression('count', 'count - 1')
                        ->where('mid = ?', $tag));
                }
            }
        }
    
        /** 取出插入tag */
        $insertTags = $this->widget('Widget_Abstract_Metas')->scanTags($tags);
    
        /** 插入tag */
        if ($insertTags) {
            foreach ($insertTags as $tag) {
                if (0 == strlen($tag)) {
                    continue;
                }
    
                $this->db->query($this->db->insert('table.relationships')
                    ->rows(array(
                        'mid'  =>   $tag,
                        'cid'  =>   $cid
                    )));
    
                if ($afterCount) {
                    $this->db->query($this->db->update('table.metas')
                        ->expression('count', 'count + 1')
                        ->where('mid = ?', $tag));
                }
            }
        }
    }
    
    /**
     * 设置分类
     *
     * @access protected
     * @param integer $cid 内容id
     * @param array $categories 分类id的集合数组
     * @param boolean $count 是否参与计数
     * @return integer
     */
    protected function setCategories($cid, array $categories, $beforeCount = true, $afterCount = true)
    {
        $categories = array_unique(array_map('trim', $categories));
    
        /** 取出已有category */
        $existCategories = Typecho_Common::arrayFlatten($this->db->fetchAll(
            $this->db->select('table.metas.mid')
            ->from('table.metas')
            ->join('table.relationships', 'table.relationships.mid = table.metas.mid')
            ->where('table.relationships.cid = ?', $cid)
            ->where('table.metas.type = ?', 'category')), 'mid');
    
        /** 删除已有category */
        if ($existCategories) {
            foreach ($existCategories as $category) {
                $this->db->query($this->db->delete('table.relationships')
                    ->where('cid = ?', $cid)
                    ->where('mid = ?', $category));
    
                if ($beforeCount) {
                    $this->db->query($this->db->update('table.metas')
                        ->expression('count', 'count - 1')
                        ->where('mid = ?', $category));
                }
            }
        }
    
        /** 插入category */
        if ($categories) {
            foreach ($categories as $category) {
                /** 如果分类不存在 */
                if (!$this->db->fetchRow($this->db->select('mid')
                    ->from('table.metas')
                    ->where('mid = ?', $category)
                    ->limit(1))) {
                        continue;
                    }
    
                    $this->db->query($this->db->insert('table.relationships')
                        ->rows(array(
                            'mid'  =>   $category,
                            'cid'  =>   $cid
                        )));
    
                    if ($afterCount) {
                        $this->db->query($this->db->update('table.metas')
                            ->expression('count', 'count + 1')
                            ->where('mid = ?', $category));
                    }
            }
        }
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
        parent::render('publish.php');
    }

    public function header(){
        $html = <<<EOT
<link rel="stylesheet" href="{$this->options->themeUrl('codemirror/codemirror.css','default')}">
<link rel="stylesheet" href="{$this->options->themeUrl('codemirror/theme/neo.css','default')}">
EOT;
        echo $html;
    }
    public function footer(){
        $nodetags = Forum_Common::allNodeTags();
        $topicNode = isset($this->currentTag['parent']) ? (($this->currentTag['parent'] != 0) ? $this->currentTag['parent'] : $this->currentTag['mid'] ) : 0;
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
	$.post( window.siteUrl+"publish/preview", { 'md' : md }, function( data ) {
        preview.html('<div class="post-content">' + data + '</div>');
    });
}
</script>
EOT;
        echo $html;
    }
}