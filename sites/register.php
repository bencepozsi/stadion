<?php
  $users = json_decode(file_get_contents("../sources/users.json"), true);
  session_start();
?>
<!DOCTYPE html>
<html>

<head lang="hu">
  <meta charset="utf-8">
  <title>Bead2</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="../css/forms.css">
</head>

<body class="text-center">
  <main class="form-signin">
    <form novalidate>
      <h1 class="h3 mb-3 fw-normal">Regisztráció</h1>

      
      <?php
      $errors = array();
      
      if (isset($_POST) && !empty($_POST)){
        if(!isset($_POST["email"]) || $_POST["email"]===""){
          array_push($errors,"Az e-mail cím megadása kötelező!");
        } else if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)){
          array_push($errors,"Hibás e-mail cím!");
        }

        if(!isset($_POST["username"]) || $_POST["username"]===""){
          array_push($errors,"A felhasználónév megadása kötelező!");
        } 
        
        if(!isset($_POST["password"]) || $_POST["password"]===""){
          array_push($errors,"A jelszó megadása kötelező!");
        } else if (!isset($_POST["repassword"]) || $_POST["repassword"]==="") {
          array_push($errors,"A jelszót kétszer kell megadni!");
        } else if ( $_POST["repassword"] != $_POST["password"]){
          array_push($errors,"A jelszavaknak meg kell egyezniük!");
        }

      }

      if (!empty($errors)){
        echo("<ul class='list-unstyled alert-danger p-3'>");      

        foreach($errors as $error){
          echo("<li class='mb-2'>".$error."</li>");
        }

        echo("</ul>");
      }

      if(isset($_GET["success"])){
        echo("<div class='alert-success p-1 mb-2'><span>Sikeres regisztráció!</span></div>");
      }

      if (!empty($_POST) && empty($errors) && !isset($_GET["success"])){
        $index = intval(end($users)["id"]) + 1 ;
        $new_user = array(
          "id" => $index,
          "username" => $_POST["username"],
          "email" => $_POST["email"],
          "password" => $_POST["password"],
          "permissions" => "user"
        );
        array_push($users,$new_user);
        file_put_contents("../sources/users.json",json_encode($users,JSON_PRETTY_PRINT));
        unset($_POST);
        header("location: register.php?success");
        exit;
      }

      ?>
      <?php
        if (!isset($_GET["success"])){
      ?>
      <form action="register.php" method="POST" novalidate>
        <div class="form-floating">
          <input type="email" class="form-control" id="emailIn" name="email" value="<?= $_POST['email'] ?? '' ?>">
          <label for="emailIn">E-mail cím</label>
        </div>
        <div class="form-floating">
          <input type="text" class="form-control" id="usernameIn" name="username" value="<?= $_POST['username'] ?? '' ?>">
          <label for="usernameIn">Felhasználónév</label>
        </div>
        <div class="form-floating">
          <input type="password" class="form-control" id="Password" name="password" value="<?= $_POST['password'] ?? '' ?>">
          <label for="Password">Jelszó</label>
        </div>
        <div class="form-floating">
          <input type="password" class="form-control" id="RePassword" name="repassword" value="<?= $_POST['password'] ?? '' ?>">
          <label for="RePassword">Jelszó újra</label>
        </div>
        <button class="w-100 btn btn-lg btn-primary" formmethod="post" type="submit" formaction="register.php">Regisztráció</button>
      </form>
      <?php } else {?>
      <a class="w-100 btn btn-lg btn-primary" href="../index.php">Vissza a főoldalra</a>
      <?php } ?>
  </main>

</body>

</html>