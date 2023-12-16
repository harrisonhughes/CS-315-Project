<?php
  include_once 'functions.php';
  session_start();

  if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Add to cart button has been pressed
    if(isset($_POST['item'])){
      // Obtain quantity ordered, and specific product
      $quantity = test_input($_POST['item']);
      $pid = test_input($_SESSION['item']);


      if($quantity > 0){
        $_SESSION['productMess'] = "Item added to cart";
        setcookie($pid, true, time() + 86400, "/");

        // If order exists, either add new product to session variable, or add new quantity to current quantity
        if(isset($_SESSION['order'])){
          if(array_key_exists($pid, $_SESSION['order'])){
            $_SESSION['order'][$pid] += $quantity;
          }
          else{
            $_SESSION['order'][$pid] = $quantity;
          }
        }
        // Otherwise, create session variable for order, and initialize order cookie
        else{
          $_SESSION['order'] = array($pid => $quantity);
          setcookie("currentOrder", true, time() + 86400, "/");
        }
      }
      // Otherwise, quantity is invalid, remain on product page
      else{
        $_SESSION['numErr'] = "Quantity must be a positive integer";
        header("Location: item.php?id={$pid}");
        exit();
      }
    }
  }
  header("Location: products.html");
?>