<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Child Vaccination System</title>
    <link rel="stylesheet" href="home.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="logo"><a href="home.php"><img id ="motherchild" src="childm.jpeg"></a></div>
            <ul class="nav-items">
                <li><a href="#">Home</a></li>
                <li><a href="chart.html">VaccineChart</a></li>
                <li><a href="#">About Us</a></li>
                <li><a id="loginBtn" href="#">Login</a></li>
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

    <section class="hero">
        <img src="home.jpg" alt="Vaccination" class="hero-image">
        <h1>Protect Your Child's Future with Timely Vaccination</h1>
    </section>

    <section class="info-boxes">
        <div class="box">
            <h2>Why Vaccinate?</h2>
            <p>Vaccination is crucial for protecting your child from preventable diseases.
            </p>
        </div>
        <div class="box">
            <h2>Safe and Effective</h2>
            <p>Vaccines are tested rigorously to ensure they are safe for your child.</p>
        </div>
        <div class="box">
            <h2>Community Protection</h2>
            <p>When most people are vaccinated, it helps protect everyone in the community.</p>
        </div>
    </section>
    <!-- Login/Registration Popup -->
    <div id="popupForm" class="popup">
        <div class="popup-content">
            <span id="closePopup" class="close">&times;</span>
            <div class="form-container">
                <div class="form-toggle">
                    <button id="loginTab" class="tab active">Login</button>
                    <button id="registerTab" class="tab">Register</button>
                </div>
    

                <form id="loginForm" class="form" action="#" method="POST">
                    <h3>Login</h3>
                    <input type="text" placeholder="Email" required name="email">
                <input type="password" placeholder="Password" required name="password">
                    <input class="button" type="submit" value="Login" name="submit1">
                </form>
                

                <form id="registerForm" class="form hidden" action="#" method="POST">
                    <h3>Register</h3>
                    <input type="text" placeholder="Username" required name="username">
                <input type="email" placeholder="Email" required name="email">
                <input type="password" placeholder="Password" required name="password">
                <input type="password" placeholder="Confirm Password" required name="conf">
                <input type="number" placeholder="Mobile Number" required name="mobno">
                    <input class="button" type="submit" value="Register" name="submit">
                </form>
            </div>
        </div>
    </div>
    <div id="popupMessage" class="popup-message hidden">
    <div class="popup-message-content">
        <p id="popupMessageText"></p>
        <!-- <button id="closePopupMessage" class="button">&times;</button> -->
    </div>
</div>

    <footer>
        <p>&copy; 2024 Child Vaccination System. All rights reserved.</p>
    </footer>

    <script src="home.js"></script>
</body>
</html>

<?php 

include("index.php");
 if(isset($_POST['submit']))
 {
  $name   = $_POST['username'];
  $email  = $_POST['email'];
  $pass =$_POST['password'];
  $confirm= $_POST['conf'];
  $mobno= $_POST['mobno'];
  
 
$passwordError = "";
    $query = "SELECT * FROM regi WHERE email='$email'";

    $data=mysqli_query($conn,$query);
    $total=mysqli_num_rows($data);
    if($total !=0)
    {
        echo "<script>showPopupMessage('User already exists');</script>";
    }
    else{
    if($name !="" && $email !="" &&  $pass != "" && $confirm != "" && $mobno != "")
    {
        if ($_POST['password'] === $_POST['conf']) {
            $query = "INSERT INTO regi (username,email,passw,confirm,mobno) values('$name','$email','$pass','$confirm','$mobno')";
     
            $data=mysqli_query($conn,$query);
             if($query)
            {
            echo "<script>window.location.href = 'userdash.html';</script>"; 
            }
            else{
                echo "<script>showPopupMessage('Failed to insert data');</script>";
            }
        }
         
           else {
    
            echo "<script>showPopupMessage('Passwords do not match');</script>";
     
             
         }
    } else{
        echo "<script>showPopupMessage('Please fill in all fields');</script>";
    }      
 
}
 }

if (isset($_POST['submit1'])) { // Login logic
    $username = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {
        $query = "SELECT * FROM regi WHERE email='$username' AND passw='$password'";
        $data = mysqli_query($conn, $query);

        if (mysqli_num_rows($data) == 1) {
            $_SESSION['email'] = $username; // Store user email in session
            echo "<script>window.location.href = 'userdash.html';</script>";
        } else {
            echo "<script>showPopupMessage('Invalid username or password');</script>";
        }
    } else {
        echo "<script>showPopupMessage('Please fill in all fields');</script>";
    }
}
 ?>
