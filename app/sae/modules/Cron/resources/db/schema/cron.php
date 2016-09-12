<?php
/**
 *
 * Schema definition for 'cron'
 *
 * Last update: 2016-06-24
 *
 */
$schemas = (!isset($schemas)) ? array() : $schemas;
$schemas['cron'] = array(
    'cron_id' => array(
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true,
    ),
    'name' => array(
        'type' => 'varchar(128)',
    ),
    'command' => array(
        'type' => 'text',
    ),
    'minute' => array(
        'type' => 'varchar(128)',
        'default' => -1,
    ),
    'hour' => array(
        'type' => 'varchar(128)',
        'default' => -1,
    ),
    'month_day' => array(
        'type' => 'varchar(64)',
        'default' => -1,
    ),
    'month' => array(
        'type' => 'varchar(24)',
        'default' => -1,
    ),
    'week_day' => array(
        'type' => 'varchar(14)',
        'default' => -1,
    ),
    'is_active' => array(
        'type' => 'boolean',
        'is_null' => false,
        'default' => 1,
    ),
    'priority' => array(
        'type' => 'tinyint(4)',
        'default' => 5,
    ),
    'standalone' => array(
        'type' => 'tinyint(1)',
        'default' => 0,
    ),
    'last_error' => array(
        'type' => 'longtext',
    ),
    'last_trigger' => array(
        'type' => 'datetime',
    ),
    'last_success' => array(
        'type' => 'datetime',
    ),
    'last_fail' => array(
        'type' => 'datetime',
    ),
    'created_at' => array(
        'type' => 'datetime',
    ),
    'updated_at' => array(
        'type' => 'datetime',
    ),
);