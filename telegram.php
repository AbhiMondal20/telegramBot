<?php
session_start();

include('../db_conn.php');

$sql = "SELECT user.name AS dr_name, patient.id AS id, patient.name AS name, patient.email AS email, patient.mobile AS mobile, patient.gender AS gender, patient.address AS address, patient.appt_time AS appt_time, patient.status AS status, dept.name AS dept_name, schedule.start AS appt_date FROM patient 
        lEFT JOIN user ON patient.user_id = user.id
        LEFT JOIN dept ON patient.dept_id = dept.id
        LEFT JOIN schedule ON patient.appt_date = schedule.id
        ORDER BY patient.id DESC LIMIT 1";
$res = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($res);

$name = $row['name'];
$p_email = $row['email'];
$mobile = $row['mobile'];
$gender = $row['gender'];

$old_date = $row["appt_date"];
$middle = strtotime($old_date);
$appt_date = date("d-m-Y ", $middle);
$appt_time = $row['appt_time'];
$address = $row['address'];
$status = $row['status'];

// User table 
$dr_name = $row['dr_name'];

// Dept Table   
$dept_name = $row['dept_name'];

if (
    isset($_SESSION['last_patient_data']) &&
    $_SESSION['last_patient_data']['name'] === $name &&
    $_SESSION['last_patient_data']['email'] === $p_email &&
    $_SESSION['last_patient_data']['mobile'] === $mobile &&
    $_SESSION['last_patient_data']['appt_date'] === $appt_date &&
    $_SESSION['last_patient_data']['appt_time'] === $appt_time
) {
    // echo "Same patient data already sent. Skipping message sending.";
} else {
    $apiToken = '6694521778:AAH1j7D6Qj3t7HHEqnSvMgNr8a993TN1NA8';

    // --bot chat id
    // $chatId = '1486093575'; 

    // -- Group Chat Id
    $chatId = '-953039465'; 
    $message = 'Appointment Booked Successfully';
    $apiUrl = "https://api.telegram.org/bot$apiToken/sendMessage";
    $data = array(
        'chat_id' => $chatId,
        'text' => $message . "\n Patient Name: " . $name . "\n Email: " . $p_email . "\n Mobile: " . $mobile . "\n Appointment Date: " . $appt_date . "\n Appointment Time: " . $appt_time . "\n Address: " . $address . "\n Doctor: " . $dr_name . "\n Department: " . $dept_name,
    );
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'cURL error: ' . curl_error($ch);
    } else {
        $result = json_decode($response, true);

        if ($result['ok']) {
            echo 'Message sent successfully!';
        } else {
            echo 'Error: ' . $result['description'];
        }
    }

    curl_close($ch);

    $_SESSION['last_patient_data'] = array(
        'name' => $name,
        'email' => $p_email,
        'mobile' => $mobile,
        'appt_date' => $appt_date,
        'appt_time' => $appt_time,
    );
}
?>

<script>
    function autoReload() {
        setTimeout(function() {
            location.reload();
        }, 10000); // 10000 milliseconds (10 seconds)
    }

    window.addEventListener('load', autoReload);
</script>
