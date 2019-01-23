<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class b2c_finder_poscard{
	var $column_edit = '编辑';
	function column_edit($row){
		$return = "<a href='index.php?app=b2c&ctl=admin_poscard&act=edit&_finder[finder_id]={$_GET['_finder']['finder_id']}&p[0]={$row['card_id']}' "."target=dialog::{title:'编辑',width:460,height:460} >编辑</a>";
    	return $return;
    }

}
