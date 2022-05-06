<?php
$matches = json_decode(file_get_contents("../sources/matches.json"), true);
$teams = json_decode(file_get_contents("../sources/teams.json"), true);

$team1 = $teams[$matches[$_GET["matchid"]]["home"]["id"]]["name"];
$team2 = $teams[$matches[$_GET["matchid"]]["away"]["id"]]["name"];

session_start();

if (isset($_SESSION["userid"]) && $_SESSION["userid"] != 0) {
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

    <body>
        <main class="text-center" style="margin: auto;">
            <h1 class="fw-normal mb-4">Meccs szerkesztése</h1>
            <?php
            $errors = array();
            if ($_POST["homescore"] != "" && $_POST["awayscore"] != "" && $_POST["date"] != "") {

                if (!is_numeric($_POST["homescore"])) {
                    array_push($errors,"A hazai csapat pontszáma nem szám!");
                }
                if (!is_numeric($_POST["homescore"])) {
                    array_push($errors,"Az idegen csapat pontszáma nem szám!");
                }
            }

            if (!empty($errors)){
                echo("<ul class='list-unstyled alert-danger p-3'>");      
        
                foreach($errors as $error){
                  echo("<li class='mb-2'>".$error."</li>");
                }
        
                echo("</ul>");
            }

            if (isset($_POST["homescore"]) && isset($_POST["awayscore"]) && empty($errors)){
                $matches[$_GET["matchid"]]["home"]["score"] = $_POST["homescore"];
                $matches[$_GET["matchid"]]["away"]["score"] = $_POST["awayscore"];
                $matches[$_GET["matchid"]]["date"] = $_POST["date"];
                file_put_contents("../sources/matches.json",json_encode($matches,JSON_PRETTY_PRINT));
                header("location: details.php?id=".$_GET["teamid"]);
            }
            ?>
            <form action="edit.php?teamid=<?= $_GET["teamid"] ?>&matchid=<?= $_GET["matchid"] ?>" method="POST" novalidate>
                <div class="editborder">
                    <input type="text" maxlength="2" class="mt-2 twodigit" name="homescore" value="<?= $matches[$_GET["matchid"]]["home"]["score"] ?>">
                    <span class="h4"><?= $team1 ?> - <?= $team2 ?></span>
                    <input type="text" maxlength="2" class="mt-2 twodigit" name="awayscore" value="<?= $matches[$_GET["matchid"]]["away"]["score"] ?>"><br />
                    <input type="date" class="mt-2 mb-3" name="date" value="<?= $matches[$_GET["matchid"]]["date"] ?>"><br />
                </div>
                <button class="w-50 btn btn-lg btn-primary" formmethod="POST" type="submit" formaction="edit.php?teamid=<?= $_GET["teamid"] ?>&matchid=<?= $_GET["matchid"] ?>">Mentés</button>
            </form>
        </main>
    </body>

    </html>
<?php
}
