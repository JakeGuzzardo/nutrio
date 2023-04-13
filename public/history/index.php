<?php
session_start();

// Redirect to login page if user is not logged in
if (!isset($_SESSION['user_name'])) {
    header('Location: /CSE442-542/2023-Spring/cse-442g/project_s23-the-ai-violators/public/login/');
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
  <link rel="stylesheet" href="/CSE442-542/2023-Spring/cse-442g/project_s23-the-ai-violators/public/history/history.css">

  <title>history</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@200;400;500;700;800&display=swap" rel="stylesheet">
</head>

<body>
  
  <div class="navbar">
    <a id="NUTRIO" href="/CSE442-542/2023-Spring/cse-442g/project_s23-the-ai-violators/public/content">
      <div>
        <img id="carrot" src="/CSE442-542/2023-Spring/cse-442g/project_s23-the-ai-violators/public/image/carrot.png" alt="">
        <p id="logoName">nutr.io</p>
      </div>
    </a>
    <a href="/CSE442-542/2023-Spring/cse-442g/project_s23-the-ai-violators/public/profile/">Profile Page</a>
    <a href="/CSE442-542/2023-Spring/cse-442g/project_s23-the-ai-violators/public/track/">Track Page</a>
    <a href="/CSE442-542/2023-Spring/cse-442g/project_s23-the-ai-violators/public/login/">Logout</a>
    <div>
      <div id="username">%Cusername%</div>
    </div>
  </div>

  <h1>Lets see how you did last week...</h1>

  <div id="box">
    <div class="historyContainer">
      <form id = "form">
          <table id = "myTable" class="GeneratedTable">
            <thead>
                <tr>
                    <th></th>
                    <th>Meal</th>
                    <th>Calories</th>
                    <th>Protein</th>
                    <th>Carbs</th>
                    <th>Fats</th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>


                <?php
                $row = array();
                $rowId = array();
                $meals = getHistory($_SESSION['user_name']);
                for ($i = 0; $i < count($meals); $i++) {
                    $output = "";
                    $meal = $meals[$i];
                    $output .= "<tr><td>$meal[3]</td>";
                    $output .= "<td class = 'tableMeal'>$meal[2]</td>";
                    $output .= "<td class = 'tableCalories'>$meal[4]</td>";
                    $output .= "<td class = 'tableProtein'>$meal[5]</td>";
                    $output .= "<td class = 'tableCarbs'>$meal[6]</td>";
                    $output .= "<td class = 'tableFats'>$meal[7]</td>";
                    $output .= "<td class = 'tableId'>$meal[1]</td>";
                    $row[] = $output;
                    $rowId[] = $meal[1];
                }

                for ($i = 0; $i < count($row); $i++) {
                  echo $row[$i];
                  ?>
                  <td class = "edit"><button onclick="findRow(<?php echo $rowId[$i];?>)">Edit</td>
                  <td class = 'delete'><button type ="button" onclick="del(<?php echo $rowId[$i];?>)">Delete</td></tr>
                  <?php
                }
                ?>

            </tbody>

        </div>
        </table>
      </form>
    </div>
  </div>

  <script src="/CSE442-542/2023-Spring/cse-442g/project_s23-the-ai-violators/public/history/history.js"></script>

</body>
</html>
