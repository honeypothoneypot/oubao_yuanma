<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

$db['postype']=array (
	'columns' =>
	array (
		'pos_type' => array(
            'type' => 'varchar(128)',
            'required' => true,
            'in_list' => true,
            'default_in_list' => true,
            'label' => app::get('b2c')->_('刷卡机代号'),
            'comment' => app::get('b2c')->_('刷卡机代号'),
        ),
		'name' => array(
			'type' => 'varchar(128)',
            'required' => true,
			'is_title' => true,
			'in_list' => true,
			'default_in_list' => true,
			'label' => app::get('b2c')->_('刷卡机名称'),
			'comment' => app::get('b2c')->_('刷卡机名称'),
		),
		'feilv' => array(
            'type' => 'money',
            'required' => true,
            'in_list' => true,
            'default_in_list' => true,
            'label' => app::get('b2c')->_('刷卡费率'),
            'comment' => app::get('b2c')->_('刷卡费率'),
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
            'label' => app::get('b2c')->_('创建时间'),
            'comment' => app::get('b2c')->_('创建时间'),
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
			'prefix' => 'UNIQUE',
		),
	),
	'engine' => 'innodb',
	'version' => '$Rev: 42376 $',
	'comment' => app::get('b2c')->_('pos机类型表'),
);
