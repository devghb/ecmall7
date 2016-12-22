<?php
class Lease_goodsModel extends BaseModel
{
    public $table  = 'lease_goods';
    public $prikey = 'id';
    public $alias  = 'lgoods';
    public $_name  = 'lease_goods';
    var $_relation = array(
        //一个作品多对应该多个订单
        'has_order' => array(
            'model'         => 'lease_order',
            'type'          => HAS_MANY,
            'foreign_key'  => 'gid',
            'dependent' => true
        ),
        //一个作品多对应该多个购物车
        'has_cart' => array(
            'model'         => 'lease_cart',
            'type'          => HAS_MANY,
            'foreign_key'  => 'gid',
            'dependent' => true
        ),
    );
}
?>