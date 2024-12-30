<?php
include('index.php'); // Include database connection file

// Handle AJAX request to save vaccination record
if (isset($_POST['childname']) && isset($_POST['vaccinename'])) {
    $childname = $_POST['childname'];
    $vaccinename = $_POST['vaccinename'];
    $date_of_vaccination = date("Y-m-d");

    // Check if the vaccination record already exists
    $check_sql = "SELECT * FROM vaccination_records WHERE childname = ? AND vaccinename = ? AND date_of_vaccination = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("sss", $childname, $vaccinename, $date_of_vaccination);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    // If record doesn't exist, insert it
    if ($check_result->num_rows == 0) {
        $stmt = $conn->prepare("INSERT INTO vaccination_records (childname, vaccinename, date_of_vaccination) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $childname, $vaccinename, $date_of_vaccination);
        $stmt->execute();
        $stmt->close();
        echo "Vaccinated successfully for $childname on $date_of_vaccination";
    } else {
        echo "This vaccination record already exists for $childname.";
    }

    $check_stmt->close();
    $conn->close();
    exit; // Stop further output since this is an AJAX request
}

// Fetch data from childdetail, VaccinationSchedule, and exclude vaccinated children
$sql = "SELECT 
            c.childname, 
            c.birthdate, 
            DATEDIFF(CURRENT_DATE, c.birthdate) AS days_diff,
            v.vaccine_name
        FROM 
            childdetail c
        LEFT JOIN 
            VaccinationSchedule v ON DATEDIFF(CURRENT_DATE, c.birthdate) BETWEEN v.min_age AND v.max_age
        LEFT JOIN
            vaccination_records vr ON vr.childname = c.childname AND vr.vaccinename = v.vaccine_name
        WHERE 
            vr.childname IS NULL  -- This ensures we only show children who have not been vaccinated for this vaccine
        ORDER BY 
            c.childname";

$result = $conn->query($sql);

// Add some basic CSS styles for the modal popup
echo "<style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f9; color: #333; }
        h1 { text-align: center; color: #007bff; margin-top: 30px; }
        table { width: 80%; margin: 20px auto; border-collapse: collapse; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); background-color: #fff; }
        th, td { padding: 12px; text-align: center; border: 1px solid #ddd; }
        th { background-color: #007bff; color: white; font-size: 18px; }
        td { font-size: 16px; }
        button { padding: 10px 20px; background-color: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
        button:hover { background-color: #218838; }
        .no-scheduled-vaccine { color: #dc3545; font-weight: bold; }
        .message { text-align: center; font-size: 18px; color: green; padding: 10px; background-color: #d4edda; border-radius: 5px; }

        /* Modal styles */
        .modal {
            display: none; 
            position: fixed; 
            z-index: 1; 
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
            background-color: #fff;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 400px;
            text-align: center;
        }
        .close {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
      </style>";

echo "<script>
        function markAsVaccinated(childname, vaccinename, rowId) {
            // Send AJAX request to save vaccination record
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '', true); // Use same page for AJAX
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    // Show the modal with the vaccination success message
                    var modal = document.getElementById('vaccinationModal');
                    var message = document.getElementById('successMessage');
                    message.textContent = xhr.responseText;  // Set the success message
                    modal.style.display = 'block'; // Show the modal
                    if (xhr.responseText.includes('Vaccinated successfully')) {
                        document.getElementById('row-' + rowId).remove();
                    }
                }
            };
            xhr.send('childname=' + encodeURIComponent(childname) + '&vaccinename=' + encodeURIComponent(vaccinename));
        }

        // Close the modal when the user clicks on <span> (x)
        function closeModal() {
            var modal = document.getElementById('vaccinationModal');
            modal.style.display = 'none';
        }
      </script>";

echo "<h1>Child Vaccination Schedule</h1>";

if ($result->num_rows > 0) {
    echo "<table>
            <tr>
                <th>Child Name</th>
                <th>Birthdate</th>
                <th>Age</th>
                <th>Scheduled Vaccine</th>
                <th>Action</th>
            </tr>";
    
    // Output data for each row
    $rowId = 0; // To uniquely identify each row
    while ($row = $result->fetch_assoc()) {
        $name = $row["childname"];
        $birthdate = $row["birthdate"];
        $days_diff = $row["days_diff"];
        $vaccine_name = $row["vaccine_name"] ?? 'No scheduled vaccine';

        // Determine age format based on days_diff
        if ($days_diff < 42) {
            $age = $days_diff . " days";
        } elseif ($days_diff < 98) {
            $age = floor($days_diff / 7) . " weeks";
        } elseif ($days_diff < 577) {
            $age = floor($days_diff / 30.44) . " months";
        } else {
            $age = floor($days_diff / 365.25) . " years";
        }

        echo "<tr id='row-$rowId'>
                <td>" . $name . "</td>
                <td>" . $birthdate . "</td>
                <td>" . $age . "</td>
                <td>" . $vaccine_name . "</td>
                <td>";
        
        // Show "Vaccinated" button if a vaccine is scheduled and the child is not yet vaccinated
        if ($vaccine_name !== 'No scheduled vaccine') {
            echo "<button type='button' onclick=\"markAsVaccinated('$name', '$vaccine_name', '$rowId')\">Vaccinated</button>";
        } else {
            echo "<span class='no-scheduled-vaccine'>No scheduled vaccine</span>";
        }

        echo "</td></tr>";
        $rowId++;
    }
    echo "</table>";
} else {
    echo "<p class='message'>No records found</p>";
}

// Modal for vaccination success
echo "<div id='vaccinationModal' class='modal'>
        <div class='modal-content'>
            <span class='close' onclick='closeModal()'>&times;</span>
            <p id='successMessage'></p>
        </div>
      </div>";

$conn->close();
?>
