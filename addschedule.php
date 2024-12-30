<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vaccination Form</title>
    <link rel="stylesheet" href="addschedule.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: #f7f7f7;
        }
        .container {
            margin-top: 20px;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
        }
        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }
        .button {
            padding: 10px 20px;
            margin: 10px;
            border: none;
            border-radius: 5px;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .button:hover {
            opacity: 0.9;
        }
        .add-button {
            background-color: #4CAF50;
        }
        .update-button {
            background-color: #007BFF;
        }
        .input-field {
            display: flex;
            align-items: center;
        }
        .input-field input[type="number"] {
            width: 70%;
            padding: 8px;
            border-radius: 5px 0 0 5px;
            border: 1px solid #ccc;
            font-size: 16px;
        }
        .input-field select {
            width: 30%;
            padding: 8px;
            border-radius: 0 5px 5px 0;
            border: 1px solid #ccc;
            border-left: none;
            font-size: 16px;
            background-color: #f1f1f1;
        }
        .hidden {
            display: none;
        }
        /* Modal styles */
        .modal {
            display: none; 
            position: fixed; 
            z-index: 1000; 
            left: 0;
            top: 0;
            width: 100%; 
            height: 100%; 
            overflow: auto; 
            background-color: rgb(0,0,0); 
            background-color: rgba(0,0,0,0.4); 
            padding-top: 60px; 
        }
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto; 
            padding: 20px;
            border: 1px solid #888;
            width: 80%; 
            max-width: 400px;
            text-align: center;
            border-radius: 5px;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <header>
        <nav class="navbar">
            <ul class="nav-items">
                <li><a href="admin.php">Home</a></li>
                <!-- <li><a href="#">Add Schedule</a></li> -->
                <li><a id="loginBtn" href="#">Login</a></li>
            </ul>
        </nav>
    </header>
    
    <div class="container">
        <h2>Vaccination Schedule Management</h2>
        <!-- Action Selection Buttons -->
        <button class="button add-button" onclick="showAddForm()">Add Vaccine</button>
        <button class="button update-button" onclick="showUpdateForm()">Update Vaccine</button>

        <!-- Add Vaccine Form -->
        <form id="addVaccineForm" class="hidden" action="#" method="POST">
            <h3>Add Vaccine</h3>
            <div class="form-group">
                <label>Vaccine Name</label>
                <input type="text" name="vaccine" required>
            </div>
            <div class="form-group">
                <label>Center Name</label>
                <input type="text" name="center" required>
            </div>
            <div class="form-group">
                <label>Start Date</label>
                <input type="date" name="from" required>
            </div>
            <div class="form-group">
                <label>End Date</label>
                <input type="date" name="to" required>
            </div>
            <div class="form-group">
                <label>Minimum Age</label>
                <div class="input-field">
                    <input type="number" name="min_age" required>
                    <select name="min_age_unit" required>
                        <option value="days">Days</option>
                        <option value="weeks">Weeks</option>
                        <option value="months">Months</option>
                        <option value="years">Years</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label>Maximum Age</label>
                <div class="input-field">
                    <input type="number" name="max_age" required>
                    <select name="max_age_unit" required>
                        <option value="days">Days</option>
                        <option value="weeks">Weeks</option>
                        <option value="months">Months</option>
                        <option value="years">Years</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <button type="submit" name="add_vaccine" class="button add-button">Add Vaccine</button>
            </div>
        </form>

        <!-- Update Vaccine Form -->
        <form id="updateVaccineForm" class="hidden" action="#" method="POST">
            <h3>Update Vaccine</h3>
            <div class="form-group">
                <label>Vaccine Name</label>
                <input type="text" name="vaccine" required>
            </div>
            <div class="form-group">
                <label>Center Name</label>
                <input type="text" name="center" required>
            </div>
            <div class="form-group">
                <label>Start Date</label>
                <input type="date" name="from" required>
            </div>
            <div class="form-group">
                <label>End Date</label>
                <input type="date" name="to" required>
            </div>
            <div class="form-group">
                <button type="submit" name="update_vaccine" class="button update-button">Update Vaccine</button>
            </div>
        </form>

        <div id="output"></div>
    </div>

    <!-- Modal for messages -->
    <div id="messageModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <p id="modalMessage"></p>
        </div>
    </div>
    
    <footer>
        <p>&copy; 2024 Child Vaccination System. All rights reserved.</p>
    </footer>

    <script>
        // Function to show Add Vaccine form and hide Update Vaccine form
        function showAddForm() {
            document.getElementById('addVaccineForm').classList.remove('hidden');
            document.getElementById('updateVaccineForm').classList.add('hidden');
        }

        // Function to show Update Vaccine form and hide Add Vaccine form
        function showUpdateForm() {
            document.getElementById('addVaccineForm').classList.add('hidden');
            document.getElementById('updateVaccineForm').classList.remove('hidden');
        }

        // Function to open the modal
        function openModal(message) {
            document.getElementById('modalMessage').innerText = message;
            document.getElementById('messageModal').style.display = 'block';
        }

        // Function to close the modal
        function closeModal() {
            document.getElementById('messageModal').style.display = 'none';
        }

        // Close the modal when the user clicks anywhere outside of it
        window.onclick = function(event) {
            var modal = document.getElementById('messageModal');
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>

<?php
include("index.php");
$message = ''; // Initialize message variable

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['vaccine'];
    $center = $_POST['center'];

    // Use DateTime to format the dates into SQL format
    $fromdate = (new DateTime($_POST['from']))->format('Y-m-d');
    $todate = (new DateTime($_POST['to']))->format('Y-m-d');

    // Handle add vaccine request
    if (isset($_POST['add_vaccine'])) {
        $min_age = (int)$_POST['min_age'];
        $max_age = (int)$_POST['max_age'];
        $min_age_unit = $_POST['min_age_unit'];
        $max_age_unit = $_POST['max_age_unit'];

        // Convert min and max age to days
        $min_age_days = convertAgeToDays($min_age, $min_age_unit);
        $max_age_days = convertAgeToDays($max_age, $max_age_unit);

        // Prepare the query
        $query = "INSERT INTO VaccinationSchedule (vaccine_name, center_name, start_date, end_date, min_age, max_age) VALUES ('$name', '$center', '$fromdate', '$todate', '$min_age_days', '$max_age_days')";
        $data = mysqli_query($conn, $query);
        $message = $data ? "Vaccine added successfully." : "Failed to add vaccine.";
    }

    // Handle update vaccine request
    elseif (isset($_POST['update_vaccine'])) {
        $query = "UPDATE VaccinationSchedule SET center_name='$center', start_date='$fromdate', end_date='$todate'
                  WHERE vaccine_name='$name'";
        $data = mysqli_query($conn, $query);
        $message = $data ? "Vaccine updated successfully." : "Failed to update vaccine.";
    }

    // JavaScript to show message
    echo "<script>openModal('$message');</script>";
}

// Function to convert age to days
function convertAgeToDays($age, $unit) {
    switch ($unit) {
        case 'days': return $age;
        case 'weeks': return $age * 7;
        case 'months': return $age * 30; // Note: average month is considered as 30 days
        case 'years': return $age * 365;  // Note: average year is considered as 365 days
        default: return 0;
    }
}
?>
