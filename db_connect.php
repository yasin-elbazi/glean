<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "GleanDB";

// Créer connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>