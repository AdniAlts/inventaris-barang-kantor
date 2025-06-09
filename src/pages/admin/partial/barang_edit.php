<div id="edit-barang-modal" class="fixed inset-0 flex items-center justify-center hidden edit-modal-backdrop opacity-0 transition-opacity duration-500 ease-in-out">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-2xl p-8 w-11/12 max-w-lg mx-auto transform transition-all duration-300 scale-95 opacity-0 edit-modal-content">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white" id="edit-modal-title">Edit Barang: <span id="barang-codename-display"></span></h3>
            <button type="button" id="close-edit-modal" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-700 dark:hover:text-white">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>
        </div>

        <form class="space-y-6" method="post" action="<?php echo Helper::basePath() . "barang/update" ?>" enctype="multipart/form-data">
            <input type="hidden" id="kode-barang-input" name="kode_barang">

            <!-- Current Image Display -->
            <div class="flex flex-col items-center">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Current Image:</label>
                <img id="current-barang-image" src="" alt="Current Barang Image" class="rounded-lg w-24 h-24 object-cover border border-gray-300 dark:border-gray-600 mb-4">
                <input type="file" id="image-upload-input" name="image" accept="image/png, image/jpeg" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400">
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-300" id="file_input_help">PNG, JPG, JPEG, or GIF (Max 5MB).</p>
            </div>

            <!-- Condition Selector -->
            <div>
                <label for="barang-condition" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Condition:</label>
                <select id="barang-condition" name="kualitas" class="block w-full p-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option value="1">Baik</option>
                    <option value="2">Patah</option>
                    <option value="3">Rusak</option>
                    <option value="4">Aus</option>
                    <option value="5">Retak</option>
                </select>
            </div>

            <!-- Status Selector -->
            <div>
                <label for="barang-status" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Status:</label>
                <select id="barang-status" name="status" class="block w-full p-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option value="dipinjam">dipinjam</option>
                    <option value="tersedia">tersedia</option>
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-4 pt-4">
                <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 transition-colors duration-200">Save Changes</button>
                <button type="button" id="cancel-edit-barang" class="px-5 py-2.5 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:outline-none focus:ring-gray-100 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700 dark:focus:ring-gray-700 transition-colors duration-200">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const editBarangModal = document.getElementById('edit-barang-modal');
        const closeEditModalBtn = document.getElementById('close-edit-modal');
        const cancelEditModalBtn = document.getElementById('cancel-edit-barang');
        // const saveEditModalBtn = document.getElementById('save-edit-barang');

        const barangCodenameDisplay = document.getElementById('barang-codename-display');
        const currentBarangImage = document.getElementById('current-barang-image');
        const imageUploadInput = document.getElementById('image-upload-input');
        const barangConditionSelect = document.getElementById('barang-condition');
        const barangStatusSelect = document.getElementById('barang-status');

        // Pop up edit
        document.addEventListener('openEditBarangModal', (event) => {

            const mainContentArea = document.getElementById('main-content-area');
            mainContentArea.classList.add('blur-background');
            const data = event.detail;

            barangCodenameDisplay.textContent = data.codename;
            currentBarangImage.src = data.image;
            barangConditionSelect.value = data.condition;
            barangStatusSelect.value = data.status;
            imageUploadInput.value = '';

            document.getElementById('kode-barang-input').value = data.id;
            if (!currentBarangImage.src || currentBarangImage.src.trim() === '') {
                currentBarangImage.src = "<?php echo Helper::basePath() . 'src/' ?>" + data.image;
            }


            editBarangModal.classList.remove('hidden');
            requestAnimationFrame(() => {
                editBarangModal.classList.add('opacity-100');
                editBarangModal.classList.remove('opacity-0');
            });
            editBarangModal.querySelector('.edit-modal-content').classList.remove('scale-95', 'opacity-0');
            editBarangModal.querySelector('.edit-modal-content').classList.add('scale-100', 'opacity-100');
            document.body.classList.add('overflow-hidden');
        });

        // Close Pop up
        function closeEditModal() {
            editBarangModal.classList.remove('opacity-100');
            editBarangModal.classList.add('opacity-0');
            const mainContentArea = document.getElementById('main-content-area');
            mainContentArea.classList.remove('blur-active');
            mainContentArea.classList.remove('blur-background');
            editBarangModal.querySelector('.edit-modal-content').classList.remove('scale-100', 'opacity-100');
            editBarangModal.querySelector('.edit-modal-content').classList.add('scale-95', 'opacity-0');

            editBarangModal.addEventListener('transitionend', () => {
                editBarangModal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
                document.dispatchEvent(new CustomEvent('closeEditBarangModal'));
                editBarangModal.removeEventListener('transitionend', handleTransition);
            }, {
                once: true
            });
        }

        // Event listeners for closing the modal
        closeEditModalBtn.addEventListener('click', closeEditModal);
        cancelEditModalBtn.addEventListener('click', closeEditModal);

        // Close modal when clicking on the backdrop
        editBarangModal.addEventListener('click', (event) => {
            if (event.target === editBarangModal) {
                closeEditModal();
            }
        });

        // Handle image preview for upload (optional)
        imageUploadInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    currentBarangImage.src = e.target.result; // Update preview with new image
                };
                reader.readAsDataURL(file);
            }
        });

        // // Handle Save Changes (dummy function)
        // saveEditModalBtn.addEventListener('click', () => {
        //     const barangId = barangCodenameDisplay.textContent; // In a real app, this would be from data passed to modal
        //     const newCondition = barangConditionSelect.value;
        //     const newStatus = barangStatusSelect.value;
        //     // const newImageFile = imageUploadInput.files[0]; // Access the uploaded file

        //     console.log(`Saving changes for Barang ID: ${barangId}`);
        //     console.log(`New Condition: ${newCondition}`);
        //     console.log(`New Status: ${newStatus}`);
        //     // if (newImageFile) {
        //     //     console.log(`New Image File: ${newImageFile.name}`);
        //     // } else {
        //     //     console.log('No new image uploaded.');
        //     // }

        //     // In a real application, you would send this data to your backend
        //     // After successful save, close the modal
        //     closeEditModal();
        // });
    });
</script>