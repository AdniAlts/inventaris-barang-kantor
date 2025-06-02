<?php
session_start();
require "../modules/search.php";

$db = new db();
$connDB = $db->conn;

register_shutdown_function(function() use ($db) {
  if ($db && $db->conn)
    $db->close();
});

// Fetch all items for client-side processing
$allItems = Search::getAllItemsForClientSide($connDB);

// Prepare filter definitions (this part remains largely the same)
$categories = GetNames::category($connDB);
$states = GetNames::state($connDB);
$statuses = [ 'Tersedia' => 'Tersedia',
              'Dipinjam' => 'Dipinjam' ];

$filters_display = [];
foreach ($categories as $category)
  $filters_display['category_' . strtolower(str_replace(' ', '_', $category['nama']))] = [ 'label' => $category['nama'],
                                                               'type' => 'category',
                                                               'value' => $category['nama'] ];
foreach ($states as $state)
  $filters_display['state_' . strtolower(str_replace(' ', '_', $state['nama']))] = [ 'label' => $state['nama'],
                                                      'type' => 'state',
                                                      'value' => $state['nama'] ];
foreach ($statuses as $status_key => $status_label)
  $filters_display['status_' . strtolower(str_replace(' ', '_', $status_key))] = [ 'label' => $status_label,
                                                                                                                     'type' => 'status',
                                                                                                                     'value' => $status_key ];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Search Items (Client-Side)</title>
  <style>
    .filter-button {
      padding: 8px 12px;
      margin: 2px;
      border: 1px solid #ccc;
      background-color: #f0f0f0;
      cursor: pointer;
      border-radius: 4px;
    }
    .filter-button.active {
      background-color: #4CAF50;
      color: white;
      border-color: #4CAF50;
    }
    #search-container {
      margin-bottom: 15px;
    }
    #search-input {
      padding: 8px;
      margin-right: 5px;
      border: 1px solid #ccc;
      border-radius: 4px;
    }
    #results-container .item {
      border: 1px solid #eee;
      padding: 10px;
      margin-bottom: 10px;
      border-radius: 4px;
    }
    #results-container .item p {
      margin: 5px 0;
    }
  </style>
</head>
<body>
  <h1>Inventory Search</h1>

  <div id="search-container">
    <input type="text" id="search-input" placeholder="Search by name, code, status...">
  </div>

  <div id="filters-container">
    <strong>Filters:</strong><br>
    <?php
    foreach ($filters_display as $key => $data) {
      // Removed server-side active class, JS will handle this
      echo "<button type='button' class='filter-button' data-filter-key='{$key}' data-filter-type='{$data['type']}' data-filter-value='".htmlspecialchars($data['value'], ENT_QUOTES)."'>" . htmlspecialchars($data['label']) . "</button> ";
    }
    ?>
    <button type="button" id="clear-filters-button" class="filter-button" style="background-color: #f44336; color:white;">Clear All Filters</button>
  </div>
  
  <hr>

  <div id="results-container">
    <!-- Search results will be populated here by JavaScript -->
  </div>

  <script>
    const allItems = <?= json_encode($allItems); ?>;
    const filtersDisplay = <?= json_encode($filters_display); ?>; // Though not directly used in filtering logic below, good to have if needed for UI
    
    let currentSearchTerm = '';
    let activeFilters = {
      category: [],
      state: [],
      status: []
    };

    const searchInput = document.getElementById('search-input');
    const resultsContainer = document.getElementById('results-container');
    const filterButtons = document.querySelectorAll('.filter-button[data-filter-key]');
    const clearFiltersButton = document.getElementById('clear-filters-button');

    function renderItems(itemsToRender) {
      resultsContainer.innerHTML = ''; // Clear previous results
      if (itemsToRender.length === 0) {
        resultsContainer.innerHTML = '<p>No items match your criteria.</p>';
        return;
      }

      itemsToRender.forEach(item => {
        const itemDiv = document.createElement('div');
        itemDiv.className = 'item';
        itemDiv.innerHTML = 
          '<p><strong>Code:</strong> ' + (item.kode_barang || 'N/A') + '</p>' +
          '<p><strong>Name:</strong> ' + (item.item_name || 'N/A') + '</p>' +
          '<p><strong>Status:</strong> ' + (item.status || 'N/A') + '</p>' +
          '<p><strong>Condition:</strong> ' + (item.state_name || 'N/A') + '</p>' +
          '<p><strong>Category:</strong> ' + (item.category_name || 'N/A') + '</p>';
        resultsContainer.appendChild(itemDiv);
      });
    }

    function applyFiltersAndSearch() {
      let filteredItems = allItems;
      const searchTerm = searchInput.value.toLowerCase().trim();

      // 1. Filter by search term
      if (searchTerm) {
        filteredItems = filteredItems.filter(item => {
          return (item.item_name && item.item_name.toLowerCase().includes(searchTerm)) ||
                 (item.kode_barang && item.kode_barang.toLowerCase().includes(searchTerm)) ||
                 (item.status && item.status.toLowerCase().includes(searchTerm)) ||
                 (item.state_name && item.state_name.toLowerCase().includes(searchTerm)) ||
                 (item.category_name && item.category_name.toLowerCase().includes(searchTerm));
        });
      }

      // 2. Filter by active categories
      if (activeFilters.category.length > 0) {
        filteredItems = filteredItems.filter(item => activeFilters.category.includes(item.category_name));
      }

      // 3. Filter by active states
      if (activeFilters.state.length > 0) {
        filteredItems = filteredItems.filter(item => activeFilters.state.includes(item.state_name));
      }

      // 4. Filter by active statuses
      if (activeFilters.status.length > 0) {
        filteredItems = filteredItems.filter(item => activeFilters.status.includes(item.status));
      }
      
      renderItems(filteredItems);
    }

    searchInput.addEventListener('input', () => {
      applyFiltersAndSearch();
    });

    filterButtons.forEach(button => {
      button.addEventListener('click', () => {
        const filterType = button.dataset.filterType;
        const filterValue = button.dataset.filterValue;
        
        button.classList.toggle('active');
        
        if (activeFilters[filterType]) {
          const index = activeFilters[filterType].indexOf(filterValue);
          if (index > -1) {
            activeFilters[filterType].splice(index, 1); // Remove if exists
          } else {
            activeFilters[filterType].push(filterValue); // Add if not exists
          }
        }
        applyFiltersAndSearch();
      });
    });

    clearFiltersButton.addEventListener('click', () => {
      activeFilters = { category: [], state: [], status: [] };
      filterButtons.forEach(button => button.classList.remove('active'));
      searchInput.value = '';
      applyFiltersAndSearch();
    });

    // Initial render of all items
    applyFiltersAndSearch();

  </script>
</body>
</html>