<?php

$host = "localhost";
$user = "root";
$pass = "";
$db   = "unihunt";

$conn = new mysqli($localhost, $root, $, $unihunt);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

include("../config/db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name     = $_POST['name'];
    $email    = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $sql = "INSERT INTO users (name, email, password) VALUES ('$name','$email','$password')";

    if ($conn->query($sql) === TRUE) {
        header("Location: login.php?success=1");
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<form method="post">
  <input type="text" name="name" placeholder="Full Name" required>
  <input type="email" name="email" placeholder="Email" required>
  <input type="password" name="password" placeholder="Password" required>
  <button type="submit">Register</button>
</form>

}
<?php
session_start();
include("../config/db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email    = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();

        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_name'] = $row['name'];
            header("Location: index.php");
            exit;
        } else {
            echo "Invalid Password!";
        }
    } else {
        echo "User not found!";
    }
}
?>

<form method="post">
  <input type="email" name="email" placeholder="Email" required>
  <input type="password" name="password" placeholder="Password" required>
  <button type="submit">Login</button>
</form>
<?php
include("../config/db.php");

$stream = $_GET['stream'] ?? '';
$location = $_GET['location'] ?? '';

$sql = "SELECT * FROM universities WHERE 1=1";
if ($stream) $sql .= " AND stream LIKE '%$stream%'";
if ($location) $sql .= " AND state LIKE '%$location%'";

$result = $conn->query($sql);
?>

<h2>Search Results</h2>
<?php while ($row = $result->fetch_assoc()) { ?>
  <div>
    <h3><a href="university.php?id=<?php echo $row['id']; ?>"><?php echo $row['name']; ?></a></h3>
    <p>State: <?php echo $row['state']; ?></p>
    <p>Fees: <?php echo $row['fees']; ?></p>
  </div>
<?php } ?>
<?php
include("../config/db.php");
$id = $_GET['id'];
$sql = "SELECT * FROM universities WHERE id=$id";
$result = $conn->query($sql);
$uni = $result->fetch_assoc();
?>

<h2><?php echo $uni['name']; ?></h2>
<p>State: <?php echo $uni['state']; ?></p>
<p>Courses: <?php echo $uni['courses']; ?></p>
<p>Fees: <?php echo $uni['fees']; ?></p>
<p>Placement Rate: <?php echo $uni['placement_rate']; ?>%</p>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $answers = $_POST['answers'];
    $science = 0; $commerce = 0; $arts = 0;

    foreach ($answers as $ans) {
        if ($ans == "science") $science++;
        if ($ans == "commerce") $commerce++;
        if ($ans == "arts") $arts++;
    }

    $recommended = max([$science=>"Science", $commerce=>"Commerce", $arts=>"Arts"]);
    header("Location: result.php?stream=$recommended");
}
?>

<form method="post">
  <p>Q1: Do you enjoy working with numbers?</p>
  <input type="radio" name="answers[1]" value="commerce"> Yes
  <input type="radio" name="answers[1]" value="arts"> No

  <p>Q2: Do you like Biology and Physics?</p>
  <input type="radio" name="answers[2]" value="science"> Yes
  <input type="radio" name="answers[2]" value="commerce"> No

  <button type="submit">Submit Quiz</button>
</form>
<?php
include("../config/db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name   = $_POST['name'];
    $state  = $_POST['state'];
    $stream = $_POST['stream'];
    $courses= $_POST['courses'];
    $fees   = $_POST['fees'];
    $place  = $_POST['placement_rate'];

    $sql = "INSERT INTO universities (name, state, stream, courses, fees, placement_rate)
            VALUES ('$name','$state','$stream','$courses','$fees','$place')";

    if ($conn->query($sql) === TRUE) {
        echo "University Added!";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<form method="post">
  <input type="text" name="name" placeholder="University Name" required>
  <input type="text" name="state" placeholder="State" required>
  <input type="text" name="stream" placeholder="Stream (Science/Commerce/Arts)">
  <input type="text" name="courses" placeholder="Courses Offered">
  <input type="number" name="fees" placeholder="Fees">
  <input type="number" name="placement_rate" placeholder="Placement Rate %">
  <button type="submit">Add University</button>
</form>
