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
      <h1 class="h3 mb-3 fw-normal">Bejelentkezés</h1>

      <?php
      $found = false;
      $loginid = "";
      $reason = "";

      if (isset($_POST["username"]) && isset($_POST["password"]) ) {
        foreach ($users as $user) {
          if ($_POST["username"] == $user["username"]) {
            if ($user["password"] === $_POST["password"]) {
              $found = true;
              $loginid = $user["id"]; 
              break;
            } else {
              $reason = "A megadott jelszó hibás!";
            }
          }
        }
        if ($_POST["username"] === "" ||  $_POST["password"] === ""){
          $reason = "Hiányos bejelentkezési adatok!";
        }
        else if ($reason === ""){
          $reason = "Nincs ilyen felhasználónév az adatbázisban!";
        }
      }

      if (!$found && isset($_POST["username"]) && isset($_POST["password"])) {
        echo ("<div class='alert alert-danger p-2'><span>Sikertelen bejelentkezés:</span><br><span>".$reason."</span></div>");
      }

      if ($found){
        $_SESSION["userid"] = $loginid;
        header('Location: ../index.php');
      }

      ?>
      <form action="login.php" method="POST" novalidate>
        <div class="form-floating">
          <input type="text" class="form-control" id="usernameIn" name="username" value="<?= $_POST['username'] ?? '' ?>">
          <label for="usernameIn">Felhasználónév</label>
        </div>
        <div class="form-floating">
          <input type="password" class="form-control" id="Password" name="password" value="<?= $_POST['password'] ?? '' ?>">
          <label for="Password">Jelszó</label>
        </div>
        <button class="w-100 btn btn-lg btn-primary" formmethod="post" type="submit" formaction="login.php">Bejelentkezés</button>
      </form>
  </main>

</body>

</html>