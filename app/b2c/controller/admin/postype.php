<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class b2c_ctl_admin_postype extends desktop_controller{

    public function __construct($app) {
        parent::__construct($app);
        $this->model = $this->app->model('postype');
    }
	public function index(){
        $actions_base['title'] = app::get('b2c')->_('pos刷卡类型列表');
        $custom_actions[] = array('label'=>'添加','href'=>'index.php?app=b2c&ctl=admin_postype&act=add','target'=>"dialog::{title:'添加类型',width:460,height:460}");
        // $actions_base['actions'] = $custom_actions;
        $actions_base['use_buildin_recycle'] = false;
        $actions_base['use_buildin_filter'] = true;
        $actions_base['use_view_tab'] = true;
        $this->finder('b2c_mdl_postype',$actions_base);
	}

    public function add(){
        $brand = $this->app->model('posbrand');
        $brands = $brand ->getList('posbrand_id,name',array());
        //刷卡方式
        $postype = $this->model->getPostype();
        $this->pagedata['brands'] = $brands;
        $this->pagedata['postype'] = $postype;
        $this->display('admin/pos/postypeadd.html');
    }
    public function edit($postype_id){
        $info = $this->model->getRow('*',array('postype_id'=>$postype_id));
        $mdlBrand = $this->app->model('posbrand');
        $brands = $mdlBrand ->getList('posbrand_id,name,is_ErCode',array());
        foreach ($brands as $key => $value) {
            if ($value['posbrand_id'] == $info['posbrand_id']) {
                $info['is_ErCode'] = $value['is_ErCode'];
                break;
            }
        }
        //刷卡方式
        $postype = $this->model->getPostype();
        $this->pagedata['brands'] = $brands;
        $this->pagedata['postype'] = $postype;
        $this->pagedata['info'] = $info;
        $this->display('admin/pos/postypeadd.html');
    }
    public function save(){
        $data = $_POST;
        $data['create_time'] = time();
        $this->begin();
        try {
            $this->model->save($data);
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
        try {
            $obj_postype->save($data);
        } catch (Exception $e) {
            $this->end(false, app::get('b2c')->_('保存失败！'));
        }
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