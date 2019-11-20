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
		$tongji = '';
		$a = 0;
		if ($counts) {
			foreach ($counts as $ke => $value) {
				$countsSum += $value['count'];
				$moneySum += $value['moneySum'];
				$jiesuan_moneySum += $value['jiesuan_moneySum'];
				$lixiSum += $value['lixiSum'];
			}
		}
		$rets['countsSum']=$countsSum;
		$rets['moneySum']=$moneySum;
		$rets['jiesuan_moneySum']=$jiesuan_moneySum;
		$rets['lixiSum']=$lixiSum;
		$rets['tongji']=$tongji;
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
		$ret1 = parent::save($data);
		//变化的额度不等于0时才更新额度
		if ($eduMoney!=0) {
			$ret2 = $this->upEdu($data['share_flag'],$eduMoney*-1);
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
				$ret[] = $this->upEdu($share_flag,$value['money']);
			}
		}
		if (in_array('0',$ret)) {
			return false;
		}else{
			return true;

		}
	}
	//额度的增减
	public function upEdu($share_flag,$money){
		$upSql = "UPDATE sdb_b2c_poscard set usable_edu=usable_edu+{$money} where share_flag='{$share_flag}' and is_enabled='1'";
		$this->db->exec($upSql);
		$ret = $this->db->affect_row();
		return $ret;
	}
}
