
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="/public/login/login.css">
    <link rel="shortcut icon" href="/public/favicon.ico">

    <title>login</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@200;400;500;700;800&display=swap" rel="stylesheet">
</head>

<body>
    <div class="con">
        <div id="name">
            <h1>nutr.io</h1>
            <h3>nutrition made easy</h1>
                <img src="/public/image/carrot.png" alt="It's a carrot" />
        </div>
        <div id="loginForm">
            <h2>login</h2>
            <form method="POST" action="/public/login/handleIntake.php">
                <div class="userInfo">
                    <label for="username">username:</label>
                    <input type="text" id="username" name="username" placeholder="username" required>
                </div>
                <div class="userInfo">
                    <label for="password">password:</label>
                    <input type="password" name="password" id="password" placeholder="password" required>
                </div>
                <!--div class = "remember"> 
                <label for="remember">remember me</label>
                <input type="checkbox" id="remember" name="remember">
            </div-->
                <button type="submit" id="button">Login</button>
            </form>
            <footer>don't have an account? <span><a href="/public/signup">signup</a></span>
            </footer>

            <div class="error-msg">
                <i class="fa fa-times-circle"></i>
                %Error%
            </div>


        <script src="/public/login/login.js"></script>
    </div>
</body>

</html>