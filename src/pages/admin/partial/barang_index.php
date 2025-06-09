<style>
    .blur-background>div:first-child {
        transition: filter 0.5s ease;
        filter: blur(4px);
        pointer-events: none;
        user-select: none;
    }

    .edit-modal-backdrop {
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 40;
    }
</style>

<div id="main-content-area" class="p-4">
    <div class="p-4 rounded-lg dark:border-gray-700 min-h-screen">
        <h2 class="text-3xl font-extrabold mb-6 text-gray-900 dark:text-white">Barang Inventory</h2>
        <button id="create-barang-btn" type="button" class="mb-6 inline-flex items-center px-5 py-2.5 text-sm font-medium text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
            <svg class="me-2 -ms-1 w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path>
            </svg>
            Add New Barang
        </button>

        <!-- Kategori Tabs (Dynamic from $arr) -->
        <div class="mb-4 border-b border-gray-200 dark:border-gray-700">
            <ul class="flex flex-wrap -mb-px text-base font-medium text-center" id="kategori-tabs" data-tabs-toggle="#kategori-tab-content" role="tablist">
                <?php
                $arr = Barang::read();
                $first_category = true;
                foreach ($arr as $category):
                    $kategori_id = strtolower(str_replace(' ', '-', $category['nama']));
                ?>
                    <li class="me-2" role="presentation">
                        <button class="inline-block p-4 border-b-2 rounded-t-lg <?php echo $first_category ? 'active:border-blue-600 active:text-blue-600 dark:active:border-blue-500 dark:active:text-blue-500' : ''; ?> hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300"
                            id="<?php echo $kategori_id; ?>-kategori-tab"
                            data-tabs-target="#<?php echo $kategori_id; ?>-kategori"
                            type="button"
                            role="tab"
                            aria-controls="<?php echo $kategori_id; ?>-kategori"
                            aria-selected="<?php echo $first_category ? 'true' : 'false'; ?>">
                            <?php echo htmlspecialchars($category['nama']); ?>
                        </button>
                    </li>
                <?php
                    $first_category = false;
                endforeach;
                ?>
            </ul>
        </div>

        <!-- Kategori Tab Content -->
        <div id="kategori-tab-content">
            <?php
            $first_category = true;
            foreach ($arr as $category):
                $kategori_id = strtolower(str_replace(' ', '-', $category['nama']));
                $has_multiple_jenis = count($category['jenis_items']) > 1;
            ?>

                <!-- Kategori Content -->
                <div class="<?php echo $first_category ? '' : 'hidden'; ?> p-4 rounded-lg bg-white dark:bg-gray-800 shadow-md"
                    id="<?php echo $kategori_id; ?>-kategori"
                    role="tabpanel"
                    aria-labelledby="<?php echo $kategori_id; ?>-kategori-tab">
                    <h3 class="text-2xl font-semibold mb-5 text-gray-900 dark:text-white"><?php echo htmlspecialchars($category['nama']); ?> Items</h3>

                    <?php if ($has_multiple_jenis): ?>
                        <div class="mb-6">
                            <label for="<?php echo $kategori_id; ?>-jenis-select" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Select Jenis:</label>
                            <select id="<?php echo $kategori_id; ?>-jenis-select" class="block w-full p-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                <option value="" disabled>Choose a Jenis</option>
                                <?php
                                $first_jenis = true;
                                foreach ($category['jenis_items'] as $jenis):
                                    $jenis_id = strtolower(str_replace(' ', '-', $jenis['nama']));
                                ?>
                                    <option value="<?php echo $jenis_id; ?>-jenis" <?php echo $first_jenis ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($jenis['nama']); ?>
                                    </option>
                                <?php
                                    $first_jenis = false;
                                endforeach;
                                ?>
                            </select>
                        </div>

                        <div id="<?php echo $kategori_id; ?>-jenis-content-wrapper" class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <?php
                            $first_jenis = true;
                            foreach ($category['jenis_items'] as $jenis):
                                $jenis_id = strtolower(str_replace(' ', '-', $jenis['nama']));
                            ?>
                                <div class="<?php echo $first_jenis ? '' : 'hidden'; ?>" id="<?php echo $jenis_id; ?>-jenis" role="tabpanel">
                                    <h4 class="text-xl font-medium mb-2 text-gray-900 dark:text-white">Jenis: <?php echo htmlspecialchars($jenis['nama']); ?></h4>
                                    <p class="text-lg text-gray-700 dark:text-gray-200 mb-6">Stock: <?php echo $jenis['stok']; ?> units available</p>

                                    <h5 class="text-lg font-medium mb-4 text-gray-900 dark:text-white">Individual Barang Units:</h5>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                                        <?php foreach ($jenis['barang_items'] as $barang):
                                            $image_url = $barang['gambar_url'] ? $barang['gambar_url'] : 'https://placehold.co/150x100/a0c4ff/2c3e50?text=' . urlencode($jenis['nama']);
                                            $status_display = $barang['status'] == 'tersedia' ? 'Idle' : ucfirst($barang['status']);
                                        ?>
                                            <!-- Barang Unit -->
                                            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-3 flex items-center gap-3">
                                                <img src="<?php echo $image_url; ?>" alt="<?php echo htmlspecialchars($jenis['nama']); ?> unit" class="barang-image rounded-full w-10 h-10 object-cover flex-shrink-0 cursor-pointer">
                                                <p class="font-semibold text-gray-900 dark:text-white flex-grow"><?php echo htmlspecialchars($barang['kode']); ?></p>
                                                <div class="text-right flex-shrink-0">
                                                    <p class="text-xs text-gray-600 dark:text-gray-400">Condition: <?php echo htmlspecialchars($barang['state']); ?></p>
                                                    <p class="text-xs text-gray-600 dark:text-gray-400 mb-2">Status: <?php echo $status_display; ?></p>
                                                    <div class="flex space-x-2">
                                                        <button class="edit-barang-btn px-3 py-1 bg-blue-600 text-white text-xs font-semibold rounded-full hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 dark:bg-blue-700 dark:hover:bg-blue-800 dark:focus:ring-blue-900 transition-colors duration-200"
                                                            data-barang-id="<?php echo $barang['kode']; ?>"
                                                            data-barang-codename="<?php echo htmlspecialchars($barang['kode']); ?>"
                                                            data-barang-image="<?php echo $image_url; ?>"
                                                            data-barang-condition="<?php echo htmlspecialchars($barang['state']); ?>"
                                                            data-barang-status="<?php echo $status_display; ?>">Edit</button>
                                                        <button class="delete-barang-btn px-3 py-1 bg-red-600 text-white text-xs font-semibold rounded-full hover:bg-red-700 focus:ring-4 focus:ring-red-300 dark:bg-red-700 dark:hover:bg-red-800 dark:focus:ring-red-900 transition-colors duration-200"
                                                            data-barang-id="<?php echo $barang['kode']; ?>"
                                                            data-barang-codename="<?php echo htmlspecialchars($barang['kode']); ?>"
                                                            data-barang-image="<?php echo $image_url; ?>"
                                                            data-barang-condition="<?php echo htmlspecialchars($barang['state']); ?>"
                                                            data-barang-status="<?php echo $status_display; ?>">Delete</button>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php
                                $first_jenis = false;
                            endforeach;
                            ?>
                        </div>

                    <?php else: ?>
                        <!-- Direct display for single jenis categories -->
                        <?php
                        $jenis = reset($category['jenis_items']); // Get the first (and only) jenis
                        ?>
                        <h4 class="text-xl font-medium mb-2 text-gray-900 dark:text-white">Jenis: <?php echo htmlspecialchars($jenis['nama']); ?></h4>
                        <p class="text-lg text-gray-700 dark:text-gray-200 mb-6">Stock: <?php echo $jenis['stok']; ?> units available</p>

                        <h5 class="text-lg font-medium mb-4 text-gray-900 dark:text-white">Individual Barang Units:</h5>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                            <?php foreach ($jenis['barang_items'] as $barang):
                                $image_url = $barang['gambar_url'] ? $barang['gambar_url'] : 'https://placehold.co/150x100/a0c4ff/2c3e50?text=' . urlencode($jenis['nama']);
                                $status_display = $barang['status'] == 'tersedia' ? 'Idle' : ucfirst($barang['status']);
                            ?>
                                <!-- Barang Unit -->
                                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-3 flex items-center gap-3">
                                    <img src="<?php echo $image_url; ?>" alt="<?php echo htmlspecialchars($jenis['nama']); ?> unit" class="barang-image rounded-full w-10 h-10 object-cover flex-shrink-0 cursor-pointer">
                                    <p class="font-semibold text-gray-900 dark:text-white flex-grow"><?php echo htmlspecialchars($barang['kode']); ?></p>
                                    <div class="text-right flex-shrink-0">
                                        <p class="text-xs text-gray-600 dark:text-gray-400">Condition: <?php echo htmlspecialchars($barang['state']); ?></p>
                                        <p class="text-xs text-gray-600 dark:text-gray-400 mb-2">Status: <?php echo $status_display; ?></p>
                                        <div class="flex space-x-2">
                                            <button class="edit-barang-btn px-3 py-1 bg-blue-600 text-white text-xs font-semibold rounded-full hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 dark:bg-blue-700 dark:hover:bg-blue-800 dark:focus:ring-blue-900 transition-colors duration-200"
                                                data-barang-id="<?php echo $barang['kode']; ?>"
                                                data-barang-codename="<?php echo htmlspecialchars($barang['kode']); ?>"
                                                data-barang-image="<?php echo $image_url; ?>"
                                                data-barang-condition="<?php echo htmlspecialchars($barang['state']); ?>"
                                                data-barang-status="<?php echo $status_display; ?>">Edit</button>
                                            <button class="delete-barang-btn px-3 py-1 bg-red-600 text-white text-xs font-semibold rounded-full hover:bg-red-700 focus:ring-4 focus:ring-red-300 dark:bg-red-700 dark:hover:bg-red-800 dark:focus:ring-red-900 transition-colors duration-200"
                                                data-barang-id="<?php echo $barang['kode']; ?>"
                                                data-barang-codename="<?php echo htmlspecialchars($barang['kode']); ?>"
                                                data-barang-image="<?php echo $image_url; ?>"
                                                data-barang-condition="<?php echo htmlspecialchars($barang['state']); ?>"
                                                data-barang-status="<?php echo $status_display; ?>">Delete</button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

            <?php
                $first_category = false;
            endforeach;
            ?>
        </div>
    </div>
</div>

<!-- Image Maximized Modal -->
<div id="image-modal" class="fixed inset-0 flex items-center justify-center hidden modal-backdrop">
    <div class="relative p-4 rounded-lg shadow-lg">
        <button id="close-image-modal" class="absolute top-2 right-2 text-white text-3xl font-bold cursor-pointer hover:text-gray-300 focus:outline-none">&times;</button>
        <img id="maximized-image" src="" alt="Maximized Barang Image" class="modal-content rounded-lg">
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get a reference to the main content area for blurring
        const mainContentArea = document.getElementById('main-content-area');

        // Function to handle Jenis selection within a Kategori
        function setupJenisSelect(kategoriId) {
            const jenisSelect = document.getElementById(`${kategoriId}-jenis-select`);
            const jenisContentWrapper = document.getElementById(`${kategoriId}-jenis-content-wrapper`);

            if (!jenisSelect || !jenisContentWrapper) {
                return; // Exit if elements not found (e.g., for 'Books' which has no select)
            }

            const allJenisContents = jenisContentWrapper.querySelectorAll('div[id$="-jenis"]');

            // Function to update displayed content based on select value
            function updateJenisContent() {
                const selectedValue = jenisSelect.value;
                allJenisContents.forEach(contentDiv => {
                    if (contentDiv.id === selectedValue) {
                        contentDiv.classList.remove('hidden');
                    } else {
                        contentDiv.classList.add('hidden');
                    }
                });
            }

            // Event listener for select change
            jenisSelect.addEventListener('change', updateJenisContent);

            // Initial display: show the selected Jenis content on load
            updateJenisContent(); // This will show the initially selected option
        }

        // Flowbite's Tabs component handles the Kategori switching
        const kategoriTabButtons = document.querySelectorAll('#kategori-tabs button[data-tabs-target]');

        // Initial setup for the active Kategori tab
        const activeKategoriTab = document.querySelector('#kategori-tabs button[aria-selected="true"]');
        if (activeKategoriTab) {
            const activeKategoriId = activeKategoriTab.id.replace('-kategori-tab', '');
            setupJenisSelect(activeKategoriId);
        }

        // Set up event listeners for Kategori tab clicks to initialize their Jenis selects
        kategoriTabButtons.forEach(button => {
            button.addEventListener('click', () => {
                const targetKategoriId = button.id.replace('-kategori-tab', '');
                setTimeout(() => { // Small delay to allow Flowbite to activate the tab content
                    setupJenisSelect(targetKategoriId);
                }, 50);
            });
        });

        // --- Image Maximization Logic ---
        const imageModal = document.getElementById('image-modal');
        const maximizedImage = document.getElementById('maximized-image');
        const closeImageModalBtn = document.getElementById('close-image-modal'); // Changed ID to avoid conflict if any
        const barangImages = document.querySelectorAll('.barang-image'); // Select all small barang images

        // Function to open the image modal
        function openImageModal(imageUrl) {
            maximizedImage.src = imageUrl;
            imageModal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden'); // Prevent scrolling while modal is open
            mainContentArea.classList.add('blur-background'); // Apply blur to main content
        }

        // Function to close the image modal
        function closeImageModal() {
            imageModal.classList.add('hidden');
            maximizedImage.src = ''; // Clear image src
            document.body.classList.remove('overflow-hidden'); // Re-enable scrolling
            mainContentArea.classList.remove('blur-background'); // Remove blur from main content
        }

        // Add click listener to each barang image
        barangImages.forEach(img => {
            img.addEventListener('click', (event) => {
                openImageModal(event.target.src);
            });
        });

        // Add click listener to close button
        closeImageModalBtn.addEventListener('click', closeImageModal);

        // Close image modal when clicking outside the image (on the backdrop)
        imageModal.addEventListener('click', (event) => {
            if (event.target === imageModal) { // Only close if clicking the backdrop itself
                closeImageModal();
            }
        });

        // --- Edit Barang Modal Logic ---
        const editBarangButtons = document.querySelectorAll('.edit-barang-btn');

        editBarangButtons.forEach(button => {
            button.addEventListener('click', (event) => {
                // Extract data from the clicked button's data attributes
                const barangId = event.target.dataset.barangId;
                const codename = event.target.dataset.barangCodename;
                const image = event.target.dataset.barangImage;
                const condition = event.target.dataset.barangCondition;
                const status = event.target.dataset.barangStatus;

                // Dispatch a custom event to open the edit modal, passing the data
                document.dispatchEvent(new CustomEvent('openEditBarangModal', {
                    detail: {
                        id: barangId,
                        codename: codename,
                        image: image,
                        condition: condition,
                        status: status
                    }
                }));
                // Apply blur to the main content when the edit modal is opened
                mainContentArea.classList.add('blur-background');
            });
        });

        // Listen for custom event to close the edit modal (dispatched by the modal itself)
        document.addEventListener('closeEditBarangModal', () => {
            // Remove blur from the main content when the edit modal is closed
            mainContentArea.classList.remove('blur-background');
        });
    });
</script>