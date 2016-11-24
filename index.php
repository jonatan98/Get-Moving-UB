<!DOCTYPE html>
<html>
<head>
    <title>Get Moving UB</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- My styles -->
    <link rel="stylesheet" href="styles/main.css" />
    <link rel="stylesheet" href="styles/phone.css" media="only screen and (max-width: 800px)">
    
    <!-- favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="imgs/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" href="imgs/favicon/favicon-32x32.png" sizes="32x32">
    <link rel="icon" type="image/png" href="imgs/favicon/favicon-16x16.png" sizes="16x16">
    <link rel="manifest" href="imgs/favicon/manifest.json">
    <link rel="mask-icon" href="imgs/favicon/safari-pinned-tab.svg" color="#5bbad5">
    <link rel="shortcut icon" href="imgs/favicon/favicon.ico">
    <meta name="msapplication-config" content="imgs/favicon/browserconfig.xml">
    <meta name="theme-color" content="#ffffff">
    
    <!-- leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.0.1/dist/leaflet.css" />
	<script src="https://unpkg.com/leaflet@1.0.1/dist/leaflet.js"></script>
    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://norbye.com/addons/font-awesome-4.5.0/css/font-awesome.min.css">
</head>
<body>

<div id="wrap">
    <div id="map"></div><div id="sidebar">
        <img src="imgs/Get Moving FB profilbilde - cut 1.png">
        <div id="phone_menu"></div>
        <ul>
            <li><input type="text" id="search" placeholder="Søk..."></li>
            <li><select id="activity">
                <option value="0">Alle aktiviteter</option>
                <option value="1">Basketball</option>
                <option value="2">Ishockey</option>
                <option value="3">Volleyball</option>
                <option value="4">Fotball</option>
            </select></li>
            <li><select id="area">
                <option value="0">Alle områder</option>
                <option value="1">Nordberg</option>
                <option value="2">Grünerløkka</option>
                <option value="3">Voldsløkka</option>
            </select></li>
            <li><a href="#">Min profil</a></li>
            <li class="bottom"><a href="#">Om Oss</a></li></li>
        </ul>
    </div>
</div>
<div id="toggle_menu" data-active="0"><div class="line"></div><div class="line"></div><div class="line"></div></div>

<script src="scripts/menu.js"></script>
<script src="scripts/map.js"></script>
<script src="scripts/filter.js"></script>
</body>