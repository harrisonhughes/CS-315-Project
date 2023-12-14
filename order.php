<?php
  include_once 'functions.php';
  session_start();

  try {
    if($_SERVER["REQUEST_METHOD"] == "POST"){
      if(isset($_POST['item'])){
        $quantity = test_input($_POST['item']);
        $pid = test_input($_SESSION['item']);

        if($quantity > 0){
          $_SESSION['productMess'] = "Item added to cart";
          setcookie($pid, true, time() + 86400, "/");

          if(isset($_SESSION['order'])){
            if(array_key_exists($pid, $_SESSION['order'])){
              $_SESSION['order'][$pid] += $quantity;
            }
            else{
              $_SESSION['order'][$pid] = $quantity;
            }
          }
          else{
            $_SESSION['order'] = array($pid => $quantity);
            setcookie("currentOrder", true, time() + 86400, "/");
          }
        }
        else{
          $_SESSION['numErr'] = "Quantity must be a positive integer";
          header("Location: item.php?id={$pid}");
          exit();
        }
      }
    }
    header("Location: products.html");
  }
  catch(PDOException $e){
    die($e->getMessage());
  }
  $pdo = null;
?>