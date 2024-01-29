<?php
$request =  $_SERVER['REQUEST_URI'];


// echo $request;

switch ($request) {

    case '/':
        require __DIR__ . '/public/login/index.php';
        break;
    
    //test
    // case '/login':
    //     require __DIR__ . '/public/loginPage/login.php';
    //     break;

    // case '/signup':
    //     require __DIR__ . '/public/signupPage/signup.php';
    //     break; 

    // case '/db':
    //     require __DIR__ . '/config/database.php';
    //     $info =  getUserInfo("chad");
    //     for ($i =0; $i<count($info); $i++) {
    //         echo $info[$i] . " ";
    //     }
    //     break;

    // case '/historym':
    //     require __DIR__ . '/phistorym.php';
    //     break;

    // case '/historyw':
    //     require __DIR__ . '/historyw.php';
    //     break;

    // case '/content':
    //     require __DIR__ . '/content.php';
    //     break;

    // case '/recommendations':
    //     require __DIR__ . '/recommendations.php';
    //     break;
    
    // case '/mealtrack':
    //     require __DIR__ . '/mealtrack.php';
    //     break;
    
    // default:
    //     http_response_code(404);
    //     require __DIR__ . '/public/404.php';
    //     break;
}

