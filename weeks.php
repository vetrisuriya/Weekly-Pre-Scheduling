<?php require_once('./classes/autoload.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weeks</title>
    <link rel="stylesheet" href="./style.css">
</head>
<body>

    <?php include_once("./inc/header.inc.php"); ?>

    <?php
    $employee = new InMemory('weeks.txt');

    if(isset($_POST["week-submit"])) {
        if(isset($_POST["week-id"]) && $_POST["week-id"] != "") {
            $employee->update(json_encode([
                "uniqueID" => $_POST["week-id"],
                "weekName" => $_POST["old-week-name"],
                "fromDate" => $_POST["old-from-date"],
                "toDate" => $_POST["old-to-date"]
            ]), json_encode([
                "uniqueID" => $_POST["week-id"],
                "weekName" => $_POST["week-name"],
                "fromDate" => $_POST["from-date"],
                "toDate" => $_POST["to-date"]
            ]));
            echo "<div class='msg'> Data has been Updated </div>";
        } else {
            $employee->insert(json_encode([
                "uniqueID" => generateRandomString(),
                "weekName" => $_POST["week-name"],
                "fromDate" => $_POST["from-date"],
                "toDate" => $_POST["to-date"]
            ]));
            echo "<div class='msg'> Data has been inserted </div>";
        }
    }

    if(isset($_GET["delete"])) {
        $editData = $employee->read(["uniqueID" => $_GET["delete"]]);
        if($editData) {
            $result = $employee->remove(json_encode([
                "uniqueID" => $editData->uniqueID,
                "weekName" => $editData->weekName,
                "fromDate" => $editData->fromDate,
                "toDate" => $editData->toDate
            ]));
            
            if($result) {
                header("location: weeks.php");
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
                $weekName = $editData->weekName;
                $fromDate = $editData->fromDate;
                $toDate = $editData->toDate;
                $btnName = "Edit";
            } else {
                $uniqueID = "";
                $weekName = "";
                $fromDate = "";;
                $toDate = "";
                $btnName = "Add";
            }
            ?>
            <form action="weeks.php" method="POST">
                <div class="form-group">
                    <label for="week-name">Week Name</label>
                    <br>
                    <input type="text" id="week-name" name="week-name" value="<?= (isset($_GET["name"])) ? $_GET["name"] : $weekName ; ?>" required>
                </div>
                <div class="form-group">
                    <label for="from-date">From Date</label>
                    <br>
                    <input type="date" id="from-date" name="from-date" value="<?= (isset($_GET["from"])) ? $_GET["from"] : $fromDate ; ?>" required>
                </div>
                <div class="form-group">
                    <label for="to-date">To Date</label>
                    <br>
                    <?php
                    if(isset($_GET["from"])) {
                    ?>
                    <input type="date" id="to-date" name="to-date" value="<?= $toDate; ?>" required min="<?= (isset($_GET["from"])) ? date("Y-m-d",strtotime($_GET["from"]."+6 day")) : $fromDate; ?>" max="<?= (isset($_GET["from"])) ? date("Y-m-d",strtotime($_GET["from"]."+6 day")) : $fromDate; ?>">
                    <?php
                    } else {
                    ?>
                    <input type="date" id="to-date" name="to-date" value="<?= $toDate ; ?>" required>
                    <?php
                    }
                    ?>
                </div>
                <input type="hidden" id="old-week-name" name="old-week-name" value="<?= $weekName; ?>">
                <input type="hidden" id="old-from-date" name="old-from-date" value="<?= $fromDate; ?>">
                <input type="hidden" id="old-to-date" name="old-to-date" value="<?= $toDate; ?>">
                <input type="hidden" id="week-id" name="week-id" value="<?= $uniqueID; ?>">
                <div class="form-group">
                    <input type="submit" id="week-submit" name="week-submit" value="<?= $btnName; ?>">
                </div>
                <a href="weeks.php">New Weeks</a>
            </form>
        </div>
        <div class="table-team">
            <table>
                <colgroup>
                    <col style="width: 30%;">
                    <col style="width: 20%;">
                    <col style="width: 20%;">
                    <col style="width: 30%;">
                </colgroup>
                <thead>
                    <tr class="blue">
                        <td colspan="4">Week Records</td>
                    </tr>
                    <tr class="blue">
                        <td>Week Name</td>
                        <td>From Date</td>
                        <td>To Date</td>
                        <td>Action</td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if($arrData = $employee->read()) {
                        foreach($arrData as $key => $value) {
                            if($value) {
                                $objData = json_decode($value);
                    ?>
                                <tr>
                                    <td><?= $objData->weekName; ?></td>
                                    <td><?= $objData->fromDate; ?></td>
                                    <td><?= $objData->toDate; ?></td>
                                    <td>
                                        <a href="weeks.php?edit=<?= $objData->uniqueID; ?>">Edit</a>
                                        <a href="weeks.php?delete=<?= $objData->uniqueID; ?>">Delete</a>
                                    </td>
                                </tr>
                    <?php
                            }
                        }
                    } else {
                    ?>
                        <tr>
                            <td colspan="4" align="center">No Data Found</td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </main>

    <script>
        let weekName = document.querySelector("#week-name");
        let fromDate = document.querySelector("#from-date");
        fromDate.addEventListener("change", function() {
            if(this.value) {
                const queryString = window.location.search;
                const urlParams = new URLSearchParams(queryString);
                let params;
                if(urlParams.get('edit')) {
                    params = "?edit="+urlParams.get('edit')+"&name="+weekName.value+"&from="+this.value;
                } else {
                    params = "?name="+weekName.value+"&from="+this.value;
                }
                window.location.href = "weeks.php"+params;
            } else {
                window.location.href = "weeks.php";
            }
        })
    </script>
    
</body>
</html>