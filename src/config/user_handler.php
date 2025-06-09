<?php
require_once __DIR__ . "/../config/db.php";

class UserHandler {
  public static function login($username, $password) {
    $errmsg = "";
    $success = false;

    if (empty($username) || empty($password)) {
      $errmsg = "Username dan password harus diisi.";
    } else {
      $db = new db();

      $stmt = $db->conn->prepare("SELECT id, username, name, email, password, role FROM user WHERE username = ?");
      $stmt->bind_param("s", $username);
      $stmt->execute();
      $result = $stmt->get_result();

      if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if ($password === $user['password']) {
          $_SESSION['user'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role']
          ];
          $success = true;
        } else {
          $errmsg = "Username atau password salah.";
        }
      } else {
        $errmsg = "Username atau password salah.";
      }

      $stmt->close();
      $db->close();
    }

    if ($success) {
      $_SESSION['login_errmsg'] = '';
      return true;
    } else {
      $_SESSION['login_errmsg'] = $errmsg;
      return false;
    }
  }

  public static function logout() {
    session_destroy();
    session_start();
    Helper::route("login");
    exit;
  }

  public static function dashboard_verify() {
    if (!empty($_SESSION['user'])) {
      if ($_SESSION['user']['role'] === "Admin") {
        require_once __DIR__ . "/../pages/admin/dashboard.php";
      }
      if ($_SESSION['user']['role'] === "Pegawai") {
        require_once __DIR__ . "/../pages/pegawai/dashboard.php";
      }
    } else {
      $_SESSION['login_errmsg'] = "Silahkan login terlebih dahulu.";
      self::login_verify();
    }
    exit;
  }

  public static function page_verify() {
    if (!empty($_SESSION['user'])) {
      return true;
    } else {
      $_SESSION['login_errmsg'] = "Silahkan login terlebih dahulu.";
      Helper::route("login");
      exit;
    }
  }

  public static function login_verify() {
    if (!empty($_SESSION['user'])) {
      Helper::route("dashboard");
    } else {
      require_once __DIR__ . "/../pages/login.php";
    }
    exit;
  }

  public static function switch($to) {
    if ($_SESSION['user']['role'] === "Admin" && $to === "pegawai") {
      $_SESSION['user']['role'] = "Pegawai";
    } elseif ($_SESSION['user']['role'] === "Pegawai" && $to === "admin") {
      $_SESSION['user']['role'] = "Admin";
    }
    Helper::route("dashboard");
  }
}
?>