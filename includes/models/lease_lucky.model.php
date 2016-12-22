<?php
class Lease_luckyModel extends BaseModel
{
    public $table  = 'lease_lucky';
    public $prikey = 'id';
    public $alias  = 'lucky';
    public $_name  = 'lease_lucky'; 
    
     var $_relation = array(
        'lucky_to_godinfo' => array(
            'model'         => 'lease_godinfo',
            'type'          => BELONGS_TO,
            'foreign_key'   => 'godinfo_id',
            'reverse'       => 'has_lucky_godinfo',
        ),
      );
}
?>