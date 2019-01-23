<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

$db['poscard'] = array (
	'columns' => array (
		'card_id' => array (
			'type' => 'number',
			'required' => true,
			'pkey' => true,
			'extra' => 'auto_increment',
			'width' => 110,
			'editable' => false,
			'in_list' => true,
			'default_in_list' => true,
			'label' => app::get('b2c')->_('信用卡id'),
			'comment' => app::get('b2c')->_('信用卡id'),
		),
		'name' => array(
			'type' => 'varchar(128)',
			'required' => true,
			'is_title' => true,
			'in_list' => true,
			'default_in_list' => true,
			'filtertype' => 'normal',
			'filterdefault' => true,
			'label' => app::get('b2c')->_('信用卡所属银行'),
			'comment' => app::get('b2c')->_('信用卡所属银行'),
		),
		'belong_to' => array(
			'type' => 'varchar(128)',
			'required' => true,
			'in_list' => true,
			'default_in_list' => true,
			'filtertype' => 'normal',
			'filterdefault' => true,
			'label' => app::get('b2c')->_('信用卡所属人'),
			'comment' => app::get('b2c')->_('信用卡所属人'),
		),
		'card_no' => array(
			'type' => 'varchar(128)',
			'in_list' => true,
			'default_in_list' => true,
			'label' => app::get('b2c')->_('信用卡卡号'),
			'comment' => app::get('b2c')->_('信用卡卡号'),
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
		'ind_belong_to' => array (
			'columns' => array (
				0 => 'belong_to',
			),
		),
	),
	'engine' => 'innodb',
	'version' => '$Rev: 42376 $',
	'comment' => app::get('b2c')->_('信用卡列表'),
);
