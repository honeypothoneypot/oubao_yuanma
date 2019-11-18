<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class b2c_ctl_admin_poscard extends desktop_controller{

	public function index(){
        $actions_base['title'] = app::get('b2c')->_('pos日志列表');
        $custom_actions[] = array('label'=>'添加信用卡','href'=>'index.php?app=b2c&ctl=admin_poscard&act=add','target'=>"dialog::{title:'添加信用卡',width:460,height:460}");
        $actions_base['actions'] = $custom_actions;
        $actions_base['use_buildin_recycle'] = true;
        $actions_base['use_buildin_filter'] = true;
        $actions_base['use_view_tab'] = true;
        $this->finder('b2c_mdl_poscard',$actions_base);
	}

    public function add(){
        $mdl = app::get('b2c')->model('poscard');
        $banks = $mdl->getBanks();
        $this->pagedata['banks'] = $banks;
        $this->display('admin/pos/poscard.html');
    }
    public function edit($card_id){
        $mdl = app::get('b2c')->model('poscard');
        $banks = $mdl->getBanks();
        $info = $mdl->getRow('*',array('card_id'=>$card_id));
        $this->pagedata['banks'] = $banks;
        $this->pagedata['info'] = $info;
        $this->display('admin/pos/poscard.html');
    }
    public function save(){
        $data = $_POST;
        $data['create_time'] = time();
        $this->begin();
        $obj_pos = app::get('b2c')->model('poscard');
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
    public function setShare(){
        $sql = "SELECT belong_to,name,card_no FROM sdb_b2c_poscard where 1 GROUP BY belong_to,name";
        $rowsetList = app::get('b2c')->model('poscard')->db->select($sql);
        foreach ($rowsetList as $key => $value) {
            $upSql = "UPDATE sdb_b2c_poscard set share_flag='{$value['card_no']}' where belong_to='{$value['belong_to']}' and name='{$value['name']}' ";
            app::get('b2c')->model('poscard')->db->exec($upSql);
        }
        echo "成功";
    }
}