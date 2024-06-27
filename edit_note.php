<!DOCTYPE html>
<html>
<head>
    <title>Edit Note</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <h1>Edit Note</h1>
    <form action="edit_note.php" method="post">
        <label for="note_id">Note ID:</label><br>
        <input type="number" id="note_id" name="note_id" required><br>
        <label for="title">New Note Title:</label><br>
        <input type="text" id="title" name="title" required><br>
        <label for="text">New Note Text:</label><br>
        <textarea id="text" name="text" required></textarea><br>
        <input type="submit" value="Update Note">
    </form>
</body>
</html>

<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $note_id = $_POST['note_id'];
    $title = $_POST['title'];
    $text = $_POST['text'];
    $date_modification = date('Y-m-d H:i:s');

    $sql = "UPDATE Note SET Titre_Note='$title', Texte_de_Note='$text', Derniere_modification='$date_modification' WHERE ID_Note='$note_id'";

    if ($conn->query($sql) === TRUE) {
        echo "Note updated successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>

