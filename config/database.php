<?php

/**
 * Returns a connection to the database.
 * @return mysqli A connection to the database.
 */
function getConnection()
{
  $user = getenv('DB_USER');
  $password = getenv('DB_PASSWORD');
  return mysqli_connect("db", $user, $password, "nutrio", 3306);
}

/**
 * Returns the ID of a recipe given its name.
 * @param string $recipe_name The name of the recipe.
 * @return int The ID of the recipe.
 */
function getRestrictionId(string $restriction_name)
{
  $mysqli = getConnection();
  $result = mysqli_query($mysqli, "SELECT restriction_id FROM restrictions WHERE restriction_name = '$restriction_name';");
  $row = mysqli_fetch_row($result);
  return $row[0];
}

function reccomendExercise(string $user_name, int $num)
{
  $mysqli = getConnection();
  $user_id = getIDFromUsername($user_name);
  $user_info = getUserInfo($user_name);
  $goal = $user_info[10];
  $query = "";
  if ($goal == "CUT") {
    $query = "SELECT * FROM exercises WHERE exercise_type = 'aerobic' ORDER BY RAND() LIMIT $num";
  } else if ($goal == "BULK") {
    $query = "SELECT * FROM exercises WHERE exercise_type = 'anaerobic' ORDER BY RAND() LIMIT $num";
  } else {
    $query = "SELECT * FROM exercises ORDER BY RAND() LIMIT $num";
  }

  $excersises = array();

  $result = mysqli_query($mysqli, $query);
  while ($row = mysqli_fetch_row($result)) {
    $excersises[] = $row;
  }

  return $excersises;
}


/**
 *  Retrieves all meal input history for a given user.
 *  @param string $user_name The username of the user whose meal input history should be retrieved.
 *  @return array A 2D Matrix with rows in the form [userID, mealID, meal_name, date, calories, carbs, protein, fats]. Each row is a seperate meal.
 */
function getHistory(string $user_name)
{
  $user_id = getIDFromUsername($user_name);
  $mysqli = getConnection();
  $result = mysqli_query($mysqli, "SELECT * FROM daily_intake WHERE user_id='$user_id'");
  $rows = array();
  while ($row = mysqli_fetch_row($result)) {
    $rows[] = $row;
  }
  return $rows;
}

/**
 * Deltes a users meal input.
 * @param string $user_name The username of the user.
 * @param string $id The id of the meal
 * @return bool True if the meal was deleted successfully, false otherwise.
 */
function del(string $user_name, string $id)
{
  $user_id = getIDFromUsername($user_name);
  $mysqli = getConnection();
  $result = mysqli_query($mysqli, "DELETE FROM daily_intake WHERE user_id='$user_id' AND meal_id='$id'");

  if ($result) {
    return true;
  } else {
    return false;
  }
}

/**
 * Edits a users meal input.
 * @param string $user_name The username of the user.
 * @param string $meal_name New name of the meal input.
 * @param string $date New date of the meal input.
 * @param float $calories New calories of the meal input.
 * @param float $protein New protein of the meal input.
 * @param float $carbs New carbs of the meal input.
 * @param float $fat New fat of the meal input.
 * @param float $mId Meal id number of the meal input.
 * @return bool True if the meal was edited successfully, false otherwise.
 */
function edit(string $user_name, string $meal_name, string $date, float $calories, float $protein, float $carbs, float $fat, float $mId)
{
  $user_id = getIDFromUsername($user_name);
  $mysqli = getConnection();
  $result = mysqli_query($mysqli, "UPDATE daily_intake SET meal_name = '$meal_name', date = '$date', calories = '$calories', protein ='$protein', carbs = '$carbs', fat = '$fat' WHERE user_id='$user_id' AND meal_id='$mId'");
  // echo $result;
  if ($result) {
    return true;
  } else {
    return false;
  }
}

function setProfilePic(string $user, string $pfp)
{
  $mysqli = getConnection();
  $result = mysqli_query($mysqli, "UPDATE users SET profile_pic='$pfp' WHERE user_name = '$user'");
}

function getProfilePic(string $user)
{
  $mysqli = getConnection();
  $result = mysqli_query($mysqli, "SELECT profile_pic FROM users WHERE user_name='$user'");
  $row = mysqli_fetch_row($result);

  if ($row == NULL || $row[0] == "") {
    return '/public/uploads/no-pfp.png';
  }
  return substr($row[0], 4); # substr to remove /web
}


/**
 * Returns the name of a restriction given its ID.
 * @param int $restriction_id The ID of the restriction.
 * @return string The name of the restriction.
 */
function getRestrictionName(int $restriction_id)
{
  $mysqli = getConnection();
  $result = mysqli_query($mysqli, "SELECT restriction_name FROM restrictions WHERE restriction_id = '$restriction_id';");
  $row = mysqli_fetch_row($result);
  return $row[0];
}

/**
 * Adds restrictions for a given user. No change if the user already has the restriction.
 * @param string $user_name The username of the user.
 * @param array $restrictions An array of strings representing the user's restrictions.
 * @return bool True if the restrictions were set successfully, false otherwise.
 */
function addRestrictions(string $user_name, array $restrictions)
{
  $user_id = getIDFromUsername($user_name);
  // print_r($user_id);
  $mysqli = getConnection();
  for ($i = 0; $i < count($restrictions); $i++) {
    $restriction = $restrictions[$i];
    $result = mysqli_query($mysqli, "SELECT restriction_id FROM restrictions WHERE restriction_name = '$restriction';");
    $row = mysqli_fetch_row($result);
    $restriction_id = $row[0];
    mysqli_query($mysqli, "INSERT IGNORE INTO user_restrictions (user_id, restriction_id) VALUES ('$user_id', '$restriction_id');");
  }
  return true;
}

/**
 * Removes restrictions for a given user. No change if the user does not have the restriction.
 * @param string $user_name The username of the user.
 * @param array $restrictions An array of strings representing the user's restrictions.
 * @return bool True if the restrictions were removed successfully, false otherwise.
 */
function removeRestriction(string $user_name, array $restrictions)
{
  $user_id = getIDFromUsername($user_name);
  $mysqli = getConnection();
  for ($i = 0; $i < count($restrictions); $i++) {
    $restriction = $restrictions[$i];
    $result = mysqli_query($mysqli, "SELECT restriction_id FROM restrictions WHERE restriction_name = '$restriction';");
    $row = mysqli_fetch_row($result);
    $restriction_id = $row[0];
    mysqli_query($mysqli, "DELETE FROM user_restrictions WHERE user_id = '$user_id' AND restriction_id = '$restriction_id';");
  }
  return true;
}

/**
 * Returns the restrictions of a user.
 * @param string $user_name The username of the user.
 * @return array An array of the user's restrictions.
 */
function getRestrictions(string $user_name)
{
  $user_id = getIDFromUsername($user_name);
  $mysqli = getConnection();
  $result = mysqli_query($mysqli, "SELECT r.restriction_name FROM restrictions r JOIN user_restrictions ur ON r.restriction_id = ur.restriction_id WHERE ur.user_id = '$user_id';");
  $restrictions = array();
  while ($row = mysqli_fetch_row($result)) {
    array_push($restrictions, $row[0]);
  }
  return $restrictions;
}


/**
 * Retruns the user ID of a user from their username.
 * @param string $user_name The username of the user.
 * @return int The user ID of the user.
 */
function getIDFromUsername(string $user_name): int
{
  $mysqli = getConnection();
  $result = mysqli_query($mysqli, "SELECT user_id FROM users WHERE user_name='$user_name'");
  $row = mysqli_fetch_row($result);
  return $row[0];
}

/**
 * Checks if a user has taken the survey.
 * @param string $user_name The username of the user.
 * @return bool True if the user has taken the survey, false otherwise.
 */
function checkInitalLogin(string $user_name)
{
  $mysqli = getConnection();
  $userID = getIDFromUsername($user_name);
  $result = mysqli_query($mysqli, "SELECT * FROM user_info WHERE user_id='$userID'");
  $row = mysqli_fetch_row($result);
  if ($row) {
    return true;
  } else {
    return false;
  }
}


/**
 * Add new meal entry into the database.
 * @param string $user_name The username of the user.
 * @param string $meal_name The name of the meal.
 * @param string $date In the format YYYY-MM-DD.
 * @param float $calroies The number of calories in the meal.
 * @param float $protein The number of grams of protein in the meal.
 * @param float $carbs The number of grams of carbs in the meal.
 * @param float $fat The number of grams of fat in the meal.
 * @return bool True if the meal was added successfully, false otherwise.
 */
function trackCaloriesAndMacros(string $user_name, string $meal_name, string $date, float $calroies, float $protein, float $carbs, float $fat)
{
  // echo "HELLO! This probaly worked, but useres arent set up yet, so expect error";
  $user_id = getIDFromUsername($user_name);
  $mysqli = getConnection();
  $result = mysqli_query($mysqli, "INSERT INTO daily_intake (user_id, meal_name, date, calories, protein, carbs, fat) VALUES ('$user_id', '$meal_name', '$date', '$calroies', '$protein', '$carbs', '$fat')");
  // echo $result;
  if ($result) {
    return true;
  } else {
    return false;
  }
}

/**
 * Returns the daily calories and macros for a user.
 * @param string $user_name The username of the user.
 * @return array An array of the user's daily calories and macros in the form [cals, carbs, protien, fat].
 */

function getRemainingMacros(string $user_name)
{
  $macros = getMacroGoals($user_name);
  $cals = getCalorieGoals($user_name);

  $dailyMacros = getDailyCalories($user_name, date("y-m-d"));

  $todaysCarbs = 0;
  $todaysCals = 0;
  $todaysProtien = 0;
  $todaysFat = 0;

  foreach ($dailyMacros as $macro) {
    $todaysCarbs += $macro[2];
    $todaysCals += $macro[0];
    $todaysProtien += $macro[1];
    $todaysFat += $macro[3];
  }

  $carbsLeft = $macros[1] - $todaysCarbs;
  $calsLeft = $cals - $todaysCals;
  $protienLeft = $macros[0] - $todaysProtien;
  $fatLeft = $macros[2] - $todaysFat;

  return [$calsLeft, $carbsLeft, $protienLeft, $fatLeft];
}


/**
 *  Retrieves the daily calorie goal for the given user.
 *  @param string $user_name The username of the user whose calorie goal should be retrieved.
 *  @return float The user's daily calorie goal.
 */
function getCalorieGoals(string $user_name)
{
  $user_id = getIDFromUsername($user_name);
  $mysqli = getConnection();
  $result = mysqli_query($mysqli, "SELECT targetCAL FROM user_info WHERE user_id='$user_id'");
  $row = mysqli_fetch_row($result);
  return $row[0];
}


/**
 *  Retrieves the daily macro nutrient goals for the given user.
 *  @param string $user_name The username of the user whose macro nutrient goals should be retrieved.
 *  @return array An array representing the user's daily macro nutrient goals, including targetPROTIEN, targetCARBS, and targetFAT.
 */
function getMacroGoals(string $user_name)
{
  $user_id = getIDFromUsername($user_name);
  $mysqli = getConnection();
  $result = mysqli_query($mysqli, "SELECT targetPROTIEN, targetCARBS, targetFAT FROM user_info WHERE user_id='$user_id'");
  $row = mysqli_fetch_row($result);
  return $row;
}

/**
 *  Retrieves the daily calorie intake for the given user on the given date.
 *  @param string $user_name The username of the user whose calorie intake should be retrieved.
 *  @param string $date The date for which the calorie intake should be retrieved.
 *  @return array A 2D Matrix with rows in the form [calories, protien, carbs, fat, meal_name]. Each row is a seperate meal.
 */
function getDailyCalories(string $user_name, string $date)
{
  $user_id = getIDFromUsername($user_name);
  $mysqli = getConnection();
  $result = mysqli_query($mysqli, "SELECT calories, protein, carbs, fat, meal_name FROM daily_intake WHERE user_id='$user_id' AND date='$date'");
  $rows = array();
  while ($row = mysqli_fetch_row($result)) {
    $rows[] = $row;
  }
  return $rows;
}


/**
 *  Retrieves user information from the database based on the given username.
 *  @param string $user_name The username of the user whose information should be retrieved.
 *  @return array An array representing the user's information, including user_id, height, weight, age, sex,
 *                activityLevel, targetCAL, targetPROTIEN, targetCARBS, targetFAT, goal, and focus in that order.
 */
function getUserInfo(string $user_name)
{
  $user_id = getIDFromUsername($user_name);
  $mysqli = getConnection();
  $result = mysqli_query($mysqli, "SELECT * FROM user_info WHERE user_id='$user_id'");
  $row = mysqli_fetch_row($result);

  return $row;
}



/**
 * Updates the user's information in the database. Only the parameters you pass in will be updated ---  EXAMPLE: updateUserInfo("chad", age: 20, weight: 180) - This will only update the user's age and weight.
 * @param string $user_name The username of the user whose information should be updated.
 * @param int $height The user's height in inches.
 * @param int $weight The user's weight in pounds.
 * @param string $sex "MALE" or "FEMALE", The user's biological sex.
 * @param int $age The user's age.
 * @param float $activityLvl Range from 1.2 to 1.9, The user's activity level.
 * @param string $goal "BULK" OR "CUT" OR "MAINTAIN", The user's primary fitness goal.
 * @param string $focus "PROTIEN" OR "CARB" OR "FAT", The user's primary area of focus.
 * @return void
 */
function updateUserInfo(string $user_name, int $height = NULL, int $weight = NULL, string $sex = NULL, int $age = NULL, float $activityLvl = NULL, string $goal = NULL, string $focus = NULL, string $diet = NULL)
{
  $mysqli = getConnection();
  $user_id = getIDFromUsername($user_name);
  $query = "UPDATE user_info SET ";
  if ($height != NULL) {
    $query .= "height='$height', ";
  }
  if ($weight != NULL) {
    $query .= "weight='$weight', ";
  }
  if ($sex != NULL) {
    $query .= "sex='$sex', ";
  }
  if ($age != NULL) {
    $query .= "age='$age', ";
  }
  if ($activityLvl != NULL) {
    $query .= "activityLevel='$activityLvl', ";
  }
  if ($goal != NULL) {
    $query .= "goal='$goal', ";
  }
  if ($focus != NULL) {
    $query .= "focus='$focus', ";
  }
  if ($diet != NULL) {
    $query .= "diet='$diet', ";
  }


  $query = substr($query, 0, -2);
  $query .= " WHERE user_id='$user_id'";

  $result = mysqli_query($mysqli, $query);




  $user_info = getUserInfo($user_name);

  $newGoals = calcualteGoals($user_info[1], $user_info[2], $user_info[3], $user_info[4], $user_info[5], $user_info[8], $user_info[9]);

  $query = "UPDATE user_info SET ";
  $query .= "targetCAL='$newGoals[0]', ";
  $query .= "targetPROTIEN='$newGoals[1]', ";
  $query .= "targetCARBS='$newGoals[2]', ";
  $query .= "targetFAT='$newGoals[3]'";
  $query .= " WHERE user_id='$user_id'";

  // echo $query;

  $result = mysqli_query($mysqli, $query);
}



/**
 *  Stores the survey information for the given user in the database.
 *  @param string $user_name The username of the user whose survey information should be stored.
 *  @param int $height The user's height in inches.
 *  @param int $weight The user's weight in pounds.
 *  @param string $sex "MALE" or "FEMALE", The user's biological sex.
 *  @param int $age The user's age.
 *  @param float $activityLvl Range from 1.2 to 1.9, The user's activity level.
 *  @param string $goal "BULK" OR "CUT" OR "MAINT", The user's primary fitness goal.
 *  @param string $focus "PROTIEN" OR "CARBS" OR "FATS", The user's primary area of focus.
 *  @return void
 */
function storeSurveyInformation(string $user_name, int $height, int $weight, string $sex, int $age, float $activityLvl, string $goal, string $focus)
{
  $mysqli = getConnection();
  $userID = getIDFromUsername($user_name);
  $goals = calcualteGoals($height, $weight, $age, $sex, $activityLvl, $goal, $focus);

  $targetCAL = $goals[0];
  $targetPROTIEN = $goals[1];
  $targetCARBS = $goals[2];
  $targetFAT = $goals[3];

  // SQL INJECTION?
  $result = mysqli_query($mysqli, "INSERT INTO user_info (user_id, height, weight, age, sex, activityLevel, targetCAL, targetPROTIEN, targetCARBS, targetFAT, goal, focus) VALUES ('$userID', '$height', '$weight', '$age', '$sex', '$activityLvl', '$targetCAL', '$targetPROTIEN', '$targetCARBS', '$targetFAT', '$goal', '$focus')");
}

function calcualteGoals($height, $weight, $age, $sex, $activityLvl, $goal, $focus)
{
  if ($sex == "MALE") {
    $bmr = 66.47 + (6.24 * $weight) + (12.7 * $height) - (6.75 * $age);
  } else {
    $bmr = 65.51 + (4.3 * $weight) + (4.7 * $height) - (4.68 * $age);
  }

  $targetCAL = $bmr * $activityLvl;


  if ($goal == "CUT") {
    $targetCAL = $targetCAL - 500;
  } else if ($goal == "BULK") {
    $targetCAL = $targetCAL + 200;
  }

  $targetPROTIEN = $weight;
  $targetFAT = $weight * 0.4;
  $targetCARBS = ($targetCAL - ($targetPROTIEN * 4.) - ($targetFAT * 9.)) / 4.;

  if ($focus == "PROTIEN") {
    $targetPROTIEN = $weight * 1.2;
  } else if ($focus == "CARB") {
    $targetCARBS *= 1.2;
  } else if ($focus == "FAT") {
    $targetFAT *= 1.1;
  }

  return [$targetCAL, $targetPROTIEN, $targetCARBS, $targetFAT];
}

/**
 *  Checks if an email is already in use.
 *  @param string $email The email to check.
 *  @return bool True if the email is already in use, false otherwise.
 */
function checkIfEmailUsed($email)
{
  $mysqli = getConnection();
  $result = mysqli_query($mysqli, "SELECT * FROM users WHERE email='$email'");
  $row = mysqli_fetch_row($result);
  if ($row) {
    return true;
  } else {
    return false;
  }
}


/**
 * Checks if a username is already in use.
 * @param string $user_name The username to check.
 * @return bool True if the username is already in use, false otherwise.
 */
function checkIfUserNameUsed($user_name)
{
  $mysqli = getConnection();
  $result = mysqli_query($mysqli, "SELECT * FROM users WHERE user_name='$user_name'");
  $row = mysqli_fetch_row($result);
  if ($row) {
    return true;
  } else {
    return false;
  }
}

/**
 * Returns a users email.
 * @param string $user_name The username to linked to the email.
 * @return string The string email of the input user.
 */
function getEmail($user_name)
{
  $mysqli = getConnection();
  $result = mysqli_query($mysqli, "SELECT email FROM users WHERE user_name='$user_name'");
  $row = mysqli_fetch_row($result);
  return $row[0];
}

/**
 * Creates a new user in the database.
 * @param string $user_name The username of the new user.
 * @param string $email The email of the new user.
 * @param string $password The password of the new user.
 * @return bool True if the user was successfully created, false if user or email already in use.
 */
function createUser($user_name, $email, $password)
{
  $mysqli = getConnection();

  if (checkIfEmailUsed($email) || checkIfUserNameUsed($user_name)) {
    return false;
  }
  $hashed = password_hash($password, PASSWORD_DEFAULT);
  mysqli_query($mysqli, "INSERT INTO users (user_name, email, password_hash) VALUES ('$user_name', '$email', '$hashed')");
  return true;
}


/**
 * Checks if a user's login information is correct.
 * @param string $user_name The username of the user.
 * @param string $password The password of the user.
 * @return bool True if the login information is correct, false otherwise.
 */
function checkLogin($user_name, $password)
{
  $mysqli = getConnection();
  $result = mysqli_query($mysqli, "SELECT password_hash FROM users WHERE user_name='$user_name'");
  $row = mysqli_fetch_row($result);
  if ($row) {
    $hashed = $row[0];
    if (password_verify($password, $hashed)) {
      // echo "Login successful!<br>";
      return true;
    } else {
      // echo "Login failed! Password doesn't match!<br>";
      return false;
    }
  } else {
    // echo "Login failed! No user found with that email!<br>";
    return false;
  }
}
