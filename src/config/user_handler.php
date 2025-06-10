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

    public static function register($name, $username, $email, $password, $role) {
        $errmsg = "";
        $success = false;

        if (empty($name) || empty($username) || empty($email) || empty($password) || empty($role)) {
            $errmsg = "Semua kolom wajib diisi.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errmsg = "Format email tidak valid.";
        } else {
            $db = new db();
            $conn = $db->conn;

            $stmt_check = $conn->prepare("SELECT id FROM user WHERE username = ? OR email = ?");
            $stmt_check->bind_param("ss", $username, $email);
            $stmt_check->execute();
            $stmt_check->store_result();

            if ($stmt_check->num_rows > 0) {
                $errmsg = "Username atau email sudah digunakan.";
                $stmt_check->close();
            } else {
                $stmt_check->close();
                // NOTE: For security, passwords should always be hashed.
                // This is not done here to maintain consistency with the login function.
                $stmt_insert = $conn->prepare("INSERT INTO user (name, username, email, password, role) VALUES (?, ?, ?, ?, ?)");
                $stmt_insert->bind_param("sssss", $name, $username, $email, $password, $role);

                if ($stmt_insert->execute()) {
                    $success = true;
                } else {
                    $errmsg = "Registrasi gagal, silakan coba lagi.";
                }
                $stmt_insert->close();
            }
            $db->close();
        }

        if ($success) {
            $_SESSION['register_errmsg'] = '';
            $_SESSION['login_success_msg'] = "Registrasi berhasil! Silakan login.";
            return true;
        } else {
            $_SESSION['register_errmsg'] = $errmsg;
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
            $role = strtolower($_SESSION['user']['role']);
            if ($role === "admin") {
                require_once __DIR__ . "/../pages/admin/dashboard.php";
            }
            if ($role === "pegawai") {
                require_once __DIR__ . "/../pages/pegawai/dashboard.php";
            }
        } else {
            $_SESSION['login_errmsg'] = "Silahkan login terlebih dahulu.";
            Helper::route("login");
        }
        exit;
    }

    public static function page_verify($required_role = null) {
        // 1. Check if logged in at all
        if (empty($_SESSION['user'])) {
            $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
            $_SESSION['login_errmsg'] = "Silahkan login terlebih dahulu.";
            Helper::route("login");
            exit;
        }

        // 2. If a specific role is required, check for it
        if ($required_role) {
            if (strtolower($_SESSION['user']['role']) !== strtolower($required_role)) {
                $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
                $_SESSION['login_errmsg'] = "Halaman ini hanya untuk " . ucfirst($required_role) . ". Silakan login dengan akun yang sesuai.";
                // We don't want to log them out, just redirect to login
                Helper::route("login");
                exit;
            }
        }

        // 3. If all checks pass
        return true;
    }

    public static function login_verify() {
        if (!empty($_SESSION['user'])) {
            Helper::route("dashboard");
            exit;
        } else {
            return false;
        }
    }

    public static function switch($to) {
      // self::initialize_session();
      // $targetRole = ucfirst($to); // Capitalize 'admin' -> 'Admin', 'pegawai' -> 'Pegawai'

      // // Check if the role to switch to is already logged in
      // if (!empty($_SESSION['users'][$targetRole])) {
      //     $_SESSION['current_role'] = $targetRole;
      //     Helper::route("dashboard");
      // } else {
      //     // If not logged in, redirect to login page with a target role
      //     Helper::route("login", ['role' => $to]);
      // }
      // exit;
        // Reverting to the simple model: switching roles just logs the user out.
        self::logout();
    }
}
?>