<?php

class TimetableManager {

    public function subjectValidation() {
        $args = [
            'subject' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'day_name' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'start_hour' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'end_hour' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
        ];

        $data = filter_input_array(INPUT_GET, $args);

        $subject_array = array(
            'subject_name' => $data["subject"],
            'day_name' => $data["day_name"],
            'start_hour' => date('H:i:s', strtotime($data["start_hour"])),
            'end_hour' => date('H:i:s', strtotime($data["end_hour"])),
        );

        return $subject_array;
    }

    public function addSubjectToDB($db, $user_id) {
        $subject_array = $this->subjectValidation();

        $subject_name = $subject_array['subject_name'];
        $day_name = $subject_array['day_name'];
        $start_hour = $subject_array['start_hour'];
        $end_hour = $subject_array['end_hour'];

        $error = '';

        //check validation of start and end of subject
        if(strtotime($start_hour) >= strtotime($end_hour)) {
            $error = 'Błędnie podana godzina';
            return $error;
        }

        //check if subject exists between submitted hours
        $is_subject = $db->selectElements("SELECT * FROM school_subjects WHERE ((start_hour >= '$start_hour' AND end_hour <= '$end_hour') OR (start_hour <= '$end_hour' AND end_hour >= '$start_hour')) AND (day_name = '$day_name' AND user_id = '$user_id')");

        //add to database if $is_subject is empty array
        if(count($is_subject) == 0) {
            $db->insert("INSERT INTO school_subjects (subject_id, subject_name, day_name, start_hour, end_hour, user_id) VALUES (NULL, '$subject_name', '$day_name', '$start_hour', '$end_hour', '$user_id')");
        } else {
            $error = "Istnieją zajęcia w podanym czasie";
            return $error;
        }

    }

    public function deleteSubjectFromDB($db, $id) {

        $db->delete("DELETE FROM school_subjects WHERE subject_id = $id");

    }

    public function showSubjects($db, $day, $user_id) {
        $subjects = $db->selectElements("SELECT * FROM school_subjects WHERE day_name = '$day' AND user_id = '$user_id' ORDER BY start_hour ASC");

        return $subjects;
    }

}