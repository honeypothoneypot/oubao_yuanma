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
class b2c_mdl_poscard extends dbeav_model{
    var $defaultOrder = array('convert(name using gbk)','ASC');

	public function getList($cols='*', $filter=array(), $offset=0, $limit=-1, $orderType=null){
		$datas = parent::getList($cols, $filter, $offset, $limit, $orderType);
		// $he = array_sum(array_map(create_function('$val', 'return $val["money"];'), $datas));
		// $heji[] = array(
		// 	'id'=>'合计',
		// 	'money' => $he,
		// );
		// $datas = array_merge($datas,$heji);
		return $datas;
	}

	public function getBanks(){
		$banks = array(
			'上海银行',
			'工商银行',
			'广发银行',
			'民生银行',
			'兴业银行',
			'平安银行',
			'建设银行',
			'浦发银行',
			'交通银行',
			'中信银行',
			'中国银行',
			'招商银行',
			'光大银行',
		);
		foreach($banks as $key=>$value ){
			$new[$key]['bank'] = $value;
			$new[$key]['py'] = pinyin(mb_substr($value,0,6));
		}
		$py = utils::_array_column($new,'py');
		array_multisort($py,SORT_ASC,$new);
		return $new;
	}
}
