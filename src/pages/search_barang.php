<?php
session_start();
require "../modules/search.php";

$db = new db();
$connDB = $db->conn;

register_shutdown_function(function() use ($db) {
  if ($db && $db->conn)
    $db->close();
});

$result = null;
$search_display_term = null;
$search_query_term = null;

$categories = GetNames::category($connDB);
$states = GetNames::state($connDB);
$statuses = [ 'Tersedia' => 'Tersedia',
              'Dipinjam' => 'Dipinjam' ];

$filters_display = [];
foreach ($categories as $category)
  $filters_display['category_' . $category['id_kategori']] = [ 'label' => $category['nama'],
                                                               'type' => 'category',
                                                               'value' => $category['nama'] ];
foreach ($states as $state)
  $filters_display['state_' . $state['id_state']] = [ 'label' => $state['nama'],
                                                      'type' => 'state',
                                                      'value' => $state['nama'] ];
foreach ($statuses as $status_key => $status_label)
  $filters_display['status_' . strtolower(str_replace(' ', '_', $status_key))] = [ 'label' => $status_label,
                                                                                                                     'type' => 'status',
                                                                                                                     'value' => $status_key ];
$filter_keys = array_keys($filters_display);
if (!isset($_SESSION['active_filters'])) {
  $_SESSION['active_filters'] = [];
} else
  $_SESSION['active_filters'] = array_intersect($_SESSION['active_filters'], $filter_keys);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  if (isset($_POST['clear_filters'])) {
    $_SESSION['active_filters'] = [];
    unset($_SESSION['last_search_query']);
  } else {
    $posted_search_term = (!empty($_POST['search'])) ? trim($_POST['search']) : null;

    $clicked_filter_key = null;
    foreach ($filter_keys as $key)
      if (isset($_POST[$key]) && $_POST[$key] === 'true') {
        $clicked_filter_key = $key;
        break;
      }

    if ($clicked_filter_key) {
      if (in_array($clicked_filter_key, $_SESSION['active_filters'])) {
        $_SESSION['active_filters'] = array_values(array_diff($_SESSION['active_filters'], [$clicked_filter_key]));
      } else {
        $_SESSION['active_filters'][] = $clicked_filter_key;
      }
    }

    if ($posted_search_term !== null) {
      $_SESSION['last_search_query'] = $posted_search_term;
    }
  }

  header("Location: /inventaris-barang-kantor/search");
  exit();
}

$search_query_term = $_SESSION['last_search_query'] ?? null;
$active_filter_keys = $_SESSION['active_filters'] ?? [];

$selected_categories = [];
$selected_states = [];
$selected_status = [];

foreach ($active_filter_keys as $key)
  if (isset($filters_display[$key])) {
    $filter_data = $filters_display[$key];
    if ($filter_data['type'] === 'category')
      $selected_categories[] = $filter_data['value'];
    elseif ($filter_data['type'] === 'state')
      $selected_states[] = $filter_data['value'];
    elseif ($filter_data['type'] === 'status')
      $selected_status[] = $filter_data['value'];
  }

$search_display_term = null;

unset($_SESSION['last_search_query']);

if ($search_query_term !== null || !empty($selected_categories) || !empty($selected_states) || !empty($selected_status))
  $result = Search::search($search_query_term, 'all', $selected_categories, $selected_states, $selected_status, $connDB);

foreach ($filter_keys as $key)
  $$key = in_array($key, $active_filter_keys);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <style>
    .filter-button { /* Renamed for clarity and consistency */
      padding: 8px 12px;
      margin: 2px;
      border: 1px solid #ccc;
      background-color: #f0f0f0;
      cursor: pointer;
    }
    .filter-button.active { /* Style for active buttons */
      background-color: #4CAF50;
      color: white;
    }
  </style>
</head>
<body>
  <form method="POST" action="">
    <input type="text" name="search" placeholder="Search" value="<?= htmlspecialchars($search_display_term) ?? ''?>">
    <input type="submit" value="Search">
    <br>
    <?php
    foreach ($filters_display as $key => $data) {
      $active = $$key;
      $class = 'filter-button' . ($active ? ' active' : '');
      echo "<button type='submit' name='{$key}' value='true' class='{$class}'>" . htmlspecialchars($data['label']) . " (" . $data['type'] . ")</button> ";
    }
    ?>
    <button type="submit" name="clear_filters" value="true" class="filter-button">Clear All Filters</button>
  </form>
  <?php
  if ($result != null)
    while ($row = mysqli_fetch_array($result)) {
      echo "Kode Barang: " . htmlspecialchars($row["kode_barang"]) . "<br>";
      echo "Nama Barang: " . htmlspecialchars($row["item_name"] ?? 'N/A') . "<br>";
      echo "Status: " . htmlspecialchars($row["status"]) . "<br>";
      echo "Kondisi: " . htmlspecialchars($row["state"]) . "<br>";
      echo "Kategori: " . htmlspecialchars($row["kategori"]) . "<br>";
      echo "<br>";
    }
  ?>
</body>
</html>