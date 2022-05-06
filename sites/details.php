<?php
$teams = json_decode(file_get_contents("../sources/teams.json"), true);
$matches = json_decode(file_get_contents("../sources/matches.json"), true);
$comments = json_decode(file_get_contents("../sources/comments.json"), true);
$users = json_decode(file_get_contents("../sources/users.json"), true);
$fmatches = array();
$fcomments = array();

session_start();

foreach ($matches as $match) {
  if ($match["home"]["id"] == $_GET["id"] || $match["away"]["id"] == $_GET["id"]) {
    array_push($fmatches, $match);
  }
}

foreach ($comments as $comment) {
  if ($comment["teamid"] == $_GET["id"]) {
    array_push($fcomments, $comment);
  }
}

usort($fmatches, fn ($a, $b) => strtotime($b['date']) - strtotime($a['date']));

function addComment(){
  $comments = json_decode(file_get_contents("../sources/comments.json"), true);
  $entry = array(
    "author" => $_SESSION["userid"],
    "text" => $_POST["commentText"],
    "teamid" => $_GET["id"],
    "date" => date("Y-m-d")
  );
  array_push($comments,$entry);
  file_put_contents("../sources/comments.json",json_encode($comments,JSON_PRETTY_PRINT));
  header("location: ". "details.php?id=" . $_GET["id"]);
}

if (isset($_POST["commentText"]) && $_POST["commentText"] != ""){
  addComment();
}

if($users[$_SESSION["userid"]]["permissions"] === "admin" && isset($_GET["rm"])){
  unset($comments[$_GET["rm"]]);
  file_put_contents("../sources/comments.json",json_encode($comments,JSON_PRETTY_PRINT));
  header("location: ". "details.php?id=" . $_GET["id"]);
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
  <link rel="stylesheet" href="../css/main.css">
</head>

<body>
  <header>
    <nav class="navbar navbar-expand-sm bg-dark navbar-dark sticky-top">
      <div class="container-fluid">
        <ul class="navbar-nav">
          <li class="navbar-item"><a class="nav-link" href="../index.php">Főoldal</a></li>
          <?php
          if (!isset($_SESSION["userid"]) || $_SESSION["userid"] == 0) {
            echo ("<div class='navbar-item ms-auto row'>");
            echo ("<li class='navbar-item col'><a class='nav-link' href='../sites/login.php'>Bejelentkezés</a></li>");
            echo ("<li class='navbar-item col'><a class='nav-link' href='../sites/register.php'>Regisztráció</a></li>");
            echo ("</div>");
          } else {
            echo ("<li class='login-data ms-auto'>" . $users[$_SESSION["userid"]]["username"] . " <a href='../index.php?logout'>Kijelentkezés</a>" . "</span>");
          }
          ?>
        </ul>
      </div>
    </nav>
  </header>
  <main>
    <section class="jumbotron text-center">
      <div class="container">
        <h1 class="jumbotron-heading">Csapatrészletek</h1>
        <h2 class="lead text-muted"><?= $teams[$_GET["id"]]["name"] ?></h2>
      </div>
    </section>
    <div class="container">
      <div class="row">
        <div class="col">
          <h2>Meccsek</h2>
          <ul class="list-unstyled">
            <?php
            foreach ($fmatches as $match) {
              $myteam = ($match["home"]["id"] == $_GET["id"]) ? intval($match["home"]["score"]) : intval($match["away"]["score"]);
              $theirteam = ($match["home"]["id"] != $_GET["id"]) ? intval($match["home"]["score"]) : intval($match["away"]["score"]);
              $classcolor = "";
              if ($match["date"] < date("Y-m-d")) {
                if ($myteam === $theirteam) {
                  $classcolor = "draw";
                } else if ($myteam > $theirteam) {
                  $classcolor = "victory";
                } else {
                  $classcolor = "defeat";
                }
              }
              echo ("<li class='match-box " . $classcolor . "'>" .
                "<p>" . $match['date'] . "<p>" .
                "<span class='px-2'>" . $match['home']['score'] . "</span>" . $teams[$match['home']['id']]['name'] . "   -   " . $teams[$match['away']['id']]['name'] . "<span class='px-2'>" . $match['away']['score'] .
                "</li>");
              
              if ($users[$_SESSION["userid"]]["permissions"] === "admin"){
                echo("<a class='btn btn-warning edit-button' href='edit.php?teamid=".$_GET["id"]."&matchid=".$match["id"]."' >
                Szerkesztés</a>");
              }
            }
            ?>
          </ul>
        </div>
        <div class="col">
          <h2 class="text-center">Kommentek</h2>
          <div class="container comment-container">
            <ul class="list-unstyled">
              <?php
              foreach ($fcomments as $comment) {
                echo ("<li class='comment'><span>" .
                  $users[$comment["author"]]["username"] . "</span><span>" . $comment["date"] . "</span><p>" . $comment["text"] . "</p></li>"
                );
                $commentid = array_search($comment, $comments);
                if ($users[$_SESSION["userid"]]["permissions"] === "admin"){
                  echo("<a class='btn btn-danger remove-button' href='details.php?id=".$_GET["id"]."&rm=".$commentid."' >
                  Törlés</a>");
                }
              }
              ?>
            </ul>
            <?php
            if (isset($_SESSION["userid"]) && $_SESSION["userid"] != 0) {
            ?>
              <div class="boxborder">
                  <?php
                    if (isset($_POST["commentText"]) && $_POST["commentText"] == ""){
                      echo("<p class='alert-danger p-1'>Üresen nem teheti közzé a kommentjét!</p>");
                    } 
                  ?>
                  <form class="form-inline" action="<?= "details.php?id=" . $_GET["id"] . "'" ?> method="POST" novalidate>
                    <textarea placeholder="Write your comment here!" class="commentBox" name="commentText"></textarea>
                    <button class="btn btn-primary float-end" formmethod="POST" type="submit" formaction='<?= "details.php?id=" . $_GET["id"] . "'" ?>'>Enter</button>
                  </form>
              </div>
            <?php } ?>
          </div>
        </div>
      </div>
    </div>
  </main>
</body>

</html>