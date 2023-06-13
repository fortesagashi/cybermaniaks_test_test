<h2>Public phonebook</h2>
<?php
// including the database connection
require_once "connection.php";

// getting the users' firstnames and lastnames
$first_lastname = "SELECT u.id, u.firstname, u.lastname, u.address, u.zip_city, c.name
                    FROM users u
                    LEFT JOIN countries c ON u.country_id = c.id
                    WHERE u.is_published = '1'";
$result = $mysqli->query($first_lastname);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $id = $row["id"];
        $firstname = $row["firstname"];
        $lastname = $row["lastname"];
        $address = $row["address"];
        $zip_city = $row["zip_city"];
        $country = $row["name"];

        // printing the names and details in html tags
        echo "<div>";
        echo "$id. $firstname $lastname";
        echo "<a href='#' onclick='toggleData($id, event)'>Details</a>";

        // hidden div
        echo "<div id='user_$id' style='display:none;'>";

        // creating a table for address, emails, and phone numbers
        echo "<table>";
        echo "<thead><tr><th>Address</th><th>Phone numbers</th><th>Emails</th></tr></thead>";
        echo "<tbody>";

        // fetching the emails for the selected user
        $emailQuery = "SELECT email FROM email_addresses WHERE user_id = $id AND is_hidden = 0";
        $emailResult = $mysqli->query($emailQuery);

        // fetching the phone numbers for the selected user
        $phoneQuery = "SELECT phone_number FROM phone_numbers WHERE user_id = $id AND is_hidden = 0";
        $phoneResult = $mysqli->query($phoneQuery);

        // displaying address, country, phone numbers, and emails
        echo "<tr><td>";
        if ($address && $zip_city) {
            echo "$address, $zip_city<br>";
            echo "Country: $country";
        } else {
            echo "No address found.";
        }
        echo "</td>";

        echo "<td>";
        if ($phoneResult && $phoneResult->num_rows > 0) {
            while ($phoneRow = $phoneResult->fetch_assoc()) {
                $phone = $phoneRow["phone_number"];
                echo "$phone<br>";
            }
        } else {
            echo "No phone numbers found.";
        }
        echo "</td>";

        echo "<td>";
        if ($emailResult && $emailResult->num_rows > 0) {
            while ($emailRow = $emailResult->fetch_assoc()) {
                $email = $emailRow["email"];
                echo "$email<br>";
            }
        } else {
            echo "No emails found.";
        }
        echo "</td></tr>";

        echo "</tbody>";
        echo "</table>";
        echo "</div>";
        echo "</div>";
    }
    $result->free();
} else {
    // handling error for executing the query
    echo "Error executing the query: " . $mysqli->error;
}

$mysqli->close();
?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function toggleData(userId, event) {
        var userDiv = $('#user_' + userId);

        if (userDiv.is(':hidden')) {
            userDiv.show();
            $(event.target).text('Hide');
        } else {
            userDiv.hide();
            $(event.target).text('Details');
        }
    }
</script>
