<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");  // Redirect to login page if not logged in
    exit;
}

include 'db.php';

// Handle note creation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['note_text'])) {
    $note_text = $_POST['note_text'];
    $image_path = '';

    // Check if an image is uploaded
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image_name = $_FILES['image']['name'];
        $image_tmp = $_FILES['image']['tmp_name'];
        $target_dir = 'uploads/';
        $target_file = $target_dir . basename($image_name);

        // Ensure the target directory exists
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);  // Create directory if it doesn't exist
        }

        // Check file size (Optional: max 2MB)
        if ($_FILES['image']['size'] > 2000000) {
            echo "File is too large. Maximum size is 2MB.";
        } else {
            // Attempt to move the uploaded file to the target directory
            if (move_uploaded_file($image_tmp, $target_file)) {
                $image_path = $target_file;
            } else {
                echo "Error uploading the file.";
            }
        }
    }

    // Save the note in the database
    $user_id = $_SESSION['user_id'];
    $sql = "INSERT INTO notes (user_id, note_text, image_path) VALUES ('$user_id', '$note_text', '$image_path')";

    if ($conn->query($sql) === TRUE) {
        echo "Note created successfully";
    } else {
        echo "Error: " . $conn->error;
    }
}

// Display user notes
$sql = "SELECT * FROM notes WHERE user_id = " . $_SESSION['user_id'];
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landing Page - Notes</title>
    <style>
        /* Global styles */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f1f1f1;
            color: #333;
        }

        h1, h2 {
            text-align: center;
            color: #4CAF50;
            font-size: 2em;
        }

        .landing-container {
            width: 80%;
            margin: 0 auto;
            max-width: 900px;
            padding: 20px;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            margin-top: 30px;
        }

        form {
            display: flex;
            flex-direction: column;
            margin-bottom: 20px;
        }

        input[type="text"], input[type="email"], input[type="password"], textarea {
            padding: 12px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }

        input[type="file"] {
            margin-top: 10px;
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        input[type="text"]:focus, input[type="email"]:focus, input[type="password"]:focus, textarea:focus {
            border-color: #4CAF50;
        }

        button {
            padding: 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #45a049;
        }

        .note {
            padding: 15px;
            background-color: #fafafa;
            border: 1px solid #e0e0e0;
            margin: 15px 0;
            border-radius: 8px;
        }

        .note img {
            max-width: 100%;
            border-radius: 5px;
            margin-top: 10px;
        }

        .note a {
            color: #4CAF50;
            text-decoration: none;
            font-weight: bold;
            margin-right: 15px;
        }

        .note a:hover {
            text-decoration: underline;
        }

        .logout {
            text-align: center;
            margin-top: 20px;
        }

        .logout a {
            color: #4CAF50;
            font-size: 1.2em;
            text-decoration: none;
        }

        .logout a:hover {
            text-decoration: underline;
        }

        /* Responsive styles */
        @media screen and (max-width: 768px) {
            .landing-container {
                width: 95%;
            }

            h1, h2 {
                font-size: 1.5em;
            }

            button {
                font-size: 14px;
            }
        }
    </style>
    <script>
        // JavaScript for form validation
        function validateNoteForm() {
            const noteText = document.getElementById('note_text').value;
            if (!noteText.trim()) {
                alert("Note text cannot be empty.");
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
    <div class="landing-container">
        <h1>Welcome, <?php echo $_SESSION['username']; ?>!</h1>
        <form action="landing.php" method="POST" enctype="multipart/form-data" onsubmit="return validateNoteForm()">
            <textarea name="note_text" id="note_text" placeholder="Write your note..." required></textarea><br>
            <input type="file" name="image"><br>
            <button type="submit">Create Note</button>
        </form>

        <h2>Your Notes</h2>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="note">
                <p><?php echo $row['note_text']; ?></p>
                <?php if ($row['image_path']): ?>
                    <img src="<?php echo $row['image_path']; ?>" alt="Note Image">
                <?php endif; ?>
                <a href="delete_note.php?note_id=<?php echo $row['note_id']; ?>">Delete</a> | 
                <a href="update_note.php?note_id=<?php echo $row['note_id']; ?>">Update</a>
            </div>
        <?php endwhile; ?>

        <div class="logout">
            <a href="logout.php">Logout</a>
        </div>
    </div>
</body>
</html>
