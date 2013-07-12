<?php
class CommonCategoriesModel extends Model {
	// 定义自动验证
    protected $_validate    =   array(
    	array('number','require','编号必须填写'),
        array('title','require','标题必须填写'),
        array('number','','编号已存在',2,'unique',1)
        );
   protected $_auto    =   array(
    	array('entityCreateTime','time',1,'function'),
        array('entityUpdateVersion','time',3,'function')
        );
   // 数据表名（不包含表前缀）
    protected $tableName        =   'common_categories';
}
?>