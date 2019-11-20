<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class b2c_ctl_admin_pos_guozhang extends desktop_controller{

	public function index(){
        $actions_base['title'] = app::get('b2c')->_('过账记录表');
        $custom_actions[] = array('label'=>'添加记录','href'=>'index.php?app=b2c&ctl=admin_pos_guozhang&act=logadd','target'=>"dialog::{title:'添加记录',width:460,height:460}");
        $actions_base['actions'] = $custom_actions;
        $actions_base['use_buildin_recycle'] = true;
        $actions_base['use_buildin_filter'] = true;
        $actions_base['use_view_tab'] = true;
        $this->finder('b2c_mdl_posguozhang',$actions_base);
	}

    //获取公共数据
    public function getPagedata(){
        //获取信用卡列表
        $mdlCard = $this->app->model('poscard');
        $cardLists = $mdlCard->getList('card_id,name,belong_to,card_no,memo,share_flag',array('is_enabled'=>'1'));
        $cardLists = utils::array_group_by($cardLists,'belong_to');
        $this->pagedata['cardLists'] = $cardLists;
    }
    public function logadd(){
        $this->pagedata['create_time'] = time();
        $this->getPagedata();
        $this->display('admin/pos/guozhangadd.html');
    }
    public function logedit($id){
        $obj_pos = app::get('b2c')->model('posguozhang');
        $filter['id'] = $id;
        $info = $obj_pos->getRow('*',$filter);
        $this->pagedata['info'] = $info;
        $this->getPagedata();
        $this->display('admin/pos/guozhangadd.html');
    }
    public function logsave(){
        $data = $_POST;
        // 创建时间
        foreach ($data['_DTIME_'] as $val) {
            $temp[] = $val['from_time'];
        }
        if ($data['id']) {
            $data['create_time'] = strtotime($data['from_time'].' '. implode(':', $temp));
        }else{
            $data['create_time'] = time();
        }
        unset($data['_DTYPE_TIME'],$data['_DTIME_'],$data['from_time']);
        $obj_pos = app::get('b2c')->model('posguozhang');
        try {
            $obj_pos->save($data);
        } catch (Exception $e) {
            echo  json_encode(array('success'=>false,'msg'=>$e->getMessage()));exit;
        }
        echo  json_encode(array('success'=>true,'msg'=>'提交成功'));exit;
    }
}