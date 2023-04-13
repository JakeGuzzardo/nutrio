<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="/CSE442-542/2023-Spring/cse-442g/project_s23-the-ai-violators/public/recomendation/meals.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Michroma&family=Montserrat:wght@200;400;500;700;800&display=swap" rel="stylesheet">

</head>

<body>
    <?php include "../../templates/navbar.php" ?>

    </div>

    <div id="mainShit">

        <div class="mealHolder">
            <div id="header">
                <h1>Meal Recomendations</h1>
                <div>380 callories left: protien: 30g carbs:20g fats: 20g</div>
            </div>

            <div id="content">
                <div class="mealRecs">
                    <h2>Salmon and Broccoli - 410 calories</h2>
                    <p>1 filet Salmon macros:
                        protein: 33g carbs: 0 fats: 18g
                    </p>
                    <p>
                        1 cup Broccoli Macros:
                        protein:1g carbs: 12g fats: 0g
                    </p>
                    <button class="add">Add</button>
                </div>
                <div class="mealRecs">
                    <h2>Chicken thigh and rice - 390 calories</h2>
                    <p>1 Chicken thigh:
                        protein: 20g carbs: 0 fats: 9g</p>
                    <p>peppers:
                        protein: 4g carbs:2g fats: 6g</p>
                    <p>Brown rice:
                        protein: 4g carbs: 20g fats: 0g
                    </p>

                    <button class="add">Add</button>
                </div>
                <div class="mealRecs">
                    <h2>peanut butter bite and protein shake - 375 calories</h2>
                    <p> peanut butter bite:
                        protien: 8g cars:2g fat: 8g</p>
                    <p>
                        protein shake:
                        protein: 25g carbs:8g fats: 2g
                    </p>

                    <button class="add">Add</button>
                </div>
            </div>

            <button id="refreshButton">Refresh</button>
        </div>
    </div>

</body>

</html>