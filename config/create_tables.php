<?php
require __DIR__ . '/database.php';

// This script will run after building the docker devcontainer
$user = getenv('DB_USER');
$password = getenv('DB_PASSWORD');

$mysqli = mysqli_connect("db", $user, $password, "nutrio", 3306);

$users_query = "CREATE TABLE IF NOT EXISTS users (user_id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,user_name text NOT NULL,email text NOT NULL,password_hash text NOT NULL, profile_pic text)";
$user_info_query = "CREATE TABLE IF NOT EXISTS user_info (user_id INT NOT NULL,height FLOAT NOT NULL,weight FLOAT NOT NULL,age INT NOT NULL,sex ENUM('MALE', 'FEMALE'),activityLevel FLOAT NOT NULL,targetCAL FLOAT NOT NULL,targetPROTIEN FLOAT NOT NULL,targetCARBS FLOAT NOT NULL,targetFAT FLOAT NOT NULL,goal ENUM('CUT', 'BULK', 'MAINTAIN') NOT NULL,focus ENUM('PROTEIN', 'CARB', 'FAT') NOT NULL, diet ENUM('Gluten Free', 'Ketogenic', 'Vegetarian', 'Vegan', 'None'), PRIMARY KEY (user_id),CONSTRAINT fk_user_info_user_id FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE)";
$daily_intake_query = "CREATE TABLE IF NOT EXISTS daily_intake (user_id INT NOT NULL, meal_id INT NOT NULL PRIMARY KEY AUTO_INCREMENT, meal_name TEXT NOT NULL, date DATE NOT NULL,calories INT NOT NULL,protein INT NOT NULL,carbs INT NOT NULL,fat INT NOT NULL,CONSTRAINT fk_daily_intake_user_id FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE)";
$restrictions_query = "CREATE TABLE IF NOT EXISTS restrictions (restriction_id INT AUTO_INCREMENT NOT NULL, restriction_name VARCHAR(255) NOT NULL, UNIQUE(restriction_name), PRIMARY KEY (restriction_id));";
$user_restrictions_query = "CREATE TABLE IF NOT EXISTS user_restrictions (user_id INT NOT NULL, restriction_id INT NOT NULL, PRIMARY KEY (user_id, restriction_id), CONSTRAINT fk_user_restrictions_user_id FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE, CONSTRAINT fk_user_restrictions_restriction_id FOREIGN KEY (restriction_id) REFERENCES restrictions(restriction_id) ON DELETE CASCADE);";
$excersises_query = "CREATE TABLE exercises (excersise_id INT AUTO_INCREMENT PRIMARY KEY,name VARCHAR(255) NOT NULL,rep_count INT NOT NULL,set_count INT NOT NULL,calories_burned DECIMAL(5,2) NOT NULL,rest_time INT NOT NULL,exercise_type ENUM('aerobic', 'anaerobic') NOT NULL,body_part ENUM('arms', 'legs', 'shoulders', 'chest', 'core', 'back', 'cardio') NOT NULL);";

$excersises = [
    // name, rep_count, set_count, calories_burned, rest_time, exercise_type, body_part
    ['Push-ups', 12, 3, 50, 60, 'anaerobic', 'chest'],
    ['Pull-ups', 8, 3, 60, 90, 'anaerobic', 'back'],
    ['Squats', 15, 4, 100, 60, 'anaerobic', 'legs'],
    ['Bicep curls', 10, 3, 40, 60, 'anaerobic', 'arms'],
    ['Tricep dips', 10, 3, 40, 60, 'anaerobic', 'arms'],
    ['Shoulder press', 10, 3, 50, 60, 'anaerobic', 'shoulders'],
    ['Lunges', 12, 3, 80, 60, 'anaerobic', 'legs'],
    ['Plank', 1, 3, 20, 60, 'anaerobic', 'core'],
    ['Leg press', 12, 3, 80, 60, 'anaerobic', 'legs'],
    ['Bench press', 10, 3, 60, 90, 'anaerobic', 'chest'],
    ['Lat pulldown', 10, 3, 60, 60, 'anaerobic', 'back'],
    ['Deadlift', 8, 3, 100, 120, 'anaerobic', 'back'],
    ['Running', 30, 1, 300, 0, 'aerobic', 'cardio'],
    ['Cycling', 30, 1, 250, 0, 'aerobic', 'cardio'],
    ['Swimming', 30, 1, 300, 0, 'aerobic', 'cardio'],
    ['Jump rope', 10, 3, 100, 60, 'aerobic', 'cardio'],
    ['Boxing', 30, 1, 400, 0, 'aerobic', 'cardio'],
    ['Rowing', 30, 1, 300, 0, 'aerobic', 'cardio']
];

$excersise_values_query = "INSERT INTO exercises (name, rep_count, set_count, calories_burned, rest_time, exercise_type, body_part) VALUES ";
$excersise_values = [];

for ($i = 0; $i < count($excersises); $i++) {
    $excersise_values[] = "('" . implode("', '", array_map('addslashes', $excersises[$i])) . "')";
}

$excersise_values_query .= implode(", ", $excersise_values);

echo "\n$excersise_values_query\n";


$restriction_types = [
    'Lactose Intolerance',
    'Gluten Intolerance',
    'Vegetarian',
    'Vegan',
    'Kosher',
    'Dairy Free',
    'Peanut Allergy',
    'Fish/Shellfish Allergy',
    'Wheat Allergy'
];

$restriction_values = implode("'), ('", array_map('addslashes', $restriction_types));
$restriction_values_query = "INSERT INTO restrictions (restriction_name) VALUES ('$restriction_values')";
echo $restriction_values_query;


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

if (mysqli_query($mysqli, $restrictions_query)) {
    echo "Table restrictions created successfully\n";
} else {
    echo "Error creating table: " . mysqli_error($mysqli) . "\n";
}

if (mysqli_query($mysqli, $user_restrictions_query)) {
    echo "Table user_restrictions created successfully\n";
} else {
    echo "Error creating table: " . mysqli_error($mysqli) . "\n";
}

if (mysqli_query($mysqli, $restriction_values_query)) {
    echo "Restriction values inserted successfully\n";
} else {
    echo "Error inserting restriction values: " . mysqli_error($mysqli) . "\n";
}

if (mysqli_query($mysqli, $excersises_query)) {
    echo "Table exercises created successfully\n";
} else {
    echo "Error creating table: " . mysqli_error($mysqli) . "\n";
}

if (mysqli_query($mysqli, $excersise_values_query)) {
    echo "Excersises added successfully\n";
} else {
    echo "Error adding excersises: " . mysqli_error($mysqli) . "\n";
}


// Create test user Chad with id 69 if it doesn't exist
$hash = password_hash('password', PASSWORD_DEFAULT);
$chad_query = "INSERT INTO users (user_id, user_name, email, password_hash) SELECT 69, 'Chad', 'chad@email.com', '$hash' FROM dual WHERE NOT EXISTS (SELECT * FROM users WHERE user_id = 69);";
if (mysqli_query($mysqli, $chad_query)) {
    echo "Chad created successfully\n";
} else {
    echo "Error creating Chad: " . mysqli_error($mysqli) . "\n";
}

storeSurveyInformation("chad", 74, 180, "MALE", 25, 1.8, "BULK", "PROTEIN");

// Create test user cam with id 88 if it doesn't exist
$camhash = password_hash('camtest', PASSWORD_DEFAULT);
$cam_query = "INSERT INTO users (user_id, user_name, email, password_hash) SELECT 88, 'cam', 'cam@test.com', '$camhash' FROM dual WHERE NOT EXISTS (SELECT * FROM users WHERE user_id = 88);";
if (mysqli_query($mysqli, $cam_query)) {
    echo "cam created successfully\n";
} else {
    echo "Error creating cam: " . mysqli_error($mysqli) . "\n";
}

storeSurveyInformation("cam", 70, 167, "MALE", 21, 1.5, "CUT", "PROTEIN");

$date = date("Y-m-d");
trackCaloriesAndMacros("chad", "eggs", $date, 2000, 100, 200, 50);

createUser("timmy", "tim@email.com", "password");

createUser("sib", "sib@email.com", "password");
storeSurveyInformation("sib", 72, 155, "MALE", 31, 1.3, "MAINTAIN", "PROTEIN");
