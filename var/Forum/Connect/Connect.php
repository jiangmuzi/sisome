<?php
// +----------------------------------------------------------------------
// | SISOME SNS登录sdk
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://sisome.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 绛木子 <master@lixianhua.com>
// +----------------------------------------------------------------------
class Connect{
    static public $sns = array();
    
    static public function getSns($type){
        if(isset(self::$sns[$type])){
            return self::$sns[$type];
        }else{
            $options = Typecho_Widget::widget('Some_Oauth')->getSnsOptions();
            if(!isset($options[$type])){
                return false;
            }
            $option = $options[$type];
            require_once 'Sdk/'.$type.'.php';
            return self::$sns[$type] = new $type($option['id'],$option['key']);
        }
    }
    
    static public function getLoginUrl($type,$callback){
        if($type=='qq'){
            $login_url = self::getSns($type)->login_url($callback,'get_user_info,add_share');
        }else{
            $login_url = self::getSns($type)->login_url($callback);
        }
        return $login_url;
    }
    
    static public function getToken($type,$callback,$code){
        $rs = self::getSns($type)->access_token($callback,$code);
        
        if(isset($rs['access_token']) && $rs['access_token']!=''){
            self::setToken($type, $rs['access_token']);
            return $rs['access_token'];
        }
        return '';
    }
    
    static public function setToken($type,$token){
        self::getSns($type)->access_token = $token;
    }
    
    static public function getOpenId($type){
        $openid = '';
        if($type=='qq'){
            $rs = self::getSns($type)->get_openid();
            if(isset($rs['openid']) && $rs['openid']!=''){
                $openid = $rs['openid'];
            }
        }elseif($type=='weibo'){
            $rs = self::getSns($type)->get_uid();
            if(isset($rs['uid']) && $rs['uid']!=''){
                $openid = $rs['uid'];
            }
        }
        return $openid;
    }
    
    static public function getNickName($type,$openid){
        $nickname = '';
        if($type=='qq'){
            $rs = self::getSns($type)->get_user_info($openid);
            $nickname = $rs['nickname'];
        }elseif($type=='weibo'){
            $rs = self::getSns($type)->show_user_by_id($openid);
            $nickname = $rs['screen_name'];
        }
        
        return $nickname;
    }
}