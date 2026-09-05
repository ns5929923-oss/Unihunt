<?php
// Get input from URL (default is 5 if not provided)
$rows = isset($_GET['rows']) ? (int) $_GET['rows'] : 5;

echo "Number of Rows: $rows\n\n";
echo "Generated Star Pattern:\n\n";

// Nested loops to print the pattern
for ($i = 1; $i <= $rows; $i++) {
    for ($j = 1; $j <= $i; $j++) {
        echo "* ";
    }
    echo " \n "; // move to next line
}
?>
