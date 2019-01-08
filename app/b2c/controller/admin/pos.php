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
        $info = $obj_pos->getRow('*',array('id'=>$id));
        $this->pagedata['id'] = $info['id'];
        $this->pagedata['name'] = $info['name'];
        $this->pagedata['pos_type'] = $info['pos_type'];
        $this->pagedata['bank'] = $info['bank'];
        $this->pagedata['money'] = $info['money'];
        $this->pagedata['mcc'] = $info['mcc'];
        $this->pagedata['memo'] = $info['memo'];
        $this->pagedata['create_time'] = $info['create_time'];
        $this->display('admin/pos/logadd.html');
    }
    public function logsave(){
        $data = $_POST;
        // 创建时间
        foreach ($data['_DTIME_'] as $val) {
            $temp[] = $val['from_time'];
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

    public function postype(){
        $actions_base['title'] = app::get('b2c')->_('pos类型列表');
        $custom_actions[] = array('label'=>'添加','href'=>'index.php?app=b2c&ctl=admin_pos&act=posTypeAdd','target'=>"dialog::{title:'添加pos机',width:460,height:460}");
        $actions_base['actions'] = $custom_actions;
        $actions_base['use_buildin_filter'] = true;
        $actions_base['use_view_tab'] = true;
        $this->finder('b2c_mdl_postype',$actions_base);
    }

    public function posTypeAdd($postype){
        $this->display('admin/pos/postypeadd.html');
    }

    public function postypesave(){
        $this->begin();
        $obj_postype = app::get('b2c')->model('postype');
        $data = $_POST;
        $data['create_time'] = time();
        $obj_postype->save($data);
        $this->end(true, app::get('b2c')->_('添加成功！'));
    }

    public function postypeedit($postype){
        $obj_postype = app::get('b2c')->model('postype');
        $info = $obj_postype->getRow('*',array('pos_type'=>$postype));
        $this->pagedata['pos_type'] = $info['pos_type'];
        $this->pagedata['name'] = $info['name'];
        $this->pagedata['feilv'] = $info['feilv'];
        $this->pagedata['memo'] = $info['memo'];
        $this->display('admin/pos/postypeadd.html');
    }
}