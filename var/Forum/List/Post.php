<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
/**
 * 最新文章
 *
 */
class Forum_List_Post extends Widget_Abstract_Contents
{
	/**
     * 获取查询对象
     *
     * @access public
     * @return Typecho_Db_Query
     */
    public function select()
    {
        return $this->db->select()->from('table.contents');
    }
    /**
     * 执行函数
     *
     * @access public
     * @return void
     */
    public function execute(){
        $this->parameter->setDefault(
            array(
                'sort'=>'created', //排序字段
                'desc'=>true,		//顺序、逆序
                'limit'=>10,		//获取内容条数
                'day'=>0,            //几天内的数据
                'cid'=>''
            ));
        
        $desc = $this->parameter->desc ? Typecho_Db::SORT_DESC : Typecho_Db::SORT_ASC ;
        $select = $this->select()
            ->where('table.contents.status = ?', 'publish')
            ->where('table.contents.created < ?', $this->options->gmtTime)
            ->where('table.contents.type = ?', 'post');
        if(!empty($this->parameter->cid)){
            $cid = explode(',', $this->parameter->cid);
            $select = $select->where('table.contents.cid in ?',$cid);
        }
        if($this->parameter->day>0){
            $time = date('Y-m-d',strtotime('-'.$this->parameter->day.' day'));
            $time = strtotime($time);
            $select = $select->where('table.contents.created > ?',$time);
        }
        $select->order('table.contents.'.$this->parameter->sort, $desc)
            ->limit($this->parameter->limit);
        $this->db->fetchAll($select, array($this, 'push'));
    }
}
