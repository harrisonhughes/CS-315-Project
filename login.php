<?php
  include_once 'functions.php';
  session_start();

  try {
    $pdo = connect(); // 'functions.php' connect() to access database
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      // Login submit button has been pressed
      if(isset($_POST['login'])){
        // Filter the user text to prevent script injection
        $username = test_input($_POST['username']);
        $password = test_input($_POST['password']);
        
        if(!empty($username) && !empty($password)){
          // Prepare and execute a select query to assert correct username and password combo
          $sql = "SELECT * FROM users WHERE username = ?";
          $result = $pdo->prepare($sql);
          $result->execute([$username]);
          $user = $result->fetch();
  
          // If the specified username is in the database, assert correct password
          if($user){
            if($user['password'] == $password){
              // Initialize session variable for access control, and cookies for "Remember me" feature
              $_SESSION['user'] = $username;
              setcookie("prevUser", $username, time() + 86400, "/");
              setcookie("prevPass", $password, time() + 86400, "/");

              header("Location: products.html");
              $pdo = null;
              exit();
            }
            else{
              // Initialize session variables for each input mistake below 
              $_SESSION['pwordErr'] = "Incorrect Password";
            }
          }
          else{
            $_SESSION['userErr'] = "Username does not exist";
          }
        }
        else{
          if(empty($username)){
            $_SESSION['userErr'] = "Username cannot be empty";
          }
          if(empty($password)){
            $_SESSION['pwordErr'] = "Password cannot be empty";
          }
        }
      }
      // Create account submit button has been pressed
      else if(isset($_POST['create'])){
        // Filter the user text to prevent script injection
        $username = test_input($_POST['newUsername']);
        $password = test_input($_POST['newPassword']);
        $fullname = test_input($_POST['fullName']);
        $state = test_input($_POST['userState']);
        $city = test_input($_POST['userCity']);
        $address = test_input($_POST['address']);
        $payment = test_input($_POST['credit']);
        $validForm = true; 

        // Initialize elements for text variables, error IDs, error message "titles", and character validation expressions 
        $textArr = [$username, $password, $fullname, $state, $city, $address, $payment];
        $idArr = ["unameErr", "pwErr", "fnameErr", "stateErr", "cityErr", "addErr", "payErr"];
        $nameArr = ["Username", "Password", "Name", "State", "City", "Address", "Payment"];
        $validCharArr = ['/^[\s\S]*$/', '/^[\s\S]*$/', '/^[a-zA-Z\s.\'-]+$/', '/^[a-zA-Z\s]+$/', '/^[a-zA-Z\s.\'-]+$/',  '/^[a-zA-Z0-9\s.\'-]+$/', '/^[0-9\-]+$/'];

        // Loop through each array to assert a nonempty and character correct entry. Assign error messages to respective session variables
        for($i = 0; $i < count($textArr); $i++){
          if(empty($textArr[$i])){
            $_SESSION[$idArr[$i]] = $nameArr[$i] . " cannot be empty";
            $validForm = false;
          }
          else if(!preg_match($validCharArr[$i], $textArr[$i])){
            $_SESSION[$idArr[$i]] = "Invalid " . strtolower($nameArr[$i]) . " format";
            $validForm = false;
          }
        }

        // If no form elements are invalid, add user to database if username is unique
        if($validForm){
          // Prepare and execute select query to assert username is unique
          $sql = "SELECT * FROM users WHERE username = ?";
          $result = $pdo->prepare($sql);
          $result->execute([$username]);
          $user = $result->fetch();

          // All elements are valid, and the username is unqiue
          if(!$user){
            // Prepare and execute insert query to add the new user information to the database
            $sqlValues = [$username, $password, $fullname, $state, $city, $address, $payment];
            $valuePlaceholders = rtrim(str_repeat('?,', 7), ',');
            $sql = "INSERT INTO users (username, password, name, state, city, address, payment) VALUES ({$valuePlaceholders})";
            $result = $pdo->prepare($sql);
            $result->execute($sqlValues);

            // Initialize session variable for access control, and cookies for "Remember me" feature
            $_SESSION['user'] = $username;
            setcookie("prevUser", $username, time() + 86400, "/");
            setcookie("prevPass", $password, time() + 86400, "/");

            header("Location: products.html");
            $pdo = null;
            exit();
          }
          // Username is the table primary key, so no duplicates are allowed
          else{
            $_SESSION['unameErr'] = "Username already exists";
          }
        }
      }
    }
    header("Location: login.html");
  }
  // Display database connection issues
  catch (PDOException $e){
    die( $e->getMessage());
  }
  $pdo = null;
?>