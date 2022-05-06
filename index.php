<?php
$teams = json_decode(file_get_contents("sources/teams.json"), true);
$matches = json_decode(file_get_contents("sources/matches.json"), true);
$users = json_decode(file_get_contents("sources/users.json"), true);

usort($matches, fn ($a, $b) => strtotime($b['date']) - strtotime($a['date']));

function ongoing($entry){
  return $entry['date'] < date("Y-m-d");
}

$fmatches = array();

foreach ($matches as $match) {
  if (ongoing($match)) {
    array_push($fmatches, $match);
  }
}

session_start();
if (isset($_GET["logout"])){
  $_SESSION["userid"] = 0;
}

?>
<!DOCTYPE html>
<html>

<head lang="hu">
  <meta charset="utf-8">
  <title>Bead2</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="css/main.css">
</head>

<body>
  <header>
    <nav class="navbar navbar-expand-sm bg-dark navbar-dark sticky-top">
      <div class="container-fluid">
        <ul class="navbar-nav">
          <li class="navbar-item"><a class="nav-link" href="#">Főoldal</a></li>
          <?php
            if (!isset($_SESSION["userid"]) || $_SESSION["userid"] == 0){
              echo("<div class='navbar-item ms-auto row'>");
              echo("<li class='navbar-item col'><a class='nav-link' href='sites/login.php'>Bejelentkezés</a></li>");
              echo("<li class='navbar-item col'><a class='nav-link' href='sites/register.php'>Regisztráció</a></li>");
              echo("</div>");
            } else {
              echo("<li class='login-data ms-auto'>".$users[$_SESSION["userid"]]["username"]." <a href='index.php?logout'>Kijelentkezés</a>"."</span>");
            }
          ?>
        </ul>
      </div>
    </nav>
  </header>
  <main>
    <section class="jumbotron text-center">
      <div class="container">
        <h1 class="jumbotron-heading">Eötvös Loránd Stadion</h1>
        <p class="lead text-muted">Az Eötvös Loránd Stadion szeretne megjelenni az interneten is, ehhez készítettük ezt a weboldalt, ahol megjelennek a nálunk játszott meccsek, illetve szeretnénk, hogy a rajongók tudják követni kedvenceik eredményeit.</p>
      </div>
    </section>
    <div class="container">
      <div class="row">
        <div class="col">
          <h2>Legutóbbi 5 meccs</h2>
          <ul class="list-unstyled">
            <?php
            for ($i = 0; $i < 5; $i++) {
              $match = $fmatches[$i];
              echo("<li class='match-box'>".
                "<p>" . $match['date'] . "<p>". 
                "<span class='px-2'>".$match['home']['score']."</span>".$teams[$match['home']['id']]['name']."   -   ".$teams[$match['away']['id']]['name']."<span class='px-2'>".$match['away']['score'].
                "</li>");
            }
            ?>
          </ul>
        </div>
        <div class="col">
          <h2>Csapatok</h2>
          <div class="w-50">
            <ul class="list-group text-center">
              <?php
              foreach ($teams as $team) {
                echo ("<li class='list-group-item'><a href='sites/details.php?id=" . $team["id"]. "'>" . $team["name"] . "</a></li>");
              }
              ?>
            </ul>
          </div>
        </div>
      </div>
      <div class="row">
      <div class="col">
          <h2>Összes meccs</h2>
          <ul class="list-unstyled">
            <?php
            foreach ($matches as $match) {
              echo("<li class='match-box'>".
                "<p>" . $match['date'] . "<p>". 
                "<span class='px-2'>".$match['home']['score']."</span>".$teams[$match['home']['id']]['name']."   -   ".$teams[$match['away']['id']]['name']."<span class='px-2'>".$match['away']['score'].
                "</li>");
            }
            ?>
          </ul>
        </div>
      </div>
    </div>
  </main>
</body>
</html>