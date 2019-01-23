<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

$db['posbrand'] = array (
	'columns' => array (
		'posbrand_id' => array (
			'type' => 'number',
			'required' => true,
			'pkey' => true,
			'extra' => 'auto_increment',
			'width' => 110,
			'editable' => false,
			'in_list' => true,
			'default_in_list' => true,
			'label' => app::get('b2c')->_('pos品牌id'),
			'comment' => app::get('b2c')->_('pos品牌id'),
		),
		'name' => array(
			'type' => 'varchar(128)',
			'required' => true,
			'is_title' => true,
			'in_list' => true,
			'default_in_list' => true,
			'filtertype' => 'normal',
			'filterdefault' => true,
			'label' => app::get('b2c')->_('pos名称'),
			'comment' => app::get('b2c')->_('pos名称'),
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
		'is_havesub' => array (
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
			'label' => app::get('b2c')->_('是否含有子品牌'),
			'comment' => app::get('b2c')->_('是否含有子品牌'),
		),
		'memo' => array(
			'type' => 'varchar(128)',
			'in_list' => true,
			'default_in_list' => true,
			'label' => app::get('b2c')->_('备注'),
			'comment' => app::get('b2c')->_('备注'),
		),
		'create_time' => array(
			'type' => 'time',
			'in_list' => true,
			'default_in_list' => true,
			'width' => '100',
			'order' => 17,
			'filtertype' => 'time',
			'filterdefault' => true,
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
		'ind_name' => array (
			'columns' => array (
				0 => 'name',
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
	'comment' => app::get('b2c')->_('pos机品牌'),
);
