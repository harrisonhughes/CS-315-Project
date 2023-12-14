<?php
  include_once 'functions.php';
  connect();
  session_start();

  try {
    $pdo = new PDO(DBCONNSTRING,DBUSER,DBPASS);
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      if(isset($_POST['login'])){
        $username = test_input($_POST['username']);
        $password = test_input($_POST['password']);
        
        if(!empty($username) && !empty($password)){
          $sql = "SELECT * FROM users WHERE username = ?";
          $result = $pdo->prepare($sql);
          $result->execute([$username]);
          $user = $result->fetch();
  
          if($user){
            if($user['password'] == $password){
              $_SESSION['user'] = $username;
              setcookie("prevUser", $username, time() + 86400, "/");
              header("Location: products.html");
              exit();
            }
            else{
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
      else if(isset($_POST['create'])){
        $username = test_input($_POST['newUsername']);
        $password = test_input($_POST['newPassword']);
        $fullname = test_input($_POST['fullName']);
        $state = test_input($_POST['userState']);
        $city = test_input($_POST['userCity']);
        $address = test_input($_POST['address']);
        $payment = test_input($_POST['credit']);
        $validForm = true;

        $textArr = [$username, $password, $fullname, $state, $city, $address, $payment];
        $idArr = ["unameErr", "pwErr", "fnameErr", "stateErr", "cityErr", "addErr", "payErr"];
        $nameArr = ["Username", "Password", "Name", "State", "City", "Address", "Payment"];
        $validCharArr = ['/^[\s\S]*$/', '/^[\s\S]*$/', '/^[a-zA-Z\s.\'-]+$/', '/^[a-zA-Z\s]+$/', '/^[a-zA-Z\s.\'-]+$/',  '/^[a-zA-Z0-9\s.\'-]+$/', '/^[0-9\-]+$/'];

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

        if($validForm){
          $sql = "SELECT * FROM users WHERE username = ?";
          $result = $pdo->prepare($sql);
          $result->execute([$username]);
          $user = $result->fetch();

          if(!$user){
            $sqlValues = [$username, $password, $fullname, $state, $city, $address, $payment];
            $valuePlaceholders = rtrim(str_repeat('?,', 7), ',');
            $sql = "INSERT INTO users (username, password, name, state, city, address, payment) VALUES ({$valuePlaceholders})";
            $result = $pdo->prepare($sql);
            $result->execute($sqlValues);

            $_SESSION['user'] = $username;
            setcookie("prevUser", $username, time() + 86400, "/");
            header("Location: products.html");
            exit();
          }
          else{
            $_SESSION['unameErr'] = "Username already exists";
          }
        }
      }
    }
    header("Location: login.html");
  }
  catch (PDOException $e){
    die( $e->getMessage());
  }
  $pdo = null;
?>