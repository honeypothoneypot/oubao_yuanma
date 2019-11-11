<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


/**
 * brand 模板
 */
class b2c_mdl_posbrand extends dbeav_model{
    var $defaultOrder = array('convert(name using gbk)','ASC');

    public function getPostype(){
    	//刷卡方式
        $postype = app::get('b2c')->model('postype')->getPostype();
        return $postype;
    }

    public function getBrandAndType(){
    	$sql = "SELECT a.posbrand_id,a.name,b.postype_id,b.shuaka_type,b.feilv,b.fengding FROM sdb_b2c_posbrand a
    			LEFT JOIN sdb_b2c_postype b ON b.posbrand_id = a.posbrand_id
    			WHERE a.display='true' and b.is_enable = '1'
    	";
    	$data = $this->db->select($sql);
        //刷卡方式
        $postype = app::get('b2c')->model('postype')->getPostype();
        foreach ($data as $key => &$value) {
            $value['shuaka_typeOld'] = $value['shuaka_type'];
            $value['shuaka_type'] = $postype[$value['shuaka_type']];
        }
    	return $data;
    }
}
