<?php
include("index.php"); // Include database connection

// Initialize variables
$userEmail = "";
$personalData = null;
$vaccinationData = null;

// Check if form is submitted and email is provided
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['email'])) {
    $userEmail = $_POST['email'];
    
    // Query to fetch personal details for the provided email
    $query = "SELECT * FROM childdetail WHERE email = '$userEmail'";
    $personalData = mysqli_query($conn, $query);

    // Query to fetch vaccination details for the provided email
    $vaccinationQuery = "SELECT * FROM vaccination_records WHERE childname IN (SELECT childname FROM childdetail WHERE email = '$userEmail')";
    $vaccinationData = mysqli_query($conn, $vaccinationQuery);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Child Details</title>
    <link rel="stylesheet" href="seedetails.css">
    <link rel="stylesheet" href="home.css">
    <style>
        /* General styling for the body and page */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            height: 100%;
            display: flex;
            flex-direction: column;
            overflow-y: auto; /* Enable scrolling on the body */
        }

        /* Styling for buttons */
        .button-container {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .button-container button {
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
            font-weight: bold;
            margin-right: 10px;
        }

        .button-container button:hover {
            background-color: #45a049;
        }

        /* Styling for input field and form */
        .search-container {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .search-container input[type="email"] {
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 300px;
            margin-right: 10px;
        }

        .search-container button {
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
            font-weight: bold;
        }

        .search-container button:hover {
            background-color: #45a049;
        }

        /* Styling for displaying child details in form format */
        .details-container {
            align-items: center;
            margin-top: 20px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            width: 350px;
            background-color: #f9f9f9;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-left: 600px;
            overflow-y: auto;  /* Enable scrolling within this container */
            max-height: 400px; /* Limit the height */
        }

        .detail-item {
            margin-bottom: 15px;
            width: 100%;
        }

        .detail-item label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
            color: #333;
        }

        .detail-item span {
            display: block;
            padding: 8px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            color: #333;
        }

        .pdf-button {
            margin-top: 20px;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            background-color: #2196F3;
            color: #fff;
            cursor: pointer;
            font-weight: bold;
            margin-left: 20px;
        }

        .pdf-button:hover {
            background-color: #0b7dda;
        }
    </style>
</head>
<body>
    <header>
    <nav class="navbar">
            <div class="logo"><a href="home.php"><img id ="motherchild" src="childm.jpeg"></a></div>
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

    <!-- Form to Search Child Details by Email -->
    <h1>Search Child Details</h1>
    <div class="search-container">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <label for="email">Enter Email:</label>
            <input type="email" id="email" name="email" required placeholder="Enter email to search" value="<?php echo htmlspecialchars($userEmail); ?>">
            <button type="submit">Search</button>
        </form>
    </div>

    <!-- Button Container for Toggle between Personal and Vaccination Details -->
    <div class="button-container">
        <button id="personalDetailsBtn" onclick="showPersonalDetails()">Personal Details</button>
        <button id="vaccinationDetailsBtn" onclick="showVaccinationDetails()">Vaccination Details</button>
    </div>

    <!-- Display Personal Details -->
    <div id="personalDetails" class="details-container" style="display: none;">
        <?php if (isset($personalData) && mysqli_num_rows($personalData) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($personalData)): ?>
                <div class="detail-item">
                    <label>ID:</label>
                    <span><?php echo htmlspecialchars($row['id']); ?></span>
                </div>
                <div class="detail-item">
                    <label>Child Name:</label>
                    <span><?php echo htmlspecialchars($row['childname']); ?></span>
                </div>
                <div class="detail-item">
                    <label>Birth Date:</label>
                    <span><?php echo date("d-m-Y", strtotime($row['birthdate'])); ?></span>
                </div>
                <div class="detail-item">
                    <label>Weight (kg):</label>
                    <span><?php echo htmlspecialchars($row['weight']); ?></span>
                </div>
                <div class="detail-item">
                    <label>Height (ft):</label>
                    <span><?php echo htmlspecialchars($row['height']); ?></span>
                </div>
            <?php endwhile; ?>
            <button class="pdf-button" onclick="generatePersonalDetailsPDF()">Download Personal Details as PDF</button>
        <?php else: ?>
            <p>No details found for this email.</p>
        <?php endif; ?>
    </div>

    <!-- Display Vaccination Details -->
    <div id="vaccinationDetails" class="details-container" style="display: none;">
        <?php if (isset($vaccinationData) && mysqli_num_rows($vaccinationData) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($vaccinationData)): ?>
                <div class="detail-item">
                    <label>Vaccination Name:</label>
                    <span><?php echo htmlspecialchars($row['vaccinename']); ?></span>
                </div>
                <div class="detail-item">
                    <label>Date of Vaccination:</label>
                    <span><?php echo date("d-m-Y", strtotime($row['date_of_vaccination'])); ?></span>
                </div>
            <?php endwhile; ?>
            <button class="pdf-button" onclick="generateVaccinationDetailsPDF()">Download Vaccination Details as PDF</button>
        <?php else: ?>
            <p>No vaccination details found for this email.</p>
        <?php endif; ?>
    </div>

    <footer>
        <p>&copy; 2024 Child Vaccination System. All rights reserved.</p>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
    <script>
        // Show Personal Details Section
        function showPersonalDetails() {
            document.getElementById("personalDetails").style.display = "block";
            document.getElementById("vaccinationDetails").style.display = "none";
        }

        // Show Vaccination Details Section
        function showVaccinationDetails() {
            document.getElementById("vaccinationDetails").style.display = "block";
            document.getElementById("personalDetails").style.display = "none";
        }

        // Generate PDF for Personal Details
        function generatePersonalDetailsPDF() {
            const element = document.getElementById('personalDetails');
            const opt = {
                margin:       0.5,
                filename:     'Personal_Details.pdf',
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2 },
                jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
            };
            html2pdf().set(opt).from(element).save();
        }

        // Generate PDF for Vaccination Details
        function generateVaccinationDetailsPDF() {
            const element = document.getElementById('vaccinationDetails');
            const opt = {
                margin:       0.5,
                filename:     'Vaccination_Details.pdf',
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2 },
                jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
            };
            html2pdf().set(opt).from(element).save();
        }

        // Automatically show the personal details section if available
        window.onload = function() {
            if (document.getElementById("personalDetails").style.display === "none" && document.getElementById("vaccinationDetails").style.display === "none") {
                showPersonalDetails(); // Default to Personal Details
            }
        };
    </script>
</body>
</html> 