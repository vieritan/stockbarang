<?php

session_start();

//membuat koneksi ke database
$conn = mysqli_connect('localhost', 'root', '', 'stockbarang');

// if (!$conn) {
//     die("Koneksi database gagal: " . mysqli_connect_error());
// } else {
//     echo "berhasil di tambahkan ke database";
// }

// menambah barang baru
if(isset($_POST['addnewbarang'])){
    $namabarang = $_POST['namabarang'];
    $deskripsi = $_POST['deskripsi'];
    $stock = $_POST['stock'];

    $addtotable = mysqli_query($conn, "INSERT INTO stock (namabarang, deskripsi, stock) VALUES ('$namabarang', '$deskripsi', '$stock')");
    if ($addtotable) {
        header("Location: index.php");
        exit;
    } else {
        header("Location: index.php");
        exit;
    }
};

//menambah barang masuk
if (isset($_POST['barangmasuk'])) {
    $barangnya = $_POST['barangnya'];
    $keterangan  = $_POST['keterangan'];
    $qty       = $_POST['qty'];

    /* hitung stock sekarang */
    $cekstocksekarang = mysqli_query($conn, "SELECT * FROM stock WHERE idbarang = '$barangnya'");
    $ambilbarangnya   = mysqli_fetch_array($cekstocksekarang);

    $stocksekarang = $ambilbarangnya['stock']; // <-- pakai variabel yg benar
    $tambahkanstocksekarangdenganquantity = $stocksekarang + $qty;

    // insert ke tabel masuk
    $addtomasuk = mysqli_query($conn,"INSERT INTO masuk (idbarang, keterangan, qty) VALUES ('$barangnya', '$keterangan', '$qty')");

    // update stok di tabel stock
    $updatestockmasuk = mysqli_query($conn, "UPDATE stock SET stock = '$tambahkanstocksekarangdenganquantity' WHERE idbarang = '$barangnya'");
    /* hitung stock sekarang end */

    if ($addtomasuk && $updatestockmasuk) {
        header("Location: masuk.php");
        exit;
    } else {
        echo "Gagal";
        header("Location: masuk.php");
        exit;
    }
}

//menambah barang keluar
if (isset($_POST['addbarangkeluar'])) {
    $barangnya = $_POST['barangnya'];
    $penerima  = $_POST['penerima'];
    $qty       = $_POST['qty'];

    /* hitung stock sekarang */
    $cekstocksekarang = mysqli_query($conn, "SELECT * FROM stock WHERE idbarang = '$barangnya'");
    $ambilbarangnya   = mysqli_fetch_array($cekstocksekarang);

    $stocksekarang = $ambilbarangnya['stock']; // <-- pakai variabel yg benar
    $tambahkanstocksekarangdenganquantity = $stocksekarang - $qty;

    // insert ke tabel masuk
    $addtokeluar = mysqli_query($conn,"INSERT INTO keluar (idkeluar, idbarang, penerima, qty) VALUES (NULL, '$barangnya', '$penerima', '$qty')");

    // update stok di tabel stock
    $updatestockkeluar = mysqli_query($conn, "UPDATE stock SET stock = '$tambahkanstocksekarangdenganquantity' WHERE idbarang = '$barangnya'");
    /* hitung stock sekarang end */

    if ($addtokeluar && $updatestockkeluar) {
        header("Location: keluar.php");
        exit;
    } else {
        echo "Gagal";
        header("Location: keluar.php");
        exit;
    }
}

// Update info barang
if (isset($_POST['updatebarang'])) {
    $idb = $_POST['idb']; // pastikan form pakai name="idb"
    $namabarang = $_POST['namabarang'];
    $deskripsi = $_POST['deskripsi'];

    $update = mysqli_query($conn, "UPDATE stock SET namabarang='$namabarang', deskripsi='$deskripsi' WHERE idbarang='$idb'");

    if ($update) {
        header("Location: index.php");
        exit;
    } else {
        echo "Gagal update data: " . mysqli_error($conn);
    }
}

//Hapus Barang
if (isset($_POST['hapusbarang'])) {
    $idb = $_POST['idb'];

    $hapus = mysqli_query($conn, "delete from stock where idbarang = '$idb'");
    
    if ($hapus) {
        header("Location: index.php");
        exit;
    } else {
        echo "Gagal hapus data: " . mysqli_error($conn);
    }
}

//Mengubah Data Barang Masuk
if(isset($_POST['updatebarangmasuk'])){
    $idb = $_POST['idb'];
    $idm = $_POST['idm'];
    $qty = $_POST['qty'];
    $keterangan = $_POST['keterangan'];

    $lihatstock = mysqli_query($conn, "select * from stock where idbarang = '$idb'");
    $stocknya = mysqli_fetch_array($lihatstock);
    $stockskrn = $stocknya['stock'];

    $qtyskrg = mysqli_query($conn, "select * from masuk where idmasuk = '$idm'");
    $qtynya = mysqli_fetch_array($qtyskrg);
    $qtyskrg = $qtynya['qty'];

    if($qty>$qtyskrg){
        $selisih = $qty - $qtyskrg;
        $kurangi = $stockskrn + $selisih;
        $kurangistocknya = mysqli_query($conn, "UPDATE stock SET stock = '$kurangi' WHERE idbarang = '$idb'");
        $updatenya = mysqli_query($conn, "UPDATE masuk SET qty = '$qty', keterangan = '$keterangan' WHERE idmasuk = '$idm'");

        if ($kurangistocknya && $updatenya) {
                header("Location: masuk.php");
                exit;
            } else {
                echo "Gagal update data"; 
                header("Location: masuk.php");
            }
        
    } else {
        $selisih = $qty - $qtyskrg; // Barang baru dikurangi yang lama
        $kurangi = $stockskrn + $selisih; // Tambahkan selisih ke stok sekarang
        $kurangistocknya = mysqli_query($conn, "UPDATE stock SET stock = '$kurangi' WHERE idbarang = '$idb'");
        $updatenya = mysqli_query($conn, "UPDATE masuk SET qty = '$qty', keterangan = '$keterangan' WHERE idmasuk = '$idm'");

        if ($kurangistocknya && $updatenya) {
                header("Location: masuk.php");
                exit;
            } else {
                echo "Gagal update data";
                header("Location: masuk.php");
            }
    }
}

//Menghapus barang masuk
if (isset($_POST['hapusbarangmasuk'])) {
    $idb = $_POST['idb'];
    $qty = $_POST['qty'];
    $idm = $_POST['idm'];

    // Ambil stok saat ini
    $getdatastock = mysqli_query($conn, "SELECT * FROM stock WHERE idbarang = '$idb'");
    $data = mysqli_fetch_array($getdatastock);
    $stockskrg = $data['stock'];

    // Kurangi stok dengan jumlah yang dihapus
    $selisih = $stockskrg - $qty;

    // Update stok dan hapus data barang masuk
    $update = mysqli_query($conn, "UPDATE stock SET stock = '$selisih' WHERE idbarang = '$idb'");
    $hapusdata = mysqli_query($conn, "DELETE FROM masuk WHERE idmasuk = '$idm'");

    if ($update && $hapusdata) {
        header("Location: masuk.php");
        exit;
    } else {
        echo "Gagal menghapus data.";
        header("Location: masuk.php");
        exit;
    }
}

//Mengubah data barang keluar
if(isset($_POST['updatebarangkeluar'])){
    $idb = $_POST['idb'];
    $idk = $_POST['idk'];
    $qty = $_POST['qty'];
    $penerima = $_POST['penerima'];

    $lihatstock = mysqli_query($conn, "select * from stock where idbarang = '$idb'");
    $stocknya = mysqli_fetch_array($lihatstock);
    $stockskrn = $stocknya['stock'];

    $qtyskrg = mysqli_query($conn, "select * from keluar where idkeluar = '$idk'");
    $qtynya = mysqli_fetch_array($qtyskrg);
    $qtyskrg = $qtynya['qty'];

    if($qty>$qtyskrg){
        $selisih = $qty - $qtyskrg;
        $kurangi = $stockskrn + $selisih;
        $kurangistocknya = mysqli_query($conn, "UPDATE stock SET stock = '$kurangi' WHERE idbarang = '$idb'");
        $updatenya = mysqli_query($conn, "UPDATE keluar SET qty = '$qty', penerima = '$penerima' WHERE idkeluar = '$idk'");

        if ($kurangistocknya && $updatenya) {
                header("Location: keluar.php");
                exit;
            } else {
                echo "Gagal update data"; 
                header("Location: keluar.php");
            }
        
    } else {
        $selisih = $qty - $qtyskrg; // Barang baru dikurangi yang lama
        $kurangi = $stockskrn + $selisih; // Tambahkan selisih ke stok sekarang
        $kurangistocknya = mysqli_query($conn, "UPDATE stock SET stock = '$kurangi' WHERE idbarang = '$idb'");
        $updatenya = mysqli_query($conn, "UPDATE keluar SET qty = '$qty', penerima = '$penerima' WHERE idkeluar = '$idk'");

        if ($kurangistocknya && $updatenya) {
                header("Location: keluar.php");
                exit;
            } else {
                echo "Gagal update data";
                header("Location: keluar.php");
            }
    }
}

//Menghapus barang keluar
if (isset($_POST['hapusbarangkeluar'])) {
    $idb = $_POST['idb'];
    $qty = $_POST['qty'];
    $idk = $_POST['idk'];

    // Ambil stok saat ini
    $getdatastock = mysqli_query($conn, "SELECT * FROM stock WHERE idbarang = '$idb'");
    $data = mysqli_fetch_array($getdatastock);
    $stockskrg = $data['stock'];

    // Tambahkan stok kembali karena transaksi keluar dihapus
    $selisih = $stockskrg + $qty;

    // Update stok dan hapus data barang keluar
    $update = mysqli_query($conn, "UPDATE stock SET stock = '$selisih' WHERE idbarang = '$idb'");
    $hapusdata = mysqli_query($conn, "DELETE FROM keluar WHERE idkeluar = '$idk'");

    if ($update && $hapusdata) {
        header("Location: keluar.php");
        exit;
    } else {
        echo "Gagal menghapus data.";
        header("Location: keluar.php");
        exit;
    }
}

?>