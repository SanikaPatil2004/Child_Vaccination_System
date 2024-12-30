<?php
include("index.php");

// Fetch all data from the VaccinationSchedule table
$query = "SELECT * FROM VaccinationSchedule";
$data = mysqli_query($conn, $query);

// Check if there is any data available
$scheduleFound = mysqli_num_rows($data) > 0;

// Helper function to format age based on thresholds
function formatAge($days) {
    if ($days < 42) {
        return "$days days";
    } elseif ($days < 105) { // 15 weeks * 7 days
        $weeks = round($days / 7, 1);
        return "$weeks weeks";
    } elseif ($days < 600) { // 20 months * 30 days (approximation)
        $months = round($days / 30, 1);
        return "$months months";
    } else {
        $years = round($days / 365, 1); // Approximate conversion to years
        return "$years years";
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
        .schedule-container {
            max-width: 90%;
            margin: 40px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background-color: #f9f9f9;
        }
        .schedule-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .schedule-table th,
        .schedule-table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        .schedule-table th {
            background-color:rgb(74, 154, 241);
            color: white;
            font-weight: bold;
            text-transform: uppercase;
        }
        .schedule-table tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .schedule-table tbody tr:hover {
            background-color:rgb(208, 222, 242);
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="logo"><a href="home.php"><img id="motherchild" src="childm.jpeg" alt="Logo"></a></div>
            <ul class="nav-items">
                <li><a href="home.php">Home</a></li>
                <li><a href="chart.html">VaccineChart</a></li>
                <li><a href="#">About Us</a></li>
                <li>
                    <select id="languageSelect">
                        <option value="English">English</option>
                        <option value="Hindi">Hindi</option>
                        <option value="Marathi">Marathi</option>
                    </select>
                </li>
            </ul>
        </nav>
    </header>
    
    <header>
        <h1>All Vaccination Schedules</h1>
    </header>

    <div class="schedule-container">
        <?php if ($scheduleFound): ?>
            <table class="schedule-table">
                <thead>
                    <tr>
                        <th>Vaccine Name</th>
                        <th>Center</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Minimum Age</th>
                        <th>Maximum Age</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($data)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['vaccine_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['center_name']); ?></td>
                            <td><?php echo date("d-m-Y", strtotime($row['start_date'])); ?></td>
                            <td><?php echo date("d-m-Y", strtotime($row['end_date'])); ?></td>
                            <td><?php echo formatAge($row['min_age']); ?></td>
                            <td><?php echo formatAge($row['max_age']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No vaccination schedules found in the database.</p>
        <?php endif; ?>
    </div>
    
    <footer>
        <p>&copy; 2024 Child Vaccination System. All rights reserved.</p>
    </footer>
</body>
</html>
