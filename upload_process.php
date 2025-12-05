<?php
include_once('dbconnect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_FILES['questions_text']) && $_FILES['questions_text']['error'] == 0) {

        $fileTmpPath = $_FILES['questions_text']['tmp_name'];
        $fileExtension = strtolower(pathinfo($_FILES['questions_text']['name'], PATHINFO_EXTENSION));

        if (!in_array($fileExtension, ['csv', 'txt'])) {
            die("Upload failed. Allowed file types: csv, txt");
        }

        $file = fopen($fileTmpPath, "r");
        if (!$file) die("Could not open file.");

        fgetcsv($file); 

        while (($data = fgetcsv($file, 1000, ",")) !== FALSE) {

            $status       = (int)$data[0];  
            $round        = (int)$data[1];  
            $question_txt = mysqli_real_escape_string($conn, $data[2]);  

            // kapag 0 sa main rounds mag iinsert ang data 
            if ($status == 0) {

                $query = "INSERT INTO questions (question_text, round, question_status)
                          VALUES ('$question_txt', '$round', 0)";
                mysqli_query($conn, $query);
                $question_id = mysqli_insert_id($conn);

                for ($i = 0; $i < 5; $i++) {
                    $answer_txt = mysqli_real_escape_string($conn, $data[3 + $i*2]);  
                    $points     = (int)$data[4 + $i*2];  

                    if (!empty($answer_txt) && $points > 0) {
                        $query2 = "INSERT INTO answers (question_id, answer_text, points)
                                   VALUES ('$question_id', '$answer_txt', '$points')";
                        mysqli_query($conn, $query2);
                    }
                }

            } else {

                $query = "INSERT INTO pre_round_questions (question_text, round, question_status)
                          VALUES ('$question_txt', '$round', 1)";
                mysqli_query($conn, $query);
                $question_id = mysqli_insert_id($conn);

                for ($i = 0; $i < 5; $i++) {
                    $answer_txt = mysqli_real_escape_string($conn, $data[3 + $i*2]);  
                    $points     = (int)$data[4 + $i*2];  
                    if (!empty($answer_txt) && $points > 0) {
                        $query2 = "INSERT INTO pre_round_answers (question_id, answer_text, points)
                                   VALUES ('$question_id', '$answer_txt', '$points')";
                        mysqli_query($conn, $query2);
                    }
                }
            }
        }
        fclose($file);
        echo "CSV successfully imported.";

    } else {
        echo "File upload error.";
    }

} else {
    echo "Invalid request.";
}
?>
