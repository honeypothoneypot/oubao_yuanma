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
class b2c_mdl_posguozhang extends dbeav_model{
	var $defaultOrder = array('create_time',' DESC');
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
			$ret2 = $this->upEdu($data['share_flag'],$eduMoney);
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
				$ret[] = $this->upEdu($share_flag,$value['money']*-1);
			}
		}
		if (in_array('0',$ret)) {
			return false;
		}else{
			return true;

		}
	}
}
