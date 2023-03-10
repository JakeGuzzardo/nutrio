<?php

// This script will run after building the docker devcontainer

$db_hostname = getenv('IN_DOCKER');

if ($db_hostname == 'yes') {
    $db_hostname = 'db';
} else {
    $db_hostname = 'oceanus.cse.buffalo.edu';
}

$mysqli = mysqli_connect($db_hostname, "sjrichel", "50338787", "cse442_2023_spring_team_g_db", 3306);

$users_query = "CREATE TABLE IF NOT EXISTS users (user_id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,user_name text NOT NULL,email text NOT NULL,password_hash text NOT NULL)";
$user_info_query = "CREATE TABLE IF NOT EXISTS user_info (user_id INT NOT NULL,height FLOAT NOT NULL,weight FLOAT NOT NULL,age INT NOT NULL,sex ENUM('MALE', 'FEMALE'),activityLevel INT NOT NULL,targetCAL FLOAT NOT NULL,targetPROTIEN FLOAT NOT NULL,targetCARBS FLOAT NOT NULL,targetFAT FLOAT NOT NULL,goal ENUM('CUT', 'BULK', 'MAINTAIN') NOT NULL,focus ENUM('PROTIEN', 'CARB', 'FAT') NOT NULL,PRIMARY KEY (user_id),CONSTRAINT fk_user_info_user_id FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE)";
$daily_intake_query = "CREATE TABLE IF NOT EXISTS daily_intake (user_id INT NOT NULL,date DATE NOT NULL,calories INT NOT NULL,protein INT NOT NULL,carbs INT NOT NULL,fat INT NOT NULL,PRIMARY KEY (user_id, date),CONSTRAINT fk_daily_intake_user_id FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE)";

if (mysqli_query($mysqli, $users_query)) {
    echo "Table users created successfully\n";
} else {
    echo "Error creating table: " . mysqli_error($mysqli) . "\n";
}

if (mysqli_query($mysqli, $user_info_query)) {
    echo "Table user_info created successfully\n";
} else {
    echo "Error creating table: " . mysqli_error($mysqli) . "\n";
}

if (mysqli_query($mysqli, $daily_intake_query)) {
    echo "Table daily_intake created successfully\n";
} else {
    echo "Error creating table: " . mysqli_error($mysqli) . "\n";
}
