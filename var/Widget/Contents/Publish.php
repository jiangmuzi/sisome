<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

// +----------------------------------------------------------------------
// | SISOME 保存、编辑主题
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.sisome.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 绛木子 <master@lixianhua.com>
// +----------------------------------------------------------------------

class Widget_Contents_Publish extends Widget_Abstract_Contents implements Widget_Interface_Do
{
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
    
    public function writePost(){
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
                Widget_Common::credits('publish');
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
        /** 设置提示信息 */
        $this->widget('Widget_Notice')->set('post' == $this->type ?
            _t('文章 "<a href="%s">%s</a>" 已经发布', $this->permalink, $this->title) :
            _t('文章 "%s" 等待审核', $this->title), 'success');
        if($this->have()){
            $this->response->redirect($this->permalink);
        }else{
            $this->response->goBack();
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
     * 输出Markdown预览
     *
     * @access public
     * @return void
     */
    public function preview()
    {
        $this->response->throwJson($this->markdown($this->request->text));
    }
    /**
     * 绑定动作
     *
     * @access public
     * @return void
     */
    public function action()
    {
        $this->on($this->request->is('do=publish') || $this->request->is('do=save'))->writePost();
        $this->on($this->request->is('do=delete'))->deletePost();
        $this->on($this->request->is('do=preview'))->preview();
    }
}