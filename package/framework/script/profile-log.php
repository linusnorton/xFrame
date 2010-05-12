<?php
/* 
 * This is based on George Schlossnagle's log analysis script.
 *
 * It relies on the apache log format
 *
 * LogFormat "%h %l %u \"%r\" %>s %b \"%{Referer}i\" \"%{User-Agent}i\" %D" combinedplus
 *
 * Usage: php5 profile-log.php log_path
 *
 */

$input = $_SERVER["argv"][1];
$fp = fopen($input, "r");

while (($line = fgets($fp)) !== false) {
    //echo $line;

    $start = (strpos($line,"GET ") !== false) ? strpos($line,"GET ") + 4 : strpos($line,"POST ") + 5;
    $end = strpos($line, "HTTP/1.1");
    $uri = substr($line, $start, $end - $start);
    $time = substr($line, strrpos($line, " "));
    
    list($file, $parmas) = explode('?', $uri, 2);

    $requests[$file][] = $time;
    $requests[$file]['count']++;
    $requests[$file]['avg'] = ($requests[$file]['avg'] * ($requests[$file]['count'] -1) + $time) / $requests[$file]['count'];
}

$my_sort = create_function('$a, $b', '
    if ($a["avg"] == $b["avg"]) {
        return 0;
    }
    else {
        return ($a["avg"] > $b["avg"]) ? 1 : -1;
    }
');

uasort($requests, $my_sort);
reset($requests);

foreach ($requests as $uri => $times) {
    printf("%s\t%d\t%d\n", $uri, $times["count"], ($times["avg"] / 1000)) ;
}
