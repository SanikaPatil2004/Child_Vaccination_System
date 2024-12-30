<?php
include("index.php");

// Initialize variables
$ageInDays = 0;
$displayUnit = 'days'; // Default unit
$vaccineScheduleFound = false; // Flag to check if a schedule is found
$vaccinatedMessage = false; // Flag to check if the child is vaccinated for all vaccines

// Check if the form was submitted and fetch child’s DOB based on email
if (isset($_POST['email'])) {
    $email = $_POST['email'];

    // Query to get the child's date of birth and name based on the provided email
    $dobQuery = "SELECT childname, birthdate FROM childdetail WHERE email = '$email'";
    $dobResult = mysqli_query($conn, $dobQuery);

    // Check if the email exists in the database
    if ($dobResult && mysqli_num_rows($dobResult) > 0) {
        $row = mysqli_fetch_assoc($dobResult);
        $dob = $row['birthdate'];
        $childname = $row['childname'];

        // Calculate age in days based on the date of birth
        $dobDate = new DateTime($dob);
        $currentDate = new DateTime();
        $ageInDays = $dobDate->diff($currentDate)->days;

        // Determine the display unit based on age in days
        if ($ageInDays <= 42) {
            $displayUnit = 'days';
        } elseif ($ageInDays <= 98) { // 14 weeks * 7 days
            $displayUnit = 'weeks';
        } elseif ($ageInDays <= 570) { // 19 months * 30 days
            $displayUnit = 'months';
        } else {
            $displayUnit = 'years';
        }

        // Fetch data from the VaccinationSchedule table excluding vaccinated records
        $query = "
            SELECT vs.*
            FROM VaccinationSchedule vs
            LEFT JOIN vaccination_records vr 
            ON vs.vaccine_name = vr.vaccinename AND vr.childname = '$childname'
            WHERE min_age <= $ageInDays AND $ageInDays < max_age AND vr.vaccinename IS NULL
        ";
        $data = mysqli_query($conn, $query);

        // Check if there are any vaccines in the schedule
        if (mysqli_num_rows($data) > 0) {
            $vaccineScheduleFound = true;
        } else {
            $vaccinatedMessage = true; // Child has been vaccinated for all vaccines in this age group
        }
    } else {
        echo "<div id='noScheduleModal' class='modal'>
            <div class='modal-content'>
                <span class='close'>&times;</span>
                <p>No child record found for this email.</p>
            </div>
        </div>";
    }
}

// Helper function to convert days to selected unit
function convertToUnit($days, $unit) {
    switch ($unit) {
        case 'weeks':
            return round($days / 7, 1);
        case 'months':
            return round($days / 30, 1);
        case 'years':
            return round($days / 365, 1);
        default: // days
            return $days;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vaccination Schedule</title>
    <link rel="stylesheet" href="home.css">
    <link rel="stylesheet" href="schedule.css">
    <style>
        .age-input-form { display: flex; justify-content: center; margin: 20px; }
        .age-input-form input, .age-input-form button { padding: 10px; margin-right: 10px; font-size: 16px; border: 1px solid #ccc; border-radius: 5px; }
        .age-input-form button { background-color: #5cb85c; color: white; cursor: pointer; transition: background-color 0.3s; }
        .age-input-form button:hover { background-color: #4cae4c; }
        .schedule-form { max-width: 600px; margin: 0 auto; padding: 20px; background: #f7f7f7; border-radius: 8px; border: 1px solid #ddd; }
        .schedule-entry { margin-bottom: 20px; padding: 10px; background: #fff; border: 1px solid #ddd; border-radius: 8px; }
        .modal { display: none; position: fixed; z-index: 1; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0, 0, 0, 0.4); align-items: center; justify-content: center; }
        .modal-content { background-color: #fefefe; margin: auto; padding: 20px; border: 1px solid #888; width: 80%; max-width: 400px; border-radius: 8px; }
        .close { color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer; }
        .close:hover, .close:focus { color: black; }
    </style>
</head>
<body>
    <header>
        <h1>
            <?php 
            if ($vaccineScheduleFound) {
                echo "Vaccines Scheduled for Your Child"; 
            } elseif ($vaccinatedMessage) {
                echo "Your Child is Fully Vaccinated for this Age Group";
            } else {
                echo "Vaccination Schedule";
            }
            ?>
        </h1>
    </header>

    <div class="age-input-form">
        <form method="POST" action="">
            <label for="email">Enter Your Email:</label>
            <input type="email" name="email" id="email" required>
            <button type="submit">Show Schedule</button>
        </form>
    </div>

    <?php if ($vaccineScheduleFound): ?>
        <div class="schedule-form">
            <?php while ($row = mysqli_fetch_assoc($data)): ?>
                <div class="schedule-entry">
                    <label>Vaccine Name</label>
                    <input type="text" value="<?php echo $row['vaccine_name']; ?>" readonly>
                    <label>Center</label>
                    <input type="text" value="<?php echo $row['center_name']; ?>" readonly>
                    <label>Start Date</label>
                    <input type="text" value="<?php echo date("d-m-Y", strtotime($row['start_date'])); ?>" readonly>
                    <label>End Date</label>
                    <input type="text" value="<?php echo date("d-m-Y", strtotime($row['end_date'])); ?>" readonly>
                </div>
            <?php endwhile; ?>
        </div>
    <?php elseif ($vaccinatedMessage): ?>
        <div id="vaccinatedModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <p>Your child is fully vaccinated for this age group.</p>
            </div>
        </div>
    <?php endif; ?>

    <footer>
        <p>&copy; 2024 Child Vaccination System. All rights reserved.</p>
    </footer>

    <script>
        <?php if ($vaccinatedMessage): ?>
            document.getElementById("vaccinatedModal").style.display = "flex";
        <?php endif; ?>
        document.querySelector(".close").onclick = function() {
            document.getElementById("vaccinatedModal").style.display = "none";
        };
        window.onclick = function(event) {
            var modal = document.getElementById("vaccinatedModal");
            if (event.target == modal) {
                modal.style.display = "none";
            }
        };
    </script>
</body>
</html>
