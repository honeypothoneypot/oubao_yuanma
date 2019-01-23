<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class b2c_ctl_admin_pos extends desktop_controller{

	public function index(){
        $actions_base['title'] = app::get('b2c')->_('pos日志列表');
        $custom_actions[] = array('label'=>'添加记录','href'=>'index.php?app=b2c&ctl=admin_pos&act=logadd','target'=>"dialog::{title:'添加记录',width:460,height:460}");
        $actions_base['actions'] = $custom_actions;
        $actions_base['use_buildin_recycle'] = true;
        $actions_base['use_buildin_filter'] = true;
        $actions_base['use_view_tab'] = true;
        $this->finder('b2c_mdl_poslog',$actions_base);
	}

    public function logadd(){
        $this->pagedata['create_time'] = time();
        $this->display('admin/pos/logadd.html');
    }
    public function logedit($id){
        $obj_pos = app::get('b2c')->model('poslog');
        $filter['id'] = $id;
        $info = $obj_pos->getLog($filter);
        $info = $info[0];
        $this->pagedata['info'] = $info;
        $this->display('admin/pos/logadd.html');
    }
    public function logsave(){
        $data = $_POST;
        // 创建时间
        foreach ($data['_DTIME_'] as $val) {
            $temp[] = $val['from_time'];
        }
        //计算结算金额
        if ($data['fengding']>0) {
            $data['jiesuan_money'] = $data['money']-$data['fengding'];
        }else{
            $jiesuan = $data['money']*(1-$data['feilv']/100);
            $data['jiesuan_money'] = substr(sprintf("%.3f",$jiesuan),0,-1);
        }
        $data['create_time'] = strtotime($data['from_time'].' '. implode(':', $temp));
        unset($data['_DTYPE_TIME'],$data['_DTIME_'],$data['from_time']);
        $this->begin();
        $obj_pos = app::get('b2c')->model('poslog');
        try {
            $obj_pos->save($data);
        } catch (Exception $e) {
            $this->end(false, app::get('b2c')->_('保存失败！'));
        }
        $this->end(true, app::get('b2c')->_('保存成功！'));
    }

    //获取pos机品牌
    public function ajaxGetPosBrand(){
        $mdlBrand = app::get('b2c')->model('posbrand');
        $lists = $mdlBrand->getList('posbrand_id,name',array());
        $this->pagedata['posbrand_id'] = $_POST['posbrand_id'];
        $this->pagedata['lists'] = $lists;
        $view = 'admin/pos/ajaxGetPosBrand.html';
        echo $this->fetch($view);
    }

    //获取刷卡方式
    public function ajaxGetPosType(){
        $posbrand_id = $_POST['posbrand_id'];
        $mdlType = $this->app->model('postype');
        $lists = $mdlType->getList('postype_id,sub_name,shuaka_type,feilv,fengding',array('posbrand_id'=>$posbrand_id));
        $this->pagedata['lists'] = $lists;
        $this->pagedata['postype_id'] = $_POST['postype_id'];
        $view = 'admin/pos/ajaxGetPosType.html';
        echo $this->fetch($view);
    }

    //获取信用卡列表
    public function ajaxGetPosCard(){
        $belong_to = $_POST['belong_to'];
        $mdlCard = $this->app->model('poscard');
        $lists = $mdlCard->getList('card_id,name,belong_to,card_no,memo',array('belong_to'=>$belong_to));
        $this->pagedata['card_id'] = $_POST['card_id'];
        $this->pagedata['lists'] = $lists;
        $view = 'admin/pos/ajaxGetPosCard.html';
        echo $this->fetch($view);
    }
}