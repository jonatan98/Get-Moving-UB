<?php
require '_init.php';

/*
 * Get all locations from database and output them as JSON
 */

$res = array();

$stmt = $db->prepare("SELECT * FROM `".$tbl['getmoving_location']."`");
$stmt->execute();
$locations = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach($locations as $l){
    $res['locations'][] = array(
        'coordinates' => array(
            'lat' => $l['lat'],
            'lng' => $l['lng']
        ),
        'name' => $l['name'],
        'description' => $l['description'],
        'icon_type' => $l['icon_type']
    );
}

die(
    json_encode(
        $res
    )
);