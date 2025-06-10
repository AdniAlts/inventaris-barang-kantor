<?php
require_once __DIR__ . "/../../../modules/search.php";
$db = (new db())->conn;
$statesArr = Search::getAllStates($db);
?>
<div id="create-barang-modal" class="fixed inset-0 flex items-center justify-center hidden edit-modal-backdrop opacity-0 transition-opacity duration-500 ease-in-out">
    <div id="skibidi" class="relative bg-white p-8 rounded-lg shadow-md max-w-2xl w-full mx-4 my-8 overflow-y-auto max-h-[90vh] opacity-0 transition-opacity duration-500 ease-in-out">
        <button id="close-create-barang-modal" class="absolute top-3 right-3 text-gray-500 hover:text-gray-800 text-2xl font-bold focus:outline-none">&times;</button>
        <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gray-100" style="min-height: auto; padding: 0;">
            <div class="max-w-2xl w-full bg-white p-8 rounded-lg shadow-md">
                <h1 class="text-2xl font-bold text-gray-800">Buat Barang</h1>
                <p class="text-sm text-gray-500 mb-8">Guided process for adding new items.</p>

                <div class="flex items-center justify-between mb-10">
                    <div class="flex items-center gap-2">
                        <div id="step1-indicator" class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center text-sm font-bold">1</div>
                        <span id="step1-text" class="font-semibold text-blue-600">Kategori</span>
                    </div>
                    <div class="w-full border-t mx-2"></div>
                    <div class="flex items-center gap-2">
                        <div id="step2-indicator" class="w-8 h-8 rounded-full bg-gray-300 text-gray-600 flex items-center justify-center text-sm font-bold">2</div>
                        <span id="step2-text" class="text-gray-400 font-semibold">Detail</span>
                    </div>
                    <div class="w-full border-t mx-2"></div>
                    <div class="flex items-center gap-2">
                        <div id="step3-indicator" class="w-8 h-8 rounded-full bg-gray-300 text-gray-600 flex items-center justify-center text-sm font-bold">3</div>
                        <span id="step3-text" class="text-gray-400 font-semibold">Review</span>
                    </div>
                </div>

                <form id="barangForm" class="space-y-10" method="get" action="<?php echo Helper::basePath() . 'barang/create' ?>">
                    <div id="step1" class="form-step">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Step 1: Pilih Kategori (Wajib) dan Jenis (Optional)</h2>
                        <div class="space-y-4">
                            <div>
                                <label for="kategori" class="block text-sm font-medium text-gray-700">Kategori</label>
                                <input name="kategori" autocomplete="off" list="kategori-list" id="kategori" class="w-full mt-1 p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" placeholder="Select Kategori" required />
                                <datalist id="kategori-list">
                                    <?php
                                    $arr = Barang::read();
                                    foreach ($arr as $category):
                                    ?>
                                        <option value="<?php echo $category['nama'] ?>">
                                        <?php
                                    endforeach;
                                        ?>
                                </datalist>
                            </div>
                            <div>
                                <label for="jenis" class="block text-sm font-medium text-gray-700">Jenis</label>
                                <input name="jenis" autocomplete="off" list="jenis-list" id="jenis" class="w-full mt-1 p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" placeholder="Select Jenis" required disabled />
                                <datalist id="jenis-list">

                                </datalist>
                            </div>
                        </div>
                        <div class="flex justify-end mt-6">
                            <button type="button" onclick="goToStep(1)" class="bg-blue-600 text-white px-5 py-2 rounded-md hover:bg-blue-700">Next</button>
                        </div>
                    </div>

                    <div id="step2" class="form-step hidden">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Step 2: Add Details</h2>
                        <div class="space-y-4">
                            <div>
                                <label for="jumlah" class="block text-sm font-medium text-gray-700">Jumlah Barang</label>
                                <input name="jumlah" type="number" id="jumlah" min="1" placeholder="e.g., 50" class="w-full mt-1 p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" required />
                            </div>
                            <div>
                                <label for="kualitas" class="block text-sm font-medium text-gray-700">Kondisi</label>
                                <select name="kualitas" id="kualitas" class="w-full mt-1 p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" required>
                                    <option value="">Select</option>
                                    <?php foreach ($statesArr as $key => $value) {
                                        echo "<option value='{$value['id_state']}'>{$value['nama']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="flex justify-between mt-6">
                            <button type="button" onclick="goToStep(0)" class="px-5 py-2 border border-gray-300 rounded-md hover:bg-gray-100">Previous</button>
                            <button type="button" onclick="goToStep(2)" class="bg-blue-600 text-white px-5 py-2 rounded-md hover:bg-blue-700">Next</button>
                        </div>
                    </div>

                    <div id="step3" class="form-step hidden">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Step 3: Review & Submit</h2>
                        <div class="bg-gray-50 p-5 rounded-lg border border-gray-200 space-y-2 text-sm text-gray-700">
                            <p><strong class="font-medium">Kategori:</strong> <span id="review-kategori"></span></p>
                            <p><strong class="font-medium">Jenis:</strong> <span id="review-jenis"></span></p>
                            <p><strong class="font-medium">Jumlah Barang:</strong> <span id="review-jumlah"></span></p>
                            <p><strong class="font-medium">Kondisi:</strong> <span id="review-kualitas"></span></p>
                        </div>
                        <div class="flex justify-between mt-6">
                            <button type="button" onclick="goToStep(1)" class="px-5 py-2 border border-gray-300 rounded-md hover:bg-gray-100">Previous</button>
                            <button type="submit" class="bg-green-600 text-white px-5 py-2 rounded-md hover:bg-green-700">Submit Barang</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$list = [];

foreach ($arr as $kategori) {
    $items = [];
    foreach ($kategori['jenis_items'] as $jenis) {
        $items[] = $jenis['nama'];
    }
    $list[$kategori['nama']] = $items;
}
?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const mainContentArea = document.getElementById('main-content-area');

        // --- Create Barang Modal Elements ---
        const createBarangBtn = document.getElementById('create-barang-btn');
        const createBarangModal = document.getElementById('create-barang-modal');
        const closeCreateBarangModalBtn = document.getElementById('close-create-barang-modal');

        // --- Multi-Step Form Elements (moved inside DOMContentLoaded) ---
        const form = document.getElementById('barangForm');
        const formSteps = document.querySelectorAll('.form-step');
        const stepIndicators = [
            document.getElementById('step1-indicator'),
            document.getElementById('step2-indicator'),
            document.getElementById('step3-indicator')
        ];
        const stepTexts = [
            document.getElementById('step1-text'),
            document.getElementById('step2-text'),
            document.getElementById('step3-text')
        ];

        let currentStepIndex = 0;

        const jenisOptions = <?php echo json_encode($list, JSON_HEX_TAG | JSON_HEX_APOS); ?>;

        function showStep(stepIdx) {
            formSteps.forEach((step, index) => {
                step.classList.toggle('hidden', index !== stepIdx);
            });
            updateStepperUI(stepIdx);
            currentStepIndex = stepIdx;
        }

        function updateStepperUI(activeStepIdx) {
            stepIndicators.forEach((indicator, index) => {
                const stepNumber = index + 1;
                indicator.classList.remove('bg-blue-600', 'text-white', 'bg-gray-300');
                stepTexts[index].classList.remove('font-medium', 'text-blue-600', 'text-gray-400');

                if (index < activeStepIdx) {
                    indicator.innerHTML = '<svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/></svg>';
                    indicator.classList.add('bg-blue-600', 'text-white');
                    stepTexts[index].classList.add('font-medium', 'text-blue-600');
                } else if (index === activeStepIdx) {
                    indicator.innerHTML = stepNumber.toString();
                    indicator.classList.add('bg-blue-600', 'text-white');
                    stepTexts[index].classList.add('font-medium', 'text-blue-600');
                } else {
                    indicator.innerHTML = stepNumber.toString();
                    indicator.classList.add('bg-gray-300');
                    stepTexts[index].classList.add('text-gray-400');
                }
            });
        }

        function validateCurrentStep() {
            let isValid = true;
            const currentStepDiv = formSteps[currentStepIndex];
            const requiredInputs = currentStepDiv.querySelectorAll('[required]');

            requiredInputs.forEach(input => {
                if (input.value.trim() === '' || (input.type === 'number' && parseInt(input.value) < 1)) {
                    input.classList.add('error-border');
                    isValid = false;
                } else {
                    input.classList.remove('error-border');
                }
            });
            return isValid;
        }

        window.goToStep = function(targetStepIdx) { // Make goToStep globally accessible
            if (targetStepIdx > currentStepIndex) {
                if (!validateCurrentStep()) {
                    return;
                }
                if (targetStepIdx === 2) {
                    populateReviewStep();
                }
            }
            showStep(targetStepIdx);
        }

        const kategoriInput = document.getElementById('kategori');
        const jenisInput = document.getElementById('jenis');
        const jenisDatalist = document.getElementById('jenis-list');

        kategoriInput.addEventListener('input', () => {
            const selectedKategori = kategoriInput.value;
            jenisDatalist.innerHTML = '';
            jenisInput.value = '';
            jenisInput.disabled = true;

            if (jenisOptions[selectedKategori]) {
                jenisOptions[selectedKategori].forEach(jenis => {
                    const option = document.createElement('option');
                    option.value = jenis;
                    jenisDatalist.appendChild(option);
                });
                jenisInput.disabled = false;
            }
            jenisInput.classList.remove('error-border');
            kategoriInput.classList.remove('error-border');
        });

        jenisInput.addEventListener('blur', () => {
            const selectedJenis = jenisInput.value;
            const kategori = kategoriInput.value;
            if (kategori && jenisOptions[kategori] && !jenisOptions[kategori].includes(selectedJenis)) {
                jenisInput.value = '';
            }
        });

        function populateReviewStep() {
            document.getElementById('review-kategori').textContent = kategoriInput.value;
            document.getElementById('review-jenis').textContent = jenisInput.value;
            document.getElementById('review-jumlah').textContent = document.getElementById('jumlah').value;
            document.getElementById('review-kualitas').textContent = document.getElementById('kualitas').value;
        }

        // form.addEventListener('submit', function(e) {
        //     e.preventDefault();

        //     // Validate first
        //     if (!validateCurrentStep()) {
        //         return;
        //     }

        //     // Gather all form data
        //     const formData = new FormData(form);
        //     const data = Object.fromEntries(formData.entries());

        //     // Show in alert (formatted for readability)
        //     alert("Data to be submitted:\n" + JSON.stringify(data, null, 2));

        //     // Your existing submission logic
        //     console.log('Barang created:', data);
        //     form.reset();
        //     closeCreateBarangModal();
        //     showStep(0);
        // });

        const contentPopup = document.getElementById('skibidi');

        // --- Create Barang Modal Functions ---
        function openCreateBarangModal() {
            isClosing = false;

            const mainContentArea = document.getElementById('main-content-area');
            mainContentArea.classList.add('blur-background');

            requestAnimationFrame(() => {
                createBarangModal.classList.add('opacity-100');
                createBarangModal.classList.remove('opacity-0');
                contentPopup.classList.add('opacity-100');
                contentPopup.classList.remove('opacity-0');
            });

            document.body.classList.add('overflow-hidden');
            createBarangModal.classList.remove('hidden');
        }

        function closeCreateBarangModal() {
            if (isClosing) return; // Skip if already closing
            isClosing = true;

            mainContentArea.classList.remove('blur-background');
            createBarangModal.classList.remove('opacity-100');
            createBarangModal.classList.add('opacity-0');
            contentPopup.classList.remove('opacity-100');
            contentPopup.classList.add('opacity-0');
            document.body.classList.remove('overflow-hidden');
            setTimeout(() => {
                createBarangModal.classList.add('hidden');
            }, 500);
        }

        createBarangBtn.addEventListener('click', openCreateBarangModal);
        closeCreateBarangModalBtn.addEventListener('click', closeCreateBarangModal);
        createBarangModal.addEventListener('click', (event) => {
            if (event.target === createBarangModal) {
                closeCreateBarangModal();
            }
        });

        // Initial setup for the first step of the multi-step form
        showStep(0);



    });
</script>