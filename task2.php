<?php
// Connect to the database
$servername = "localhost";
$username = "root";
$password = "sweetwater123";
$dbname = "sys";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Read the SQL file and extract comments using regular expressions
$directory = __DIR__;
$file = $directory . '/sweetwater_test.sql';
$sweetwaterContents = file_get_contents($file);

$pattern = "/\(\d+,'((?:\\\\'|[^'])*)'/s";
preg_match_all($pattern, $sweetwaterContents, $matches);

$comments = $matches[1];

// Parse the "Expected Ship Date" and update the database
foreach ($comments as $comment) {
    if (stripos($comment, 'Expected Ship Date:') !== false) {
        // Extract the date using regular expression
        $datePattern = "/Expected Ship Date: (\d{2}\/\d{2}\/\d{2})/";
        preg_match($datePattern, $comment, $dateMatches);

        if (isset($dateMatches[1])) {
            $expectedShipDate = $dateMatches[1];

            // Convert the date to the MySQL date format (YYYY-MM-DD 00:00:00)
            $convertedDate = date('Y-m-d H:i:s', strtotime($expectedShipDate));

            // Update the database with the converted date
            $sql = "UPDATE sweetwater_test SET shipdate_expected = '$convertedDate' WHERE comments = '$comment'";

            if ($conn->query($sql) === true) {
                echo "Updated shipdate_expected for comment: $comment<br>";
            } else {
                echo "Error updating record: " . $conn->error . "<br>";
            }
        }
    }
}

// Display all rows from the sweetwater_test table
$sqlSelect = "SELECT * FROM sweetwater_test";
$result = $conn->query($sqlSelect);

if ($result->num_rows > 0) {
    echo "<br>Current rows in sweetwater_test table:<br>";
    while ($row = $result->fetch_assoc()) {
        echo "orderid: " . $row['orderid'] . ", comments: " . $row['comments'] . ", shipdate_expected: " . $row['shipdate_expected'] . "<br>";
    }
} else {
    echo "No rows found in the sweetwater_test table.";
}

// Close the database connection
$conn->close();
?>
