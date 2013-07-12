<?php
class CommentModel extends Model {
    
   protected $_auto    =   array(
        array('entityCreateTime','time',1,'function'),
        array('entityUpdateVersion','time',3,'function')
        );
   // 数据表名（不包含表前缀）
    protected $tableName        =   'comment';
}
?>