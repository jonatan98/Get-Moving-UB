<?php

/*
 * Table structure of the database, including special attributes
 */

$this->tables['table1'] = array(
    'client_specific' => false,
    'name' => 'Table',
    'tags' => array(),
    'cols' => array()
);

/*
 * Links to ensure that the right field is used as the foreign key when 
 * tables are connected
 */

$this->links['table1-table2'] = 'foreign_key';

$this->pages['tablename'] = array(
    'name' => 'Table #1', //Name of the page in the sidemenu
    'icon' => '', //Fort-awesome icon. Leave empty if none
    'list' => array(
        'table1' => array(
            'col1',
            'col2'
        )
    ),
    'info' => array(
        'table1' => array(
            'col1',
            'col2',
            'col3'
        ),
        'table2' => array(
            'col1',
            'col2'
        )
    )
);
