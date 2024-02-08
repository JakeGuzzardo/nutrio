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
  $statement = $mysqli->prepare("SELECT restriction_id FROM restrictions WHERE restriction_name = ?");
  $statement->bind_param("s", $restriction_name);
  $statement->execute();
  $statement->store_result($result);
  $statement->close();
  #$result = mysqli_query($mysqli, "SELECT restriction_id FROM restrictions WHERE restriction_name = '$restriction_name';");
  $row = mysqli_fetch_row($result);
  return $row[0];
}

function reccomendExercise(string $user_name, int $num)
{
  //injection safe
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
  //injection safe
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
  $statement = $mysqli->prepare( "DELETE FROM daily_intake WHERE user_id = ? AND meal_id = ?");
  $statement->bind_param("ss", $user_id, $id);
  $result = $statement->execute();
  $statement->close();

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
  $preped = $mysqli->prepare("UPDATE daily_intake SET meal_name = ?, date = ?, calories = ?, protein = ?, carbs = ?, fat = ?
   WHERE user_id = ? AND meal_id = ?");

  $preped->bind_param("ssddddsd", $meal_name, $date, $calories, $protein, $carbs, $fat, $user_id, $mId);
  $result = $preped->execute();
  $preped->close();
  if ($result) {
    return true;
  } else {
    return false;
  }
}

function setProfilePic(string $user, string $pfp)
{
  #function not currently used
  $mysqli = getConnection();
  $statement = $mysqli->prepare("UPDATE users SET profile_pic = ? WHERE user_name = ?");
  $statement->bind_param("ss", $pfp, $user);
  $result = $statement->execute();
  $statement->close();

  #$result = mysqli_query($mysqli, "UPDATE users SET profile_pic='$pfp' WHERE user_name = '$user'");
}

function getProfilePic(string $user)
{
  $mysqli = getConnection();
  $statement = $mysqli->prepare("SELECT profile_pic FROM users WHERE user_name= ?");
  $statement->bind_param("s", $user);
  $statement->execute();
  $statement->bind_result($profilePic);

  if($statement->fetch()){
    #$row = mysqli_fetch_row($result);
    if ($row == NULL || $row[0] == "") {
      return '/public/uploads/no-pfp.png';
    }
    return substr($row[0], 4); # substr to remove /web

  } else {
    return '/public/uploads/no-pfp.png';
  }
  $statement->close();

  #$result = mysqli_query($mysqli, "SELECT profile_pic FROM users WHERE user_name='$user'");
  
}


/**
 * Returns the name of a restriction given its ID.
 * @param int $restriction_id The ID of the restriction.
 * @return string The name of the restriction.
 */
function getRestrictionName(int $restriction_id)
{
  $mysqli = getConnection();
  $statement = $mysqli->prepare("SELECT restriction_name FROM restrictions WHERE restriction_id = ?");
  
  $statement->bind_param("i", $restriction_id);
  $statement->execute();
  $statement->bind_result($restriction);
  
  if($statement->fetch()){
    $statement->close();
    return $restriction;
  }
  $statement->close();
  return null;
  // $result = mysqli_query($mysqli, "SELECT restriction_name FROM restrictions WHERE restriction_id = '$restriction_id';");
  // $row = mysqli_fetch_row($result);
  // return $row[0];
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
  $mysqli = getConnection();

  // Prepare the SELECT statement
  $selectStmt = $mysqli->prepare("SELECT restriction_id FROM restrictions WHERE restriction_name = ?");
  $insertStmt = $mysqli->prepare("INSERT IGNORE INTO user_restrictions (user_id, restriction_id) VALUES (?, ?)");

  foreach ($restrictions as $restriction) {
      $selectStmt->bind_param("s", $restriction);

      if (!$selectStmt->execute()) {
        die("Execute failed: " . $selectStmt->error);
    }
      $selectStmt->store_result();
      $selectStmt->bind_result($restrictionID);

      if ($selectStmt->fetch()) {
          $insertStmt->bind_param("ii", $user_id, $restrictionID);

          if(!$insertStmt->execute()) {
            die("Insertion failed: " . $insertStmt->error);
          }
      }
      $selectStmt->free_result();
  }

  $selectStmt->close();
  $insertStmt->close();
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

  $selectStmt = $mysqli->prepare("SELECT restriction_id FROM restrictions WHERE restriction_name = ? ");
  $deleteStmt = $mysqli->prepare("DELETE FROM user_restrictions WHERE user_id = ? AND restriction_id = ? ");


  foreach ($restrictions as $restriction) {
    $selectStmt->bind_param("s", $restriction);
    if(!$selectStmt->execute()){
      die("Select restricions failed: " . $selectStmt->error);
    }

    $selectStmt->store_result();
    $selectStmt->bind_result($restrictionID);

    if($selectStmt->fetch()){
      $deleteStmt->bind_param("ii", $user_id, $restrictionID);

      if(!$deleteStmt->execute()){
        die("Deletion failed: " . $insertStmt->error);
      }
    }
    $selectStmt->free_result();
  }

  $selectStmt->close();
  $deleteStmt->close();
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
  $selectStmt = $mysqli->prepare("SELECT r.restriction_name FROM restrictions r JOIN user_restrictions ur ON r.restriction_id = ur.restriction_id WHERE ur.user_id = ? " );
  $selectStmt->bind_param("i", $user_id);
  $selectStmt->execute();
  $selectStmt->bind_result($restriction);
  $restrictions = array();

  while($selectStmt->fetch()){
    array_push($restrictions, $restriction);
  }
  $selectStmt->close();
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
  $selectStmt = $mysqli->prepare("SELECT user_id FROM users WHERE user_name= ? ");
  $selectStmt->bind_param("s", $user_name);
  $selectStmt->execute();
  $selectStmt->bind_result($userId);

  if($selectStmt->fetch()){
    $selectStmt->close();
    return $userId;
  }
  $selectStmt->close();
  return NULL;
}

/**
 * Checks if a user has taken the survey.
 * @param string $user_name The username of the user.
 * @return bool True if the user has taken the survey, false otherwise.
 */
function checkInitalLogin(string $user_name)
{
  //injection safe
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
 * @param float $calories The number of calories in the meal.
 * @param float $protein The number of grams of protein in the meal.
 * @param float $carbs The number of grams of carbs in the meal.
 * @param float $fat The number of grams of fat in the meal.
 * @return bool True if the meal was added successfully, false otherwise.
 */
function trackCaloriesAndMacros(string $user_name, string $meal_name, string $date, float $calories, float $protein, float $carbs, float $fat)
{
  $user_id = getIDFromUsername($user_name);
  $mysqli = getConnection();

  $insertStmt = $mysqli->prepare("INSERT INTO daily_intake (user_id, meal_name, date, calories, protein, carbs, fat) VALUES ( ? , ? , ? , ? , ? , ? , ? )");
  $insertStmt->bind_param("issdddd", $user_id, $meal_name, $date, $calories, $protein, $carbs, $fat);
  $result = $insertStmt->execute();
  $insertStmt->close();

  if($result){
    return true;
  }
  return false;
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
    $todaysCarbs += $macro['carbs']; #2
    $todaysCals += $macro['calories']; #0
    $todaysProtien += $macro['protein']; #1
    $todaysFat += $macro['fat']; #3
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
  //injection safe
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
  //injection safe
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

  $selectStmt = $mysqli->prepare("SELECT calories, protein, carbs, fat, meal_name FROM daily_intake WHERE user_id= ?  AND date= ? ");
  $selectStmt->bind_param("is", $user_id, $date);
  $selectStmt->execute();

  $result = $selectStmt->get_result();
  $rows = array();
  while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
  }
  $selectStmt->close();
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
  // injection safe
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
  $params = [];
  $types = '';

  if ($height != NULL) {
    $query .= "height= ?, ";
    $params[] = &$height;
    $types .= 'i';
  }
  if ($weight != NULL) {
    $query .= "weight= ?, ";
    $params[] = &$weight;
    $types .= 'i';
  }
  if ($sex != NULL) {
    $query .= "sex= ?, ";
    $params[] = &$sex;
    $types .= 's';
  }
  if ($age != NULL) {
    $query .= "age= ?, ";
    $params[] = &$age;
    $types .= 'i';
  }
  if ($activityLvl != NULL) {
    $query .= "activityLevel= ?, ";
    $params[] = &$activityLvl;
    $types .= 'd';
  }
  if ($goal != NULL) {
    $query .= "goal= ?, ";
    $params[] = &$goal;
    $types .= 's';
  }
  if ($focus != NULL) {
    $query .= "focus= ?, ";
    $params[] = &$focus;
    $types .= 's';
  }
  if ($diet != NULL) {
    $query .= "diet= ?, ";
    $params[] = &$diet;
    $types .= 's';
  }

  


  $query = substr($query, 0, -2);
  $query .= " WHERE user_id= ?";

  $params[] = &$user_id;
  $types .= 'i';

  $stmt = $mysqli->prepare($query);
  $stmt->bind_param($types, ...$params);
  $stmt->execute();
  $stmt->close();

  //$result = mysqli_query($mysqli, $query);

  //not injectable
  $user_info = getUserInfo($user_name);

  $height = $user_info[1];
  $weight = $user_info[2];
  $age = $user_info[3];
  $sex = $user_info[4];
  $activityLevel = $user_info[5];
  $goal = $user_info[10];
  $focus = $user_info[11];

  $newGoals = calcualteGoals($height, $weight, $age, $sex, $activityLevel, $goal, $focus);

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

  $stmt = $mysqli->prepare("INSERT INTO user_info (user_id, height, weight, age, sex, activityLevel, targetCAL, targetPROTIEN, targetCARBS, targetFAT, goal, focus) 
  VALUES ( ? , ? , ? , ? , ? , ? , ? , ? , ? , ? , ? , ? )");

  $stmt->bind_param("iiiisdddddss", $userID, $height, $weight, $age, $sex, $activityLvl, $targetCAL, $targetPROTIEN, $targetCARBS, $targetFAT, $goal, $focus);
  if(!$stmt->execute()){
    die("Execution failed: " . $stmt->error);
  }
  $stmt->close();
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
  $selectStmt = $mysqli->prepare("SELECT * FROM users WHERE email= ? ");
  $selectStmt->bind_param("s", $email);
  $selectStmt->execute();
  $result = $selectStmt->get_result();

  if($result->fetch_assoc()){
    $selectStmt->close();
    return true;
  }
  $selectStmt->close();
  return false;
}


/**
 * Checks if a username is already in use.
 * @param string $user_name The username to check.
 * @return bool True if the username is already in use, false otherwise.
 */
function checkIfUserNameUsed($user_name)
{
  $mysqli = getConnection();
  $selectStmt = $mysqli->prepare("SELECT * FROM users WHERE user_name= ? ");
  $selectStmt->bind_param("s", $user_name);
  $selectStmt->execute();
  $result = $selectStmt->get_result();

  if($result->fetch_assoc()){
    $selectStmt->close();
    return true;
  }
  $selectStmt->close();
  return false;
}

/**
 * Returns a users email.
 * @param string $user_name The username to linked to the email.
 * @return string The string email of the input user.
 */
function getEmail($user_name)
{
  $mysqli = getConnection();
  $selectStmt = $mysqli->prepare("SELECT email FROM users WHERE user_name= ? ");
  $selectStmt->bind_param("s", $user_name);
  $selectStmt->bind_result($result);
  $selectStmt->execute();

  if($selectStmt->fetch()){
    return $result;
  }
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
  //injection safe
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
  $stmt = $mysqli->prepare("SELECT password_hash FROM users WHERE user_name= ? ");
  $stmt->bind_param("s", $user_name);
  $stmt->bind_result($result);
  $stmt->execute();

  if ($stmt->fetch()) {
    $hashed = $result;
    if (password_verify($password, $hashed)) {
      return true;
    } 
  }  
return false;  
}
