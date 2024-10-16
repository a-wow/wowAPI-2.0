<?php
header("Content-Type: application/json; charset=UTF-8");

$host = 'host';
$db = 'characters';
$user = 'user';
$pass = 'password';

$mysqli = new mysqli($host, $user, $pass, $db);

if ($mysqli->connect_error) {
    die(json_encode(array("error" => "Connection error: " . $mysqli->connect_error)));
}

$action = isset($_GET['action']) ? $_GET['action'] : null;

switch ($action) {
    case 'searchCharacter':
        searchCharacter($mysqli);
        break;
    case 'getGuild':
        getGuild($mysqli);
        break;
    default:
        echo json_encode(array("error" => "Invalid request"));
}

function searchCharacter($mysqli) {
    $name = $mysqli->real_escape_string(isset($_GET['name']) ? $_GET['name'] : '');

    $class_image = array(
        1 => '/api/images/classes/1.png',
        2 => '/api/images/classes/2.png',
        3 => '/api/images/classes/3.png',
        4 => '/api/images/classes/4.png',
        5 => '/api/images/classes/5.png',
        6 => '/api/images/classes/6.png',
        7 => '/api/images/classes/7.png',
        8 => '/api/images/classes/8.png',
        9 => '/api/images/classes/9.png',
        11 => '/api/images/classes/11.png'
    );

    $race_image = array(
        '1' => array('0' => '/api/images/race/1-0.png', '1' => '/api/images/race/1-1.png'),
        '2' => array('0' => '/api/images/race/2-0.png', '1' => '/api/images/race/2-1.png'),
        '3' => array('0' => '/api/images/race/3-0.png', '1' => '/api/images/race/3-1.png'),
        '4' => array('0' => '/api/images/race/4-0.png', '1' => '/api/images/race/4-1.png'),
        '5' => array('0' => '/api/images/race/5-0.png', '1' => '/api/images/race/5-1.png'),
        '6' => array('0' => '/api/images/race/6-0.png', '1' => '/api/images/race/6-1.png'),
        '7' => array('0' => '/api/images/race/7-0.png', '1' => '/api/images/race/7-1.png'),
        '8' => array('0' => '/api/images/race/8-0.png', '1' => '/api/images/race/8-1.png'),
        '10' => array('0' => '/api/images/race/10-0.png', '1' => '/api/images/race/10-1.png'),
        '11' => array('0' => '/api/images/race/11-0.png', '1' => '/api/images/race/11-1.png')
    );

    $class_names = [
        1 => 'Warrior',
        2 => 'Priest',
        3 => 'Hunter',
        4 => 'Rogue',
        5 => 'Prist',
        6 => 'Death Knight',
        7 => 'Shaman',
        8 => 'Mage',
        9 => 'Warlock',
        11 => 'Druid',
    ];

    $race_names = [
        1 => 'Human',
        2 => 'Orc',
        3 => 'Dwarf',
        4 => 'Night ELf',
        5 => 'Undead',
        6 => 'Tauren',
        7 => 'Gnome',
        8 => 'Troll',
        10 => 'Blood Elf',
        11 => 'Draenei',
    ];

    if ($name) {
        $sql = "SELECT c.*, 
                    (SELECT COUNT(*) FROM character_achievement WHERE guid = c.guid) AS achievement_count 
                FROM characters c 
                WHERE c.name LIKE '%$name%'";
        $result = $mysqli->query($sql);

        $characters = array();
        while ($row = $result->fetch_assoc()) {
            $classId = $row['class'];
            $raceId = $row['race'];
            $gender = $row['gender'];

            $row['class_image'] = isset($class_image[$classId]) ? $class_image[$classId] : null;
            $row['race_image'] = isset($race_image[$raceId][$gender]) ? $race_image[$raceId][$gender] : null;
            $row['class_name'] = isset($class_names[$classId]) ? $class_names[$classId] : 'Unknown class';
            $row['race_name'] = isset($race_names[$raceId]) ? $race_names[$raceId] : 'Unknown race';

            $money = $row['money'];
            $gold = floor($money / 10000);
            $silver = floor(($money % 10000) / 100);
            $copper = $money % 100;

            $row['formatted_money'] = sprintf("%d gold, %d silver, %d copper", $gold, $silver, $copper);

            $row['totalKills'] = $row['totalKills'];
            $row['arenaPoints'] = $row['arenaPoints'];
            $row['totalHonorPoints'] = $row['totalHonorPoints'];
            $row['online'] = $row['online'] ? 'Online' : 'Offline';
            $row['achievement_count'] = $row['achievement_count'];

            $characters[] = $row;
        }

        echo json_encode($characters);
    } else {
        echo json_encode(array("error" => "Character name not specified"));
    }
}

function getGuild($mysqli) {
    $guildName = $mysqli->real_escape_string(isset($_GET['name']) ? $_GET['name'] : '');

    if ($guildName) {
        $sql = "SELECT g.*, 
                    p.name AS leader_name,
                    p.class AS leader_class,
                    p.race AS leader_race,
                    p.gender AS leader_gender
                FROM guild g
                LEFT JOIN characters p ON g.leaderguid = p.guid
                WHERE g.name LIKE '%$guildName%'";
        $result = $mysqli->query($sql);

        $guilds = array();
        while ($row = $result->fetch_assoc()) {
            $classId = $row['leader_class'];
            $raceId = $row['leader_race'];
            $gender = $row['leader_gender'];

            $row['leader_class_image'] = isset($class_image[$classId]) ? $class_image[$classId] : null;
            $row['leader_race_image'] = isset($race_image[$raceId][$gender]) ? $race_image[$raceId][$gender] : null;

            $row['createdate'] = date('Y-m-d H:i:s', $row['createdate']);
            
            $money = $row['BankMoney'];
            $gold = floor($money / 10000);
            $silver = floor(($money % 10000) / 100);
            $copper = $money % 100;

            $row['formatted_bank_money'] = sprintf("%d gold, %d silver, %d copper", $gold, $silver, $copper);

            $guilds[] = $row;
        }

        echo json_encode($guilds);
    } else {
        echo json_encode(array("error" => "Guild name not specified"));
    }
}

$mysqli->close();
?>
