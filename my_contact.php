<?php
session_start();
// including the database connection
require_once "connection.php";
// Get the username from the session
$username = $_SESSION["username"];
// getting the user's firstnames and lastnames
$first_lastname = "SELECT u.id, u.firstname, u.lastname, u.address, u.zip_city, c.name, u.is_published
                    FROM users u
                    LEFT JOIN countries c ON u.country_id = c.id
                    WHERE  u.username = '$username'"; // Filter by user ID 1
$result = $mysqli->query($first_lastname);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $id = $row["id"];
        $firstname = $row["firstname"];
        $lastname = $row["lastname"];
        $address = $row["address"];
        $zip_city = $row["zip_city"];
        $country = $row["name"];
        $isPublished = $row["is_published"];

        echo "<form method='post' action='my_contact.php'>"; // <-- Modify the action attribute to point to the same file
        // printing the names in html tags
        echo "<div>";

        // creating a form for address, emails, and phone numbers
        echo "<table>";
        echo "<thead><tr><th>Address</th><th>Phone numbers</th><th>Emails</th><th>Publish</th></tr></thead>";
        echo "<tbody>";

        //fetching the emails for the selected user
        $emailQuery = "SELECT id, email, is_hidden FROM email_addresses WHERE user_id = $id";
        $emailResult = $mysqli->query($emailQuery);

        // fetching the phone numbers for the selected user
        $phoneQuery = "SELECT id, phone_number, is_hidden FROM phone_numbers WHERE user_id = $id";
        $phoneResult = $mysqli->query($phoneQuery);

        // displaying address, country, phone numbers, and emails
        echo "<tr><td>";
        if ($address && $zip_city) {
            echo "<input type='text' name='address' value='$address'><br>";
            echo "<input type='text' name='zip_city' value='$zip_city'><br>";
            echo "Country: ";
            echo "<select name='country'>";
            echo "<option value='$country'>$country</option>";
            // fetching countries from the database
            $countryQuery = "SELECT id, name FROM countries";
            $countryResult = $mysqli->query($countryQuery);
            if ($countryResult && $countryResult->num_rows > 0) {
                while ($countryRow = $countryResult->fetch_assoc()) {
                    $countryId = $countryRow['id'];
                    $countryName = $countryRow['name'];
                    echo "<option value='$countryId'>$countryName</option>";
                }
            }
            echo "</select>";
        } else {
            echo "No address found.";
        }
        echo "</td>";

        // fetching and displaying phone numbers
        echo "<td id='phoneNumbers_$id'>";
        if ($phoneResult && $phoneResult->num_rows > 0) {
            while ($phoneRow = $phoneResult->fetch_assoc()) {
                $phoneId = $phoneRow["id"];
                $phone = $phoneRow["phone_number"];
                $isHidden = $phoneRow["is_hidden"];
                echo "<input type='text' name='phone[]' value='$phone'>";
                echo "<input type='checkbox' name='hidePhoneNumber[]' value='$phoneId' " . ($isHidden ? "" : "checked") . "><br>";
            }
        } else {
            echo "No phone numbers found.";
        }
        echo "<button onclick='addPhoneNumber($id)'>Add Phone</button>";
        echo "</td>";

        // fetching and displaying emails
        echo "<td id='emails_$id'>";
        if ($emailResult && $emailResult->num_rows > 0) {
            while ($emailRow = $emailResult->fetch_assoc()) {
                $emailId = $emailRow["id"];
                $email = $emailRow["email"];
                $isHidden = $emailRow["is_hidden"];
                echo "<input type='text' name='email[]' value='$email'>";
                echo "<input type='checkbox' name='hideEmail[]' value='$emailId' " . ($isHidden ? "" : "checked") . "><br>";
            }
        } else {
            echo "No emails found.";
        }
        echo "<button onclick='addEmail($id)'>Add Email</button>";
        echo "</td>";

        // checkbox for publishing the contact of the user
        echo "<td>";
        $isPublished = ($row['is_published'] == 1) ? true : false;
        echo "<input type='checkbox' name='publishContact' value='1' " . ($isPublished ? "checked" : "") . "> Publish<br>";
        echo "</td>";

        echo "</tr>";

        echo "</tbody>";
        echo "</table>";

        // submit button
        echo "<input type='submit' name='submit' value='Save Changes'>";

        echo "</form>";
        echo "</div>";
    }
    $result->free();
} else {
    // handling error for executing the query
    echo "Error executing the query: " . $mysqli->error;
}

// Update is_published if form is submitted
if (isset($_POST['submit'])) {
    $publish = isset($_POST['publishContact']) ? 1 : 0;

    // Update the is_published value in the users table
    $updateQuery = "UPDATE users SET is_published = '$publish' WHERE id = '$id'";
    $updateResult = $mysqli->query($updateQuery);
    if ($updateResult) {
        echo "<script>window.location.href = 'main.php';</script>"; // Redirect to main.php after successful update
        exit; 
    } else {
        // ...
    }
}

$mysqli->close();
?>
<!--- adding new input row for phone and email --->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function addPhoneNumber(userId) {
        var phoneNumbersContainer = $("#phoneNumbers_" + userId);
        var newPhoneNumberInput = $("<input type='text' name='phone[]'><br>");
        var newHidePhoneNumberInput = $("<input type='checkbox' name='hidePhoneNumber[]' checked><br>");
        phoneNumbersContainer.append(newPhoneNumberInput);
        phoneNumbersContainer.append(newHidePhoneNumberInput);
    }

    function addEmail(userId) {
        var emailsContainer = $("#emails_" + userId);
        var newEmailInput = $("<input type='text' name='email[]'><br>");
        var newHideEmailInput = $("<input type='checkbox' name='hideEmail[]' checked><br>");
        emailsContainer.append(newEmailInput);
        emailsContainer.append(newHideEmailInput);
    }
</script>
</body>
</html>
