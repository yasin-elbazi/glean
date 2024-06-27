<!DOCTYPE html>
<html>
<head>
    <title>Add Note</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <h1>Add a New Note</h1>
    <form action="add_note.php" method="post">
        <label for="user_id">User ID:</label><br>
        <input type="number" id="user_id" name="user_id" required><br>
        <label for="content_id">Content ID:</label><br>
        <input type="number" id="content_id" name="content_id" required><br>
        <label for="title">Note Title:</label><br>
        <input type="text" id="title" name="title" required><br>
        <label for="text">Note Text:</label><br>
        <textarea id="text" name="text" required></textarea><br>
        <input type="submit" value="Add Note">
    </form>
</body>
</html>


<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST['user_id'];
    $content_id = $_POST['content_id'];
    $title = $_POST['title'];
    $text = $_POST['text'];
    $date_creation = date('Y-m-d H:i:s');
    $date_modification = $date_creation;

    $sql = "INSERT INTO Note (ID_Utilisateur, ID_Contenu, Titre_Note, Texte_de_Note, Date_creation, Derniere_modification) 
            VALUES ('$user_id', '$content_id', '$title', '$text', '$date_creation', '$date_modification')";

    if ($conn->query($sql) === TRUE) {
        echo "New note added successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>