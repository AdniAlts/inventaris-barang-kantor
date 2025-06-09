<div id="delete-barang-modal" class="fixed inset-0 flex items-center justify-center edit-modal-backdrop hidden delete-modal-backdrop  opacity-0 transition-opacity duration-500 ease-in-out">
    <div class="bg-white rounded-lg shadow-2xl p-8 w-11/12 max-w-md mx-auto transform transition-all duration-300 scale-95 opacity-0 delete-modal-content">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-2xl font-bold text-red-600">Confirm Deletion</h3>
            <button type="button" id="close-delete-modal" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>
        </div>

        <p class="text-gray-700 mb-6">Are you sure you want to delete this item? This action cannot be undone.</p>

        <!-- Item Details to be Removed -->
        <div class="bg-gray-100 p-4 rounded-lg mb-6 flex flex-col sm:flex-row items-center gap-4">
            <img id="delete-item-image" src="" alt="Item to be deleted" class="rounded-lg w-20 h-20 object-cover flex-shrink-0 border border-gray-300">
            <div class="flex-grow text-center sm:text-left">
                <p class="font-bold text-lg text-gray-900" id="delete-item-codename"></p>
                <p class="text-sm text-gray-600" id="delete-item-condition"></p>
                <p class="text-sm text-gray-600" id="delete-item-status"></p>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end space-x-4 pt-4">
            <form method="POST" action="<?php echo Helper::basePath() . 'barang/delete'  ?>">
                <input type="hidden" id="elmo-s-world" name="elmo">

                <button type="submit" id="confirm-delete-barang" class="px-5 py-2.5 text-sm font-medium text-white bg-red-700 rounded-lg hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 transition-colors duration-200">Yes, I'm sure</button>
            </form>
            <button type="button" id="cancel-delete-barang" class="px-5 py-2.5 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:outline-none focus:ring-gray-100 transition-colors duration-200">Cancel</button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const deleteBarangModal = document.getElementById('delete-barang-modal');
        const closeDeleteModalBtn = document.getElementById('close-delete-modal');
        const cancelDeleteModalBtn = document.getElementById('cancel-delete-barang');

        const deleteItemImage = document.getElementById('delete-item-image');
        const deleteItemCodename = document.getElementById('delete-item-codename');
        const deleteItemCondition = document.getElementById('delete-item-condition');
        const deleteItemStatus = document.getElementById('delete-item-status');

        let currentBarangIdToDelete = null; // To store the ID of the item to be deleted
        document.querySelectorAll('.delete-barang-btn').forEach(button => {
            button.addEventListener('click', () => {
                const eventData = {
                    id: button.getAttribute('data-barang-id'),
                    codename: button.getAttribute('data-barang-codename'),
                    image: button.getAttribute('data-barang-image'),
                    condition: button.getAttribute('data-barang-condition'),
                    status: button.getAttribute('data-barang-status')
                };

                const openEvent = new CustomEvent('openDeleteBarangModal', {
                    detail: eventData
                });
                document.dispatchEvent(openEvent);
            });
        });


        document.addEventListener('openDeleteBarangModal', (event) => {
            const mainContentArea = document.getElementById('main-content-area');
            mainContentArea.classList.add('blur-background');
            const data = event.detail;

            document.getElementById('elmo-s-world').value = data.id;

            currentBarangIdToDelete = data.id;
            deleteItemImage.src = data.image;
            deleteItemCodename.textContent = data.codename;
            deleteItemCondition.textContent = `Condition: ${data.condition}`;
            deleteItemStatus.textContent = `Status: ${data.status}`;

            deleteBarangModal.classList.remove('hidden');
            requestAnimationFrame(() => {
                deleteBarangModal.classList.add('opacity-100');
                deleteBarangModal.classList.remove('opacity-0');
            });
            deleteBarangModal.querySelector('.delete-modal-content').classList.remove('scale-95', 'opacity-0');
            deleteBarangModal.querySelector('.delete-modal-content').classList.add('scale-100', 'opacity-100');
            document.body.classList.add('overflow-hidden'); // Prevent main page scroll
        });

        function closeDeleteModal() {
            deleteBarangModal.classList.remove('opacity-100');
            deleteBarangModal.classList.add('opacity-0');
            const mainContentArea = document.getElementById('main-content-area');
            mainContentArea.classList.remove('blur-active');
            mainContentArea.classList.remove('blur-background');
            deleteBarangModal.querySelector('.delete-modal-content').classList.remove('scale-100', 'opacity-100');
            deleteBarangModal.querySelector('.delete-modal-content').classList.add('scale-95', 'opacity-0');
            deleteBarangModal.addEventListener('transitionend', () => {
                deleteBarangModal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
                document.dispatchEvent(new CustomEvent('closeDeleteBarangModal'));
            }, {
                once: true
            });
        }

        // Event listeners for closing the modal
        closeDeleteModalBtn.addEventListener('click', closeDeleteModal);
        cancelDeleteModalBtn.addEventListener('click', closeDeleteModal);

        // Close modal when clicking on the backdrop
        deleteBarangModal.addEventListener('click', (event) => {
            if (event.target === deleteBarangModal) {
                closeDeleteModal();
            }
        });

    });
</script>