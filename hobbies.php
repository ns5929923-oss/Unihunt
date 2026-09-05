<?php
$selectedHobbies = [];

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hobbies'])) {
    $selectedHobbies = $_POST['hobbies'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head> 
    <meta charset="UTF-8">
    <title>Hobbies Selection</title>
</head>
<body>
    <h1>Select Your Hobbies</h1>
    <form method="post">
        <label><input type="checkbox" name="hobbies[]" value="Reading"> Reading</label>
        <label><input type="checkbox" name="hobbies[]" value="Sports"> Sports</label>
        <label><input type="checkbox" name="hobbies[]" value="Music"> Music</label>
        <label><input type="checkbox" name="hobbies[]" value="Traveling"> Traveling</label>
        <input type="submit" value="Submit Hobbies">
    </form>

    <?php if (!empty($selectedHobbies)): ?>
        <h2>Your Selected Hobbies:</h2>
        <ul>
            <?php foreach ($selectedHobbies as $hobby): ?>
                <li><?php echo htmlspecialchars($hobby); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</body>
</html>