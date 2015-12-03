<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

// +----------------------------------------------------------------------
// | SISOME SomeBBS公用方法
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.sisome.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 绛木子 <master@lixianhua.com>
// +----------------------------------------------------------------------

class Widget_Common
{
    public static function allNodeTags(){
        $nodes = array();
        Typecho_Widget::widget('Widget_Metas_Tag_Cloud@hotTags', 'sort=count&ignoreZeroCount=0&desc=count&limit=10')->to($tags);
        if($tags->have()){
            while ($tags->next()){
                $nodes[0][$tags->mid] = array(
                    'mid'=>$tags->mid,
                    'name'=>$tags->name,
                    'slug'=>$tags->slug,
                    'parent'=>$tags->parent,
                );
            }
        }
        Typecho_Widget::widget('Widget_Metas_Category_List')->to($categories);
        if($categories->have()){
            while ($categories->next()){
                Typecho_Widget::widget('Widget_Metas_Tag_Cloud@Meta_'.$categories->mid, 'sort=count&ignoreZeroCount=0&desc=count&limit=20&parent='.$categories->mid)->to($tags);
                
                if($tags->have()){
                    while ($tags->next()){
                        $nodes[$categories->mid][$tags->mid] = array(
                            'mid'=>$tags->mid,
                            'name'=>$tags->name,
                            'slug'=>$tags->slug,
                            'parent'=>$tags->parent,
                        ); 
                    }
                }
                
                
            }
        }
        return json_encode($nodes);
    }
    /**
     * 发送验证码
     * @param unknown $data
     * @return boolean
     */
    public static function sendVerify($data){
        if(empty($data['uid']) || empty($data['confirm']) || empty($data['type'])){
            return false;
        }
        Typecho_Widget::widget('Util_Verify',$data)->send();
    }
    /**
     * 触发的积分规则
     * @param string $type
     */
    public static function credits($type,$uid=null,$srcId = null){
		if($uid){
			$user = Typecho_Widget::widget('Widget_Users_Query@uid_'.$uid,'uid='.$uid);
		}else{
			$user = Typecho_Widget::widget('Widget_User');
		}
        
        if($user->have()){
            Typecho_Widget::widget('Widget_Users_Credits')->setUserCredits($user->uid,$type,$srcId);
        }
    } 
    /**
     * 获取提示消息
     */
    public static function getNotice(){
        $notice = Typecho_Cookie::get('__some_notice');
        if(empty($notice)){
            echo "''";
            return ;
        }
        $notice = json_decode($notice,true);
        $rs = array(
            'msg'=>$notice[0],
            'type'=>Typecho_Cookie::get('__some_notice_type')
        );
        Typecho_Cookie::delete('__some_notice');
        Typecho_Cookie::delete('__some_notice_type');
        echo json_encode($rs);
    }
    /**
     * 格式化时间
     * @param number $time 时间戳
     * @param string $str 显示格式
     * @return string
     */
    public static function formatTime($time,$str='Y-m-d h:i:s'){
        
        static $nowTime=null;
        $time = new Typecho_Date($time);
        $time = $time->timeStamp;
        if($nowTime==null)
            $nowTime = new Typecho_Date(Typecho_Widget::widget('Widget_Options')->gmtTime);
        $way = $nowTime->timeStamp - $time;
        $r = '';
        if($way < 60){
            $r = '刚刚';
        }elseif($way >= 60 && $way <3600){
            $r = floor($way/60).'分钟前';
        }elseif($way >=3600 && $way <86400){
            $r = floor($way/3600).'小时前';
        }elseif($way >=86400 && $way <2592000){
            $r = floor($way/86400).'天前';
        }elseif($way >=2592000 && $way <15552000){
            $r = floor($way/2592000).'个月前';
        }else{
            $r = date("$str",$time);
        }
        return $r;
    }
	/**
	 * 根据用户ID获取头像路径
	 * @param number $uid
	 * @return string
	 */
    public static function getAvatarPath($uid){
		$uid = abs(intval($uid));
		$uid = sprintf("%09d", $uid);
		$dir1 = substr($uid, 0, 3);
		$dir2 = substr($uid, 3, 2);
		$dir3 = substr($uid, 5, 2);
		$avatar = $dir1.'/'.$dir2.'/'.$dir3.'/'.substr($uid, -2);
		return '/usr/avatar/'.$avatar.'/';
	}
	/**
	 * 根据用户ID及头像大小获取头像地址
	 * @param number $uid
	 * @param string $type
	 * @return string
	 */
	public static function avatar($uid,$size='48',$screenName = null, $class = 'avatar',$isEcho = true){
		$url = Typecho_Common::url('action/misc?do=avatar&uid='.$uid.'&s='.$size,Typecho_Widget::widget('Widget_Options')->index);
        $html = '<img' . (empty($class) ? '' : ' class="' . $class . '"') . ' src="' . $url . '" '.
        (empty($screenName) ? '' :  ' alt="' . $screenName . '"'). '" width="' . $size . '" height="' . $size . '" />';
	   if($isEcho){
	       echo $html;
	   }else{
	       return $html;
	   }
	}
}
