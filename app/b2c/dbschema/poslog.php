<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

$db['poslog'] = array (
	'columns' => array (
		'id' =>	array (
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
		'card_id' => array(
			'type' => 'number',
			'required' => true,
			'in_list' => true,
			'default_in_list' => true,
			'filtertype' => 'normal',
			'label' => app::get('b2c')->_('信用卡'),
			'comment' => app::get('b2c')->_('信用卡'),
		),
		'postype_id' => array(
			'type' => 'number',
			'default' => 0,
			'in_list' => true,
			'default_in_list' => true,
			'filtertype' => 'normal',
			'label' => app::get('b2c')->_('刷卡类型'),
			'comment' => app::get('b2c')->_('刷卡类型'),
		),
		'mcc' => array(
			'type' => 'varchar(10)',
			'in_list' => true,
			'default_in_list' => true,
			'label' => app::get('b2c')->_('mcc'),
			'comment' => app::get('b2c')->_('mcc'),
		),
		'money' => array(
			'type' => 'money',
			'required' => true,
			'in_list' => true,
			'default_in_list' => true,
			'label' => app::get('b2c')->_('刷卡金额'),
			'comment' => app::get('b2c')->_('刷卡金额'),
		),
		'feilv' => array(
			'type' => 'money',
			'in_list' => true,
			'default_in_list' => true,
			'label' => app::get('b2c')->_('费率'),
			'comment' => app::get('b2c')->_('费率'),
		),
		'jiesuan_money' => array(
			'type' => 'money',
			'in_list' => true,
			'default_in_list' => true,
			'label' => app::get('b2c')->_('结算金额'),
			'comment' => app::get('b2c')->_('结算金额'),
		),
		'memo' => array(
			'type' => 'varchar(20)',
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
			'label' => app::get('b2c')->_('修改时间'),
			'comment' => app::get('b2c')->_('修改时间'),
		),
		'type' => array (
			'type' => array (
				'pos' => app::get('b2c')->_('pos刷卡'),
				'xiaofei' => app::get('b2c')->_('消费'),
				'huankuan' => app::get('b2c')->_('还款'),
				'nianfei' => app::get('b2c')->_('年费'),
				'change' => app::get('b2c')->_('手动调整'),
			),
			'default' => 'pos',
			'required' => true,
			'width' => 75,
			'editable' => false,
			'filtertype' => 'yes',
			'filterdefault' => true,
			'in_list' => true,
			'default_in_list' => true,
			'label' => app::get('b2c')->_('账单类型'),
			'comment' => app::get('b2c')->_('账单类型'),
		),
	),
	'index' => array (
		'ind_card_id' => array (
			'columns' => array (
				0 => 'card_id',
			),
		),
		'ind_postype_id' => array (
			'columns' => array (
				0 => 'postype_id',
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
	'comment' => app::get('b2c')->_('信用卡账单记录表'),
);
