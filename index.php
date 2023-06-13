<!DOCTYPE html>
<html>
<head>
    <title>Phonebook application</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        // loading content in the "block" div 
        function loadContent(page) {
            $.ajax({
                url: page,
                success: function(data) {
                    $("#block").html(data);
                }
            });
        }
    </script>
</head>
<body>
    <p>Phonebook</p>
    
    <?php
    session_start();

    if (isset($_SESSION["username"])) {
        // User is logged in
        echo '
        <ul>
            <li><a href="#" onclick="loadContent(\'public_phonebook.php\')">Public Phonebook</a></li>
            <li><a href="#" onclick="loadContent(\'my_contact.php\')">My contact</a></li>
            <li><a href="#" onclick="logout()">Logout</a></li>
        </ul>';
    } else {
        // User is not logged in
        echo '
        <ul>
            <li><a href="#" onclick="loadContent(\'login.php\')">Login</a></li>
            <li><a href="#" onclick="loadContent(\'public_phonebook.php\')">Public Phonebook</a></li>
        </ul>';
    }

    // Logout logic
    if (isset($_GET['logout'])) {
        // Destroy the session
        session_destroy();
        // Redirect to the current page to refresh the content
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    }
    ?>

    <div id="block">
        
    </div>

    <script>
        function logout() {
            // Redirect to the current page with logout parameter
            window.location.href = '<?php echo $_SERVER['PHP_SELF']; ?>?logout=true';
        }
    </script>

</body>
</html>
