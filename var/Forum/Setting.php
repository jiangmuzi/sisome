<?php
// +----------------------------------------------------------------------
// | SISOME 用户设置
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.sisome.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 绛木子 <master@lixianhua.com>
// +----------------------------------------------------------------------
class Forum_Setting extends Widget_Abstract_Users{
    public function __construct($request, $response, $params = NULL){
        parent::__construct($request, $response, $params);
        
        if(!$this->user->hasLogin()){
            $this->response->redirect($this->___loginUrl().'?redir='.$this->request->getRequestUrl());
        }
    }
   
    public function execute(){
        
        if($this->request->isPost()){
            
            //保存头像
            if($this->request->is('do=avatar')){
                $this->security->protect();
                $this->doAvatar();
            }
            //发送验证码
            if($this->request->is('do=sendverify')){
                $this->sendVerify();
            }
            //保存个人信息
            if($this->request->is('do=profile')){
                $this->security->protect();
                $this->saveProfile();
            }
            //修改邮箱
            if($this->request->is('do=changemail')){
                $this->security->protect();
                $this->changeMail();
            }
            //修改密码
            if($this->request->is('do=changepass')){
                $this->security->protect();
                $this->changePwd();
            }
            
        }
        
    }
    public function render(){
        $this->setMetaTitle('设置');
        parent::render('user/setting.php');
    }
    
    public function avatar(){
        $this->setMetaTitle('设置头像');
        parent::render('user/setting_avatar.php');
    }
    
    
    protected function doAvatar(){
        
        $file = array_pop($_FILES);

        $rs = $this->uploadAvatar($file);

        if($rs){
			$filepath = __TYPECHO_ROOT_DIR__.$rs['path'];
			$big = $filepath.$this->user->uid.'_96.jpg';
			$normal = $filepath.$this->user->uid.'_48.jpg';
			$small = $filepath.$this->user->uid.'_32.jpg';
			require_once 'Util/Image.php';
			
			$image = new Image();
			
			$image = $image->open(__TYPECHO_ROOT_DIR__.$rs['file']);
			$image->thumb(96,96,4)->save($big);
			$image->thumb(48,48,4)->save($normal);
			$image->thumb(24,24,4)->save($small);
			@unlink(__TYPECHO_ROOT_DIR__.$rs['file']);
			
			$this->widget('Widget_Notice')->set('头像已上传');
			$this->response->goBack();
		}else{
		    $this->widget('Widget_Notice')->set('头像上传出错');
		    $this->response->goBack();
		}
    }
    
    
    protected function saveProfile(){
        //用户操作类
        $users = $this->widget('Widget_Abstract_Users');
        
        //初始化验证类
        $validator = new Typecho_Validate();
        $fields = array();
        $name = $this->request->get('name');
        if(!is_null($name)){
            $validator->addRule('name', 'required', _t('必须填写用户名称'));
            $validator->addRule('name', 'minLength', _t('用户名至少包含2个字符'), 2);
            $validator->addRule('name', 'maxLength', _t('用户名最多包含32个字符'), 32);
            $validator->addRule('name', 'xssCheck', _t('请不要在用户名中使用特殊字符'));
            $validator->addRule('name', array($users, 'nameExists'), _t('用户名已经存在'));
            $fields[] = 'name';
        }
        $validator->addRule('screenName', 'xssCheck', _t('请不要在昵称中使用特殊字符'));
        $validator->addRule('screenName', array($users, 'screenNameExists'), _t('昵称已经存在'));
        $fields[] = 'screenName';
        $validator->addRule('url', 'url', _t('个人主页地址格式错误'));
        $fields[] = 'url';
        
        $validator->addRule('location', 'xssCheck', _t('请不要在地址中使用特殊字符'));
        $fields[] = 'location';
        $validator->addRule('sign', 'xssCheck', _t('请不要在签名中使用特殊字符'));
        $fields[] = 'sign';
        $validator->addRule('intro', 'xssCheck', _t('请不要在简介中使用特殊字符'));
        $fields[] = 'intro';
        
        $error = $validator->run($this->request->from($fields));
        
        /** 截获验证异常 */
        if ($error) {
            /** 设置提示信息 */
            $this->widget('Widget_Notice')->set($error,'error');
            $this->response->goBack();
        }
        /** 取出数据 */
        
        $user = $this->request->from($fields);
        
        /** 更新数据 */
        $users->update($user, $this->db->sql()->where('uid = ?', $this->user->uid));
        
        /** 提示信息 */
        $this->widget('Widget_Notice')->set(_t('您的档案已经更新'), 'success');
        
        /** 转向原页 */
        $this->response->goBack();
    }
    
    protected function changeMail(){
        $confirm = $this->request->get('confirm');
        //用户操作类
        $users = $this->widget('Widget_Abstract_Users');
        
        $validator = new Typecho_Validate();
        
        $validator->addRule('mail', 'required', _t('必须填写电子邮箱'));
        $validator->addRule('mail', array($users, 'mailExists'), _t('电子邮箱地址已经存在'));
        $validator->addRule('mail', 'email', _t('电子邮箱格式错误'));
        $validator->addRule('mail', 'maxLength', _t('电子邮箱最多包含200个字符'), 200);
        
        $validator->addRule('confirm', 'required', _t('必须填写验证码'));
        $validator->addRule('confirm', array($this,'checkVerify'), _t('验证码错误'));
        
        $error = $validator->run($this->request->from('mail','confirm'));
        /** 截获验证异常 */
        if ($error) {
            /** 设置提示信息 */
            $this->widget('Widget_Notice')->set($error,'error');
            $this->response->goBack();
        }
        /** 更新数据 */
        $users->update(array('mail' => $this->request->mail),
            $this->db->sql()->where('uid = ?', $this->user->uid));
        $this->widget('Forum_Util_Verify')->setParams('type=changemail')->delete($confirm);
        /** 提示信息 */
        $this->widget('Widget_Notice')->set(_t('邮箱已经成功修改'), 'success');
        
        /** 转向原页 */
        $this->response->goBack();
        
    }
    
    protected function changePwd(){
        $validator = new Typecho_Validate();
        
        $validator->addRule('password', 'required', _t('必须填写旧密码'));
        $validator->addRule('newpassword', 'required', _t('必须填写新密码'));
        $validator->addRule('newpassword', 'minLength', _t('为了保证账户安全, 请输入至少六位的密码'), 6);
        $validator->addRule('confirm', 'confirm', _t('两次输入的密码不一致'), 'newpassword');
        
        $error = $validator->run($this->request->from('password','newpassword','confirm'));
        
        /** 截获验证异常 */
        if ($error) {
            /** 设置提示信息 */
            $this->widget('Widget_Notice')->set($error,'error');
            $this->response->goBack();
        }
        $data = $this->request->from('password','newpassword','confirm');
        
        $hasher = new PasswordHash(8, true);
        $hashValidate = $hasher->CheckPassword($data['password'], $this->user->password);
        if(!$hashValidate){
            $this->widget('Widget_Notice')->set('原密码错误!','error');
            $this->response->goBack();
        }
        $password = $hasher->HashPassword($data['newpassword']);
        //用户操作类
        $users = $this->widget('Widget_Abstract_Users');
        /** 更新数据 */
        $users->update(array('password' => $password),
            $this->db->sql()->where('uid = ?', $this->user->uid));
        
        /** 提示信息 */
        $this->widget('Widget_Notice')->set(_t('密码已经成功修改'), 'success');
        
        /** 转向原页 */
        $this->response->goBack();
        
    }
    
    public function checkVerify($confirm){
        $verify = $this->widget('Forum_Util_Verify')->setParams('type=changemail')->check($confirm);
        if(!empty($verify) && !$verify['status'] && $verify['uid']==$this->user->uid){
            return $this->request->mail == $verify['confirm'];
        }
        return false;
    }
    
    protected function sendVerify(){
        $mail = $this->request->get('mail');
        $params = array(
            'uid'=>$this->user->uid,
            'confirm'=>$mail,
            'name'=>$this->user->screenName,
            'type'=>'changemail'
        );
        Forum_Common::sendVerify($params);
        $this->response->throwJson(array('status'=>1));
    }
    
    /**
     * 上传头像
     * @param array $file
     * @return boolean|multitype:string unknown number Ambigous <string, unknown> Ambigous <Ambigous, string, mixed>
     */
    private function uploadAvatar($file){
		
        if (empty($file['name'])) {
            return false;
        }

        $ext = $this->getSafeName($file['name']);

        if (!Widget_Upload::checkFileType(strtolower($ext)) || Typecho_Common::isAppEngine()) {
            return false;
        }
        
        $options = Typecho_Widget::widget('Widget_Options');
        $path = Forum_Common::getAvatarPath($this->user->uid);
        $realPath = Typecho_Common::url($path,defined('__TYPECHO_UPLOAD_ROOT_DIR__') ? __TYPECHO_UPLOAD_ROOT_DIR__ : __TYPECHO_ROOT_DIR__);
        
        //创建上传目录
        if (!is_dir($realPath)) {
            if (!$this->makeAvatarDir($realPath)) {
                return false;
            }
        }
        
        //获取文件名
        $fileName = $this->user->uid . '.' . $ext;
        $realPath = $realPath . '/' . $fileName;
        
        if (isset($file['tmp_name'])) {
        
            //移动上传文件
            if (!@move_uploaded_file($file['tmp_name'], $realPath)) {
                return false;
            }
        } else if (isset($file['bytes'])) {
        
            //直接写入文件
            if (!file_put_contents($realPath, $file['bytes'])) {
                return false;
            }
        } else {
            return false;
        }
        
        if (!isset($file['size'])) {
            $file['size'] = filesize($realPath);
        }

        //返回相对存储路径
        return array(
            'name' => $file['name'],
            'path' => $path,
			'file' => $path.$fileName,
            'size' => $file['size'],
            'type' => $ext,
            'mime' => Typecho_Common::mimeContentType($realPath)
        );
    }
    /**
     * 获取文件扩展名
     * @param string $name
     * @return Ambigous <string, mixed>
     */
    private function getSafeName(&$name){
        $name = str_replace(array('"', '<', '>'), '', $name);
        $name = str_replace('\\', '/', $name);
        $name = false === strpos($name, '/') ? ('a' . $name) : str_replace('/', '/a', $name);
        $info = pathinfo($name);
        $name = substr($info['basename'], 1);
    
        return isset($info['extension']) ? $info['extension'] : '';
    }
    /**
     * 创建头像目录
     * @param string $path
     * @return boolean
     */
    private function makeAvatarDir($path){
        $path = preg_replace("/\\\+/", '/', $path);
        $current = rtrim($path, '/');
        $last = $current;
    
        while (!is_dir($current) && false !== strpos($path, '/')) {
            $last = $current;
            $current = dirname($current);
        }
    
        if ($last == $current) {
            return true;
        }
    
        if (!@mkdir($last)) {
            return false;
        }
    
        $stat = @stat($last);
        $perms = $stat['mode'] & 0007777;
        @chmod($last, $perms);
    
        return $this->makeAvatarDir($path);
    }
}