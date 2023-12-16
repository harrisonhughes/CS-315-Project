<?php
  session_start();
  if($_SERVER["REQUEST_METHOD"] == "POST") {
    // If shopping cart is not empty
    if(isset($_SESSION['order'])){
      // If "Checkout" submit button is pressed, direct to placeOrder.php final checkout
      if(isset($_POST['submit'])){
        header("Location: placeOrder.php");
      }
      // If "Clear Order" submit button is pressed, remove all order variables and reset page
      else if(isset($_POST['clear'])){
        $productKeys = array_keys($_SESSION['order']);
        foreach($productKeys as $pid){
          setcookie($pid, "", time() - 86400, "/");
        }

        unset($_SESSION['order']);
        unset($_SESSION['total']);
        setcookie("currentOrder", "", time() - 86400, "/");

        // Save response message to display upon page reload
        $_SESSION['checkMess'] = "Shopping cart has been cleared";

        header("Location: checkout.html");
      }
    }
    // Otherwise, save error message and reset page
    else{
      $_SESSION['checkErr'] = "Cart is empty";
      header("Location: checkout.html");
    }
  }
?>