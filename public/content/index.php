<?php
session_start();

// Redirect to login page if user is not logged in
if (!isset($_SESSION['user_name'])) {
    header('Location: /public/login/');
    exit();
}

require __DIR__ . "../../../config/database.php";

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="/public/content/mainpage.css">
    <link rel="stylesheet" href="mainpage.css">
    <title>content</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Michroma&family=Montserrat:wght@200;400;500;700;800&display=swap" rel="stylesheet">
</head>

<body>

   <?php include "../../templates/navbar.php" ?>
    

    <div class="mainContent">

        <div class="stats">
            <div class="protienProgress">
                <div class="inner carbsProgress">
                    <div class="inner fatsProgress">
                        <div class="inner inner-2">
                            <div>
                                Calories: <br />
                                <span id="cals">
                                    <span id="curCals">
                                        %cals%
                                    </span>/
                                    <span id="calGoal">
                                        <?php echo getCalorieGoals($_SESSION['user_name']) ?>
                                    </span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="macros">
                <ul>
                    <li class="macroListItem">
                        <div class="macroHolder">
                            <span class="macsName">Protein:</span> <span class="macs" id="protien"><span id="curPro">%curPro%</span> / <span id="totalPro"><?php echo getMacroGoals($_SESSION['user_name'])[0] ?></span> </span>
                        </div>
                    </li>
                    <li class="macroListItem">
                        <div class="macroHolder">
                            <span class="macsName">Carbs:</span> <span class="macs" id="carbs"><span id="curCarb">%curCar%</span> / <span id="totalCar"><?php echo getMacroGoals($_SESSION['user_name'])[1] ?></span></span>
                        </div>
                    </li>
                    <li class="macroListItem">
                        <div class="macroHolder">
                            <span class="macsName">Fats:</span> <span class="macs" id="fats"><span id="curFat">%curFat%</span> / <span id="totalFat"><?php echo getMacroGoals($_SESSION['user_name'])[2] ?></span></span>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        <div class="historyContainer">
            <div class=history">
                Meal History
            </div>

            <div>

                <table class="GeneratedTable">
                    <thead>
                        <tr>
                            <th>Food</th>
                            <th>Calories</th>
                            <th>Protein</th>
                            <th>Carbs</th>
                            <th>Fats</th>

                        </tr>
                    </thead>
                    <tbody>


                        <?php
                        date_default_timezone_set('America/New_York');
                        $output = "";
                        $meals = getDailyCalories($_SESSION['user_name'], date("y-m-d"));
                        for ($i = 0; $i < count($meals); $i++) {
                            $meal = $meals[$i];
                            $output .= "<tr><td>" . $meal['meal_name'] . "</td>";
                            $output .= "<td class = 'tableCalories'>" . $meal['calories'] .  "</td>";
                            $output .= "<td class = 'tableProtein'>" . $meal['protein'] .  "</td>";
                            $output .= "<td class = 'tableCarbs'>" . $meal['carbs'] .  "</td>";
                            $output .= "<td class = 'tableFats'>" . $meal['fat'] .  "</td></tr>";
                        }

                        echo $output;
                        ?>

                    </tbody>

            </div>
            </table>


        </div>
    </div>
    </div>



    <script src="/public/content/mainpage.js"></script>
    <script src="mainpage.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
   
</body>


</html>