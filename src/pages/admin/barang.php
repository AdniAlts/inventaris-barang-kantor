<?php
require_once __DIR__ . '/../../config/helper.php';
require_once __DIR__ . "/barang_logic.php";
?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Barang - Inventaris Barang Kantor</title>
  <link rel="stylesheet" href="<?= Helper::basePath(); ?>src/output.css">
  <style>
    html {
      overflow: scroll;
      overflow-x: hidden;
    }

    ::-webkit-scrollbar {
      width: 0;
      background: transparent;
    }

    ::-webkit-scrollbar-thumb {
      background: #FF0000;
    }
  </style>
</head>


<body>
  <?php require_once __DIR__ . "/../sidebar_template.php" ?>

  <main class="p-4 sm:ml-64 main-content transition-margin duration-300">
    <?php require_once "partial/barang_create.php" ?>
    <?php require_once "partial/barang_edit.php" ?>
    <?php require_once "partial/barang_index.php" ?>
    <?php require_once "partial/barang_delete.php" ?>
  </main>

  <script src="<?= Helper::basePath(); ?>node_modules/flowbite/dist/flowbite.min.js"></script>
  <script>
    
  </script>
</body>

</html>