<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Child Growth Graph with Diet Suggestions</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            /* margin: 20px; */
            padding: 0;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        form {
            margin-left: 500px;
            text-align: center;
            margin-bottom: 20px;
            background-color: #007bff;
            padding: 15px;
            border-radius: 10px;
            display: inline-block;
            margin-top: 30px;
        }
        form label {
            font-size: 18px;
            color: white;
            margin-right: 10px;
        }
        form input, form button {
            font-size: 16px;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            margin: 5px;
        }
        form input {
            width: 150px;
        }
        form button {
            background-color: #0056b3;
            color: white;
            cursor: pointer;
        }
        form button:hover {
            background-color: #003d80;
        }
        canvas {
            display: block;
            margin: 0 auto;
            max-width: 800px;
        }
        .no-data {
            text-align: center;
            color: red;
            font-weight: bold;
        }
        .suggestion {
            text-align: center;
            margin-top: 20px;
            padding: 15px;
            border-radius: 10px;
            font-size: 18px;
            font-weight: bold;
        }
        .healthy {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .diet-plan {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }
        ul.diet-plan {
            list-style-type: square;
            padding-left: 20px;
        }
        .navbar {
            display: flex;
            height: 80px;
            justify-content: space-between;
            align-items: center;
            background-color: #007bff;
            padding: 15px;
        }
        .nav-items {
            list-style: none;
            display: flex;
            gap: 15px;
        }
        .nav-items li a {
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 5px;
        }
        .nav-items li a:hover {
            background-color: #0056b3;
        }
        #languageSelect {
            padding: 5px;
            border-radius: 10px;
        }
        #motherchild {
            height: 50px;
        }
    </style>
</head>
<body>
<header>
    <nav class="navbar">
        <div class="logo"><a href="home.php"><img id="motherchild" src="childm.jpeg"></a></div>
        <ul class="nav-items">
            <li><a href="#">Home</a></li>
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
<h2>View Child Growth Over Time</h2>
<form id="fetchDataForm" method="POST">
    <label for="childId">Enter Child ID:</label>
    <input type="number" id="childId" name="childId" required>
    <button type="submit">Show Graph</button>
</form>

<canvas id="growthChart" width="800" height="400"></canvas>
<div id="suggestion"></div>

    <?php
    // Include database connection
    include("index.php"); // Assumes index.php connects to your database

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['childId'])) {
        $childId = $_POST['childId'];

        // Query to fetch initial details from `childdetail`
        $query1 = "SELECT birthdate AS date, weight, height FROM childdetail WHERE id = ?";
        $stmt1 = $conn->prepare($query1);
        $stmt1->bind_param("i", $childId);
        $stmt1->execute();
        $result1 = $stmt1->get_result();

        // Query to fetch updates from `childdetailupdate`
        $query2 = "SELECT date, weight, height FROM childdetailupdate WHERE id = ?";
        $stmt2 = $conn->prepare($query2);
        $stmt2->bind_param("i", $childId);
        $stmt2->execute();
        $result2 = $stmt2->get_result();

        // Initialize arrays to store the combined data
        $dates = [];
        $weights = [];
        $heights = [];

        // Add data from `childdetail`
        if ($result1->num_rows > 0) {
            while ($row = $result1->fetch_assoc()) {
                $dates[] = $row['date'];
                $weights[] = $row['weight'];
                $heights[] = $row['height'];
            }
        }

        // Add data from `childdetailupdate`
        if ($result2->num_rows > 0) {
            while ($row = $result2->fetch_assoc()) {
                $dates[] = $row['date'];
                $weights[] = $row['weight'];
                $heights[] = $row['height'];
            }
        }

        // Sort the data by date
        array_multisort($dates, SORT_ASC, $weights, $heights);

        // Analyze growth trends
        $isHealthy = true;
        $hasGrowth = false;
        $weightTrend = 0;
        $heightTrend = 0;

        for ($i = 1; $i < count($weights); $i++) {
            $weightTrend += $weights[$i] - $weights[$i - 1];
            $heightTrend += $heights[$i] - $heights[$i - 1];
        }

        if ($weightTrend <= 0 && $heightTrend <= 0) {
            $isHealthy = false;
            $hasGrowth = false;
        } elseif ($weightTrend > 0 || $heightTrend > 0) {
            $hasGrowth = true;
        }

        // Close connections
        $stmt1->close();
        $stmt2->close();
        $conn->close();
    }
    ?>

    <script>
        // Get PHP data from the backend
        const dates = <?php echo json_encode($dates ?? []); ?>;
        const weights = <?php echo json_encode($weights ?? []); ?>;
        const heights = <?php echo json_encode($heights ?? []); ?>;
        const isHealthy = <?php echo isset($isHealthy) ? json_encode($isHealthy) : "null"; ?>;
        const hasGrowth = <?php echo isset($hasGrowth) ? json_encode($hasGrowth) : "null"; ?>;

        // Check if there is data to display
        if (dates.length > 0) {
            const ctx = document.getElementById('growthChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: dates, // X-axis: Dates
                    datasets: [
                        {
                            label: 'Weight (kg)',
                            data: weights,
                            borderColor: 'blue',
                            borderWidth: 2,
                            fill: false,
                            tension: 0.4, // Makes the graph smooth
                        },
                        {
                            label: 'Height (ft)',
                            data: heights,
                            borderColor: 'green',
                            borderWidth: 2,
                            fill: false,
                            tension: 0.4, // Makes the graph smooth
                        },
                    ],
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                font: { size: 14 },
                            },
                        },
                        title: {
                            display: true,
                            text: 'Child Growth Over Time',
                            font: { size: 18 },
                        },
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Date',
                                font: { size: 16 },
                            },
                            ticks: {
                                font: { size: 12 },
                            },
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Value',
                                font: { size: 16 },
                            },
                            ticks: {
                                font: { size: 12 },
                            },
                        },
                    },
                },
            });

            // Display suggestion based on health status
            const suggestionDiv = document.getElementById('suggestion');
            if (isHealthy && hasGrowth) {
                suggestionDiv.innerHTML = '<p class="suggestion healthy">Your child is growing well! Keep up their current diet and care.</p>';
            } else if (!hasGrowth) {
                suggestionDiv.innerHTML = `
                    <p class="suggestion diet-plan">There has been no growth. Consider these diet tips:</p>
                    <ul class="diet-plan">
                        <li>Ensure proper protein intake: eggs, chicken, fish, lentils.</li>
                        <li>Include iron-rich foods: spinach, broccoli, beans.</li>
                        <li>Serve calcium-rich foods: milk, yogurt, cheese.</li>
                        <li>Add healthy fats: avocados, nuts, and seeds.</li>
                        <li>Encourage frequent meals and healthy snacks.</li>
                    </ul>
                `;
            } else {
                suggestionDiv.innerHTML = `
                    <p class="suggestion diet-plan">Your child is growing slowly. Improve their diet with balanced meals.</p>
                `;
            }
        } else {
            document.write('<p class="no-data">No data available for the given Child ID.</p>');
        }
    </script>
</body>
</html>
