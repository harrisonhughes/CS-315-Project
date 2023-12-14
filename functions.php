<?php
  function connect(){
    define('DBHOST', 'localhost');
    define('DBNAME', 'project_tables');
    define('DBUSER', 'root');
    define('DBPASS', 'root');
    define('DBCONNSTRING',"mysql:host=". DBHOST. ";dbname=". DBNAME);
  }

  function test_input($string) {
    $string = trim($string);
    $string = stripslashes($string);
    $string = htmlspecialchars($string);
    return $string;
  }

  function fillItems(){
    connect();
    session_start();
    
    try {
      $pdo = new PDO(DBCONNSTRING,DBUSER,DBPASS);
      $sql = "SELECT * FROM products";
      $result = $pdo->query($sql);
      $products = $result->fetchAll();

      foreach($products as $product){
        $name = $product['name'];
        $image = $product['image'];

        if(isset($_SESSION['user'])){
          $price = $product['price'] - 5;
        }
        else{
          $price = $product['price'];
        }

        if(isset($_COOKIE[$product['pid']])){
          $class = "class='recentlyOrdered'";
        }
        else{
          $class = "";
        }

        echo "
        <a href='item.php?id={$product['pid']}'>            
        <figure {$class}>
        <picture>
        <source media='(min-width:769px)' srcset='Shopping/{$image}.jpg'>
        <img src='Shopping_Med/{$image}.jpg'> 
        </picture>
        <figcaption>Truman State {$name}</figcaption>
        <figcaption>\${$price}</figcaption>
        </figure>
        </a>";
      }
    }
    catch (PDOException $e){
      die( $e->getMessage());
    }
    $pdo = null;
  }

  function fillCart(){
    connect();
    session_start();
    
    try {
      if(isset($_SESSION['order'])){
        $pdo = new PDO(DBCONNSTRING,DBUSER,DBPASS);
        $runningTotal = 0;
        $orderKeys = array_keys($_SESSION['order']);

        foreach($orderKeys as $pid){
          $sql = "SELECT * FROM products WHERE pid = ?";
          $result = $pdo->prepare($sql);
          $result->execute([test_input($pid)]);
          $product = $result->fetch();

          $name = $product['name'];
          $quantity = $_SESSION['order'][$pid];

          if(isset($_SESSION['user'])){
            $price = $product['price'] - 5;
          }
          else{
            $price = $product['price'];
          }

          $total = number_format($price * $quantity, 2);
          $runningTotal += ($price * $quantity);

          echo "<tr>
                <td>{$name}</td>
                <td>\${$price}</td>
                <td>$quantity</td>
                <td>\${$total}</td>
            </tr>";
        }
        
        $tax = number_format($runningTotal * 0.0835, 2);
        $net = number_format($runningTotal * 1.0835, 2);
        $runningTotal = number_format($runningTotal, 2);
        $_SESSION['total'] = $net;
      }
      else{
        $runningTotal = number_format(0, 2);
        $tax = number_format(0, 2);
        $net = number_format(0, 2);
        $_SESSION['total'] = 0;
      }

      echo "</tbody>
      <tfoot>
        <tr>
          <td></td>
          <td></td>
          <td><b>Subtotal</b></td>
          <td>\${$runningTotal}</td>
        </tr>
        <tr>
          <td></td>
          <td></td>
          <td><b>Tax (8.35%)</b></td>
          <td>\${$tax}</td>
        </tr>
        <tr>
        <td></td>
        <td></td>
        <td><b>Total</b></td>
        <td>\${$net}</td>
      </tr>
      </tfoot>";
    }
    catch (PDOException $e){
      die( $e->getMessage());
    }
    $pdo = null;
  }

  function fillAccount(){
    session_start();
    if($_SERVER["REQUEST_METHOD"] == "POST") {
      header('Location: accountHistory.html');
    }

    connect();
    try{
      $pdo = new PDO(DBCONNSTRING,DBUSER,DBPASS);

      if(isset($_SESSION['user'])){
        $username = test_input($_SESSION['user']);
        
        $sql = "SELECT * FROM users WHERE username = ?";
        $result = $pdo->prepare($sql);
        $result->execute([$username]);
        $user = $result->fetch();
        
        if($user){
          echo "<table>
            <tbody>
              <tr>
                <th>Username</th>
                <td>{$user['username']}</td>
              </tr>
              <tr>
              <th>Password</th>
              <td>{$user['password']}</td>
              </tr>
                <tr>
                <th>Name</th>
                <td>{$user['name']}</td>
              </tr>
              <tr>
                <th>State</th>
                <td>{$user['state']}</td>
              </tr>
              <tr>
                <th>City</th>
                <td>{$user['city']}</td>
              </tr>
              <tr>
                <th>Address</th>
                <td>{$user['address']}</td>
              </tr>
              <tr>
                <th>Credit Card</th>
                <td>{$user['payment']}</td>
              </tr>
            </tbody>
          </table>";

          $sql = "SELECT * FROM orders WHERE username = ?";
          $result = $pdo->prepare($sql);
          $result->execute([$username]);
          $orders = $result->fetchAll();
          if($orders){
            $numOrders = count($orders);
            $totalSpent = 0;
            foreach($orders as $order){
              $totalSpent += $order['total'];
            }
            $totalSpent = number_format($totalSpent, 2);

            if($numOrders > 1){
              $plural = "s";
            }

            echo "<p>You have spent <b>\${$totalSpent}</b> over <b>{$numOrders}</b> order{$plural}.</p>
            <form method='post'>
              <fieldset>
                <input type='submit' value='Order History' name='hist' id='hist'/>
              </fieldset>
            </form>";
          }
          else{
            echo "<p>You have not ordered any items yet.";
          }
        }
      }
    }
    catch (PDOException $e){
      die( $e->getMessage());
    }
    $pdo = null;
  }

  function fillInfo(){
    connect();
    session_start();

    try{
      $pdo = new PDO(DBCONNSTRING,DBUSER,DBPASS);

      if(isset($_SESSION['user'])){
        $username = test_input($_SESSION['user']);
        
        $sql = "SELECT * FROM orders WHERE username = ?";
        $result = $pdo->prepare($sql);
        $result->execute([$username]);
        $allOrders = $result->fetchAll();
        
        if($allOrders){
          if(!isset($_SESSION['page']) || $_SESSION['page'] < 1){
            $_SESSION['page'] = 1;
          }
          else if($_SESSION['page'] > count($allOrders)){
            $_SESSION['page'] = count($allOrders);
          }
          $page = $_SESSION['page'];
          $order = $allOrders[$page - 1];

          $sql = "SELECT * FROM order_info WHERE orderid = ?";
          $result = $pdo->prepare($sql);
          $result->execute([test_input($order['orderid'])]);
          $orderInfo = $result->fetch();

          echo "<h2>Order #{$page} <em>({$order['order_date']})</em></h2>
          <table>
            <thead>
              <tr>
                <th>Name</th>
                <th>State</th>
                <th>City</th>
                <th>Address</th>
                <th>Credit Card</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>{$orderInfo['name']}</td>
                <td>{$orderInfo['state']}</td>
                <td>{$orderInfo['city']}</td>
                <td>{$orderInfo['address']}</td>
                <td>{$orderInfo['payment']}</td>
              </tr>
            </tbody>
          </table>
          <h2>Order Details</h2>
          <table>
            <thead>
              <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total</th>
              </tr>
            </thead>
            <tbody>";

          $sql = "SELECT * FROM products";
          $result = $pdo->query($sql);
          $products = $result->fetchAll();
          $numItems = count($products);

          $order = array_slice($order, 3, -3);
          $orderKeys = array_keys($order);

          foreach($orderKeys as $pid){
            if($order["p{$pid}"] > 0){
              $pname = $products[$pid]['name'];
              $pprice = $products[$pid]['price'] - 5;
              $pquant = $order["p{$pid}"];

              $ptotal = number_format($pquant * $pprice, 2);
              $subtotal = number_format($order['total'] / 1.0835, 2);
              $tax = number_format(($order['total'] / 1.0835) * .0835, 2);
              $orderTotal = number_format($order['total'], 2);

              echo "<tr>
                <td>{$pname}</td>
                <td>\${$pprice}</td>
                <td>{$pquant}</td>
                <td>\${$ptotal}</td>
                </tr>";
            }
          }
          echo "</tbody>
            <tfoot>
              <tr>
                <td></td>
                <td></td>
                <td><b>Subtotal</b></td>
                <td>\$$subtotal</td>
              </tr>
              <tr>
              <td></td>
              <td></td>
              <td><b>Tax (8.35%)</b></td>
              <td>\$$tax</td>
            </tr>
              <tr>
                  <td></td>
                  <td></td>
                  <td><b>Total</b></td>
                  <td>\$$orderTotal</td>
                </tr?
              </tfoot>
          </table>";
        }
      }
    }
    catch (PDOException $e){
      die( $e->getMessage());
    }
    $pdo = null;
  }
?>
