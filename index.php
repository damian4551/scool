<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./styles/header.css">
    <link rel="stylesheet" href="./styles/dashboard.css">
    <link rel="icon" href="http://example.com/favicon.png">
    <title>Panel główny</title>
</head>

<?php
    
    include_once 'classes/TimetableManager.php';
    include_once 'classes/TasksManager.php';
    include_once 'classes/UserManager.php';
    include_once 'classes/Database.php';

    $um = new UserManager();
    $tm = new TimetableManager();
    $taskm = new TasksManager();
    $db = new Database("localhost", "root", "", "scool_db");

    if (filter_input(INPUT_GET, "logout")) {
        $um->logoutUser($db);
    }

    //get current user_id
    session_start();
    $session_id = session_id();
    $user_id = $um->getLoggedInUser($db, $session_id);
    session_destroy();

    if ($user_id < 0) {
        header("location:login.php");
    }

    if (filter_input(INPUT_GET, "update_status")) {
        $id = $_GET['update_status'];
        $taskm->updateTaskStatusToDB($db, $id, $user_id);
    }

    //progress
    $all_tasks = $taskm->showAllTasks($db, $user_id);
    $all_tasks_amount = count($all_tasks);
    $tasks_done_amount = 0;
    $tasks_to_do = 0;
    $progress = 0;

    foreach($all_tasks as $task) {
        if($task->status == 1) {
            $tasks_done_amount++;
        }
    }

    $tasks_to_do = $all_tasks_amount - $tasks_done_amount;

    if ($all_tasks_amount === 0) {
        $progress = 0;
    } else {
        $progress = $tasks_done_amount/$all_tasks_amount * 100;
    }

    //timetable on present day
    $day_number = date('N');
    $days = array('poniedziałek', 'wtorek', 'środa', 'czwartek', 'piątek', 'sobota', 'niedziela');

?>

<body>
    <div class="dashboard">
        <!-- HEADER -->
        <?php
            require 'header.php';
        ?>

        <!-- MAIN CONTAINER -->
        <div class="container">

            <!-- TIMETABLE CONTAINER -->
            <div class="timetable-container">

                <?php
                $subjects = $tm->showSubjects($db, $days[$day_number - 1], $user_id);
                echo "
                <div class='timetable-title-container'>
                    <div class='title-block'>
                        <p class='main-line'>
                            plan
                        </p>
                        <p class='sub-line'>
                            na dzisiaj
                        </p>
                    </div>
                </div>
                <div class='subject-list'>
                    <ul>";
                    foreach($subjects as $subject) {
                        $new_start_hour = date('H:i', strtotime($subject->start_hour));
                        $new_end_hour = date('H:i', strtotime($subject->end_hour));

                        echo "
                        <li class='subject-item'>
                            <p class='subject-name'>$subject->subject_name</p>
                            <p class='subject-time'>$new_start_hour - $new_end_hour</p>
                        </li>";
                    }
                echo "           
                    </ul>
                </div>";

                ?>

                <div class="link-block">
                    <a href="timetable.php" class="link">Sprawdź cały plan -></a>
                </div>
            </div>

            <!-- TASKS PROGRESS CONTAINER -->
            <div class="progress-container">
                <div class="progress-text-block">
                    <div class="text-block">
                        <p class="description-line">wszystkich zadań</p>
                        <p class="amount-line"><?php echo "$all_tasks_amount"?></p>
                    </div>
                    <div class="text-block">
                        <p class="description-line">zadań do zrobienia</p>
                        <p class="amount-line"><?php echo "$tasks_to_do"?></p>
                    </div>
                </div>
                <div class="progress-bar">
                    <div class="progress-bar-amount" style="width: <?php echo $progress ?>%"></div>
                </div>
            </div>

            <!-- FIRST TASKS CONTAINER -->
            <div class="tasks-container-1">
                <div class="tasks-title-container">
                    <div class="title-block">
                        <p class="main-line">
                            zadania
                        </p>
                        <p class="sub-line">
                            na dzisiaj
                        </p>
                    </div>
                </div>
                <div class="tasks-list">

                    <?php
                        $current_date =  date("Y-m-d 00:00:00");
                        $today_tasks = $taskm->showDayTasks($db, $current_date, $user_id);
                        foreach($today_tasks as $task) {
                            echo "
                            <label class='custom-checkbox'> <a href=dashboard.php?update_status=$task->task_id>$task->title</a>
                                <input type='checkbox'>
                                <a href=dashboard.php?update_status=$task->task_id><span class='checkmark'";
                                if($task->status == 1) {
                                    echo 'style="background-color: #A5A9D3"';
                                }
                                echo "
                                ></span></a>
                                <a href=tasks.php?delete_task=$task->task_id><span class='delete-task'>x</span></a>
                            </label>
                            ";
                        }
                    ?>

                </div>
                <div class="link-block">
                    <a href="tasks.php" class="link">Sprawdź zadania -></a>
                </div>
            </div>

            <!-- SECOND TASKS CONTAINER -->
            <div class="tasks-container-2">
                <div class="tasks-title-container">
                    <div class="title-block">
                        <p class="main-line">
                            zadania
                        </p>
                        <p class="sub-line">
                            do jutra
                        </p>
                    </div>
                </div>
                <div class="tasks-list">

                    <?php
                        $tomorrow_date =  date("Y-m-d 00:00:00", strtotime("+1 day"));
                        $tommorow_tasks = $taskm->showDayTasks($db, $tomorrow_date, $user_id);
                        foreach($tommorow_tasks as $task) {
                            echo "
                            <label class='custom-checkbox'> <a href=dashboard.php?update_status=$task->task_id>$task->title</a>
                                <input type='checkbox'>
                                <a href=dashboard.php?update_status=$task->task_id><span class='checkmark'";
                                if($task->status == 1) {
                                    echo 'style="background-color: #A5A9D3"';
                                }
                                echo "
                                ></span></a>
                                <a href=tasks.php?delete_task=$task->task_id><span class='delete-task'>x</span></a>
                            </label>
                            ";
                        }
                    ?>

                </div>
                <div class="link-block">
                    <a href="tasks.php" class="link">Sprawdź zadania -></a>
                </div>
            </div>

        </div>
    </div>
</body>
</html>