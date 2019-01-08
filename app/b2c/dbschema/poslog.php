<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

$db['poslog']=array (
	'columns' =>
	array (
		'id' =>
		array (
			'type' => 'number',
			'required' => true,
			'pkey' => true,
			'extra' => 'auto_increment',
			'label' => app::get('b2c')->_('记录id'),
			'width' => 110,
			'editable' => false,
			'in_list' => true,
			'default_in_list' => true,
		),
		'name' => array(
			'type' => 'varchar(128)',
			'required' => true,
			'is_title' => true,
			'in_list' => true,
			'default_in_list' => true,
			'filtertype' => 'normal',
			'filterdefault' => true,
			'label' => app::get('b2c')->_('姓名'),
			'comment' => app::get('b2c')->_('姓名'),
		),
		'pos_type' => array(
			'type' => 'varchar(128)',
			'required' => true,
			'in_list' => true,
			'default_in_list' => true,
			'filtertype' => 'normal',
			'label' => app::get('b2c')->_('刷卡机类型'),
			'comment' => app::get('b2c')->_('刷卡机类型'),
		),
		'mcc' => array(
			'type' => 'varchar(128)',
			'required' => true,
			'in_list' => true,
			'default_in_list' => true,
			'label' => app::get('b2c')->_('mcc'),
			'comment' => app::get('b2c')->_('mcc'),
		),
		'bank' => array(
			'type' => 'varchar(128)',
			'required' => true,
			'in_list' => true,
			'default_in_list' => true,
			'filtertype' => 'normal',
			'filterdefault' => true,
			'label' => app::get('b2c')->_('所属银行'),
			'comment' => app::get('b2c')->_('所属银行'),
		),
		'money' => array(
			'type' => 'money',
			'required' => true,
			'in_list' => true,
			'default_in_list' => true,
			'label' => app::get('b2c')->_('刷卡金额'),
			'comment' => app::get('b2c')->_('刷卡金额'),
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
	'index' =>
	array (
		'ind_name' =>
		array (
			'columns' =>
			array (
				0 => 'name',
			),
		),
		'ind_pos_type' =>
		array (
			'columns' =>
			array (
				0 => 'pos_type',
			),
		),
		'ind_bank' =>
		array (
			'columns' =>
			array (
				0 => 'bank',
			),
		),
		'ind_create_time' =>
		array (
			'columns' =>
			array (
				0 => 'create_time',
			),
		),
	),
	'engine' => 'innodb',
	'version' => '$Rev: 42376 $',
	'comment' => app::get('b2c')->_('pos刷卡记录表'),
);
