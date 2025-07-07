<?php

//membuat koneksi ke database
$conn = mysqli_connect('localhost', 'root', 'root', 'stockbarang');

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
} else {
    echo "Koneksi database berhasil";
}


?>