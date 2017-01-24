<?php

/*
 * Table structure of the database, including special attributes
 */

$this->tables['getmoving_location'] = array(
    'client_specific' => false,
    'name' => 'Steder',
    'tags' => array(
        'create_permission' => 0, 'delete_permission' => 0,
        'order' => array('col' => 'name', 'dir' => 'asc')
    ),
    'cols' => array(
        'locationID' => array(
            'name' => 'Sted ID',
            'type' => 'permanent'
        ),
        'name' => array(
            'name' => 'Navn',
            'type' => 'text',
            'min_permval' => 0
        ),
        'active' => array(
            'name' => 'Aktiv',
            'type' => 'number',
            'min_permval' => 0
        )
    )
);

$this->tables['getmoving_location_area'] = array(
    'client_specific' => false,
    'name' => 'Steder',
    'tags' => array(
        'create_permission' => 0, 'delete_permission' => 0,
        'order' => array('col' => 'name', 'dir' => 'asc')
    ),
    'cols' => array(
        'location_areaID' => array(
            'name' => 'Sted-Område ID',
            'type' => 'permanent'
        ),
        'locationID' => array(
            'name' => 'Sted',
            'type' => 'foreign_key',
            'foreign_displayvalue' => 'name',
            'foreign_table' => 'getmoving_location'
        ),
        'areaID' => array(
            'name' => 'Område',
            'type' => 'foreign_key',
            'foreign_displayvalue' => 'name',
            'foreign_table' => 'getmoving_area'
        )
    )
);

$this->tables['getmoving_location_activity'] = array(
    'client_specific' => false,
    'name' => 'Steder',
    'tags' => array(
        'create_permission' => 0, 'delete_permission' => 0,
        'order' => array('col' => 'name', 'dir' => 'asc')
    ),
    'cols' => array(
        'location_activityID' => array(
            'name' => 'Sted-Aktivitet ID',
            'type' => 'permanent'
        ),
        'locationID' => array(
            'name' => 'Sted',
            'type' => 'foreign_key',
            'foreign_displayvalue' => 'name',
            'foreign_table' => 'getmoving_location'
        ),
        'activityID' => array(
            'name' => 'Aktivitet',
            'type' => 'foreign_key',
            'foreign_displayvalue' => 'name',
            'foreign_table' => 'getmoving_activity'
        )
    )
);

$this->tables['getmoving_area'] = array(
    'client_specific' => false,
    'name' => 'Områder',
    'tags' => array(
        'create_permission' => 0, 'delete_permission' => 0,
        'order' => array('col' => 'name', 'dir' => 'asc')
    ),
    'cols' => array(
        'areaID' => array(
            'name' => 'Område ID',
            'type' => 'permanent'
        ),
        'name' => array(
            'name' => 'Navn',
            'type' => 'text',
            'min_permval' => 0
        )
    )
);

$this->tables['getmoving_activity'] = array(
    'client_specific' => false,
    'name' => 'Aktiviteter',
    'tags' => array(
        'create_permission' => 0, 'delete_permission' => 0,
        'order' => array('col' => 'name', 'dir' => 'asc')
    ),
    'cols' => array(
        'activityID' => array(
            'name' => 'Aktivitet ID',
            'type' => 'permanent'
        ),
        'name' => array(
            'name' => 'Navn',
            'type' => 'text',
            'min_permval' => 0
        )
    )
);

/*
 * Links to ensure that the right field is used as the foreign key when 
 * tables are connected
 */

$this->links['getmoving_location-getmoving_location_area'] = 'locationID';
$this->links['getmoving_area-getmoving_location_area'] = 'areaID';
$this->links['getmoving_location-getmoving_location_activity'] = 'locationID';
$this->links['getmoving_activity-getmoving_location_activity'] = 'activityID';

$this->pages['location'] = array(
    'name' => 'Steder', //Name of the page in the sidemenu
    'icon' => 'fa-pin', //Fort-awesome icon. Leave empty if none
    'list' => array(
        'getmoving_location' => array(
            'name',
            'active'
        )
    ),
    'info' => array(
        'getmoving_location' => array(
            'name',
            'active'
        )
    )
);

$this->pages['area'] = array(
    'name' => 'Områder', //Name of the page in the sidemenu
    'icon' => 'fa-pin', //Fort-awesome icon. Leave empty if none
    'list' => array(
        'getmoving_area' => array(
            'name'
        )
    ),
    'info' => array(
        'getmoving_area' => array(
            'areaID',
            'name'
        ),
        'getmoving_location_area' => array(
            'locationID'
        )
    )
);

$this->pages['activity'] = array(
    'name' => 'Aktiviteter', //Name of the page in the sidemenu
    'icon' => 'fa-pin', //Fort-awesome icon. Leave empty if none
    'list' => array(
        'getmoving_activity' => array(
            'name'
        )
    ),
    'info' => array(
        'getmoving_activity' => array(
            'activityID',
            'name'
        ),
        'getmoving_location_activity' => array(
            'locationID'
        )
    )
);