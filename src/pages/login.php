<?php

require_once "helper.php"; // Pastikan helper.php ada di lokasi yang benar

$error_message = "";

if (isset($_GET['error'])) {
    $error_message = urldecode($_GET['error']);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        // Warna kustom dari desain login Anda
                        loginBg: '#FF0000', // Merah terang untuk latar belakang
                        formBg: '#4A5568', // Abu-abu gelap untuk latar belakang input
                        inputPlaceholder: '#cbd5e0', // Warna placeholder terang
                        loginButton: '#FF9800', // Oranye untuk tombol Login
                        redText: '#FF0000', // Merah untuk teks "Forgot password?"
                        navText: '#FFFFFF', // Putih untuk teks navigasi
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'], // Menggunakan Inter atau font default Tailwind
                    },
                },
            },
            // Anda mungkin perlu Flowbite plugin jika menggunakan komponen Flowbite
            // plugins: [require('flowbite/plugin')],
            plugins: [],
        };
    </script>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body class="font-sans bg-loginBg flex flex-col items-center justify-center h-screen overflow-hidden m-0 p-0">
    <div class="absolute inset-0 z-0">
        <video autoplay loop muted playsinline class="min-w-full min-h-full w-auto h-auto absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 object-cover">
            <source src="/inventaris-barang-kantor/src/pages/images/vitagekantor.mp4" type="video/mp4">
        </video>
    </div>

    <nav class="absolute top-0 left-0 w-full flex justify-between items-center px-8 py-6 z-10">
        <div class="text-navText text-2xl font-bold">
            <p>Login Admin</p>
        </div>
        <div class="text-navText text-lg font-semibold">
            <a href="/homepage/homepage.html" class="hover:underline">Homepage</a>
        </div>
    </nav>

    <div class="relative z-10 bg-transparent flex items-center justify-center h-full w-full">
        <div class="bg-transparent text-white rounded-lg p-8 sm:p-10 max-w-sm w-full">
            <div class="mb-8 text-center">
                <header class="text-3xl font-bold text-navText">Admin</header>
                <?php if ($error_message): ?>
                    <p class="mt-4 text-redText text-sm"><?= htmlspecialchars($error_message) ?></p>
                <?php endif; ?>
            </div>
            <form id="loginForm" method="POST" action="process_login.php">
                <div class="relative mb-6">
                    <input type="text" class="w-full pl-12 pr-4 py-3 rounded-full bg-formBg text-navText placeholder-inputPlaceholder focus:outline-none focus:ring-2 focus:ring-loginButton text-lg" placeholder="Username or Email" id="usernameInput" name="username">
                    <i class="bx bx-user absolute left-4 top-1/2 -translate-y-1/2 text-inputPlaceholder text-2xl"></i>
                </div>
                <div class="relative mb-6">
                    <input type="password" class="w-full pl-12 pr-4 py-3 rounded-full bg-formBg text-navText placeholder-inputPlaceholder focus:outline-none focus:ring-2 focus:ring-loginButton text-lg" placeholder="Password" id="passwordInput" name="password">
                    <i class="bx bx-lock-alt absolute left-4 top-1/2 -translate-y-1/2 text-inputPlaceholder text-2xl"></i>
                </div>
                <div class="mb-6">
                    <button type="submit" class="w-full py-3 rounded-full bg-loginButton text-white font-semibold text-xl hover:bg-orange-600 transition duration-300">Login</button>
                </div>
                <div class="flex justify-between items-center text-sm">
                    <div class="flex items-center">
                        <input type="checkbox" id="login-check" class="form-checkbox h-4 w-4 text-loginButton rounded border-gray-400 focus:ring-loginButton mr-2 bg-transparent border-2 checked:bg-loginButton checked:border-loginButton">
                        <label for="login-check" class="text-navText">Remember Me</label>
                    </div>
                    <div>
                        <a href="/forgotpassword.html" class="text-redText hover:underline">Forgot password?</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const usernameInput = document.getElementById('usernameInput');
            const passwordInput = document.getElementById('passwordInput');
            const loginForm = document.getElementById('loginForm');
            // Mengubah dari class .submit menjadi ID atau menggunakan type="submit" untuk event listener
            const loginButton = document.querySelector('button[type="submit"]');

            // Fokus ke password ketika tekan Enter di username
            if (usernameInput) {
                usernameInput.addEventListener('keydown', function(event) {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                        if (passwordInput) passwordInput.focus();
                    }
                });
            }

            // Submit form ketika tekan Enter di password
            if (passwordInput) {
                passwordInput.addEventListener('keydown', function(event) {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                        if (loginForm) loginForm.submit(); // Submit form langsung
                    }
                });
            }

            // Klik tombol login (tetap submit form)
            // Event listener ini tidak lagi memanggil handleLogin() karena form.submit() akan menangani itu
            // Namun, jika Anda ingin validasi JS sebelum submit, letakkan di sini
            if (loginButton) {
                loginButton.addEventListener('click', function(event) {
                    // event.preventDefault(); // Hapus ini jika ingin form disubmit secara default
                    // handleLogin(); // Panggil fungsi login jika Anda ingin validasi di JS
                });
            }

            // Fungsi handleLogin() tidak lagi diperlukan jika Anda melakukan submit form biasa
            // dan validasi/redirect di PHP
            /*
            function handleLogin() {
                // Validasi sederhana (jika diperlukan di sisi klien)
                if (usernameInput.value.trim() === '' || passwordInput.value.trim() === '') {
                    alert('Username dan password harus diisi!');
                    return;
                }

                // Jika ingin redirect langsung setelah validasi JS (tanpa submit ke PHP):
                // window.location.href = '/dashboard/dashboard.html';

                // Jika ingin menggunakan form submission dengan AJAX:
                // (Kode AJAX dari komentar sebelumnya)
            }
            */
        });
    </script>

</body>

</html>