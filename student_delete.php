<?php
session_start();
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
}

include ("connection.php");

if (isset($_GET['nim']) && !empty($_GET['nim'])) {
    $nim = $_GET['nim'];

    $query = "DELETE FROM student WHERE nim = '$nim'";
    $result = mysqli_query($connection, $query);
    if ($result) {
        header("Location: student_view.php?message=Data Mahasiswa dengan NIM $nim 
berhasil dihapus.");
        exit();
    } else {
        header("Location: student_view.php?message=Gagal menghapus data mahasiswa.");
        exit();
    }
} else {
    header("Location: student_view.php?message=NIM tidak valid.");
    exit();
}
?>