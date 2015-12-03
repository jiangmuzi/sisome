<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
/**
 * 反馈提交
 *
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/**
 * 反馈提交组件
 *
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Widget_Feedback extends Widget_Abstract_Comments implements Widget_Interface_Do
{
    /**
     * 内容对象
     *
     * @access private
     * @var Widget_Archive
     */
    private $_content;

    /**
     * 评论处理函数
     *
     * @throws Typecho_Widget_Exception
     * @throws Exception
     * @throws Typecho_Exception
     */
    private function comment()
    {
        // modified_by_jiangmuzi 2015.09.23
        // 必须登录后才可以回复
        if (!$this->user->hasLogin()) {
            $this->widget('Widget_Notice')->set(_t('请先<a href="%s">登录</a>',$this->options->someUrl('login',null,false).'?redir='.$this->request->getRequestUrl()),NULL,'success');
            $this->response->goBack();
        }
        // end modified
        
        // 使用安全模块保护
        $this->security->protect();

        $comment = array(
            'cid'       =>  $this->_content->cid,
            'created'   =>  $this->options->gmtTime,
            'agent'     =>  $this->request->getAgent(),
            'ip'        =>  $this->request->getIp(),
            'ownerId'   =>  $this->_content->author->uid,
            'type'      =>  'comment',
            'status'    =>  !$this->_content->allow('edit') && $this->options->commentsRequireModeration ? 'waiting' : 'approved'
        );

        //检验格式
        $validator = new Typecho_Validate();
        $validator->addRule('text', 'required', _t('必须填写评论内容'));

        $comment['text'] = $this->request->text;

        /** 记录登录用户的id */
        $comment['authorId'] = $this->user->uid;

        if ($error = $validator->run($comment)) {
            /** 记录文字 */
            Typecho_Cookie::set('__some_remember_text', $comment['text']);
            throw new Typecho_Widget_Exception(implode("\n", $error));
        }

        /** 生成过滤器 */
        try {
            $comment = $this->pluginHandle()->comment($comment, $this->_content);
        } catch (Typecho_Exception $e) {
            Typecho_Cookie::set('__some_remember_text', $comment['text']);
            throw $e;
        }
        
        // modified_by_jiangmuzi 2015.09.23
        // 解析@数据
		$atArr = $this->searchAt($comment);

        // end modified
        /** 添加评论 */
        $commentId = $this->insert($comment);
        Typecho_Cookie::delete('__some_remember_text');
        $this->db->fetchRow($this->select()->where('coid = ?', $commentId)
        ->limit(1), array($this, 'push'));
        //更新最后评论人及时间
        $this->db->query($this->db->update('table.contents')->rows(array('lastUid'=>$this->authorId,'lastComment'=>$this->created))->where('cid = ?',$this->cid));
        //提醒主题作者
        if($comment['authorId'] != $comment['ownerId']){
            $atArr[] = array(
                'uid'=>$comment['ownerId'],
                'type'=>'comment'
            );
        }
        if(!empty($atArr)){
            foreach($atArr as $v){
                $this->widget('Widget_Users_Messages')->addMessage($v['uid'],$commentId,$v['type']);
            }
        }
        //触发评论积分规则
        Widget_Common::credits('reply',null,$commentId);
        
        /** 评论完成接口 */
        $this->pluginHandle()->finishComment($this);
        
        $this->response->goBack('#' . $this->theId);
    }

    /**
     * 过滤评论内容
     *
     * @access public
     * @param string $text 评论内容
     * @return string
     */
    public function filterText($text)
    {
        $text = str_replace("\r", '', trim($text));
        $text = preg_replace("/\n{2,}/", "\n\n", $text);

        return Typecho_Common::removeXSS(Typecho_Common::stripTags(
        $text, $this->options->commentsHTMLTagAllowed));
    }

    /**
     * 初始化函数
     *
     * @access public
     * @return void
     * @throws Typecho_Widget_Exception
     */
    public function action()
    {
        /** 回调方法 */
        $callback = $this->request->type;
        $this->_content = Typecho_Router::match($this->request->permalink);

        /** 判断内容是否存在 */
        if (false !== $this->_content && $this->_content instanceof Widget_Archive &&
        $this->_content->have() && $this->_content->is('single') && ($callback == 'comment')) {

            /** 评论关闭 */
			if (!$this->_content->allow('comment')) {
				throw new Typecho_Widget_Exception(_t('对不起,主题‘%s’已关闭回复.',$this->_content->title), 403);
			}
			
			/** 检查来源 */
			if ($this->options->commentsCheckReferer && 'false' != $this->parameter->checkReferer) {
				$referer = $this->request->getReferer();

				if (empty($referer)) {
					throw new Typecho_Widget_Exception(_t('评论来源页错误.'), 403);
				}

				$refererPart = parse_url($referer);
				$currentPart = parse_url($this->_content->permalink);

				if ($refererPart['host'] != $currentPart['host'] ||
				0 !== strpos($refererPart['path'], $currentPart['path'])) {
					
					//自定义首页支持
					if ('page:' . $this->_content->cid == $this->options->frontPage) {
						$currentPart = parse_url(rtrim($this->options->siteUrl, '/') . '/');
						
						if ($refererPart['host'] != $currentPart['host'] ||
						0 !== strpos($refererPart['path'], $currentPart['path'])) {
							throw new Typecho_Widget_Exception(_t('评论来源页错误.'), 403);
						}
					} else {
						throw new Typecho_Widget_Exception(_t('评论来源页错误.'), 403);
					}
				}
			}

			/** 检查ip评论间隔 */
			if (!$this->user->pass('editor', true) && $this->_content->authorId != $this->user->uid &&
			$this->options->commentsPostIntervalEnable) {
				$latestComment = $this->db->fetchRow($this->db->select('created')->from('table.comments')
				->where('cid = ?', $this->_content->cid)
				->order('created', Typecho_Db::SORT_DESC)
				->limit(1));

				if ($latestComment && ($this->options->gmtTime - $latestComment['created'] > 0 &&
				$this->options->gmtTime - $latestComment['created'] < $this->options->commentsPostInterval)) {
					throw new Typecho_Widget_Exception(_t('对不起, 您的发言过于频繁, 请稍侯再次发布.'), 403);
				}
			}
			$this->comment();
        } else {
            throw new Typecho_Widget_Exception(_t('找不到内容'), 404);
        }
    }
}
