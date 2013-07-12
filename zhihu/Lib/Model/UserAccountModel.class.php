<?php
class UserAccountModel extends Model {
	// 定义自动验证
    protected $_validate    =   array(
    	array('userEmail','require','邮箱必须填写'),
        array('userEmail','email','邮箱格式错误'),
        array('userEmail','','邮箱已经注册',2,'unique',1),
        array('userName','require','昵称必须填写'),
        array('userName','2,10','昵称必须2到10个字符',2,'length',1),
        array('userName','','昵称已存在',2,'unique',1),
        array('userPass','require','密码必须填写'),
        array('userConfirmPass','require','确认密码必须填写'),
        array('userConfirmPass','userPass','确认密码与密码不同',0,'confirm'), // 验证确认密码是否和密码一致
        array('verifyCode','require','验证码必须填写'),
        );
   protected $_auto    =   array(
    	array('entityCreateTime','time',1,'function'),
        array('entityUpdateVersion','time',3,'function'),
        array('status','1'),
        array('registerType','1')
        );
   // 数据表名（不包含表前缀）
    protected $tableName        =   'useraccount';
}
?>