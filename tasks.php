<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="http://example.com/favicon.png">

    <!-- STYLES -->
    <link rel="stylesheet" href="./styles/header.css">
    <link rel="stylesheet" href="./styles/tasks.css">
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.css">
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">

    <!-- SCRIPTS -->
    <script src="https://unpkg.com/swiper/swiper-bundle.js" defer ></script>
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js" defer ></script>
    <script src="app.js" defer ></script>

    <title>Zadania</title>

</head>

<?php

    include_once 'classes/TasksManager.php';
    include_once 'classes/UserManager.php';
    include_once 'classes/Database.php';

    $um = new UserManager();
    $taskm = new TasksManager();
    $db = new Database();

    //get current user_id
    session_start();
    $session_id = session_id();
    $user_id = $um->getLoggedInUser($db, $session_id);
    session_destroy();

    $task_error = '';

    if ($user_id < 0) {
        header("location:login.php");
    }

    if (filter_input(INPUT_GET, "add_task")) {
        $task_error = $taskm->addTaskToDB($db, $user_id);
    }

    if (filter_input(INPUT_GET, "delete_task")) {
        $id = $_GET['delete_task'];
        $taskm->deleteTaskFromDB($db, $id);
    }

    if (filter_input(INPUT_GET, "update_status")) {
        $id = $_GET['update_status'];
        $taskm->updateTaskStatusToDB($db, $id, $user_id);
    }
?>

<body>
    <div class="timetable">
        <!-- HEADER -->
        <?php
            require 'header.php';
        ?>
        
        <!-- HINT BLOCK -->
        <div class="hint-tasks-block">
            <p>Przeciągnij w <span>lewo</span> zadania, aby ujrzeć więcej zadań</p>
            <p class="close-tasks-hint-btn"> Zamknij </p>
        </div>

        <!-- MAIN CONTAINER -->
        <div class="container">

            <!-- TASKS CONTAINER -->
            <div class="tasks-container swiper-container">
                <div class="swiper-wrapper">
                    <div class="tasks-day-container swiper-slide">
                        <div class="tasks-title-container">
                            <div class="title-block">
                                <p class="main-line">
                                    zadania
                                </p>
                                <p class="sub-line">
                                    do dzisiaj
                                </p>
                            </div>
                        </div>
                        <div class="tasks-list">

                            <?php
                                $current_date =  date("Y-m-d 00:00:00");
                                $today_tasks = $taskm->showDayTasks($db, $current_date, $user_id);
                                foreach($today_tasks as $task) {
                                    echo "
                                    <label class='custom-checkbox'> <a href=tasks.php?update_status=$task->task_id>$task->title</a>
                                        <input type='checkbox'>
                                        <a href=tasks.php?update_status=$task->task_id><span class='checkmark'";
                                        if($task->status == 1) {
                                            echo 'style="background-color: #A5A9D3"';
                                        }
                                        echo "
                                        ></span></a>
                                        <a href=tasks.php?delete_task=$task->task_id><span class='delete-span'>x</span></a>
                                    </label>
                                    ";
                                }
                            ?>

                        </div>
                    </div>
                    <div class="tasks-day-container swiper-slide">
                        <div class="tasks-title-container">
                            <div class="title-block">
                                <p class="main-line">
                                    zadania
                                </p>
                                <p class="sub-line">
                                    wszystkie
                                </p>
                            </div>
                        </div>
                        <div class="tasks-list">

                            <?php
                                $all_tasks = $taskm->showAllTasks($db, $user_id);
                                foreach($all_tasks as $task) {
                                    $new_date = date('d.m', strtotime($task->date_to));
                                    echo "
                                    <label class='custom-checkbox'> <a href=tasks.php?update_status=$task->task_id>$task->title</a>
                                        <input type='checkbox'>
                                        <a href=tasks.php?update_status=$task->task_id><span class='checkmark'";
                                        if($task->status == 1) {
                                            echo 'style="background-color: #A5A9D3"';
                                        }
                                        echo "
                                        ></span></a>
                                        <span class='date-to'>$new_date</span>
                                        <span class='delete-span'><a href=tasks.php?delete_task=$task->task_id>x</a></span>
                                    </label>
                                    ";
                                }
                            ?>

                        </div>
                    </div>
                </div>
                <div class="delete-task-block">
                    <button class="delete-btn">
                        <img src="./icons/trash.svg" alt="delete">
                    </button>
                </div>
            </div>  

            <!-- ADD BUTTON -->
            <div class="add-btn-wrapper">
                <button class="add-btn">
                    <span>+</span>
                </button>
            </div>

            <!-- ADD TASK FORM -->
            <div class="add-subject-container">
                <div class="add-title-container">
                    <div class="title-block">
                        <p class="main-line">
                            dodaj
                        </p>
                        <p class="sub-line">
                            zadanie
                        </p>
                    </div>
                </div>
                <div class="form-container">
                    <form action="tasks.php" method="get">
                        <div class="input-row">
                            <label>tytuł:</label>
                            <input name="title" type="text" required />
                        </div>
                        <div class="input-row">
                            <label>data:</label>
                            <input name="date_to" type="date" required />
                        </div>
                        <div class="btn-add-subject">
                            <button type="submit" name="add_task" value="add_task">
                                <img src="./icons/edit.svg" alt="add">
                            </button>
                        </div>
                    </form>
                </div>
                <div class="error-block">
                    <p><?php echo $task_error ?></p>
                </div>
            </div>

        </div>

    </div>

</body>
</html>