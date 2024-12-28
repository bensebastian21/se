<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");  // Redirect to login page if not logged in
    exit;
}

include 'db.php';

// Check if note ID is provided
if (isset($_GET['note_id'])) {
    $note_id = $_GET['note_id'];

    // Fetch the note from the database
    $sql = "SELECT * FROM notes WHERE note_id = $note_id AND user_id = " . $_SESSION['user_id'];
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $note = $result->fetch_assoc();
    } else {
        echo "Note not found or you do not have permission to update it.";
        exit;
    }
}

// Handle form submission for updating the note
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['note_text'])) {
    $note_text = $_POST['note_text'];
    $image_path = $note['image_path'];  // Keep the old image path if no new image is uploaded

    // Check if a new image is uploaded
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

    // Update the note in the database
    $sql = "UPDATE notes SET note_text = '$note_text', image_path = '$image_path' WHERE note_id = $note_id";
    if ($conn->query($sql) === TRUE) {
        header("Location: landing.php");  // Redirect to landing page after update
        exit;
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Note</title>
    <style>
        /* Add your CSS here */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f1f1f1;
            color: #333;
            padding: 30px;
        }

        .update-container {
            width: 80%;
            margin: 0 auto;
            max-width: 600px;
            padding: 20px;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        h1 {
            text-align: center;
            color: #4CAF50;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        textarea, input[type="text"], input[type="file"] {
            padding: 12px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            padding: 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        .note-image {
            max-width: 100%;
            margin-top: 10px;
            border-radius: 5px;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            font-size: 1.2em;
            color: #4CAF50;
        }

        .back-link a {
            color: #4CAF50;
            text-decoration: none;
        }

        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="update-container">
        <h1>Update Note</h1>
        <form action="update_note.php?note_id=<?php echo $note['note_id']; ?>" method="POST" enctype="multipart/form-data">
            <textarea name="note_text" placeholder="Update your note..." required><?php echo $note['note_text']; ?></textarea><br>

            <?php if ($note['image_path']): ?>
                <img src="<?php echo $note['image_path']; ?>" class="note-image" alt="Note Image">
                <p>Current Image: <?php echo basename($note['image_path']); ?></p>
            <?php endif; ?>

            <input type="file" name="image"><br>

            <button type="submit">Update Note</button>
        </form>

        <div class="back-link">
            <a href="landing.php">Back to Landing Page</a>
        </div>
    </div>
</body>
</html>
