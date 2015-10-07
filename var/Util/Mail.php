<?php
// +----------------------------------------------------------------------
// | SISOME 
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://sisome.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 绛木子 <master@lixianhua.com>
// +----------------------------------------------------------------------
class Util_Mail{
    public static function send($params){
        $options = Typecho_Widget::widget('Widget_Options');
        if(empty($options->smtpHost) || empty($options->smtpUser) || empty($options->smtpPass)){
            return false;
        }

        if(is_string($params)){
            $file = self::getDIr().$params;
            if(file_exists($file)){
                $params = unserialize(file_get_contents($file));
                if(!Typecho_Widget::widget('Widget_User')->simpleLogin($params['uid'])){
                    @unlink($file);
                    return false;
                }
                @unlink($file);
            }else{
                return false;
            }
        }
        
        require_once 'Mail/PHPMailer/phpmailer.class.php';
        
        $mail             = new PHPMailer();
        $mail->CharSet    = "UTF-8"; //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
        $mail->Encoding   = 'base64';
        $mail->IsSMTP();  // 设定使用SMTP服务
        $mail->SMTPDebug  = 0;                     // 关闭SMTP调试功能
        $mail->SMTPAuth   = true;                  // 启用 SMTP 验证功能
        $mail->SMTPSecure = "ssl";                 // 使用安全协议
        $mail->Host       = $options->smtpHost;  // SMTP 服务器
        $mail->Port       = $options->smtpPort;  // SMTP服务器的端口号
        $mail->Username   = $options->smtpUser;  // SMTP服务器用户名
        $mail->Password   = $options->smtpPass;  // SMTP服务器密码
        $mail->SetFrom($options->smtpMail, $options->smtpName);
        $mail->AddReplyTo($options->smtpMail, $options->smtpName);
        $mail->Subject    = $params['subject'];
        $mail->MsgHTML($params['body']);
        $mail->AddAddress($params['mail'],$params['name']);
        if(!$mail->Send()){
            file_put_contents(self::getDIr().'error_log.txt', $this->mail->ErrorInfo);
        }
    }
    
    /**
     * 异步发送邮件
     *
     * @access public
     * @return void
     */
    public static function asyncSendMail($data){
        $filename = Typecho_Common::randString(8);
        file_put_contents(self::getDIr().$filename, serialize($data));
        self::putAsyncMail($filename);
    }
    /**
     * 异步发送邮件
     */
    protected static function putAsyncMail($filename){
        $options = Typecho_Widget::widget('Widget_Options');
        $siteUrl = ($options->rewrite)?$options->siteUrl:$options->siteUrl.'index.php';
        $dmpt=parse_url($siteUrl);
    
        $host = $dmpt['host'];
        $port = isset($dmpt['port'])?$dmpt['port']:80;
    
        if(substr($dmpt['path'], -1) != '/') $dmpt['path'] .= '/';
        $url = $dmpt['path'].'action/forum';
    
        $get='do=sendmail&name='.$filename;
        $head = "GET ". $url . "?" . $get . " HTTP/1.0\r\n";
        $head .= "Host: " . $host . "\r\n";
        $head .= "\r\n";
    
        if(function_exists('fsockopen')){
            $fp = @fsockopen ($host, $port, $errno, $errstr, 30);
        }
        elseif(function_exists('pfsockopen')){
            $fp = @pfsockopen ($host, $port, $errno, $errstr, 30);
        }  else {
            $fp = stream_socket_client($host.":$port", $errno, $errstr, 30);
        }
    
        if($fp){
            fputs ($fp, $head);
            fclose($fp);
        }else{
            file_put_contents('.'.self::getDIr.'error_log.txt', "SOCKET错误,".$errno.':'.$errstr);
        }
    }
    public static function getDIr(){
        return __TYPECHO_ROOT_DIR__ . '/var/tmp/';
    } 
}