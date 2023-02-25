<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="signup.css">
  <title>signup</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@200;400;500;700;800&display=swap" rel="stylesheet">
</head>

<body>
  <h1>Nutr.io</h1>
  <div id="signupForm">
    <h2>signup</h2>
    <form action="#">
      <div class = "userInfo">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" placeholder="Email" required>
      </div>
      <div class = "userInfo">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" placeholder="Username" required>
      </div>
      <div class = "userInfo"> 
        <label for="password">password:</label>
        <input type="password" name="password" id="password" placeholder="Password" oninput="check()" required>
      </div>
      <div class = "userInfo">
        <label for="confirmPassword">confirm password:</label>
        <input type="password" name="confirmPassword" id="confirmPassword" placeholder="Confirm Password" oninput="check()" required>
      </div>
      <button id="submitButton" type="submit">SignUp</button>
    </form>
    <footer>already have an account? <span><a href="/login.php">login</a></span></footer>
  </div>

  <script src="signup.js"></script>
</body>

</html>