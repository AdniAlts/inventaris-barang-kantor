<?php
// src/pages/register.php

// helper.php diperlukan di sini untuk memanggil Helper::basePath()
// Asumsi routes.php ada di src/config/, dan register.php ada di src/pages/.
// Maka untuk mencapai helper.php dari register.php (src/pages/).
require_once __DIR__ . "/../config/helper.php";
require_once __DIR__ . "/../config/routes.php"; // <--- PERBAIKAN PATH INI

$error_message = "";
if (!empty($_SESSION['register_errmsg'])) {
    $error_message = $_SESSION['register_errmsg'];
    unset($_SESSION['register_errmsg']); // Hapus agar tidak muncul terus
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="<?= Helper::basePath(); ?>src/output.css">
    <style>
        body {
            background-color: #e0f2f7; /* Light blue from the image */
        }
    </style>
</head>
<body class="flex justify-center items-center min-h-screen p-4">

    <div class="w-full max-w-sm bg-white rounded-lg shadow-md p-8 md:p-10">
        <div class="flex justify-center mb-8">
            <div class="w-24 h-24 bg-blue-600 rounded-full flex items-center justify-center text-white">
                <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                </svg>
            </div>
        </div>

        <?php if (!empty($error_message)): ?>
            <div id="alert-border-2" class="flex items-center p-4 mb-6 text-red-800 border-t-4 border-red-300 bg-red-50" role="alert">
                <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 .5a9.5 9.5 0 1 1 9.5 9.5A9.51 9.51 0 0 1 10 .5ZM10 15a1 1 0 1 0 0 2 1 1 0 0 0 0-2Zm1-4a1 1 0 0 0-2 0v2a1 1 0 1 0 2 0v-2Z"/>
                </svg>
                <div class="ms-3 text-sm font-medium">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
                <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-red-50 text-red-500 rounded-lg focus:ring-2 focus:ring-red-400 p-1.5 hover:bg-red-200 inline-flex items-center justify-center h-8 w-8" data-dismiss-target="#alert-border-2" aria-label="Close">
                    <span class="sr-only">Dismiss</span>
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                </button>
            </div>
        <?php endif; ?>

        <form class="space-y-6" action="<?php echo Helper::basePath(); ?>register" method="post">
            <div>
                <label for="name" class="sr-only">Nama</label>
                <div class="relative">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <input type="text" name="name" id="name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5" placeholder="Nama" required autocomplete="name">
                </div>
            </div>

            <div>
                <label for="username" class="sr-only">Username</label>
                <div class="relative">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <input type="text" name="username" id="username" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5" placeholder="Username" required autocomplete="username">
                </div>
            </div>

            <div>
                <label for="email" class="sr-only">E-mail</label>
                <div class="relative">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <input type="email" name="email" id="email" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5" placeholder="E-mail" required autocomplete="email">
                </div>
            </div>

            <div>
                <label for="password" class="sr-only">Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 16 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M14 7h-1.5V6a4.5 4.5 0 1 0-9 0v1H2a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2Zm-1.5 0h-9V6a3 3 0 1 1 6 0v1h-1.5Z"></path>
                        </svg>
                    </div>
                    <input type="password" name="password" id="password" placeholder="Password" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5" required autocomplete="current-password">

                    <button type="button" id="togglePassword" class="absolute inset-y-0 end-0 flex items-center pe-3">
                        <svg id="eyeOpen" class="w-5 h-5 text-gray-500 hover:text-blue-600" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 18">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1.933 10.909A4.357 4.357 0 0 1 1 9c0-1 4-6 9-6m7.6 3.8A5.068 5.068 0 0 1 19 9c0 1-4 6-9 6-.91 0-1.786-.2-2.6-.5M6 10a3 3 1 1 0 6 0 3 1 1 0-6 0Z"/>
                        </svg>
                        <svg id="eyeClosed" class="w-5 h-5 text-gray-500 hover:text-blue-600 hidden" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1.933 10.909A4.357 4.357 0 0 1 1 9c0-1 4-6 9-6m7.6 3.8A5.068 5.068 0 0 1 19 9c0 1-4 6-9 6-.91 0-1.786-.2-2.6-.5M6 10a3 3 0 1 1 6 0 3 3 0 0 1-6 0Z" />
                        </svg>
                    </button>
                </div>
            </div>

            <div>
                <label for="role" class="text-sm text-gray-500">User sebagai</label>
                <select id="role" name="role" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-3 p-2.5" required>
                    <option value="Admin" selected>Admin</option>
                    <option value="Pegawai">Pegawai</option>
                </select>
            </div>

            <button type="submit" class="w-full text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-lg px-5 py-2.5 text-center transition-colors duration-200">
                REGISTER
            </button>

            <div class="text-gray-500 text-center text-sm">
                <p>Telah memiliki akun? <a class="primary-color font-bold" href="<?= Helper::basePath(); ?>login">Login</a></p>
            </div>
        </form>
    </div>

    <script src="<?= Helper::basePath(); ?>src/flowbite.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const togglePasswordButton = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const eyeOpenIcon = document.getElementById('eyeOpen');
            const eyeClosedIcon = document.getElementById('eyeClosed');

            // Log untuk memastikan elemen ditemukan
            console.log('passwordInput:', passwordInput);
            console.log('togglePasswordButton:', togglePasswordButton);
            console.log('eyeOpenIcon:', eyeOpenIcon);
            console.log('eyeClosedIcon:', eyeClosedIcon);

            if (togglePasswordButton && passwordInput && eyeOpenIcon && eyeClosedIcon) {
                console.log('Semua elemen password toggle ditemukan. Menambahkan event listener.');
                togglePasswordButton.addEventListener('click', function() {
                    console.log('Tombol toggle diklik!');
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);

                    // Toggle visibilitas ikon
                    eyeOpenIcon.classList.toggle('hidden');
                    eyeClosedIcon.classList.toggle('hidden');
                    console.log('Tipe input diubah menjadi:', type);
                });
            } else {
                console.error('ERROR: Salah satu elemen password toggle tidak ditemukan di DOM!');
            }
        });
    </script>
</body>
</html>