<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class b2c_ctl_admin_posbrand extends desktop_controller{

    public function __construct($app) {
        parent::__construct($app);
        $this->model = $this->app->model('posbrand');
    }

	public function index(){
        $actions_base['title'] = app::get('b2c')->_('pos机品牌');
        $custom_actions[] = array('label'=>'添加','href'=>'index.php?app=b2c&ctl=admin_posbrand&act=add','target'=>"dialog::{title:'添加',width:660,height:560}");
        $actions_base['actions'] = $custom_actions;
        $actions_base['use_buildin_recycle'] = true;
        $actions_base['use_buildin_filter'] = true;
        $actions_base['use_view_tab'] = true;
        $this->finder('b2c_mdl_posbrand',$actions_base);
	}

    public function add(){
        $postype = $this->model->getPostype();
        // $this->pagedata['postype'] = json_encode($postype);
        $this->pagedata['postype'] = $postype;
        $this->pagedata['postypeCount'] = count($postype);
        $this->display('admin/pos/posbrand.html');
    }

    //ajax添加html
    public function ajaxGetHtml(){
        $flag = $_POST['flag'];
        $posbrand_id = $_POST['posbrand_id'];
        $postype = $this->model->getPostype();
        $postypeKeys = array_keys($postype);
        if ($posbrand_id) {
            $postypeInfo = $this->app->model('postype')->getList('*',array('posbrand_id'=>$posbrand_id,'is_sub'=>1));
            if (!$postypeInfo) {
                $ret = json_encode(array('error'=>1));
                echo $ret;exit;
            }
            if ($postypeInfo) {
                foreach ($postypeInfo as $key => $value) {
                    $newGroup[$value['sub_name']][] = $value;
                }
                $newGroup = array_values($newGroup);
                foreach ($newGroup as $ke => &$group) {
                    $flag = utils::_array_column($group,'shuaka_typeOld');
                    $newCha = array_diff ($postypeKeys,$flag);
                    foreach ($newCha as $k => $val) {
                        $arr['shuaka_typeOld'] = $val;
                        $arr['shuaka_type'] = $postype[$val];
                        $arr['feilv'] = '';
                        array_push($group,$arr);
                    }
                }
            }
        }
        $this->pagedata['flag'] = $flag;
        $this->pagedata['postype'] = $postype;
        $this->pagedata['newGroup'] = $newGroup;
        $view = 'admin/pos/ajagGetHtml.html';
        $html = $this->fetch($view);
        $ret = json_encode(array('error'=>0,'html'=>$html));
        echo $ret;
    }

    //编辑
    public function edit($posbrand_id){
        $postype = $this->model->getPostype();
        $postypeKeys = array_keys($postype);
        $info = $this->model->getRow('*',array('posbrand_id'=>$posbrand_id));
        $postypeInfo = $this->app->model('postype')->getList('*',array('posbrand_id'=>$posbrand_id));
        $flag = utils::_array_column($postypeInfo,'shuaka_typeOld');
        $newCha = array_diff ($postypeKeys,$flag);
        foreach ($newCha as $k => $val) {
            $arr['shuaka_typeOld'] = $val;
            $arr['shuaka_type'] = $postype[$val];
            $arr['feilv'] = '';
            array_push($postypeInfo,$arr);
        }
        $this->pagedata['info'] = $info;
        $this->pagedata['postype'] = $postype;
        $this->pagedata['newGroup'] = $postypeInfo;
        $this->display('admin/pos/posbrand.html');
    }

    //保存
    public function save(){
        $mdlPostype = $this->app->model('postype');
        $this->begin();
        $data = $_POST;
        $postypes = $this->model->getPostype();
        //子品牌删除操作
        if ($data['sub_delete']) {
            $data['sub_delete'] = trim($data['sub_delete'],'-');
            $sub_delete = explode('-', $data['sub_delete']);
            foreach ($sub_delete as $key => $value) {
                $mdlPostype ->delete(array('postype_id'=>$value));
            }
        }
        //是否含有子品牌切换操作
        if ($data['is_havesub'] == 1 && $data['posbrand_id']) {
            //删除不是子品牌的
            $mdlPostype ->delete(array('posbrand_id'=>$data['posbrand_id'],'is_sub'=>0));
        }elseif ($data['is_havesub'] == 0 && $data['posbrand_id']) {
            //删除是子品牌的
            $mdlPostype ->delete(array('posbrand_id'=>$data['posbrand_id'],'is_sub'=>1));
        }
        // $data['create_time'] = time();
        if ($data['is_havesub']=='1') {
            $data['merchant_code'] = '-';
            $data['faren'] = '-';
            $data['jiesuan_bank'] = '-';
            $data['memo'] = '-';
        }
        $data['display'] = $_POST['display']?'true':'false';
        $posbrand_id = $data['posbrand_id'];
        try {
            if ($posbrand_id) {
                $this->model->save($data);
            }else{
                $data['create_time'] = time();
                $posbrand_id = $this->model->insert($data);
            }
            // $this->end(false, app::get('b2c')->_('保存失败！'));
            foreach ($postypes as $key => $value) {
                foreach ($data[$key] as $ke => $val) {
                    if ($val['is_enable']) {
                        $postypeSave = array(
                            'postype_id'=>$val['postype_id'],
                            'posbrand_id'=>$posbrand_id,
                            'sub_name'=>$data['sub_name'][$ke],
                            'shuaka_type'=>$key,
                            'feilv'=>$val['feilv']?$val['feilv']:'0',
                            'fengding'=>$val['fengding'],
                            'merchant_code'=>$val['merchant_code'],
                            'faren'=>$data['sub_faren'][$ke],
                            'jiesuan_bank'=>$data['sub_jiesuan_bank'][$ke],
                            'logon_name'=>$data['logon_name'][$ke],
                            'logon_password'=>$data['logon_password'][$ke],
                            'memo'=>$data['sub_memo'][$ke],
                            'is_sub'=>$data['is_havesub'],
                            'is_enable'=>1,
                            'create_time'=>time(),
                        );
                        $mdlPostype ->save($postypeSave);
                    }else{
                        if ($val['postype_id']) {
                            $mdlPostype->update(array('is_enable'=>0),array('postype_id'=>$val['postype_id']));
                        }
                    }
                }
            }
        } catch (Exception $e) {
            $this->end(false, app::get('b2c')->_('保存失败！'));
        }
        $this->end(true, app::get('b2c')->_('保存成功！'));
    }
}