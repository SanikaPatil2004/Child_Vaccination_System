<?php
include('index.php');
require 'vendor/autoload.php'; // Load Twilio SDK

use Twilio\Rest\Client;

// Twilio API credentials (replace with your actual credentials)
$account_sid = 'AC06f534419f2453c89787eb87d8d02b33';
$auth_token = '89c91de4c358218691ffcf0e30cff192';
$twilio_number = '+12402452434'; // Your Twilio phone number

$client = new Client($account_sid, $auth_token);

// Query to fetch child details with relevant vaccine details, mobile number, and SMS status
$sql = "SELECT 
            c.childname, 
            c.birthdate, 
            DATEDIFF(CURRENT_DATE, c.birthdate) AS days_diff,
            v.vaccine_name,
            CONCAT('+91', r.mobno) AS mobno,
            c.sms_sent
        FROM 
            childdetail c
        LEFT JOIN 
            VaccinationSchedule v 
        ON 
            DATEDIFF(CURRENT_DATE, c.birthdate) BETWEEN v.min_age AND v.max_age
        LEFT JOIN 
            regi r 
        ON 
            c.email = r.email
        WHERE 
            (c.sms_sent = 0 OR c.sms_sent IS NULL)  -- Select only unsent notifications
        ORDER BY 
            c.childname";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table border='1'>
            <tr>
                <th>Child Name</th>
                <th>Birthdate</th>
                <th>Age</th>
                <th>Vaccine</th>
                <th>Mobile Number</th>
                <th>SMS Status</th>
            </tr>";
    
    // Output data for each row
    while ($row = $result->fetch_assoc()) {
        $name = $row["childname"];
        $birthdate = $row["birthdate"];
        $days_diff = $row["days_diff"];
        $vaccine_name = $row["vaccine_name"] ?? 'No scheduled vaccine';
        $mobno = $row["mobno"];
        $sms_sent = $row["sms_sent"];

        // Determine age format based on days_diff
        if ($days_diff < 98) { // Less than 14 weeks
            $age = floor($days_diff / 7) . " weeks";
        } elseif ($days_diff < 577) { // Between 14 weeks and 19 months
            $age = floor($days_diff / 30.44) . " months";
        } else { // 19 months or more
            $age = floor($days_diff / 365.25) . " years";
        }

        echo "<tr>
                <td>" . $name . "</td>
                <td>" . $birthdate . "</td>
                <td>" . $age . "</td>
                <td>" . $vaccine_name . "</td>
                <td>" . $mobno . "</td>
                <td>" . ($sms_sent ? 'Sent' : 'Not Sent') . "</td>
              </tr>";

        // Send SMS if a vaccine is scheduled and SMS has not been sent yet
        if ($vaccine_name !== 'No scheduled vaccine' && !$sms_sent) {
            $message = "Hello, the vaccine '$vaccine_name' is scheduled for $name. Please ensure timely vaccination.";

            try {
                // Send the SMS
                $client->messages->create(
                    $mobno,
                    [
                        'from' => $twilio_number,
                        'body' => $message
                    ]
                );
                
                // Update the sms_sent status in the database
                $update_sql = "UPDATE childdetail SET sms_sent = 1 WHERE childname = ? AND birthdate = ?";
                $stmt = $conn->prepare($update_sql);
                $stmt->bind_param("ss", $name, $birthdate);
                $stmt->execute();

                echo "<p>SMS sent to $mobno for $name regarding $vaccine_name.</p>";
            } catch (Exception $e) {
                echo "<p>Failed to send SMS to $mobno: " . $e->getMessage() . "</p>";
            }
        }
    }
    echo "</table>";
} else {
    echo "No records found";
}

$conn->close();
?>
