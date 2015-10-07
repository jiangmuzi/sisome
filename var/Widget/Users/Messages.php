<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

// +----------------------------------------------------------------------
// | SISOME 用户消息
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.sisome.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 绛木子 <master@lixianhua.com>
// +----------------------------------------------------------------------

class Widget_Users_Messages extends Widget_Abstract_Messages{
    public function addMessage($uid,$srcId,$type='comment'){
        $row = array(
            'uid'=>$uid,
            'type'=>$type,
            'srcId'=>$srcId,
            'created'=>$this->options->gmtTime,
            'status'=>0
        );
        return $this->insert($row);
    }
}
