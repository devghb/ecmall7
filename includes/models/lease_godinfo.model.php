<?php
class Lease_godinfoModel extends BaseModel
{
    public $table  = 'lease_godinfo';
    public $prikey = 'id';
    public $alias  = 'godinfo';
    public $_name  = 'lease_godinfo';
    
    var $_relation = array(
       'has_lucky_godinfo'=>array(
            'model'       =>'lease_lucky',
            'type'        => HAS_ONE,
            'foreign_key' =>'godinfo_id',
        )
     );
}
?>