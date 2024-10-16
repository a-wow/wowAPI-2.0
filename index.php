<?php
if (isset($_GET['search'])) {
    $search = urlencode($_GET['search']);
    $type = isset($_GET['type']) ? $_GET['type'] : 'character';

    if ($type === 'character') {
        $url = "http://URL/api/api.php?action=searchCharacter&name={$search}";
    } elseif ($type === 'guild') {
        $url = "http://URL/api/api.php?action=getGuild&name={$search}";
    } else {
        die('Invalid type in search');
    }

    $response = file_get_contents($url);
    $data = json_decode($response, true);
    
    if (isset($data['error'])) {
        echo "Error: " . $data['error'];
    } else {
        echo "<h2>Search results:</h2>";
        echo "<div class='results'>";

        if ($type === 'character') {
            foreach ($data as $character) {
                echo "<div class='character'>";
                echo "<h4>Character: " . htmlspecialchars($character['name']) . "</h4>";
                echo "<p><strong>Class:</strong> " . htmlspecialchars($character['class_name']) . "</p>";
                echo "<p><strong>Race:</strong> " . htmlspecialchars($character['race_name']) . "</p>";
                echo "<p><strong>Level:</strong> " . htmlspecialchars($character['level']) . "</p>";
                echo "<p><strong>Money:</strong> " . htmlspecialchars($character['formatted_money']) . "</p>";
                echo "<p><strong>Total Kills:</strong> " . htmlspecialchars($character['totalKills']) . "</p>";
                echo "<p><strong>Arena Points:</strong> " . htmlspecialchars($character['arenaPoints']) . "</p>";
                echo "<p><strong>Honor Points:</strong> " . htmlspecialchars($character['totalHonorPoints']) . "</p>";
                echo "<p><strong>InGame:</strong> " . htmlspecialchars($character['online']) . "</p>";
                echo "<p><strong>Achievements:</strong> " . htmlspecialchars($character['achievement_count']) . "</p>";
                echo "<img src='" . htmlspecialchars($character['class_image']) . "' alt='Class' class='class-image'>";
                echo "<img src='" . htmlspecialchars($character['race_image']) . "' alt='Race' class='race-image'>";
                echo "</div>";
            }
        } elseif ($type === 'guild') {
            foreach ($data as $guild) {
                echo "<div class='guild'>";
                echo "<h4>Guild: " . htmlspecialchars($guild['name']) . "</h4>";
                echo "<p><strong>Guild leader:</strong> " . htmlspecialchars($guild['leader_name']) . " ";
                echo "<p><strong>Information:</strong> " . htmlspecialchars($guild['info']) . "</p>";
                echo "<p><strong>Date of creation:</strong> " . htmlspecialchars($guild['createdate']) . "</p>";
                echo "<p><strong>Money in the bank:</strong> " . htmlspecialchars($guild['formatted_bank_money']) . "</p>";
                echo "</div>";
            }
        }

        echo "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search for characters and guilds</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        h1, h2 {
            text-align: center;
        }

        form {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        input[type="text"] {
            padding: 10px;
            width: 300px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-right: 10px;
        }

        select {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-right: 10px;
        }

        input[type="submit"] {
            padding: 10px 15px;
            background-color: #5cb85c;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #4cae4c;
        }

        .results {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .character, .guild {
            background-color: white;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 15px;
            margin: 10px 0;
            width: 400px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .class-image, .race-image {
            width: 25px;
            height: auto;
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <h1>Search for characters and guilds</h1>
    <form method="get">
        <input type="text" name="search" placeholder="Enter character or guild name" required>
        <select name="type">
            <option value="character">Character</option>
            <option value="guild">Guild</option>
        </select>
        <input type="submit" value="Search">
    </form>
</body>
</html>
