<?php
  include_once 'functions.php';
  session_start();

  try{
    if($_SERVER["REQUEST_METHOD"] == "POST") {
      if(isset($_SESSION['order'])){
        if(isset($_POST['submit'])){
          header("Location: placeOrder.php");
        }
        else if(isset($_POST['clear'])){
          $productKeys = array_keys($_SESSION['order']);
          foreach($productKeys as $pid){
            setcookie($pid, "", time() - 86400, "/");
          }
  
          unset($_SESSION['order']);
          unset($_SESSION['total']);
          setcookie("currentOrder", "", time() - 86400, "/");
  
          $_SESSION['checkMess'] = "Shopping cart has been cleared";
  
          header("Location: checkout.html");
        }
      }
      else{
        $_SESSION['checkErr'] = "Cart is empty";
        header("Location: checkout.html");
      }
    }
  }
  catch (PDOException $e){
    die( $e->getMessage());
  }
?>