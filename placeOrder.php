<?php
  include_once 'functions.php';
  connect();
  session_start();

  try{
    $pdo = new PDO(DBCONNSTRING,DBUSER,DBPASS);

    if(isset($_SESSION['user'])){
      $username = test_input($_SESSION['user']);
      $sql = "SELECT * FROM users WHERE username = ?";
      $result = $pdo->prepare($sql);
      $result->execute([$username]);
      $user = $result->fetch();

      if($user){
        $name = $user['name'];
        $state = $user['state'];
        $city = $user['city'];
        $address = $user['address'];
        $credit = $user['payment'];
      }
    }
    else{
      $button = "Save Information";
    }
  }
  catch (PDOException $e){
    die( $e->getMessage());
  }
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <title>Truman Merchandise</title>
    <link rel="stylesheet" href="stylesheet_med.css" media="screen and (max-width:768px)"/>
    <link rel="stylesheet" href="stylesheet.css" media="screen and (min-width:769px)"/>
    <script src="functions.js"></script>
  </head>
  <body>
    <header>
      <nav>
        <a href="home.html">Home</a></li>
        <div class="homemenu">
          <h1>Navigation</h1>
          <div>
            <a href="information.html">Information</a>
            <a href="kirksville.html">Kirksville</a>
            <a href="academics.html">Academics</a>
            <a href="athletics.html">Athletics</a>
            <a href="trivia.html">Trivia</a>
          </div>
        </div>
        <div class="dropmenu">
          <h1>History</h1>
          <div>
            <a href="normal.html">A Normal Start</a>
            <a href="teachers.html">Teacher Training</a>
            <a href="nemo.html">Regional Roots</a>
            <a href="today.html">Truman Today</a>
          </div>
        </div>
      </nav>
    </header>
    </nav>
    <main class="placeOrder">
      <aside>
        <p>
          Welcome,
          <br>
          <?php
            session_start(); 
            if(isset($_SESSION['user'])){
              echo $_SESSION['user'];
            }
            else{
              echo "Customer";
            }
          ?>
        </p> 
        <a href="login.html">
          <?php
            if(isset($_SESSION['user'])){
              echo "Log Out";
            }
            else{
              echo "Sign In";
            }
          ?>
        </a>
        <a href="products.html">Products</a>
        <a href="account.html">Account</a>
        <a href="checkout.html">Checkout</a>
      </aside>
      <div>
        <h1>Place Order</h1>
        <div>
          <form onsubmit="return validateOrder(event)" action="confirmOrder.php" method="post">
            <fieldset>
              <div class="productList">
                <table>
                  <tbody>
                    <?php
                      try {
                        if(isset($_SESSION['order'])){
                          $runningTotal = 0;
                          $orderKeys = array_keys($_SESSION['order']);

                          foreach($orderKeys as $pid){
                            $sql = "SELECT * FROM products WHERE pid = ?";
                            $result = $pdo->prepare($sql);
                            $result->execute([test_input($pid)]);
                            $product = $result->fetch();

                            $pname = $product['name'];
                            $quantity = $_SESSION['order'][$pid];
                            $image = $product['image'];

                            if(isset($_SESSION['user'])){
                              $price = $product['price'] - 5;
                            }
                            else{
                              $price = $product['price'];
                            }

                            $total = number_format($price * $quantity, 2);
                            $runningTotal += ($price * $quantity);

                            echo "<tr>
                                  <td><input type='submit' value='&times' name='x{$pid}' id='x{$pid}'/></td>
                                  <td>
                                    <picture>
                                      <source media='(min-width:769px)' srcset='Shopping/{$image}.jpg'>
                                      <img src='Shopping_Med/{$image}.jpg'> 
                                    </picture>
                                  </td>
                                  <td>{$pname}</td>
                                  <td><input type='submit' value='&#8722' name='-{$pid}' id='-{$pid}'/>{$quantity}<input type='submit' value='+' name='+{$pid}' id='+{$pid}'/></td>
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
                      }
                      catch (PDOException $e){
                        die( $e->getMessage());
                      }
                      $pdo = null;
                    ?>
                  </tbody>
                </table>
              </div>
              <div class="accountInfo">
                <div>
                  <label for="confName">Full Name</label>
                  <input type="text" value="<?php if($name){echo $name;} else{echo '';} ?>" name="confName" id="confName">
                  <p id="confNameErr">
                    <?php
                    if(isset($_SESSION['confNameErr'])){
                      echo $_SESSION['confNameErr'];
                      unset($_SESSION['confNameErr']);}
                    ?>
                  </p>
                </div>
                <div>
                  <label for="confState">State</label>
                  <input type="text" value="<?php if($state){echo $state;} else{echo '';} ?>" name="confState" id="confState">
                  <p id="confStateErr">
                    <?php
                    if(isset($_SESSION['confStateErr'])){
                      echo $_SESSION['confStateErr'];
                      unset($_SESSION['confStateErr']);}
                    ?>
                  </p>
                </div>
                <div>
                  <label for="confCity">City</label>
                  <input type="text" value="<?php if($city){echo $city;} else{echo '';} ?>" name="confCity" id="confCity">
                  <p id="confCityErr">
                    <?php
                    if(isset($_SESSION['confCityErr'])){
                      echo $_SESSION['confCityErr'];
                      unset($_SESSION['confCityErr']);}
                    ?>
                  </p>
                </div>
                <div>
                  <label for="confAddress">Address</label>
                  <input type="text" value="<?php if($address){echo $address;} else{echo '';} ?>" name="confAddress" id="confAddress">
                  <p id="confAddErr">
                    <?php
                    if(isset($_SESSION['confAddErr'])){
                      echo $_SESSION['confAddErr'];
                      unset($_SESSION['confAddErr']);}
                    ?>
                  </p>
                </div>
                <div>
                  <label for="confCredit">Credit Card</label>
                  <input type="text" value="<?php if($credit){echo $credit;} else{echo '';} ?>" name="confCredit" id="confCredit">
                  <p id="confCredErr">
                    <?php 
                    if(isset($_SESSION['confCredErr'])){
                      echo $_SESSION['confCredErr'];
                      unset($_SESSION['confCredErr']);}
                    ?>
                  </p>
                </div>
                <div class="orderInfo">
                  <div>
                    <p>Subtotal</p>
                    <p>$<?php echo $runningTotal; ?></p>
                  </div>
                  <div>
                    <p>Tax</p>
                    <p>$<?php echo $tax; ?></p>
                  </div>
                  <div>
                    <p>Total</p>
                    <p>$<?php echo $net; ?></p>
                  </div>
                </div>
                <div>
                  <input type="submit" value="Place Order" name="confirm" id="confirm"/>
                </div>
              </div>
            </fieldset>
          </form>
        </div>
      </div>
    </main>
    <footer>
      <a href="products.html">Merchandise</a>
    </footer>
  </body>
</html>

