<?php 

error_reporting(E_ERROR | E_PARSE);
include 'db.php';
$conn = new mysqli($servername, $username, $password, $dbname);

function query($query) {
    global $conn;
    $result = mysqli_query($conn, $query);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}

function register($data,$pwd) {
    global $conn;

    $username = htmlspecialchars($data['username']);
    $email = htmlspecialchars($data['email']);
    $hashed_pwd = password_hash($pwd, PASSWORD_DEFAULT);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "INSERT INTO users(username,email,pwd) VALUES('$username','$email','$hashed_pwd')";
    mysqli_query($conn, $sql);
    return mysqli_affected_rows($conn);
}

function checkUsers($username, $email) {
    global $conn;
    $sql = "SELECT * FROM users WHERE username='$username' OR email='$email'";
    $hasil = mysqli_query($conn, $sql);
    return mysqli_num_rows($hasil);
}

function login($data) {
    global $conn;

    $email = htmlspecialchars($data['email']);
    $pwd = htmlspecialchars($data['pwd']);
    // cek email
    $result = count(query("SELECT * FROM users WHERE email='$email'"));
    if($result > 0) {
        // cek password
        $result = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
        $row = mysqli_fetch_assoc($result);
        if(password_verify($pwd,$row['pwd'])) {
             // sukses login
            return 1;
        }
        else {
            // wrong password
            return 2;
        }
    }
    else {
        // no user
        return 3;
    }
}

?>
