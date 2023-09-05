<?php require_once('./classes/autoload.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team</title>
    <link rel="stylesheet" href="./style.css">
</head>
<body>

    <?php include_once("./inc/header.inc.php"); ?>

    <?php
    $team = new InMemory('team.txt');
    $employee = new InMemory('employee.txt');

    if(isset($_POST["team-submit"])) {
        if(isset($_POST["team-id"]) && $_POST["team-id"] != "") {
            $team->update(json_encode([
                "uniqueID" => $_POST["team-id"],
                "teamName" => $_POST['old-team-name']
            ]), json_encode([
                "uniqueID" => $_POST["team-id"],
                "teamName" => $_POST['team-name']
            ]));
            echo "<div class='msg'> Data has been Updated </div>";
        } else {
            $team->insert(json_encode([
                "uniqueID" => generateRandomString(),
                "teamName" => $_POST['team-name']
            ]));
            echo "<div class='msg'> Data has been inserted </div>";
        }
    }

    if(isset($_GET["delete"])) {
        $editData = $team->read(["uniqueID" => $_GET["delete"]]);
        if($editData) {
            $empData = $employee->read(["teamName" => $_GET["delete"]]);
            if($empData) {
                echo "<div class='msg'> Dude someone used this team, So we cant delete right now </div>";
            }
            else {
                $result = $team->remove(json_encode([
                    "uniqueID" => $editData->uniqueID,
                    "teamName" => $editData->teamName
                ]));
                
                if($result) {
                    header("location: team.php");
                }
            }
        }
    }
    ?>

    <main>
        <div class="form">
            <?php
            if(isset($_GET["edit"])) {
                $editData = $team->read(["uniqueID" => $_GET["edit"]]);
                $uniqueID = $editData->uniqueID;
                $teamName = $editData->teamName;
                $btnName = "Edit";
            } else {
                $uniqueID = "";
                $teamName = "";
                $btnName = "Add";
            }
            ?>
            <form action="team.php" method="POST">
                <div class="form-group">
                    <label for="team-name">Team Name</label>
                    <br>
                    <input type="text" id="team-name" name="team-name" value="<?= $teamName; ?>" required>
                </div>
                <input type="hidden" id="old-team-name" name="old-team-name" value="<?= $teamName; ?>">
                <input type="hidden" id="team-id" name="team-id" value="<?= $uniqueID; ?>">
                <div class="form-group">
                    <input type="submit" id="team-submit" name="team-submit" value="<?= $btnName; ?>">
                </div>
                <a href="team.php">New Team</a>
            </form>
        </div>
        <div class="table-team">
            <table>
                <colgroup>
                    <col style="width: 70%;">
                    <col style="width: 30%;">
                </colgroup>
                <thead>
                    <tr class="blue">
                        <td colspan="2">Team Records</td>
                    </tr>
                    <tr class="blue">
                        <td>Team Name</td>
                        <td>Action</td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if($arrData = $team->read()) {
                        foreach($arrData as $key => $value) {
                            if($value) {
                                $objData = json_decode($value);
                    ?>
                                <tr>
                                    <td><?= $objData->teamName; ?></td>
                                    <td>
                                        <a href="team.php?edit=<?= $objData->uniqueID; ?>">Edit</a>
                                        <a href="team.php?delete=<?= $objData->uniqueID; ?>">Delete</a>
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                    } else {
                    ?>
                        <tr>
                            <td colspan="2" align="center">No Data Found</td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </main>
    
</body>
</html>