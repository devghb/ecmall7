<?php
class Lease_memberModel extends BaseModel
{
    public $table  = 'lease_member';
    public $prikey = 'user_id';
    public $alias  = 'member';
    public $_name  = 'lease_member';
    var $_relation = array(
        //一个用户对应多个评论
        'has_comment' => array(
            'model'         => 'lease_comment',
            'type'          => HAS_MANY,
            'foreign_key'   => 'user_id',
            'dependent' => true
        ),
        //一个用户对应多个订单
        'has_order' => array(
            'model'         => 'lease_order',
            'type'          => HAS_MANY,
            'foreign_key'   => 'user_id',
            'dependent' => true
        )
    );
}
?>