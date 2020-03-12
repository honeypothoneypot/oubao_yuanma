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
	var $defaultOrder = array('id',' DESC');
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
					a.*,b.card_id,b.name as bankname,b.belong_to,
					b.card_no,b.memo as bankmemo,c.postype_id,c.posbrand_id,c.sub_name,c.shuaka_type,d.name as brandname
				FROM sdb_b2c_poslog AS a
				LEFT JOIN sdb_b2c_poscard AS b ON a.card_id = b.card_id
				LEFT JOIN sdb_b2c_postype AS c ON a.postype_id  = c.postype_id
				LEFT JOIN sdb_b2c_posbrand AS d ON c.posbrand_id  = d.posbrand_id WHERE 1 ";
		//搜索条件
        //$id,$belong_to,$card_id,$postype_id,$from_time,$to_time,posbrand_id
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
		if ($filter['posbrand_id']) {
			$sql.=" AND d.posbrand_id ={$filter['posbrand_id']}";
		}
		if ($filter['mcc']) {
			$sql.=" AND a.mcc in (4511,4722,7011)";
		}
		if ($filter['type']) {
			$sql .=" And a.type='{$filter['type']}'";
		}
		$sql.=" ORDER BY a.create_time DESC LIMIT {$offset},{$limit}";
		$datas = kernel::database()->select($sql);
		// $moneySum = array_sum(array_map(create_function('$val', 'return $val["money"];'), $datas));
		// $jiesuan_moneySum = array_sum(array_map(create_function('$val', 'return $val["jiesuan_money"];'), $datas));
		// $lixiSum = bcsub($moneySum,$jiesuan_moneySum,2);
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
		if ($filter['posbrand_id']) {
			$sql.=" AND d.posbrand_id ={$filter['posbrand_id']}";
		}
		if ($filter['mcc']) {
			$sql.=" AND a.mcc in (4511,4722,7011)";
		}
		$sql.=" GROUP BY d.posbrand_id";
		//获取所有品牌：
		// $brands = app::get('b2c')->model('posbrand')->getList('posbrand_id,name');
		$countsSum = 0;
		$moneySum = 0;
		$jiesuan_moneySum = 0;
		$lixiSum = 0;
		$counts = kernel::database()->select($sql);
		if ($counts) {
			foreach ($counts as $ke => $value) {
				$countsSum += $value['count'];
				if ($value['posbrand_id']) {
					$moneySum += $value['moneySum'];
					$jiesuan_moneySum += $value['jiesuan_moneySum'];
					$lixiSum += $value['lixiSum'];
				}
			}
		}
		$rets['countsSum']=$countsSum;
		$rets['moneySum']=$moneySum;
		$rets['jiesuan_moneySum']=$jiesuan_moneySum;
		$rets['lixiSum']=$lixiSum;
		return $rets;
	}
	public function save($data){
		$db = $this->db;
		$db->beginTransaction();
		if ($data['id']) {//编辑时对比额度的变化
			//新的-老的？
			$eduMoney = round($data['money']-$data['oldMoney'],2);
		}else{
			$eduMoney = $data['money'];
		}
		if (in_array($data['type'], array('pos','xiaofei','nianfei'))) {
			$eduMoney = $eduMoney*-1;
			if ($data['type']=='pos' && !$data['postype_id']) {
				throw new Exception("pos刷卡请选择刷卡方式");
			}
		}
		if ($data['type']=='huankuan') {
			// $data['money'] = $data['money']*-1;
		}
		if ($data['type']!='pos') {
			$data['posbrand_id'] = 0;
			$data['postype_id'] = 0;
			$data['jiesuan_money'] = 0;
		}
		$ret1 = parent::save($data);
		//变化的额度不等于0时才更新额度
		if ($eduMoney!=0) {
			$ret2 = $this->app->model('poscard')->upEdu($data['share_flag'],$eduMoney);
		}else{
			$ret2 = 1;
		}
		if ($ret1 && $ret2>0) {
			$db->commit();
		}else{
			$db->rollback();
			throw new Exception("记录保存失败");
			return false;
		}
	}
	//删除之前的动作。恢复额度
	public function pre_recycle($data){
		$objCard = app::get('b2c')->model('poscard');
		foreach ($data as $key => $value) {
			if ($value['card_id']) {
				$share_flag = $objCard->getRow('share_flag',array('card_id'=>$value['card_id']));
				$share_flag = $share_flag['share_flag'];
				$ret[] = $objCard->upEdu($share_flag,$value['money']);
			}
		}
		if (in_array('0',$ret)) {
			return false;
		}else{
			return true;
		}
	}

	//查询账单
	public function getZhangdan($flag,$card_id){
		// $time = '2020-3-9';
        if ($time) {
            $time = strtotime($time);
        }else{
            $time = time();
        }
        $arg = $arg2= -1;
        if ($flag=='1') {
            $arg = $arg2= 0;
        }
        $arg2++;
        //本月：年-月
        $thisMonth = date("Y-m",strtotime("{$arg2} months",$time));
        //上月：年-月
        $lastMonth = date("Y-m",strtotime("{$arg} months",$time));
        //前月：年-月
        $arg--;
        $prevMonth = date("Y-m",strtotime("{$arg} months",$time));
        //下月：年-月
        $arg2++;
        $nextMonth = date("Y-m",strtotime("{$arg2} months",$time));
        //前月1号作为开始时间
        $b_time = strtotime("{$prevMonth}-1");
		$sql = "SELECT a.*,b.card_no,b.belong_to,b.zhangdan_date,b.huankuan_date,b.name,b.zhangdan_dateTime,b.is_guding,b.zhangdanToDays,c.shuaka_type
			FROM sdb_b2c_poslog a
			LEFT JOIN sdb_b2c_poscard b ON b.card_id = a.card_id
			LEFT JOIN sdb_b2c_postype c ON c.postype_id = a.postype_id
			WHERE a.create_time>={$b_time} and a.type in('pos','xiaofei','huankuan','nianfei')
			and b.is_enabled='1'
		";
		if ($card_id) {
			$sql .= " and a.card_id='{$card_id}'";
		}
		$sql .=" order by a.create_time asc";
		$data =$this->db->select($sql);
		foreach ($data as $key => &$value) {
			// if ($value['name']=='广发银行' && in_array($value['shuaka_type'], array('weixin','zhifubao'))) {
			// 	$value['zhangdan_dateTime'] = '24';
			// }
			/*
			1.不是固定还款日的，则根据账单日-账单日多少天后来计算还款日;
			2.上月还款日=前月-账单日的关系
			3.本月还款日=上月-账单日的关系
			*/
			//本期账单计算时间：开始时间
			//根据当前日期来算账单周期
			// $thisDay = date('j',$time);//j-月份中的第几天，没有前导零，1到31
			//本月账单日
        	$thisZhangdanTime = strtotime("{$thisMonth}-{$value['zhangdan_date']} {$value['zhangdan_dateTime']}:00:00");
			//如果账单日大于当天，则说明账单周期是前个月的账单日-上个月的账单日;再判断还款日
			if ($thisZhangdanTime>$time) {
				//非固定还款日
				if ($value['is_guding']=='0') {
					//上次还款日
					$tmp = "{$prevMonth}-{$value['zhangdan_date']}";
					$value['huankuanDateLast'] = strtotime("{$tmp} + {$value['zhangdanToDays']} day 23:59:59");
					//本次还款日
					$lastMonthZhangdan = "{$lastMonth}-{$value['zhangdan_date']}";
					$value['huankuanDateThis'] = strtotime("{$lastMonthZhangdan} + {$value['zhangdanToDays']} day 23:59:59");
				}else{
					if ($value['huankuan_date']>$value['zhangdan_date']) {
						//上次还款日
						$value['huankuanDateLast'] = strtotime("{$prevMonth}-{$value['huankuan_date']} 23:59:59");
						//本次还款日
						$value['huankuanDateThis'] = strtotime("{$lastMonth}-{$value['huankuan_date']} 23:59:59");
					}else{
						//上次还款日
						$value['huankuanDateLast'] = strtotime("{$lastMonth}-{$value['huankuan_date']} 23:59:59");
						//本次还款日
						$value['huankuanDateThis'] = strtotime("{$thisMonth}-{$value['huankuan_date']} 23:59:59");
					}
				}
				$zhangdanStart = strtotime("{$prevMonth}-{$value['zhangdan_date']} {$value['zhangdan_dateTime']}:00:00");
				$zhangdanEnd = strtotime("{$lastMonth}-{$value['zhangdan_date']} {$value['zhangdan_dateTime']}:00:00");
				if ($value['create_time']>=$zhangdanStart && $value['create_time']<= $zhangdanEnd) {
					$value['isBenqi'] = 1;
				}
			}else{//如果账单日小于等于当天，则说明账单周期是上个月的账单日-这个月的账单日;再判断还款日
				$zhangdanStart = strtotime("{$lastMonth}-{$value['zhangdan_date']} {$value['zhangdan_dateTime']}:00:00");
				$zhangdanEnd = strtotime("{$thisMonth}-{$value['zhangdan_date']} {$value['zhangdan_dateTime']}:00:00");
				if ($value['create_time']>=$zhangdanStart && $value['create_time']<= $zhangdanEnd) {
					$value['isBenqi'] = 2;
				}
				if ($value['is_guding']=='0') {
					//上次还款日
					$tmp = "{$lastMonth}-{$value['zhangdan_date']}";
					$value['huankuanDateLast'] = strtotime("{$tmp} + {$value['zhangdanToDays']} day 23:59:59");
					//本次还款日
					$lastMonthZhangdan = "{$thisMonth}-{$value['zhangdan_date']}";
					$value['huankuanDateThis'] = strtotime("{$lastMonthZhangdan} + {$value['zhangdanToDays']} day 23:59:59");
				}else{
					if ($value['huankuan_date']>$value['zhangdan_date']) {
						//上次还款日
						$value['huankuanDateLast'] = strtotime("{$lastMonth}-{$value['huankuan_date']} 23:59:59");
						//本次还款日
						$value['huankuanDateThis'] = strtotime("{$thisMonth}-{$value['huankuan_date']} 23:59:59");
					}else{
						//上次还款日
						$value['huankuanDateLast'] = strtotime("{$thisMonth}-{$value['huankuan_date']} 23:59:59");
						//本次还款日
						$value['huankuanDateThis'] = strtotime("{$nextMonth}-{$value['huankuan_date']} 23:59:59");
					}
				}
			}
			$value['huankuanDate2'] = date('n-j',$value['huankuanDateThis']);
			$value['money'] = round($value['money'],2);
			if ($value['type']=='huankuan' && $value['create_time']>$value['huankuanDateLast'] && $value['create_time']<=$value['huankuanDateThis']) {
				$value['yiHuan_benqi'] = 1;
			}
		}
		//重新排序
		$flag2 = utils::_array_column($data,'huankuanDateThis');
		$flag3 = utils::_array_column($data,'create_time');
		array_multisort($flag2,SORT_ASC,$flag3,SORT_DESC,$data);
		foreach ($data as $key => $value2) {
			$newData[$value2['belong_to']]["{$value2['card_id']}"][] = $value2;
		}
		foreach ($newData as $key => &$valu) {
			foreach ($valu as $ke => &$val) {
				$needHuankuan = array_sum(
					array_map(
						create_function('$val',
							'if($val["isBenqi"] && $val["type"]!="huankuan"){
								return $val["money"];
							}'
						),$val)
				);
				$benqiHuankuan = array_sum(
					array_map(
						create_function('$val',
							'if($val["yiHuan_benqi"]){
								return $val["money"];
							}'
						),$val)
				);
				$ret[$key]["{$ke}"]['flag'] = $flag;
				$ret[$key]["{$ke}"]['card_id'] = $val['0']['card_id'];
				$ret[$key]["{$ke}"]['needHuankuan'] = $needHuankuan;
				$ret[$key]["{$ke}"]['benqiHuankuan'] = $benqiHuankuan;
				$ret[$key]["{$ke}"]['daiHuankuan'] = bcsub($needHuankuan,$benqiHuankuan,2);
				$ret[$key]["{$ke}"]['name'] = $val['0']['name'];
				$ret[$key]["{$ke}"]['huankuan_date'] = $val['0']['huankuanDate2'];
				$ret[$key]["{$ke}"]['card_no'] = $val['0']['card_no'];
				$ret[$key]["{$ke}"]['zhangdan_date'] = $val['0']['zhangdan_date'];
				if ($card_id) {
					foreach ($val as $k => $v) {
						if ($v['isBenqi']) {
							$ret[$key]["{$ke}"]['benqiMx'][$k] = $v;
						}
					}
				}
			}
		}
		return $ret;
	}
}
