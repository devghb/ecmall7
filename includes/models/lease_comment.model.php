<?php
class Lease_commentModel extends BaseModel
{
    public $table  = 'lease_comment';
    public $prikey = 'id';
    public $alias  = 'comment';
    public $_name  = 'lease_comment';
    var $_relation = array(
        //一个图片多对应该一个相册
        'comment_to_member' => array(
            'model'         => 'lease_member',
            'type'          => BELONGS_TO,
            'foreign_key'   => 'user_id',
            'reverse'       => 'has_comment',
        ),
    );
}
?>