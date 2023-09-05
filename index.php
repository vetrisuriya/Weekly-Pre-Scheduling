<?php require_once('./classes/autoload.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plan for the week</title>
    <link rel="stylesheet" href="./style.css">
    <style>
        select {
            background: unset;
            border: unset !important;
            width: fit-content;
            outline: none;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            text-transform: capitalize;
        }
        input.weeks-time {
            width: 60px;
        }
        button {
            padding: 5px 10px;
            margin: 10px 0px;
            cursor: pointer;
        }
    </style>
</head>
<body>

    <?php include_once("./inc/header.inc.php"); ?>

    <?php
    $weeks = new InMemory('weeks.txt');
    $team = new InMemory('team.txt');
    $employee = new InMemory('employee.txt');
    $week_plan = new InMemory('week_plan.txt');
    
    if(isset($_GET["weeks"]) && $_GET["weeks"] != "") {
        $weekGetVal = $_GET["weeks"];
        $weekGetData = $weeks->read(["uniqueID" => $weekGetVal]);
    }

    if(isset($_POST["week-submit-btn"])) {
        $weeksInput = $_POST["weeks-input"];
        $weekTime = $_POST["week-time"];
        $weekArr = [];

        foreach($weekTime as $weekTimeKey => $weekTimeValue) {
            foreach($weekTimeValue as $weetKey => $weekValue) {
                if($weekValue[0] != '00:00') {
                    $weekArr[] = [
                        "emp_id" => $weekTimeKey,
                        "order" => $weetKey,
                        "time" => $weekValue[0]
                    ];
                }
            }
        }

        $weekFinalVal = [
            "weekID" => $weeksInput,
            "weekVal" => $weekArr
        ];

        if($arrData = $week_plan->read(["weekID" => $weeksInput])) {
            if($week_plan->update(json_encode($arrData), json_encode($weekFinalVal))) {
                echo "<div class='msg'> Data has been Updated </div>";
            }
        } else {
            if($week_plan->insert(json_encode($weekFinalVal))) {
                echo "<div class='msg'> Data has been inserted </div>";
            }
        }
    }
    ?>

    <main>
        <div class="table-dashboard">
            <?php
            if(isset($weekGetVal)) {
            ?>
            <form method="POST" action="index.php?weeks=<?= $weekGetVal; ?>">
            <?php } ?>
            <table>
                <colgroup>
                    <col style="width: 20%;">
                    <col style="width: calc(calc(100% - 35%) / 7);">
                    <col style="width: calc(calc(100% - 35%) / 7);">
                    <col style="width: calc(calc(100% - 35%) / 7);">
                    <col style="width: calc(calc(100% - 35%) / 7);">
                    <col style="width: calc(calc(100% - 35%) / 7);">
                    <col style="width: calc(calc(100% - 35%) / 7);">
                    <col style="width: calc(calc(100% - 35%) / 7);">
                    <col style="width: 15%;">
                </colgroup>
                <thead>
                    <tr class="blue">
                        <td colspan="9">
                            <select name="weeks-input" id="weeks-input">
                                <option value="">Select Week</option>
                                <?php
                                if($weeksArr = $weeks->read()) {
                                    foreach($weeksArr as $weekskey => $weeksValue) {
                                        if($weeksValue) {
                                            $weeksObj = json_decode($weeksValue);
                                ?>
                                            <option value="<?= $weeksObj->uniqueID; ?>" <?= (isset($weekGetVal) && ($weeksObj->uniqueID == $weekGetVal)) ? 'selected' : ''; ?>><?= $weeksObj->weekName; ?> (<?= date("d M Y", strtotime($weeksObj->fromDate)); ?> - <?= date("d M Y", strtotime($weeksObj->toDate)); ?>)</option>
                                <?php
                                        }
                                    }
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <?php
                    if(isset($weekGetVal)) {
                    ?>
                        <tr class="blue">
                            <td>#</td>
                            <?php
                            $weekPeriod = new DatePeriod(
                                new DateTime($weekGetData->fromDate),
                                new DateInterval('P1D'),
                                new DateTime($weekGetData->toDate)
                            );
                            foreach ($weekPeriod as $key => $value) {
                            ?>
                                <td><?= $value->format('D'); ?> <br> <?= $value->format('d M'); ?></td>
                            <?php
                            }
                            ?>
                            <td><?= date("D", strtotime($weekGetData->toDate)); ?> <br> <?= date("d M", strtotime($weekGetData->toDate)); ?></td>
                            <td>Total</td>
                        </tr>
                    <?php } ?>
                </thead>
                <?php
                if(isset($weekGetVal)) {
                    if($arrData = $week_plan->read(["weekID" => $weekGetVal])) {
                        $weekArrVal = [];
                        foreach($arrData->weekVal as $key => $value) {
                            $weekArrVal[$value->emp_id] = [];
                        }
                        foreach($arrData->weekVal as $key => $value) {
                            $weekArrVal[$value->emp_id][$value->order] = $value->time;
                        }
                    }
                ?>
                    <tbody>
                    <?php
                    if($teamArr = $team->read()) {
                        foreach($teamArr as $teamKey => $teamValue) {
                            if($teamValue) {
                                $teamObj = json_decode($teamValue);
                    ?>
                                <tr class="green" data-parent="<?= $teamObj->uniqueID; ?>">
                                    <td><?= $teamObj->teamName; ?></td>
                                    <td class="total" data-order="2">00:00</td>
                                    <td class="total" data-order="3">00:00</td>
                                    <td class="total" data-order="4">00:00</td>
                                    <td class="total" data-order="5">00:00</td>
                                    <td class="total" data-order="6">00:00</td>
                                    <td class="total" data-order="7">00:00</td>
                                    <td class="total" data-order="8">00:00</td>
                                    <td>00:00</td>
                                </tr>
                    <?php
                                if($empArr = $employee->read()) {
                                    foreach($empArr as $empKey => $empValue) {
                                        if($empValue) {
                                            $empObj = json_decode($empValue);
                                            if($empObj->teamName == $teamObj->uniqueID) {
                    ?>
                                                <tr data-child="<?= $teamObj->uniqueID; ?>">
                                                    <td class="team-members"><?= $empObj->empName; ?></td>
                                                    <td data-order="2" data-user="<?= $empObj->uniqueID; ?>"><input type="text" class="time-polyfill weeks-time" value="<?= (isset($weekArrVal[$empObj->uniqueID][2])) ? $weekArrVal[$empObj->uniqueID][2] : '00:00'; ?>" name="week-time[<?= $empObj->uniqueID; ?>][2][]"></td>
                                                    <td data-order="3" data-user="<?= $empObj->uniqueID; ?>"><input type="text" class="time-polyfill weeks-time" value="<?= (isset($weekArrVal[$empObj->uniqueID][3])) ? $weekArrVal[$empObj->uniqueID][3] : '00:00'; ?>" name="week-time[<?= $empObj->uniqueID; ?>][3][]"></td>
                                                    <td data-order="4" data-user="<?= $empObj->uniqueID; ?>"><input type="text" class="time-polyfill weeks-time" value="<?= (isset($weekArrVal[$empObj->uniqueID][4])) ? $weekArrVal[$empObj->uniqueID][4] : '00:00'; ?>" name="week-time[<?= $empObj->uniqueID; ?>][4][]"></td>
                                                    <td data-order="5" data-user="<?= $empObj->uniqueID; ?>"><input type="text" class="time-polyfill weeks-time" value="<?= (isset($weekArrVal[$empObj->uniqueID][5])) ? $weekArrVal[$empObj->uniqueID][5] : '00:00'; ?>" name="week-time[<?= $empObj->uniqueID; ?>][5][]"></td>
                                                    <td data-order="6" data-user="<?= $empObj->uniqueID; ?>"><input type="text" class="time-polyfill weeks-time" value="<?= (isset($weekArrVal[$empObj->uniqueID][6])) ? $weekArrVal[$empObj->uniqueID][6] : '00:00'; ?>" name="week-time[<?= $empObj->uniqueID; ?>][6][]"></td>
                                                    <td data-order="7" data-user="<?= $empObj->uniqueID; ?>"><input type="text" class="time-polyfill weeks-time" value="<?= (isset($weekArrVal[$empObj->uniqueID][7])) ? $weekArrVal[$empObj->uniqueID][7] : '00:00'; ?>" name="week-time[<?= $empObj->uniqueID; ?>][7][]"></td>
                                                    <td data-order="8" data-user="<?= $empObj->uniqueID; ?>"><input type="text" class="time-polyfill weeks-time" value="<?= (isset($weekArrVal[$empObj->uniqueID][8])) ? $weekArrVal[$empObj->uniqueID][8] : '00:00'; ?>" name="week-time[<?= $empObj->uniqueID; ?>][8][]"></td>
                                                    <td>00:00</td>
                                                </tr>
                    <?php
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    ?>
                    </tbody>
                    <tfoot>
                        <tr class="green">
                            <td>Total</td>
                            <td data-order="2">00:00</td>
                            <td data-order="3">00:00</td>
                            <td data-order="4">00:00</td>
                            <td data-order="5">00:00</td>
                            <td data-order="6">00:00</td>
                            <td data-order="7">00:00</td>
                            <td data-order="8">00:00</td>
                            <td>00:00</td>
                        </tr>
                    </tfoot>
                <?php } ?>
            </table>
            <?php
            if(isset($weekGetVal)) {
            ?>
            <button type="submit" name="week-submit-btn">Submit</button>
            </form>
            <?php } ?>
        </div>
    </main>
    
    <script type="text/javascript" src="https://unpkg.com/input-time-polyfill"></script>
    <script>

        let selDropDown = document.querySelector("#weeks-input");
        selDropDown.addEventListener("change", function() {
            if(this.value) {
                window.location.href = "index.php?weeks="+this.value;
            } else {
                window.location.href = "index.php";
            }
        })

        let arrVal = [];

        function dataChild(parentID = 0) {
            let childOpt;
            
            if(parentID) {
                childOpt = "tr[data-child='"+parentID+"']";
            } else {
                childOpt = "tr[data-child]";
            }

            let childEl = document.querySelectorAll(childOpt);

            childEl.forEach(cEl => {
                let elChildID = cEl.getAttribute("data-child");
                arrVal[String(elChildID)] =  [];
            });

            childEl.forEach(cEl => {
                let elChildID = cEl.getAttribute("data-child");
                let elChildInputs = cEl.querySelectorAll("input");

                let objInputVal = {};

                elChildInputs.forEach((iEl, ind) => {
                    let elInputOrder = elChildInputs[ind].closest("td").getAttribute("data-order");
                    let elValue = elChildInputs[ind].value;

                    objInputVal[elInputOrder] = elValue;
                })
                arrVal[String(elChildID)].push(objInputVal);
            });


            dataParent(arrVal);
        }

        let allInputs = document.querySelectorAll("input.weeks-time");
        allInputs.forEach(inputEl => {
            inputEl.addEventListener("input", function() {
                dataChild(this.closest("tr").getAttribute("data-child"));
            })
        });

        dataChild();

        function dataParent(childVal) {
            let arr = [];
            for(const parentKey in childVal) {
                arr[parentKey] = [];
                arr[parentKey][2] = [];
                arr[parentKey][3] = [];
                arr[parentKey][4] = [];
                arr[parentKey][5] = [];
                arr[parentKey][6] = [];
                arr[parentKey][7] = [];
                arr[parentKey][8] = [];
                childVal[parentKey].forEach(val => {
                    arr[parentKey][2].push(val[2]);
                    arr[parentKey][3].push(val[3]);
                    arr[parentKey][4].push(val[4]);
                    arr[parentKey][5].push(val[5]);
                    arr[parentKey][6].push(val[6]);
                    arr[parentKey][7].push(val[7]);
                    arr[parentKey][8].push(val[8]);
                });
            }

            for(const k in arr) {
                updateParent(k, 2, arr[k][2]);
                updateParent(k, 3, arr[k][3]);
                updateParent(k, 4, arr[k][4]);
                updateParent(k, 5, arr[k][5]);
                updateParent(k, 6, arr[k][6]);
                updateParent(k, 7, arr[k][7]);
                updateParent(k, 8, arr[k][8]);
            }

            updateBottomTotal();
            updateRightTotal();
        }

        function updateParent(parentID, order, value) {
            let hour = 0;
            let mins = 0;

            value.forEach(v => {
                hour = +hour + +(v.split(":")[0]);
                mins = +mins + +(v.split(":")[1]);

                if(mins >= 60) {
                    hour += 1;
                    mins -= 60;
                }
            })

            let parentTR = document.querySelector("tr[data-parent='"+parentID+"']");
            parentTR.querySelector("td:nth-child("+order+")").innerHTML = String(hour).padStart(2, '0')+":"+String(mins).padStart(2, '0');
        }

        function updateBottomTotal() {
            let allParents = document.querySelectorAll("tr[data-parent]");
            let footTR = document.querySelector("tfoot tr");

            for(let i=2; i<=8; i++) {
                let hour = 0;
                let mins = 0;

                allParents.forEach(tr => {
                    let td = tr.querySelector("td:nth-child("+i+")");

                    hour = +hour + +(td.textContent.split(":")[0]);
                    mins = +mins + +(td.textContent.split(":")[1]);

                    if(mins >= 60) {
                        hour += 1;
                        mins -= 60;
                    }
                });

                footTR.querySelector("td:nth-child("+i+")").innerHTML = String(hour).padStart(2, '0')+":"+String(mins).padStart(2, '0');
            }
        }
        function updateRightTotal() {
            let parentTR = document.querySelectorAll("tbody tr, tfoot tr");

            parentTR.forEach(tr => {
                let lastChild = tr.querySelector("td:last-child");
                let childTD = tr.querySelectorAll("td[data-order]");

                let hour = 0;
                let mins = 0;

                childTD.forEach(td => {
                    if(td.textContent) {
                        hour = +hour + +(td.textContent.split(":")[0]);
                        mins = +mins + +(td.textContent.split(":")[1]);
                    } else {
                        hour = +hour + +(td.querySelector("input").value.split(":")[0]);
                        mins = +mins + +(td.querySelector("input").value.split(":")[1]);
                    }   

                    if(mins >= 60) {
                        hour += 1;
                        mins -= 60;
                    }
                })

                lastChild.innerHTML = String(hour).padStart(2, '0')+":"+String(mins).padStart(2, '0');
            })
        }

    </script>
    
</body>
</html>