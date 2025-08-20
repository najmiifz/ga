<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight" id="header-title">
            Dasbor & Tabel Aset
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <main>
                <div id="assets-view" class="view">
                    </div>

                <div id="vehicles-view" class="view hidden">
                    </div>

                <div id="splicers-view" class="view hidden">
                    </div>

                <div id="maintenance-view" class="view hidden">
                    </div>

            </main>
        </div>
    </div>


    @include('assets.partials.modal-form')
    @include('assets.partials.modal-detail')
    @include('assets.partials.modal-confirm')

    @push('scripts')
    {{-- Memuat library Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        // STATE MANAGEMENT
        let allAssets = [];
        let currentUserRole = '{{ auth()->user()->role }}';
        let charts = {}; // Untuk menyimpan instance semua chart

        // HELPER FUNCTIONS
        const formatCurrency = (number) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
        const showToast = (message, isSuccess = true) => { /* ... (fungsi toast bisa disalin dari HTML asli) ... */ };

        // --- TEMPLATING ---
        // Fungsi ini akan membuat blok HTML untuk setiap view agar tidak mengotori kode utama
        const templates = {
            assetsView: () => `
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                    <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-200">Dasbor & Tabel Aset</h1>
                    ${currentUserRole === 'admin' ? `
                    <div class="flex-shrink-0 flex gap-2 w-full sm:w-auto">
                        <button id="export-csv-btn" class="btn-action w-full md:w-auto inline-flex items-center justify-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md hover:bg-gray-50 dark:hover:bg-gray-700">Ekspor CSV</button>
                        <button id="add-asset-btn" class="btn-action w-full md:w-auto inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">Tambah Aset</button>
                    </div>` : ''}
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border dark:border-gray-700">
                    <div class="flex flex-wrap items-end gap-4 w-full mb-6">
                        </div>
                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                </thead>
                            <tbody id="asset-table-body" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700"></tbody>
                        </table>
                    </div>
                    <div id="pagination-controls" class="mt-4 flex items-center justify-between"></div>
                </div>
            `,
            vehiclesView: () => `
                <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-200 mb-6">Dasbor Kendaraan</h1>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                    <div class="lg:col-span-1 bg-white dark:bg-gray-800 p-5 rounded-lg shadow-sm border dark:border-gray-700"><h3 class="text-sm font-medium text-gray-500 mb-2">Status Pajak Kendaraan</h3><canvas id="tax-status-chart" style="max-height: 250px;"></canvas></div>
                    <div class="lg:col-span-2 bg-white dark:bg-gray-800 p-5 rounded-lg shadow-sm border dark:border-gray-700">
                        <canvas id="vehicle-service-chart" style="max-height: 250px;"></canvas>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border dark:border-gray-700">
                    <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-4">Data Tabel Kendaraan</h2>
                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700"><thead class="bg-gray-50 dark:bg-gray-700/50">
                        </thead><tbody id="vehicle-table-body" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700"></tbody></table>
                    </div>
                </div>
            `,
            // ... (buat template untuk splicersView dan maintenanceView dengan cara yang sama)
        };


        // --- RENDERING FUNCTIONS ---
        // Fungsi untuk merender setiap view ke dalam DOM
        function renderAssetsView(assets) {
            document.getElementById('assets-view').innerHTML = templates.assetsView();
            // Inisialisasi Chart, isi tabel, pasang event listener untuk filter & tombol
        }
        function renderVehiclesView(vehicles) {
             document.getElementById('vehicles-view').innerHTML = templates.vehiclesView();
             // Inisialisasi Chart khusus kendaraan, isi tabel, dll.
        }
        // ... (fungsi render lainnya)


        // --- NAVIGATION & ROUTING ---
        const views = document.querySelectorAll('.view');
        const navLinks = document.querySelectorAll('.nav-link');
        const headerTitle = document.getElementById('header-title');

        function switchView(viewName) {
            views.forEach(view => view.classList.add('hidden'));
            document.getElementById(`${viewName}-view`).classList.remove('hidden');

            navLinks.forEach(link => {
                link.setAttribute('aria-current', link.dataset.view === viewName ? 'page' : 'false');
                 // Sesuaikan class active berdasarkan logika Breeze/Tailwind
                 if(link.dataset.view === viewName) {
                    link.classList.add('border-indigo-400', 'text-gray-900'); // Contoh class active
                 } else {
                    link.classList.remove('border-indigo-400', 'text-gray-900');
                 }
            });

            // Ganti judul header
            const activeLink = document.querySelector(`.nav-link[data-view="${viewName}"]`);
            headerTitle.textContent = activeLink.textContent.trim();

            // Panggil fungsi render yang sesuai
            if (viewName === 'assets') renderAssetsView(allAssets);
            if (viewName === 'vehicles') {
                const vehicleData = allAssets.filter(a => a.tipe === 'Kendaraan');
                renderVehiclesView(vehicleData);
            }
            // ... (panggil render function lainnya)
        }

        navLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const viewName = e.currentTarget.dataset.view;
                switchView(viewName);
            });
        });


        // --- INITIALIZATION ---
        async function initializeApp() {
            try {
                // Ambil semua data aset dari API Laravel kita
                const response = await fetch('{{ route("api.assets.data") }}');
                if (!response.ok) throw new Error('Gagal mengambil data dari server');
                allAssets = await response.json();

                // Tampilkan view awal (assets)
                switchView('assets');

            } catch (error) {
                console.error("Gagal inisialisasi aplikasi:", error);
                showToast(error.message, false);
            }
        }

        initializeApp();
    });
    </script>
    @endpush
</x-app-layout>
