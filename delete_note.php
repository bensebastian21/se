<?php
include 'db.php';

if (isset($_GET['note_id'])) {
    $note_id = $_GET['note_id'];
    $sql = "DELETE FROM notes WHERE note_id = '$note_id'";

    if ($conn->query($sql) === TRUE) {
        echo "Note deleted successfully. <a href='landing.php'>Go back to notes</a>";
    } else {
        echo "Error deleting note: " . $conn->error;
    }
}
?>
