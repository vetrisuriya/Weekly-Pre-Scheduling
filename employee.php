<?php require_once('./classes/autoload.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee</title>
    <link rel="stylesheet" href="./style.css">
</head>
<body>

    <?php include_once("./inc/header.inc.php"); ?>

    <?php
    $employee = new InMemory('employee.txt');
    $team = new InMemory('team.txt');

    if(isset($_POST["emp-submit"])) {
        if(isset($_POST["emp-id"]) && $_POST["emp-id"] != "") {
            $employee->update(json_encode([
                "uniqueID" => $_POST["emp-id"],
                "empName" => $_POST['old-emp-name'],
                "teamName" => $_POST['old-team-name']
            ]), json_encode([
                "uniqueID" => $_POST["emp-id"],
                "empName" => $_POST['emp-name'],
                "teamName" => $_POST['team-name']
            ]));
            echo "<div class='msg'> Data has been Updated </div>";
        } else {
            $employee->insert(json_encode([
                "uniqueID" => generateRandomString(),
                "empName" => $_POST['emp-name'],
                "teamName" => $_POST['team-name']
            ]));
            echo "<div class='msg'> Data has been inserted </div>";
        }
    }

    if(isset($_GET["delete"])) {
        $editData = $employee->read(["uniqueID" => $_GET["delete"]]);
        if($editData) {
            $result = $employee->remove(json_encode([
                "uniqueID" => $editData->uniqueID,
                "empName" => $editData->empName,
                "teamName" => $editData->teamName
            ]));
            
            if($result) {
                header("location: employee.php");
            }
        }
    }
    ?>

    <main>
        <div class="form">
            <?php
            if(isset($_GET["edit"])) {
                $editData = $employee->read(["uniqueID" => $_GET["edit"]]);
                $uniqueID = $editData->uniqueID;
                $empName = $editData->empName;
                $teamName = $editData->teamName;
                $btnName = "Edit";
            } else {
                $uniqueID = "";
                $empName = "";
                $teamName = "";
                $btnName = "Add";
            }
            ?>
            <form action="employee.php" method="POST">
                <div class="form-group">
                    <label for="emp-name">Employee Name</label>
                    <br>
                    <input type="text" id="emp-name" name="emp-name" value="<?= $empName; ?>" required>
                </div>
                <div class="form-group">
                    <label for="team-name">Team</label>
                    <br>
                    <select id="team-name" name="team-name" value="<?= $teamName; ?>" required>
                        <option value="">Select Team</option>
                        <?php
                        if($teamData = $team->read()) {
                            foreach($teamData as $teamKey => $teamValue) {
                                if($teamValue) {
                                    $teamData = json_decode($teamValue);
                        ?>
                                    <option value="<?= $teamData->uniqueID; ?>" <?= ($teamData->uniqueID == $teamName) ? 'selected' : ''; ?>><?= $teamData->teamName; ?></option>
                        <?php
                                }
                            }
                        }
                        ?>
                    </select>
                </div>
                <input type="hidden" id="old-emp-name" name="old-emp-name" value="<?= $empName; ?>">
                <input type="hidden" id="old-team-name" name="old-team-name" value="<?= $teamName; ?>">
                <input type="hidden" id="emp-id" name="emp-id" value="<?= $uniqueID; ?>">
                <div class="form-group">
                    <input type="submit" id="emp-submit" name="emp-submit" value="<?= $btnName; ?>">
                </div>
                <a href="employee.php">New Employee</a>
            </form>
        </div>
        <div class="table-employee">
            <table>
                <colgroup>
                    <col style="width: 35%;">
                    <col style="width: 35%;">
                    <col style="width: 30%;">
                </colgroup>
                <thead>
                    <tr class="blue">
                        <td colspan="3">Employee Records</td>
                    </tr>
                    <tr class="blue">
                        <td>Emp Name</td>
                        <td>Team</td>
                        <td>Action</td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if($arrData = $employee->read()) {
                        foreach($arrData as $key => $value) {
                            if($value) {
                                $objData = json_decode($value);
                                $teamData = $team->read(["uniqueID" => $objData->teamName]);
                    ?>
                                <tr>
                                    <td><?= $objData->empName; ?></td>
                                    <td><?= ($teamData) ? $teamData->teamName : "<span class='red-badge'>Team Not Found</span>"; ?></td>
                                    <td>
                                        <a href="employee.php?edit=<?= $objData->uniqueID; ?>">Edit</a>
                                        <a href="employee.php?delete=<?= $objData->uniqueID; ?>">Delete</a>
                                    </td>
                                </tr>
                    <?php
                            }
                        }
                    } else {
                    ?>
                        <tr>
                            <td colspan="3" align="center">No Data Found</td>
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