<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Child Details Form</title>
    <link rel="stylesheet" href="home.css">
    <link rel="stylesheet" href="childdetail.css">
    <style>
        /* General Popup container style */
        .popup, .confirmation-popup, .error-popup {
            display: none; /* Hidden by default */
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }

        /* Popup content style */
        .popup-content, .confirmation-content, .error-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #fff;
            padding: 20px;
            width: 300px;
            text-align: center;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            position: relative; /* For positioning the close icon */
        }

        .popup-content h4, .confirmation-content h4, .error-content h4 {
            color: green;
            margin-bottom: 15px;
        }

        /* Error message style */
        .error-content h4 {
            color: red;
        }

        /* Close button style as "X" icon */
        .close-icon {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 20px;
            color: #333;
            cursor: pointer;
        }

        .close-icon:hover {
            color: #c9302c;
        }

        /* Buttons for Add and Update */
        .action-buttons {
            text-align: center;
            margin: 20px;
        }

        .action-buttons button {
            padding: 10px 20px;
            margin: 0 10px;
            cursor: pointer;
            border-radius: 4px;
        }

        .add-btn {
            background-color: #5cb85c;
            color: white;
        }

        .update-btn {
            background-color: #0275d8;
            color: white;
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
    
    <div class="action-buttons">
        <button class="add-btn" onclick="showAddPopup()">Add Child Detail</button>
        <button class="update-btn" onclick="showUpdatePopup()">Update Detail</button>
    </div>

    <!-- Add Child Detail Popup -->
    <div class="popup" id="addPopup">
        <div class="popup-content">
            <span class="close-icon" onclick="closeAddPopup()">&#10005;</span>
            <h4>Add Child Detail</h4>
            <form id="add-child-form" action="#" method="POST" onsubmit="return validateBirthDate()">
                <label for="email">Email:</label>
                <input type="text" id="email" name="email" required>
                
                <label for="child-name">Child Name:</label>
                <input type="text" id="child-name" name="child-name" required>
                
                <label for="birth-date">Birth Date:</label>
                <input type="date" id="birth-date" name="birth-date" required>
                
                <label for="weight">Weight (kg):</label>
                <input type="number" id="weight" name="weight" required>
                
                <label for="height">Height (ft):</label>
                <input type="number" id="height" name="height" required>
                
                <input class="button" type="submit" name="submit-add" value="Submit">
            </form>
        </div>
    </div>

    <!-- Update Detail Popup -->
    <div class="popup" id="updatePopup">
        <div class="popup-content">
            <span class="close-icon" onclick="closeUpdatePopup()">&#10005;</span>
            <h4>Update Child Detail</h4>
            <form id="update-child-form" action="#" method="POST">
                <label for="update-child-name">Id:</label>
                <input type="number" id="update-child-name" name="child-name" required>
                
                <label for="update-weight">Weight (kg):</label>
                <input type="number" id="update-weight" name="weight" required>
                
                <label for="update-height">Height (ft):</label>
                <input type="number" id="update-height" name="height" required>
                
                <input class="button" type="submit" name="submit-update" value="Update">
            </form>
        </div>
    </div>

    <!-- Confirmation Popup -->
    <div class="confirmation-popup" id="confirmationPopup">
        <div class="confirmation-content">
            <span class="close-icon" onclick="closeConfirmationPopup()">&#10005;</span>
            <h4 id="confirmationMessage"></h4>
        </div>
    </div>

    <!-- Error Popup for Invalid Birth Date -->
    <div class="error-popup" id="errorPopup">
        <div class="error-content">
            <span class="close-icon" onclick="closeErrorPopup()">&#10005;</span>
            <h4>Birth date cannot be a future date. Please select a valid date.</h4>
        </div>
    </div>

    <footer>
        <p>&copy; 2024 Child Vaccination System. All rights reserved.</p>
    </footer>

    <script>
        function showAddPopup() {
            document.getElementById("addPopup").style.display = "block";
        }

        function closeAddPopup() {
            document.getElementById("addPopup").style.display = "none";
        }

        function showUpdatePopup() {
            document.getElementById("updatePopup").style.display = "block";
        }

        function closeUpdatePopup() {
            document.getElementById("updatePopup").style.display = "none";
        }

        function showConfirmationPopup(message) {
            document.getElementById("confirmationMessage").innerText = message;
            document.getElementById("confirmationPopup").style.display = "block";
        }

        function closeConfirmationPopup() {
            document.getElementById("confirmationPopup").style.display = "none";
        }

        function showErrorPopup() {
            document.getElementById("errorPopup").style.display = "block";
        }

        function closeErrorPopup() {
            document.getElementById("errorPopup").style.display = "none";
        }

        // Validation for birth date to ensure it is not in the future
        function validateBirthDate() {
            const birthDateInput = document.getElementById('birth-date').value;
            const selectedDate = new Date(birthDateInput);
            const currentDate = new Date();

            if (selectedDate > currentDate) {
                showErrorPopup(); // Show error popup instead of alert
                return false; // Prevent form submission
            }
            return true; // Allow form submission
        }
    </script>
</body>
</html>


<?php
include("index.php");  // Assumes index.php establishes a database connection

if (isset($_POST['submit-add'])) {
    $email = $_POST['email'];
    $childname = $_POST['child-name'];
    $birthdate = $_POST['birth-date'];
    $weight = $_POST['weight'];
    $height = $_POST['height'];

    $query = "INSERT INTO childdetail (email, childname, birthdate, weight, height) VALUES ('$email', '$childname', '$birthdate', '$weight', '$height')";

    if (mysqli_query($conn, $query)) {
        echo "<script>showConfirmationPopup('Child details added successfully');</script>";
    } else {
        echo "<script>showConfirmationPopup('Failed to add child details');</script>";
    }
}

if (isset($_POST['submit-update'])) {
    $Id = $_POST['child-name'];
    $weight = $_POST['weight'];
    $height = $_POST['height'];

    $query = "INSERT INTO childdetailupdate (id, weight, height) VALUES ('$Id', '$weight', '$height')";
    if (mysqli_query($conn, $query)) {
        echo "<script>showConfirmationPopup('Child details updated successfully');</script>";
    } else {
        echo "<script>showConfirmationPopup('Failed to update child details');</script>";
    }
}
?>
