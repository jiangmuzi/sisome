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
class Forum_List_Comment extends Widget_Abstract_Comments
{
    /**
     * 执行函数
     *
     * @access public
     * @return void
     */
    public function execute(){
        $select = $this->select();
        
        $this->parameter->setDefault(
            array(
                'sort'=>'created',  //排序字段
                'desc'=>true,		//顺序、逆序
                'limit'=>10,		//获取内容条数
                'last'=>0,           //最后回复时间
                'uid'=>0            //获取某用户回复
            ));
        $desc = $this->parameter->desc ? Typecho_Db::SORT_DESC : Typecho_Db::SORT_ASC ;
        
        if($this->parameter->last){
            $select->where('table.comments.created > ?', $this->parameter->last);
        }
        if($this->parameter->uid){
            $select->where('table.comments.authorId = ?', $this->parameter->uid);
        }
        
        $select->where('table.comments.status = ?', 'approved');

        $select->order('table.comments.'.$this->parameter->sort, $desc)
        ->limit($this->parameter->limit);
        
        $this->db->fetchAll($select, array($this, 'push'));
    }
}
