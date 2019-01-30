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
class b2c_mdl_poslog extends dbeav_model{
	var $defaultOrder = array('create_time',' DESC');
	public function get_schema(){
		$columns = parent::get_schema();
		// $a = array(
		// 	'lsc'=>array(
		// 		'width'=>110,
		// 		'in_list'=>1,
		// 		'label'=>'蔺苏川',
		// 	)
		// );
  //       $columns['columns'] = array_merge($columns['columns'],$a);
  //       $columns['in_list'] = array_merge($columns['in_list'],array('lsc'));
		return $columns;
	}
	public function getList($cols='*', $filter=array(), $offset=0, $limit=-1, $orderType=null){
		$datas = parent::getList($cols, $filter, $offset, $limit, $orderType);
		$he = array_sum(array_map(create_function('$val', 'return $val["money"];'), $datas));
		$heji[] = array(
			'id'=>'合计',
			'money' => $he,
		);
		$datas = array_merge($datas,$heji);
		return $datas;
	}

	public function getLog($filter,$offset=0, $limit=50){
		$sql = "SELECT
					a.id,a.money,a.mcc,a.feilv,a.jiesuan_money,a.memo,a.create_time,b.card_id,b.name as bankname,b.belong_to,
					b.card_no,b.memo as bankmemo,c.postype_id,c.posbrand_id,c.sub_name,c.shuaka_type,d.name as brandname
				FROM sdb_b2c_poslog AS a
				LEFT JOIN sdb_b2c_poscard AS b ON a.card_id = b.card_id
				LEFT JOIN sdb_b2c_postype AS c ON a.postype_id  = c.postype_id
				LEFT JOIN sdb_b2c_posbrand AS d ON c.posbrand_id  = d.posbrand_id WHERE 1";
		//搜索条件
        //$id,$belong_to,$card_id,$postype_id,$from_time,$to_time
		if ($filter['id']) {
			$sql.=" AND a.id='{$filter['id']}' ";
		}
		if ($filter['belong_to']) {
			$sql.=" AND b.belong_to='{$filter['belong_to']}' ";
		}
		if ($filter['card_id']) {
			$sql.=" AND a.card_id={$filter['card_id']}";
		}
		if ($filter['from_time']) {
			$sql.=" AND a.create_time >={$filter['from_time']}";
		}
		if ($filter['to_time']) {
			$sql.=" AND a.create_time <={$filter['to_time']}";
		}
		$sql.=" ORDER BY a.create_time DESC LIMIT {$offset},{$limit}";
		$datas = kernel::database()->select($sql);
		$moneySum = array_sum(array_map(create_function('$val', 'return $val["money"];'), $datas));
		$jiesuan_moneySum = array_sum(array_map(create_function('$val', 'return $val["jiesuan_money"];'), $datas));
		$lixiSum = bcsub($moneySum,$jiesuan_moneySum,2);
		$ret['lists'] = $datas;
		return $ret;
	}

	public function getCount($filter){
		$sql = "SELECT
					COUNT(a.id) as count,SUM(a.money) as moneySum,SUM(a.jiesuan_money) as jiesuan_moneySum,(SUM( a.money ) - SUM( a.jiesuan_money )) as lixiSum,d.posbrand_id,d.name
				FROM  sdb_b2c_poslog AS a
				LEFT JOIN sdb_b2c_poscard AS b ON a.card_id = b.card_id
				LEFT JOIN sdb_b2c_postype AS c ON a.postype_id  = c.postype_id
				LEFT JOIN sdb_b2c_posbrand AS d ON c.posbrand_id  = d.posbrand_id WHERE 1
				";
		//搜索条件
        //$belong_to,$card_id,$postype_id,$from_time,$to_time
		if ($filter['belong_to']) {
			$sql.=" AND b.belong_to='{$filter['belong_to']}' ";
		}
		if ($filter['card_id']) {
			$sql.=" AND a.card_id={$filter['card_id']}";
		}
		if ($filter['from_time']) {
			$sql.=" AND a.create_time >={$filter['from_time']}";
		}
		if ($filter['to_time']) {
			$sql.=" AND a.create_time <={$filter['to_time']}";
		}
		$sql.=" GROUP BY d.posbrand_id ORDER BY count DESC";
		//获取所有品牌：
		$brands = app::get('b2c')->model('posbrand')->getList('posbrand_id,name');

		$countsSum = 0;
		$moneySum = 0;
		$jiesuan_moneySum = 0;
		$lixiSum = 0;

		$counts = kernel::database()->select($sql);
		$tongji = '';
		$a = 0;
		if ($counts) {
			foreach ($brands as $key => $brand) {
				foreach ($counts as $ke => $value) {
					if ($value['posbrand_id'] == $brand['posbrand_id']) {
						$al[] = $value['posbrand_id'];

						$countsSum += $value['count'];
						$moneySum += $value['moneySum'];
						$jiesuan_moneySum += $value['jiesuan_moneySum'];
						$lixiSum += $value['lixiSum'];
						$tongji[$ke] = $value['name'].'-'.$value['count'].'次';
					}
				}
				if (!in_array($brand['posbrand_id'], $al)) {
					$arrPu['count'] = 0;
					$arrPu['name'] = $brand['name'];
					$arrPu['count'] = 0;
					$tongji1[] = $brand['name'].'-'.'0次';
					array_push($counts, $arrPu);
				}
			}
			$tongji = array_merge($tongji, $tongji1);
			$tongji = implode('<br/>', $tongji);
		}
		$rets['countsSum']=$countsSum;
		$rets['moneySum']=$moneySum;
		$rets['jiesuan_moneySum']=$jiesuan_moneySum;
		$rets['lixiSum']=$lixiSum;
		$rets['tongji']=$tongji;
		return $rets;
	}
}
