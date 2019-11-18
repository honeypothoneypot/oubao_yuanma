<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class b2c_ctl_admin_everyday extends desktop_controller{

	public function remind(){
        $this->page("admin/pos/remind.html");
	}
    public function ajaxGetRemind(){
        //默认按可用额度排序
        $sql = "SELECT * FROM sdb_b2c_poscard WHERE is_enabled='1' order by usable_edu desc,all_edu desc";
        $rowsets = app::get('b2c')->model('poscard')->db->select($sql);
        //查询日志：
        //获取今天零点的时间戳：
        $start = strtotime(date('Y-m-d',time()));
        $sql = "SELECT card_id,count(id) as count FROM sdb_b2c_poslog where modified_time>'{$start}' group by card_id ";
        $logs = app::get('b2c')->model('poscard')->db->select($sql);
        foreach ($logs as $key => $value) {
            $count[$value['card_id']]=$value['count'];
        }
        foreach ($rowsets as $key => &$value) {
            $value['count'] = $count[$value['card_id']];
            $new[$value['belong_to']][] = $value;
        }
        $this->pagedata['rowsets'] = $new;
        echo $this->fetch('admin/pos/remindajax.html');exit;
    }
}