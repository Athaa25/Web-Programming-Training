<?php
session_start();
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

include "connection.php";

if (isset($_GET["nim"])) {
    $nim = $_GET["nim"];
    
    // Ambil data mahasiswa dari basis data berdasarkan NIM
    $query = "SELECT * FROM student WHERE nim = ?";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "s", $nim);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result) {
        $data = mysqli_fetch_assoc($result);
        if (!$data) {
            echo "Data mahasiswa tidak ditemukan";
            exit;
        }
        $birth_date_parts = explode('-', $data['birth_date']);
        $birth_date = intval($birth_date_parts[2]);
        $birth_month = intval($birth_date_parts[1]);
        $birth_year = intval($birth_date_parts[0]);
    } else {
        echo "Gagal mengambil data mahasiswa: " . mysqli_error($connection);
        exit;
    }

    mysqli_stmt_close($stmt);
} else {
    echo "NIM mahasiswa tidak ditemukan";
    exit;
}

if (isset($_POST["submit"])) {
    // Ambil data dari formulir
    $name = htmlentities(strip_tags(trim($_POST["name"])));
    $birth_city = htmlentities(strip_tags(trim($_POST["birth_city"])));
    $faculty = htmlentities(strip_tags(trim($_POST["faculty"])));
    $department = htmlentities(strip_tags(trim($_POST["department"])));
    $gpa = htmlentities(strip_tags(trim($_POST["gpa"])));
    $birth_date = htmlentities(strip_tags(trim($_POST["birth_date"])));
    $birth_month = htmlentities(strip_tags(trim($_POST["birth_month"])));
    $birth_year = htmlentities(strip_tags(trim($_POST["birth_year"])));

    $birth_date = $birth_year . '-' . $birth_month . '-' . $birth_date;
    // Validasi data
    $error_message = "";
    if (empty($name)) {
        $error_message .= "- Nama belum diisi <br>";
    }
    // Lanjutkan validasi untuk data lainnya sesuai kebutuhan Anda

    if ($error_message === "") {
        // Update data mahasiswa ke basis data
        $query = "UPDATE student SET name = ?, birth_city = ?, faculty = ?, department = ?, gpa = ?, birth_date = ? WHERE nim = ?";
        $stmt = mysqli_prepare($connection, $query);
        mysqli_stmt_bind_param($stmt, "ssssdss", $name, $birth_city, $faculty, $department, $gpa, $birth_date, $nim);
        $result = mysqli_stmt_execute($stmt);

        if ($result) {
            $message = "Data mahasiswa dengan NIM $nim berhasil diperbarui";
            $message = urlencode($message);
            header("Location: student_view.php?message={$message}");
        } else {
            echo "Gagal memperbarui data mahasiswa: " . mysqli_error($connection);
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "<div class='error'>$error_message</div>";
    }
}

$arr_month = [
    "1"=>"Januari",
    "2"=>"Februari",
    "3"=>"Maret",
    "4"=>"April",
    "5"=>"Mei",
    "6"=>"Juni",
    "7"=>"Juli",
    "8"=>"Agustus",
    "9"=>"September",
    "10"=>"Oktober",
    "11"=>"Nopember",
    "12"=>"Desember"
  ];

?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Edit Data Mahasiswa</title>
  <link href="assets/style.css" rel="stylesheet">
</head>
<body>
  <div class="container">
    <div id="header">
      <h1 id="logo">Edit Data Mahasiswa</h1>
    </div>
    <hr>
    <nav>
      <ul>
        <li><a href="student_view.php">Tampil</a></li>
        <li><a href="student_add.php">Tambah</a>
        <li><a href="logout.php">Logout</a>
      </ul>
    </nav>
    <h2>Form Edit Data Mahasiswa</h2>
    <form id="form_mahasiswa" action="student_edit.php?nim=<?php echo $nim; ?>" method="post">
      <fieldset>
        <legend>Data Mahasiswa</legend>
        <p>
          <label for="name">Nama : </label>
          <input type="text" name="name" id="name" value="<?php echo $data['name']; ?>">
        </p>
        <p>
          <label for="birth_city">Tempat Lahir : </label>
          <input type="text" name="birth_city" id="birth_city" value="<?php echo $data['birth_city']; ?>">
        </p>
        <p>
  <label for="birth_date">Tanggal Lahir : </label>
  <select name="birth_date" id="birth_date">
    <?php
      for ($i = 1; $i <= 31; $i++) {
        if ($i == $birth_date) {
          echo "<option value='$i' selected>$i</option>";
        } else {
          echo "<option value='$i'>$i</option>";
        }
      }
    ?>
  </select>
  <select name="birth_month" id="birth_month">
    <?php
      foreach ($arr_month as $key => $value) {
        if ($key == $birth_month) {
          echo "<option value='$key' selected>$value</option>";
        } else {
          echo "<option value='$key'>$value</option>";
        }
      }
    ?>
  </select>
  <select name="birth_year" id="birth_year">
    <?php
      for ($i = 1990; $i <= 2005; $i++) {
        if ($i == $birth_year) {
          echo "<option value='$i' selected>$i</option>";
        } else {
          echo "<option value='$i'>$i</option>";
        }
      }
    ?>
  </select>
</p>

        <p>
          <label for="faculty" >Fakultas : </label>
          <select name="faculty" id="faculty">
              <option value="FTIB" <?php if ($data['faculty'] == "FTIB") echo "selected"; ?>>FTIB</option>
              <option value="FTEIC" <?php if ($data['faculty'] == "FTEIC") echo "selected"; ?>>FTEIC</option>
            </select>
        </p>
        <p>
          <label for="department">Jurusan : </label>
          <input type="text" name="department" id="department" value="<?php echo $data['department']; ?>">
        </p>
        <p>
          <label for="gpa">IPK : </label>
          <input type="text" name="gpa" id="gpa" value="<?php echo $data['gpa']; ?>" placeholder="Contoh: 2.75"> (angka desimal dipisah dengan karakter titik ".")
        </p>
      </fieldset>
      <br>
      <p>
        <input type="submit" name="submit" value="Update Data">
      </p>
    </form>
  </div>
</body>
</html>
<?php
  mysqli_close($connection);
?>
