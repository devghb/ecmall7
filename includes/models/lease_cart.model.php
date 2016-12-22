<?php
class Lease_cartModel extends BaseModel
{
    public $table  = 'lease_cart';
    public $prikey = 'id';
    public $alias  = 'cart';
    public $_name  = 'lease_cart';
    var $_relation = array(
        //一个一下购物车对应一个作品
        'cart_belong_goods' => array(
            'model'         => 'lease_goods',
            'type'          => BELONGS_TO,
            'foreign_key'   => 'id',
            'reverse'       => 'has_cart',
        ),
    );
}
?>