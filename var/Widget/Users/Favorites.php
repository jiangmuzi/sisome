<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

// +----------------------------------------------------------------------
// | SISOME 用户收藏
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.sisome.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 绛木子 <master@lixianhua.com>
// +----------------------------------------------------------------------

class Widget_Users_Favorites extends Widget_Abstract_Favorites{
    
	private $_srcId;
	private $_title;
    
    public function __construct($request, $response, $params = NULL){
        parent::__construct($request, $response, $params);
    }
    
    public function execute(){}
    
    public function addFavorite()
	{
		$row = array(
			'uid'=>$this->user->uid,
			'type'=>$this->parameter->type,
			'srcId'=>$this->_srcId,
			'created'=>$this->options->gmtTime
		);
		return $this->insert($row);
	}
	//要收藏的数据是否存在
	public function dataExists($type=null,$slug=null){
	    $this->parameter->type = empty($type) ? $this->request->type : $type;
	    $this->parameter->slug = empty($slug) ? $this->request->slug : $slug;
		if(empty($this->parameter->type) || empty($this->parameter->slug)){
			return true;
		}
		if($this->parameter->type=='tag'){
			$row = $this->db->fetchRow($this->db->select()->from('table.metas')->where('type = ?','tag')->where('slug = ?',$this->parameter->slug)->limit(1));
			if(empty($row)){
				return true;
			}
			$this->_srcId = $row['mid'];
			$this->_title = '标签：'.$row['name'];
		}
		
		if($this->parameter->type=='post'){
			$row = $this->db->fetchRow($this->db->select()->from('table.contents')->where('cid = ?',$this->parameter->slug)->limit(1));
			if(empty($row)){
				return true;
			}
			$this->_srcId = $row['cid'];
			$this->_title = '帖子：'.$row['title'];
		}
		
		return false;
	}

	public function deleteFavorite($fid){
	    $this->delete($this->db->sql()->where('fid = ? AND uid = ?',$fid,$this->user->uid));
	}
	
	public function isFavorite($type,$slug){
	    if($this->dataExists($type,$slug)){
	        return 0;
	    }
	    $favorite = $this->favoriteExists($type, $this->_srcId);
	    return $favorite ? $favorite['fid'] : 0;
	}
	public function getTitle(){
		return $this->_title;
	}
	public function getSrcId(){
		return $this->_srcId;
	}
}
