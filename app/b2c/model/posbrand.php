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
}
