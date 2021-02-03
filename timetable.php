<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="http://example.com/favicon.png">

    <!-- STYLES -->
    <link rel="stylesheet" href="./styles/header.css">
    <link rel="stylesheet" href="./styles/timetable.css">
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.css">
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">

    <!-- SCRIPTS -->
    <script src="https://unpkg.com/swiper/swiper-bundle.js" defer ></script>
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js" defer ></script>
    <script src="app.js" defer ></script>

    <title>Plan</title>

</head>

<?php

    include_once 'classes/TimetableManager.php';
    include_once 'classes/UserManager.php';
    include_once 'classes/Database.php';

    $um = new UserManager();
    $tm = new TimetableManager();
    $db = new Database();

    //get current user_id
    session_start();
    $session_id = session_id();
    $user_id = $um->getLoggedInUser($db, $session_id);
    session_destroy();

    if ($user_id < 0) {
        header("location:login.php");
    }

    $days = array('poniedziałek', 'wtorek', 'środa', 'czwartek', 'piątek');

    $subject_error = '';

    if (filter_input(INPUT_GET, "add_subject")) {
        $subject_error = $tm->addSubjectToDB($db, $user_id);
    }

    if (filter_input(INPUT_GET, "delete_subject")) {
        $id = $_GET['delete_subject'];
        $tm->deleteSubjectFromDB($db, $id);
    }
    
?>

<body>
    <div class="timetable">
        <!-- HEADER -->
        <?php
            require 'header.php';
        ?>

        <!-- HINT BLOCK -->
        <div class="hint-timetable-block">
                <p>Przeciągnij w <span>lewo</span> plan, aby ujrzeć więcej dni</p>
                <p class="close-timetable-hint-btn"> Zamknij </p>
        </div>

        <!-- MAIN CONTAINER -->
        <div class="container">
            
            <!-- TIMETABLE CONTAINER -->
            <div class="timetable-container swiper-container">

                <div class="swiper-wrapper">

                <?php
                    foreach($days as $day) {
                    $subjects = $tm->showSubjects($db, $day, $user_id);
                    echo "
                    <div class='timetable-day-container swiper-slide'>
                        <div class='timetable-title-container'>
                            <div class='title-block'>
                                <p class='main-line'>
                                    plan
                                </p>
                                <p class='sub-line'>
                                    $day
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
                                    <span class='delete-span'><a href=timetable.php?delete_subject=$subject->subject_id>x</a></span>
                                </li>";
                            }
                     echo "           
                            </ul>
                        </div>
                    </div>";
                    }
                ?>

                </div>

                <div class="delete-subject-block">
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

            <!-- ADD SUBJECT FORM -->
            <div class="add-subject-container">
                <div class="add-title-container">
                    <div class="title-block">
                        <p class="main-line">
                            dodaj
                        </p>
                        <p class="sub-line">
                            przedmiot
                        </p>
                    </div>
                </div>
                <div class="form-container">
                    <form action="timetable.php" method="get">
                        <div class="input-row">
                            <label>przedmiot:</label>
                            <input name="subject" required />
                        </div>
                        <div class="input-row">
                            <label>dzień tygodnia:</label>
                            <select name="day_name" >
                                <option>poniedziałek</option>
                                <option>wtorek</option>
                                <option>środa</option>
                                <option>czwartek</option>
                                <option>piątek</option>
                            </select>
                        </div>
                        <div class="input-row">
                            <label>czas od:</label>
                            <input name="start_hour" type="time" required />
                        </div>
                        <div class="input-row">
                            <label>czas do:</label>
                            <input name="end_hour" type="time" required />
                        </div>
                        <div class="btn-add-subject">
                            <button type="submit" name="add_subject" value="add_subject">
                                <img src="./icons/edit.svg" alt="add">
                            </button>
                        </div>
                    </form>
                </div>
                <div class="error-block">
                    <p><?php echo $subject_error ?></p>
                </div>
            </div>

        </div>

    </div>

</body>
</html>