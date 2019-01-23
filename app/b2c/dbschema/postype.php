<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

$db['postype'] = array (
	'columns' => array (
		'postype_id' => array (
			'type' => 'number',
			'required' => true,
			'pkey' => true,
			'extra' => 'auto_increment',
			'width' => 110,
			'editable' => false,
			'in_list' => true,
			'default_in_list' => true,
			'label' => app::get('b2c')->_('pos机刷卡类型id'),
			'comment' => app::get('b2c')->_('pos机刷卡类型id'),
		),
		'posbrand_id' => array (
      		'type' => 'table:posbrand',
			'required' => true,
			'width' => 110,
			'editable' => false,
			'in_list' => true,
			'default_in_list' => true,
			'label' => app::get('b2c')->_('所属pos品牌'),
			'comment' => app::get('b2c')->_('所属pos品牌'),
		),
		'sub_name' => array(
			'type' => 'varchar(128)',
			'is_title' => true,
			'in_list' => true,
			'default_in_list' => true,
			'filtertype' => 'normal',
			'filterdefault' => true,
			'label' => app::get('b2c')->_('副名称'),
			'comment' => app::get('b2c')->_('副名称'),
		),
		'shuaka_type' => array(
			'type' => 'varchar(128)',
			'required' => true,
			'is_title' => true,
			'in_list' => true,
			'default_in_list' => true,
			'label' => app::get('b2c')->_('刷卡方式'),
			'comment' => app::get('b2c')->_('刷卡方式'),
		),
		'feilv' => array(
			'type' => 'money',
			'in_list' => true,
			'default_in_list' => true,
			'label' => app::get('b2c')->_('刷卡费率'),
			'comment' => app::get('b2c')->_('刷卡费率'),
		),
		'fengding' => array(
			'type' => 'money',
			'in_list' => true,
			'default_in_list' => true,
			'label' => app::get('b2c')->_('刷卡封顶金额'),
			'comment' => app::get('b2c')->_('刷卡封顶金额'),
		),
		'merchant_code' => array(
			'type' => 'varchar(128)',
			'in_list' => true,
			'default_in_list' => true,
			'label' => app::get('b2c')->_('pos商户编号'),
			'comment' => app::get('b2c')->_('pos商户编号'),
		),
		'faren' => array(
			'type' => 'varchar(128)',
			'in_list' => true,
			'default_in_list' => true,
			'label' => app::get('b2c')->_('pos法人'),
			'comment' => app::get('b2c')->_('pos法人'),
		),
		'jiesuan_bank' => array(
			'type' => 'varchar(128)',
			'in_list' => true,
			'default_in_list' => true,
			'label' => app::get('b2c')->_('pos结算银行'),
			'comment' => app::get('b2c')->_('pos结算银行'),
		),
		'logon_name' => array(
			'type' => 'varchar(128)',
			'in_list' => true,
			'default_in_list' => true,
			'label' => app::get('b2c')->_('二维码登录账号'),
			'comment' => app::get('b2c')->_('二维码登录账号'),
		),
		'logon_password' => array(
			'type' => 'varchar(128)',
			'in_list' => true,
			'default_in_list' => true,
			'label' => app::get('b2c')->_('二维码登录密码'),
			'comment' => app::get('b2c')->_('二维码登录密码'),
		),
		'memo' => array(
			'type' => 'varchar(128)',
			'in_list' => true,
			'default_in_list' => true,
			'label' => app::get('b2c')->_('备注'),
			'comment' => app::get('b2c')->_('备注'),
		),
		'is_sub' => array (
			'type' => array (
				0 => app::get('b2c')->_('否'),
				1 => app::get('b2c')->_('是'),
			),
			'default' => '0',
			'required' => true,
			'width' => 75,
			'editable' => false,
			'filtertype' => 'yes',
			'filterdefault' => true,
			'in_list' => true,
			'default_in_list' => true,
			'label' => app::get('b2c')->_('是否子品牌'),
			'comment' => app::get('b2c')->_('是否子品牌'),
		),
		'is_enable' => array (
			'type' => array (
				0 => app::get('b2c')->_('否'),
				1 => app::get('b2c')->_('是'),
			),
			'default' => '1',
			'required' => true,
			'width' => 75,
			'editable' => false,
			'filtertype' => 'yes',
			'filterdefault' => true,
			'in_list' => true,
			'default_in_list' => true,
			'label' => app::get('b2c')->_('是否启用'),
			'comment' => app::get('b2c')->_('是否启用'),
		),
		'create_time' => array(
			'type' => 'time',
			'in_list' => true,
			'default_in_list' => true,
			'width' => '100',
			'order' => 17,
			'label' => app::get('b2c')->_('创建时间'),
			'comment' => app::get('b2c')->_('创建时间'),
		),
		'modified_time' => array(
			'type' => 'last_modify',
			'in_list' => true,
			'default_in_list' => true,
			'width' => '100',
			'order' => 18,
			'label' => app::get('b2c')->_('修改时间'),
			'comment' => app::get('b2c')->_('修改时间'),
		),
	),
	'index' => array (
		'ind_posbrand_id' => array (
			'columns' => array (
				0 => 'posbrand_id',
			),
		),
		'ind_shuaka_type' => array (
			'columns' => array (
				0 => 'shuaka_type',
			),
		),
		'ind_create_time' => array (
			'columns' => array (
				0 => 'create_time',
			),
		),
	),
	'engine' => 'innodb',
	'version' => '$Rev: 42376 $',
	'comment' => app::get('b2c')->_('pos机刷卡方式表'),
);
