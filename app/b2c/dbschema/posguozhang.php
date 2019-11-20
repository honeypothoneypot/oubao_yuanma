<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

$db['posguozhang'] = array (
	'columns' => array (
		'id' =>	array (
			'type' => 'number',
			'required' => true,
			'pkey' => true,
			'extra' => 'auto_increment',
			'label' => app::get('b2c')->_('过账记录id'),
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
		'belong_to' => array(
			'type' => 'varchar(10)',
			'required' => true,
			'in_list' => true,
			'default_in_list' => true,
			'filtertype' => 'normal',
			'filterdefault' => true,
			'label' => app::get('b2c')->_('信用卡所属人'),
			'comment' => app::get('b2c')->_('信用卡所属人'),
		),
		'money' => array(
			'type' => 'money',
			'required' => true,
			'in_list' => true,
			'default_in_list' => true,
			'label' => app::get('b2c')->_('过账金额'),
			'comment' => app::get('b2c')->_('过账金额'),
		),
		'guozhang_type' => array (
			'type' => 'varchar(10)',
			'label' => app::get('b2c')->_('类型：还款、其他、消费等'),
			'width' => 180,
			'is_title' => true,
			'required' => true,
			'comment' => app::get('b2c')->_('类型：还款、其他、消费等'),
			'editable' => true,
			'searchtype' => 'has',
			'in_list' => true,
			'default_in_list' => true,
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
	),
	'index' => array (
		'ind_card_id' => array (
			'columns' => array (
				0 => 'card_id',
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
	'comment' => app::get('b2c')->_('额度过账记录'),
);
