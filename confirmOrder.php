<?php
  include_once 'functions.php';
  connect();
  session_start();

  try{
    $pdo = new PDO(DBCONNSTRING,DBUSER,DBPASS);
    if($_SERVER["REQUEST_METHOD"] == "POST") {
      if(isset($_POST['confirm'])){
        $name = test_input($_POST['confName']);
        $state = test_input($_POST['confState']);
        $city = test_input($_POST['confCity']);
        $address = test_input($_POST['confAddress']);
        $payment = test_input($_POST['confCredit']);
        $validForm = true;

        $textArr = [$name, $state, $city, $address, $payment];
        $idArr = ["confNameErr", "confStateErr", "confCityErr", "confAddErr", "confCredErr"];
        $nameArr = ["Name", "State", "City", "Address", "Payment"];
        $validCharArr = ['/^[a-zA-Z\s.\'-]+$/', '/^[a-zA-Z\s]+$/', '/^[a-zA-Z\s.\'-]+$/',  '/^[a-zA-Z0-9\s.\'-]+$/', '/^[0-9\-]+$/'];

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
          if(isset($_SESSION['user'])){
            $username = $_SESSION['user'];
          }
          else{
            $sql = "SELECT COUNT(*) AS guest_count FROM orders WHERE username LIKE 'GUEST%'";
            $result = $pdo->query($sql);
            $guests = $result->fetch();
            $username = "GUEST" . $guests['guest_count'];
          }
          $values = [$username];
          $columns = ['username'];

          $orderKeys = array_keys($_SESSION['order']);
          foreach($orderKeys as $pid){
            $values[] = $_SESSION['order'][$pid];
            $columns[] = "p{$pid}";

            setcookie($pid, "", time() - 86400, "/");
          }

          $total = str_replace(",", "", $_SESSION['total']);
          $values[] = $total;
          $columns[] = 'total';

          $sqlColumns = implode(',', $columns);
          $valuePlaceholders = rtrim(str_repeat('?,', count($values)), ',');

          $sql = "INSERT INTO orders ({$sqlColumns}) VALUES ({$valuePlaceholders})";
          $result = $pdo->prepare($sql);
          $result->execute($values);

          $sql = "SELECT MAX(orderid) AS max_id FROM orders";
          $result = $pdo->query($sql);
          $maxOrderID = $result->fetch();
          $newOrderID = $maxOrderID['max_id'];

          array_unshift($textArr, $newOrderID);
          $valuePlaceholders = rtrim(str_repeat('?,', 6), ',');

          $sql = "INSERT INTO order_info (orderid, name, state, city, address, payment) VALUES ({$valuePlaceholders})";
          $result = $pdo->prepare($sql);
          $result->execute($textArr);

          unset($_SESSION['order']);
          unset($_SESSION['total']);
          setcookie("currentOrder", "", time() - 86400, "/");

          $_SESSION['checkMess'] = "Order has been processed";
          header("Location: checkout.html");
          exit();
        }
      }
      else{
        $productKeys = array_keys($_SESSION['order']);
          foreach($productKeys as $pid){
            if(isset($_POST["x" . $pid])){
              unset($_SESSION['order'][$pid]);
              setcookie($pid, "", time() - 86400, "/");
            }
            else if(isset($_POST["-" . $pid])){
              $_SESSION['order'][$pid]--;
              if($_SESSION['order'][$pid] <= 0){
                unset($_SESSION['order'][$pid]);
                setcookie($pid, "", time() - 86400, "/");
              }
            }
            else if(isset($_POST["+" . $pid])){
              $_SESSION['order'][$pid]++;
            }
          }
          if(empty($_SESSION['order'])){
            unset($_SESSION['order']);
            setcookie("currentOrder", "", time() - 86400, "/");

            $_SESSION['checkErr'] = "Cart is empty";
            header("Location: checkout.html");
            exit();
          }
      }
    }
    header("Location: placeOrder.php");
    $pdo = null;
  }
  catch (PDOException $e){
    die( $e->getMessage());
  }
?>
