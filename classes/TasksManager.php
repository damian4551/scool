<?php

class TasksManager {

    public function taskValidation() {
        $args = [
            'title' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'date_to' => FILTER_SANITIZE_FULL_SPECIAL_CHARS
        ];

        $data = filter_input_array(INPUT_GET, $args);

        $task_array = array(
            'title' => $data["title"],
            'date_to' => date('Y-m-d H:i:s', strtotime($data["date_to"])),
        );

        return $task_array;
    }

    public function addTaskToDB($db, $user_id) {
        $task_array = $this->taskValidation();

        $title = $task_array['title'];
        $date_to = $task_array['date_to'];
        $current_date =  date("Y-m-d 00:00:00");

        if(strtotime($current_date) > strtotime($date_to)) {
            $error = 'Błędnie podana data';
            return $error;
        }

        $db->insert("INSERT INTO tasks (task_id, title, date_to, status, user_id) VALUES (NULL, '$title', '$date_to', 0, '$user_id')");
    }

    public function deleteTaskFromDB($db, $id) {

        $db->delete("DELETE FROM tasks WHERE task_id = $id");

    }

    public function updateTaskStatusToDB($db, $id, $user_id) {
        $old_status = $db->selectElements("SELECT status FROM tasks WHERE user_id = '$user_id' AND task_id = $id");

        if($old_status[0]->status == 0) {
            $new_status = 1;
        } else {
            $new_status = 0;
        }
        
        $db->update("UPDATE tasks SET status = $new_status WHERE task_id = $id");
        
    }

    public function showAllTasks($db, $user_id) {
        $all_tasks = $db->selectElements("SELECT task_id, title, date_to, status FROM tasks WHERE user_id = '$user_id' ORDER BY date_to ASC");

        return $all_tasks;
    }

    public function showDayTasks($db, $date, $user_id) {
        $day_tasks = $db->selectElements("SELECT task_id, title, status FROM tasks WHERE date_to = '$date' AND user_id = '$user_id'");

        return $day_tasks;
    }

}