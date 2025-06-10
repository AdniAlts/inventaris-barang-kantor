<?php
require_once __DIR__ . '/../config/helper.php';
?>
<aside id="sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen border-r-3 border-gray-300 transition-transform duration-500"
  aria-label="Sidebar">
  <div class="h-full grid content-between px-3 py-4 overflow-y-auto overflow-x-hidden bg-gray-50">
    <div class="self-start">
      <div class="flex justify-between ps-1.25 mb-5">
        <a href="<?= Helper::basePath(); ?>home" class="flex items-center sidebar-logo">
          <img src="<?= Helper::basePath(); ?>public/images/logo.png" class="h-7 sm:h-7" alt="InventaBox Logo" />
          <span class="self-center ms-4 text-xl font-extrabold whitespace-nowrap">InventaBox</span>
        </a>
        <button id="toggleSidebarBtn" type="button"
          class="inline-flex items-center p-2 text-sm text-gray-500 hover:text-gray-900 rounded hover:bg-gray-100 focus:outline-none focus:ring-3 focus:ring-gray-300">
          <span class="sr-only">Toggle sidebar</span>
          <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20"
            xmlns="http://www.w3.org/2000/svg">
            <path clip-rule="evenodd" fill-rule="evenodd"
              d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z">
            </path>
          </svg>
        </button>
      </div>
      <ul class="pt-4 mt-4 space-y-2 font-semibold border-t-2 border-gray-200">
        <?php if (isset($_SESSION['user']) && strtolower($_SESSION['user']['role']) === "admin"): ?>
        <li>
          <?php if (Helper::currentPath() == '/dashboard'): ?>
            <a class="flex items-center p-2 secondary-color rounded-lg bg-indigo-100 boxring ring-indigo-300 group pointer-btn">
              <svg class="shrink-0 w-5 h-5 primary-color transition duration-75 group-hover:primary-color"
                aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 18">
                <path
                  d="M6.143 0H1.857A1.857 1.857 0 0 0 0 1.857v4.286C0 7.169.831 8 1.857 8h4.286A1.857 1.857 0 0 0 8 6.143V1.857A1.857 1.857 0 0 0 6.143 0Zm10 0h-4.286A1.857 1.857 0 0 0 10 1.857v4.286C10 7.169 10.831 8 11.857 8h4.286A1.857 1.857 0 0 0 18 6.143V1.857A1.857 1.857 0 0 0 16.143 0Zm-10 10H1.857A1.857 1.857 0 0 0 0 11.857v4.286C0 17.169.831 18 1.857 18h4.286A1.857 1.857 0 0 0 8 16.143v-4.286A1.857 1.857 0 0 0 6.143 10Zm10 0h-4.286A1.857 1.857 0 0 0 10 11.857v4.286c0 1.026.831 1.857 1.857 1.857h4.286A1.857 1.857 0 0 0 18 16.143v-4.286A1.857 1.857 0 0 0 16.143 10Z" />
              </svg>
              <span class="flex-1 ms-5 h-5 flex items-center whitespace-nowrap">Dashboard</span>
            </a>
          <?php else: ?>
            <a href="<?= Helper::basePath(); ?>dashboard" class="flex items-center p-2 text-gray-600 rounded-lg  hover:bg-gray-100 group">
              <svg class="shrink-0 w-5 h-5 text-gray-500 transition duration-75 group-hover:text-gray-900"
                aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="3 3 18 18">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9.143 4H4.857A.857.857 0 0 0 4 4.857v4.286c0 .473.384.857.857.857h4.286A.857.857 0 0 0 10 9.143V4.857A.857.857 0 0 0 9.143 4Zm10 0h-4.286a.857.857 0 0 0-.857.857v4.286c0 .473.384.857.857.857h4.286A.857.857 0 0 0 20 9.143V4.857A.857.857 0 0 0 19.143 4Zm-10 10H4.857a.857.857 0 0 0-.857.857v4.286c0 .473.384.857.857.857h4.286a.857.857 0 0 0 .857-.857v-4.286A.857.857 0 0 0 9.143 14Zm10 0h-4.286a.857.857 0 0 0-.857.857v4.286c0 .473.384.857.857.857h4.286a.857.857 0 0 0 .857-.857v-4.286a.857.857 0 0 0-.857-.857Z" />
              </svg>
              <span class="flex-1 ms-5 h-5 flex items-center whitespace-nowrap">Dashboard</span>
            </a>
          <?php endif; ?>
        </li>
        <li>
          <?php if (Helper::currentPath() == '/kategori'): ?>
            <a class="flex items-center p-2 secondary-color rounded-lg bg-indigo-100 boxring ring-indigo-300 group pointer-btn">
              <svg class="shrink-0 w-5 h-5 primary-color transition duration-75 group-hover:primary-color"
                aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="3 3 18 18">
                <path
                  d="M12.8638 3.49613C12.6846 3.18891 12.3557 3 12 3s-.6846.18891-.8638.49613l-3.49998 6c-.18042.30929-.1817.69147-.00336 1.00197S8.14193 11 8.5 11h7c.3581 0 .6888-.1914.8671-.5019.1784-.3105.1771-.69268-.0033-1.00197l-3.5-6ZM4 13c-.55228 0-1 .4477-1 1v6c0 .5523.44772 1 1 1h6c.5523 0 1-.4477 1-1v-6c0-.5523-.4477-1-1-1H4Zm12.5-1c-2.4853 0-4.5 2.0147-4.5 4.5s2.0147 4.5 4.5 4.5 4.5-2.0147 4.5-4.5-2.0147-4.5-4.5-4.5Z" />
              </svg>
              <span class="flex-1 ms-5 h-5 flex items-center whitespace-nowrap">Kategori</span>
            </a>
          <?php else: ?>
            <a href="<?= Helper::basePath(); ?>kategori" class="flex items-center p-2 text-gray-600 rounded-lg  hover:bg-gray-100 group">
              <svg class="shrink-0 w-5 h-5 text-gray-500 transition duration-75 group-hover:text-gray-900"
                aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="3 3 18 18">
                <path stroke="currentColor" stroke-linejoin="round" stroke-width="2"
                  d="M4.07141 14v6h5.99999v-6H4.07141Zm4.5-4h6.99999l-3.5-6-3.49999 6Zm7.99999 10c1.933 0 3.5-1.567 3.5-3.5s-1.567-3.5-3.5-3.5-3.5 1.567-3.5 3.5 1.567 3.5 3.5 3.5Z" />
              </svg>
              <span class="flex-1 ms-5 h-5 flex items-center whitespace-nowrap">Kategori</span>
            </a>
          <?php endif; ?>
        </li>
        <li>
          <?php if (Helper::currentPath() == '/jenis'): ?>
            <a class="flex items-center p-2 secondary-color rounded-lg bg-indigo-100 boxring ring-indigo-300 group pointer-btn">
              <svg class="shrink-0 w-5 h-5 primary-color transition duration-75 group-hover:primary-color"
                aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="3 3 18 18">
                <path
                  d="M20 14h-2.722L11 20.278a5.511 5.511 0 0 1-.9.722H20a1 1 0 0 0 1-1v-5a1 1 0 0 0-1-1ZM9 3H4a1 1 0 0 0-1 1v13.5a3.5 3.5 0 1 0 7 0V4a1 1 0 0 0-1-1ZM6.5 18.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2ZM19.132 7.9 15.6 4.368a1 1 0 0 0-1.414 0L12 6.55v9.9l7.132-7.132a1 1 0 0 0 0-1.418Z" />
              </svg>
              <span class="flex-1 ms-5 h-5 flex items-center whitespace-nowrap">Jenis</span>
            </a>
          <?php else: ?>
            <a href="<?= Helper::basePath(); ?>jenis" class="flex items-center p-2 text-gray-600 rounded-lg  hover:bg-gray-100 group">
              <svg class="shrink-0 w-5 h-5 text-gray-500 transition duration-75 group-hover:text-gray-900"
                aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="3 3 18 18">
                <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                  d="M7.111 20A3.111 3.111 0 0 1 4 16.889v-12C4 4.398 4.398 4 4.889 4h4.444a.89.89 0 0 1 .89.889v12A3.111 3.111 0 0 1 7.11 20Zm0 0h12a.889.889 0 0 0 .889-.889v-4.444a.889.889 0 0 0-.889-.89h-4.389a.889.889 0 0 0-.62.253l-3.767 3.665a.933.933 0 0 0-.146.185c-.868 1.433-1.581 1.858-3.078 2.12Zm0-3.556h.009m7.933-10.927 3.143 3.143a.889.889 0 0 1 0 1.257l-7.974 7.974v-8.8l3.574-3.574a.889.889 0 0 1 1.257 0Z" />
              </svg>
              <span class="flex-1 ms-5 h-5 flex items-center whitespace-nowrap">Jenis</span>
            </a>
          <?php endif; ?>
        </li>
        <li>
          <?php if (Helper::currentPath() == '/kondisi'): ?>
            <a class="flex items-center p-2 secondary-color rounded-lg bg-indigo-100 boxring ring-indigo-300 group pointer-btn">
              <svg class="shrink-0 w-5 h-5 primary-color transition duration-75 group-hover:primary-color"
                aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="2 2 20 20">
                <path fill-rule="evenodd"
                  d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm13.707-1.293a1 1 0 0 0-1.414-1.414L11 12.586l-1.793-1.793a1 1 0 0 0-1.414 1.414l2.5 2.5a1 1 0 0 0 1.414 0l4-4Z"
                  clip-rule="evenodd" />
              </svg>
              <span class="flex-1 ms-5 h-5 flex items-center whitespace-nowrap">Kondisi</span>
            </a>
          <?php else: ?>
            <a href="<?= Helper::basePath(); ?>kondisi" class="flex items-center p-2 text-gray-600 rounded-lg  hover:bg-gray-100 group">
              <svg class="shrink-0 w-5 h-5 text-gray-500 transition duration-75 group-hover:text-gray-900"
                aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="2 2 20 20">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M8.5 11.5 11 14l4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
              </svg>
              <span class="flex-1 ms-5 h-5 flex items-center whitespace-nowrap">Kondisi</span>
            </a>
          <?php endif; ?>
        </li>
        <li>
          <?php if (Helper::currentPath() == '/barang'): ?>
            <a class="flex items-center p-2 secondary-color rounded-lg bg-indigo-100 boxring ring-indigo-300 group pointer-btn">
              <svg class="shrink-0 w-5 h-5 primary-color transition duration-75 group-hover:primary-color"
                aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="3 3 18 18">
                <path fill-rule="evenodd"
                  d="M5.024 3.783A1 1 0 0 1 6 3h12a1 1 0 0 1 .976.783L20.802 12h-4.244a1.99 1.99 0 0 0-1.824 1.205 2.978 2.978 0 0 1-5.468 0A1.991 1.991 0 0 0 7.442 12H3.198l1.826-8.217ZM3 14v5a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-5h-4.43a4.978 4.978 0 0 1-9.14 0H3Zm5-7a1 1 0 0 1 1-1h6a1 1 0 1 1 0 2H9a1 1 0 0 1-1-1Zm0 2a1 1 0 0 0 0 2h8a1 1 0 1 0 0-2H8Z"
                  clip-rule="evenodd" />
              </svg>
              <span class="flex-1 ms-5 h-5 flex items-center whitespace-nowrap">Barang</span>
            </a>
          <?php else: ?>
            <a href="<?= Helper::basePath(); ?>barang" class="flex items-center p-2 text-gray-600 rounded-lg  hover:bg-gray-100 group">
              <svg class="shrink-0 w-5 h-5 text-gray-500 transition duration-75 group-hover:text-gray-900"
                aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="3 3 18 18">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M4 13h3.439a.991.991 0 0 1 .908.6 3.978 3.978 0 0 0 7.306 0 .99.99 0 0 1 .908-.6H20M4 13v6a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-6M4 13l2-9h12l2 9M9 7h6m-7 3h8" />
              </svg>
              <span class="flex-1 ms-5 h-5 flex items-center whitespace-nowrap">Barang</span>
            </a>
          <?php endif; ?>
        </li>
        <?php elseif (isset($_SESSION['user']) && strtolower($_SESSION['user']['role']) === "pegawai"): ?>
        <li>
          <?php if (Helper::currentPath() == '/dashboard'): ?>
            <a class="flex items-center p-2 secondary-color rounded-lg bg-indigo-100 boxring ring-indigo-300 group pointer-btn">
              <svg class="shrink-0 w-5 h-5 primary-color transition duration-75 group-hover:primary-color"
                aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 18">
                <path
                  d="M6.143 0H1.857A1.857 1.857 0 0 0 0 1.857v4.286C0 7.169.831 8 1.857 8h4.286A1.857 1.857 0 0 0 8 6.143V1.857A1.857 1.857 0 0 0 6.143 0Zm10 0h-4.286A1.857 1.857 0 0 0 10 1.857v4.286C10 7.169 10.831 8 11.857 8h4.286A1.857 1.857 0 0 0 18 6.143V1.857A1.857 1.857 0 0 0 16.143 0Zm-10 10H1.857A1.857 1.857 0 0 0 0 11.857v4.286C0 17.169.831 18 1.857 18h4.286A1.857 1.857 0 0 0 8 16.143v-4.286A1.857 1.857 0 0 0 6.143 10Zm10 0h-4.286A1.857 1.857 0 0 0 10 11.857v4.286c0 1.026.831 1.857 1.857 1.857h4.286A1.857 1.857 0 0 0 18 16.143v-4.286A1.857 1.857 0 0 0 16.143 10Z" />
              </svg>
              <span class="flex-1 ms-5 h-5 flex items-center whitespace-nowrap">Dashboard</span>
            </a>
          <?php else: ?>
            <a href="<?= Helper::basePath(); ?>dashboard" class="flex items-center p-2 text-gray-600 rounded-lg  hover:bg-gray-100 group">
              <svg class="shrink-0 w-5 h-5 text-gray-500 transition duration-75 group-hover:text-gray-900"
                aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="3 3 18 18">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9.143 4H4.857A.857.857 0 0 0 4 4.857v4.286c0 .473.384.857.857.857h4.286A.857.857 0 0 0 10 9.143V4.857A.857.857 0 0 0 9.143 4Zm10 0h-4.286a.857.857 0 0 0-.857.857v4.286c0 .473.384.857.857.857h4.286A.857.857 0 0 0 20 9.143V4.857A.857.857 0 0 0 19.143 4Zm-10 10H4.857a.857.857 0 0 0-.857.857v4.286c0 .473.384.857.857.857h4.286a.857.857 0 0 0 .857-.857v-4.286A.857.857 0 0 0 9.143 14Zm10 0h-4.286a.857.857 0 0 0-.857.857v4.286c0 .473.384.857.857.857h4.286a.857.857 0 0 0 .857-.857v-4.286a.857.857 0 0 0-.857-.857Z" />
              </svg>
              <span class="flex-1 ms-5 h-5 flex items-center whitespace-nowrap">Dashboard</span>
            </a>
          <?php endif; ?>
        </li>
        <li>
          <?php if (Helper::currentPath() == '/peminjaman'): ?>
            <a class="flex items-center p-2 secondary-color rounded-lg bg-indigo-100 boxring ring-indigo-300 group pointer-btn">
              <svg class="shrink-0 w-5 h-5 primary-color transition duration-75 group-hover:primary-color"
                aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="3 3 18 18">
                <path
                  d="M20 14h-2.722L11 20.278a5.511 5.511 0 0 1-.9.722H20a1 1 0 0 0 1-1v-5a1 1 0 0 0-1-1ZM9 3H4a1 1 0 0 0-1 1v13.5a3.5 3.5 0 1 0 7 0V4a1 1 0 0 0-1-1ZM6.5 18.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2ZM19.132 7.9 15.6 4.368a1 1 0 0 0-1.414 0L12 6.55v9.9l7.132-7.132a1 1 0 0 0 0-1.418Z" />
              </svg>
              <span class="flex-1 ms-5 h-5 flex items-center whitespace-nowrap">Peminjaman</span>
            </a>
          <?php else: ?>
            <a href="<?= Helper::basePath(); ?>peminjaman" class="flex items-center p-2 text-gray-600 rounded-lg  hover:bg-gray-100 group">
              <svg class="shrink-0 w-5 h-5 text-gray-500 transition duration-75 group-hover:text-gray-900"
                aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="3 3 18 18">
                <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                  d="M7.111 20A3.111 3.111 0 0 1 4 16.889v-12C4 4.398 4.398 4 4.889 4h4.444a.89.89 0 0 1 .89.889v12A3.111 3.111 0 0 1 7.11 20Zm0 0h12a.889.889 0 0 0 .889-.889v-4.444a.889.889 0 0 0-.889-.89h-4.389a.889.889 0 0 0-.62.253l-3.767 3.665a.933.933 0 0 0-.146.185c-.868 1.433-1.581 1.858-3.078 2.12Zm0-3.556h.009m7.933-10.927 3.143 3.143a.889.889 0 0 1 0 1.257l-7.974 7.974v-8.8l3.574-3.574a.889.889 0 0 1 1.257 0Z" />
              </svg>
              <span class="flex-1 ms-5 h-5 flex items-center whitespace-nowrap">Peminjaman</span>
            </a>
          <?php endif; ?>
        </li>
        <li>
          <?php if (Helper::currentPath() == '/pengembalian'): ?>
            <a class="flex items-center p-2 secondary-color rounded-lg bg-indigo-100 boxring ring-indigo-300 group pointer-btn">
              <svg class="shrink-0 w-5 h-5 primary-color transition duration-75 group-hover:primary-color"
                aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="2 2 20 20">
                <path fill-rule="evenodd"
                  d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm13.707-1.293a1 1 0 0 0-1.414-1.414L11 12.586l-1.793-1.793a1 1 0 0 0-1.414 1.414l2.5 2.5a1 1 0 0 0 1.414 0l4-4Z"
                  clip-rule="evenodd" />
              </svg>
              <span class="flex-1 ms-5 h-5 flex items-center whitespace-nowrap">Pengembalian</span>
            </a>
          <?php else: ?>
            <a href="<?= Helper::basePath(); ?>pengembalian" class="flex items-center p-2 text-gray-600 rounded-lg  hover:bg-gray-100 group">
              <svg class="shrink-0 w-5 h-5 text-gray-500 transition duration-75 group-hover:text-gray-900"
                aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="2 2 20 20">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M8.5 11.5 11 14l4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
              </svg>
              <span class="flex-1 ms-5 h-5 flex items-center whitespace-nowrap">Pengembalian</span>
            </a>
          <?php endif; ?>
        </li>
        <?php endif; ?>
      </ul>
    </div>
    <div class="mt-4 space-y-2 border-t-2 border-gray-200 sidebar-user">
      <button id="dropdownUserNameButton" data-dropdown-toggle="dropdownUserName"
        class="flex justify-between items-center p-2 ps-1 mt-4 w-full h-14.5 rounded-lg hover:bg-gray-100 focus:ring-3 focus:ring-gray-300 pointer-btn"
        type="button">
        <div class="flex items-center">
          <!-- Perlu pengecekan user saat ini sama ambil data dari database -->
          <!-- <img src="https://flowbite.s3.amazonaws.com/blocks/marketing-ui/avatars/bonnie-green.png"
            class="mr-3 w-8 h-8 rounded-full" alt="Bonnie avatar"> -->
          <svg class="shrink-0 w-7 h-7 text-gray-500 transition duration-75" aria-hidden="true"
            xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="2 2 20 20">
            <path fill-rule="evenodd"
              d="M12 20a7.966 7.966 0 0 1-5.002-1.756l.002.001v-.683c0-1.794 1.492-3.25 3.333-3.25h3.334c1.84 0 3.333 1.456 3.333 3.25v.683A7.966 7.966 0 0 1 12 20ZM2 12C2 6.477 6.477 2 12 2s10 4.477 10 10c0 5.5-4.44 9.963-9.932 10h-.138C6.438 21.962 2 17.5 2 12Zm10-5c-1.84 0-3.333 1.455-3.333 3.25S10.159 13.5 12 13.5c1.84 0 3.333-1.455 3.333-3.25S13.841 7 12 7Z"
              clip-rule="evenodd" />
          </svg>
          <div class="text-left ms-4 sidebar-user-details">
            <div class="font-semibold leading-none text-gray-950 mb-1"><?= htmlspecialchars($_SESSION['user']['role']) ?></div>
            <div class="text-sm text-gray-700"><?= htmlspecialchars($_SESSION['user']['username']) ?></div>
          </div>
        </div>
        <svg class="w-5 h-5 text-gray-500 dropdown-arrow" fill="currentColor" viewBox="0 0 20 20"
          xmlns="http://www.w3.org/2000/svg">
          <path fill-rule="evenodd"
            d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
            clip-rule="evenodd"></path>
        </svg>
      </button>
      <div id="dropdownUserName" class="z-10 w-60 bg-white rounded shadow-sm hidden"
        data-popper-placement="top">
        <?php
          $other_role = ($_SESSION['user']['role'] === 'Admin') ? 'pegawai' : 'admin';
          $switch_link = Helper::basePath() . $other_role;
        ?>
        <a href="<?= $switch_link ?>" class="flex items-center hover:bg-gray-100 my-3 px-4 rounded">
          <svg class="shrink-0 w-8 h-8 mr-3 text-gray-500 transition duration-75" aria-hidden="true"
            xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="2 2 20 20">
            <path fill-rule="evenodd"
              d="M12 20a7.966 7.966 0 0 1-5.002-1.756l.002.001v-.683c0-1.794 1.492-3.25 3.333-3.25h3.334c1.84 0 3.333 1.456 3.333 3.25v.683A7.966 7.966 0 0 1 12 20ZM2 12C2 6.477 6.477 2 12 2s10 4.477 10 10c0 5.5-4.44 9.963-9.932 10h-.138C6.438 21.962 2 17.5 2 12Zm10-5c-1.84 0-3.333 1.455-3.333 3.25S10.159 13.5 12 13.5c1.84 0 3.333-1.455 3.333-3.25S13.841 7 12 7Z"
              clip-rule="evenodd" />
          </svg>
          <div class="text-left">
            <div class="font-semibold leading text-gray-950 mb-0.5">
              Ganti ke <?= htmlspecialchars(ucfirst($other_role)) ?>
            </div>
            <div class="text-sm text-gray-700">
              Klik untuk login
            </div>
          </div>
        </a>
      </div>
      <a href="<?= Helper::basePath(); ?>logout"
        class="flex items-center p-2 text-rose-500 rounded-lg  hover:bg-gray-100 group">
        <svg class="shrink-0 w-5 h-5 text-rose-400 transition duration-75 group-hover:text-rose-600"
          aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="3 3 18 18">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M20 12H8m12 0-4 4m4-4-4-4M9 4H7a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h2" />
        </svg>
        <span class="flex-1 ms-5 h-5 flex items-center whitespace-nowrap">Keluar</span>
      </a>
    </div>
  </div>
</aside>

<script src="<?= Helper::basePath(); ?>node_modules/flowbite/dist/flowbite.min.js"></script>
<script>
  const sidebar = document.getElementById('sidebar');
  const userButton = document.getElementById('dropdownUserNameButton');
  const userDropdown = document.getElementById('dropdownUserName');
  document.getElementById('toggleSidebarBtn').addEventListener('click', function () {
    sidebar.classList.toggle('sidebar-collapsed');
  });
  sidebar.addEventListener('mouseenter', function () {
    if (sidebar.classList.contains('sidebar-collapsed') && userDropdown.classList.contains('hidden')) {
      sidebar.classList.toggle('sidebar-hover');
    }
  });
  sidebar.addEventListener('mouseleave', function () {
    if (sidebar.classList.contains('sidebar-hover') && userDropdown.classList.contains('hidden')) {
      sidebar.classList.toggle('sidebar-hover');
    }
  });
  userButton.addEventListener('focusout', function () {
    if (!userDropdown.classList.contains('hidden')) {
      sidebar.classList.toggle('sidebar-hover');
    }
  });
</script>