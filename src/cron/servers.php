<?php

// 0 * * * * php cron/servers.php
//
// stores the amount of servers that pinged us in the last hour so it can be easily graphed

define('ROOT', '../');

require_once ROOT . 'config.php';
require_once ROOT . 'includes/database.php';
require_once ROOT . 'includes/func.php';

// iterate through all of the plugins
foreach (loadPlugins() as $plugin)
{
    // Calculate the closest hour
    $denom = 60 * 60; // 60 minutes * 60 seconds = 3600 seconds in an hour
    $baseEpoch = round(time() / $denom) * $denom;

    // we want the data for the last hour
    $minimum = strtotime('-1 hour', $baseEpoch);

    // load the players online in the last hour
    $servers = $plugin->countServersLastUpdatedAfter($minimum);

    // Insert it into the database
    $statement = $pdo->prepare('INSERT INTO ServerTimeline (Plugin, Servers, Epoch) VALUES (:Plugin, :Servers, :Epoch)');
    $statement->execute(array(
        ':Plugin' => $plugin->getID(),
        ':Servers' => $servers,
        ':Epoch' => $baseEpoch
    ));
}