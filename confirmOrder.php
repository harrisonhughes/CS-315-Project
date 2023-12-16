<?php
  include_once 'functions.php';
  session_start();

  try{
    $pdo = connect(); // 'functions.php' connect() to access database
    if($_SERVER["REQUEST_METHOD"] == "POST") {
      // "Place order" button has been pressed
      if(isset($_POST['confirm'])){
        // Filter the user text to prevent script injection
        $name = test_input($_POST['confName']);
        $state = test_input($_POST['confState']);
        $city = test_input($_POST['confCity']);
        $address = test_input($_POST['confAddress']);
        $payment = test_input($_POST['confCredit']);
        $validForm = true;

        // Initialize elements for text variables, error IDs, error message "titles", and character validation expressions 
        $textArr = [$name, $state, $city, $address, $payment];
        $idArr = ["confNameErr", "confStateErr", "confCityErr", "confAddErr", "confCredErr"];
        $nameArr = ["Name", "State", "City", "Address", "Payment"];
        $validCharArr = ['/^[a-zA-Z\s.\'-]+$/', '/^[a-zA-Z\s]+$/', '/^[a-zA-Z\s.\'-]+$/',  '/^[a-zA-Z0-9\s.\'-]+$/', '/^[0-9\-]+$/'];

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

        // If no form elements are invalid, order info and order details can be added to database
        if($validForm){
          // Obtain values for database insertion
          if(isset($_SESSION['user'])){
            $username = $_SESSION['user'];
          }
          else{
            // Guests are saved with a unique ID number (GUEST0, GUEST1, ...)
            // Select query must be used to obtain the current value of the GUEST ID number
            $sql = "SELECT COUNT(*) AS guest_count FROM orders WHERE username LIKE 'GUEST%'";
            $result = $pdo->query($sql);
            $guests = $result->fetch();
            $username = "GUEST" . $guests['guest_count']; // Will be used as the guest identifier
          }
          // Values indicates the row elements of the new insertion, columns holds the respective column name
          $values = [$username];
          $columns = ['username'];

          /// Use the order session variable to add each product id to the column names, and each quant ordered to the values array
          $orderKeys = array_keys($_SESSION['order']);
          foreach($orderKeys as $pid){
            $values[] = $_SESSION['order'][$pid]; // Quantity ordered of specific product
            $columns[] = "p{$pid}"; // Product columns in orders table are (p0, p1, ...)

            setcookie($pid, "", time() - 86400, "/");
          }

          // Remove comma from order total price for database insertion (5,630.21 -> 5630.21)
          $total = str_replace(",", "", $_SESSION['total']);
          $values[] = $total;
          $columns[] = 'total';

          // "Stringify" column names for database query, create string of question marks for safer database insertion
          $sqlColumns = implode(',', $columns);
          $valuePlaceholders = rtrim(str_repeat('?,', count($values)), ',');

          // Prepare and execute insertion query for order details 
          $sql = "INSERT INTO orders ({$sqlColumns}) VALUES ({$valuePlaceholders})";
          $result = $pdo->prepare($sql);
          $result->execute($values);

          // Obtain order ID (auto incremented) of the insertion from above. Use this as the key of our order information insertion below
          $sql = "SELECT MAX(orderid) AS max_id FROM orders";
          $result = $pdo->query($sql);
          $maxOrderID = $result->fetch();
          $newOrderID = $maxOrderID['max_id']; // Variable holds the order id of the query used above

          // Add orderID to the front of the text array-(contains each input result from beginning of file)
          array_unshift($textArr, $newOrderID);
          $valuePlaceholders = rtrim(str_repeat('?,', 6), ','); // Another ? string for safe querying

          // Prepare and execute insertion query for order information, using orderID and form text input results
          $sql = "INSERT INTO order_info (orderid, name, state, city, address, payment) VALUES ({$valuePlaceholders})";
          $result = $pdo->prepare($sql);
          $result->execute($textArr);

          // Order has been placed, remove session variables and cookies corresponding to previous order
          unset($_SESSION['order']);
          unset($_SESSION['total']);
          setcookie("currentOrder", "", time() - 86400, "/");

          // Load session variable with result message, return to checkout page;
          $_SESSION['checkMess'] = "Order has been processed";
          header("Location: checkout.html");
          $pdo = null;
          exit();
        }
      }
      // Otherwise, one of the item modification buttons (x, +, -) was pressed
      else{
        // Obtain product ID for each item in the current order, and loop through each to find the button pressed
        $productKeys = array_keys($_SESSION['order']);
          foreach($productKeys as $pid){
            // 'x' was pressed. Remoce item from order
            if(isset($_POST["x" . $pid])){
              unset($_SESSION['order'][$pid]);
              setcookie($pid, "", time() - 86400, "/");
            }
            // '-' was pressed. Decrement order quant; if 0, remove from order
            else if(isset($_POST["-" . $pid])){
              $_SESSION['order'][$pid]--;
              if($_SESSION['order'][$pid] <= 0){
                unset($_SESSION['order'][$pid]);
                setcookie($pid, "", time() - 86400, "/");
              }
            }
            // '+' was pressed, increment order quant
            else if(isset($_POST["+" . $pid])){
              $_SESSION['order'][$pid]++;
            }
          }
          // If item removal causes order to be empty, return to checkout page
          if(empty($_SESSION['order'])){
            unset($_SESSION['order']);
            setcookie("currentOrder", "", time() - 86400, "/");

            // Load error message 
            $_SESSION['checkErr'] = "Cart is empty";
            header("Location: checkout.html");
            $pdo = null;
            exit();
          }
      }
    }
    header("Location: placeOrder.php");
    $pdo = null;
  }
  // Display database connection issues
  catch (PDOException $e){
    die( $e->getMessage());
  }
?>
