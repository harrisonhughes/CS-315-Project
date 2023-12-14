<?php
  include_once 'functions.php';
  connect();
  session_start();
  try {
    $pdo = new PDO(DBCONNSTRING,DBUSER,DBPASS);
    $pid = test_input($_GET['id']);

    $sql = "SELECT * FROM products WHERE pid = ?";
    $result = $pdo->prepare($sql);
    $result->execute([$pid]);
    $product = $result->fetch();

    if($product){
      $name = $product['name'];
      $image = $product['image'];

      if(isset($_SESSION['user'])){
        $price = $product['price'] - 5;
      }
      else{
        $price = $product['price'];
      }

      $_SESSION['item'] = $pid;
    }
    else{
      header ("Location: products.html");
      exit();
    }
  }
  catch (PDOException $e){
    die( $e->getMessage());
  }
  $pdo = null;
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
      <main class="item">
        <aside>
          <p>
            Welcome,
            <br>
            <?php
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
      <figure>
        <picture>
        <source media='(min-width:769px)' srcset='Shopping/<?php echo $image; ?>.jpg'>
        <img src='Shopping_Med/<?php echo $image; ?>.jpg'> 
        </picture>
      </figure>
      <div>
        <h1><?php echo "Truman State {$name}";?></h1>
        <h2><?php echo "Price: \${$price}";?></h2>
        <form onsubmit="return validateNumber()" action="order.php" method="post">
          <fieldset>
            <div>
              <label for="item">Quantity</label>
              <input type="number" value="0" min="0" name="item" id="item">
            </div>
            <p id="numErr"><?php
                if(isset($_SESSION['numErr'])){
                  echo $_SESSION['numErr'];
                  unset($_SESSION['numErr']);}
              ?></p>
            <input type="submit" value="Add Item(s) to Cart"/>
          </fieldset>
        </form>
      </div>
    </div>
    </main>
    <footer>
    <a href="products.html">Merchandise</a>
    <?php
      if(isset($_COOKIE['currentOrder'])) {
        echo "<a href='checkout.html'>Checkout</a>";
      }
    ?>
  </footer>
  </body>
</html>