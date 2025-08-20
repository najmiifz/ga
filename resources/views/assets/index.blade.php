<x-app-layout>
    {{-- Slot untuk header halaman --}}
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Manajemen Aset Perusahaan
            </h2>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-2 sm:mt-0">
                Selamat datang, <span class="font-semibold">{{ auth()->user()->name }}</span>!
            </p>
        </div>
    </x-slot>

    {{-- Notifikasi Toast (untuk pesan sukses/error) --}}
    <div id="toast-notification" class="fixed top-5 right-5 z-[100] transition-transform duration-300 transform translate-x-full max-w-xs w-full">
        <div id="toast-content" class="flex items-center w-full p-4 text-gray-500 bg-white rounded-lg shadow-lg dark:text-gray-400 dark:bg-gray-800" role="alert">
            <div id="toast-icon" class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-green-500 bg-green-100 rounded-lg dark:bg-green-800 dark:text-green-200">
                {{-- Icon akan diisi oleh JavaScript --}}
            </div>
            <div id="toast-message" class="ms-3 text-sm font-normal">Pesan notifikasi.</div>
            <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex items-center justify-center h-8 w-8 dark:text-gray-500 dark:hover:text-white dark:bg-gray-800 dark:hover:bg-gray-700" id="toast-close-btn" aria-label="Close">
                <span class="sr-only">Close</span>
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/></svg>
            </button>
        </div>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <!-- Bagian Dasbor Ringkasan -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700"><h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Nilai Aset</h4><p id="total-value" class="text-3xl font-bold text-gray-900 dark:text-white mt-2">Rp 0</p></div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700"><h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Aset</h4><p id="total-assets" class="text-3xl font-bold text-gray-900 dark:text-white mt-2">0</p></div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700"><h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Aset Terpakai</h4><p id="used-assets" class="text-3xl font-bold text-red-600 dark:text-red-500 mt-2">0</p></div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700"><h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Aset Tersedia</h4><p id="available-assets" class="text-3xl font-bold text-green-600 dark:text-green-500 mt-2">0</p></div>
            </div>

            <!-- Bagian Chart -->
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
                {{-- PERBAIKAN: Mengganti inline style dengan class h-80 --}}
                <div class="relative lg:col-span-2 bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 h-80"><h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Aset per Tipe</h3><canvas id="type-chart"></canvas></div>
                <div class="relative lg:col-span-3 bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 h-80"><h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Aset per Project</h3><canvas id="project-chart"></canvas></div>
            </div>

            <!-- Kartu Utama untuk Tabel dan Filter -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-xl border border-gray-200 dark:border-gray-700">
                <div class="p-6 md:p-8 text-gray-900 dark:text-gray-100">
                    <!-- Header Kartu: Filter dan Tombol Aksi -->
                    <div class="mb-6">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                            <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Daftar Aset</h3>
                             @if(auth()->user()->role === 'admin')
                                <div class="flex items-center gap-x-2 mt-4 md:mt-0">
                                    <a href="{{ route('assets.export', request()->query()) }}" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>
                                        Ekspor CSV
                                    </a>
                                    <button type="button" id="add-asset-btn" class="w-full md:w-auto inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" /></svg>
                                        Tambah Aset Baru
                                    </button>
                                </div>
                            @endif
                        </div>
                        <form method="GET" action="{{ route('assets.index') }}" class="mt-4 p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg border dark:border-gray-700">
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                                <div><label for="search-pic" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cari PIC</label><input type="text" id="search-pic" name="search_pic" placeholder="Nama PIC..." class="w-full p-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-md shadow-sm text-sm" value="{{ request('search_pic') }}"></div>
                                <div><label for="filter-tipe" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipe</label><select id="filter-tipe" name="filter_tipe" class="w-full p-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-md shadow-sm text-sm"><option value="">Semua</option>@foreach($filters['tipe'] as $tipe)<option value="{{ $tipe }}" {{ request('filter_tipe') == $tipe ? 'selected' : '' }}>{{ $tipe }}</option>@endforeach</select></div>
                                <div><label for="filter-project" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Project</label><select id="filter-project" name="filter_project" class="w-full p-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-md shadow-sm text-sm"><option value="">Semua</option>@foreach($filters['project'] as $project)<option value="{{ $project }}" {{ request('filter_project') == $project ? 'selected' : '' }}>{{ $project }}</option>@endforeach</select></div>
                                <div><label for="filter-lokasi" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Lokasi</label><select id="filter-lokasi" name="filter_lokasi" class="w-full p-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-md shadow-sm text-sm"><option value="">Semua</option>@foreach($filters['lokasi'] as $lokasi)<option value="{{ $lokasi }}" {{ request('filter_lokasi') == $lokasi ? 'selected' : '' }}>{{ $lokasi }}</option>@endforeach</select></div>
                                <div class="flex items-end"><button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">Filter</button></div>
                            </div>
                        </form>
                    </div>

                    <!-- Tabel Aset -->
                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aset</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">PIC</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Lokasi & Project</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Info Pembelian</th>
                                    @if(auth()->user()->role === 'admin')<th class="relative px-6 py-3"><span class="sr-only">Aksi</span></th>@endif
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($assets as $asset)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                        <td class="px-6 py-4 whitespace-nowrap"><div class="flex items-center"><div class="text-sm font-medium text-gray-900 dark:text-white">{{ $asset->merk }}</div><div class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">{{ $asset->jenis_aset }}</div></div><div class="text-sm text-gray-500 dark:text-gray-400">SN: {{ $asset->nomor_sn }}</div></td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if(strtolower($asset->pic) == 'available')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Available</span>
                                            @else
                                                <div class="text-sm text-gray-900 dark:text-white">{{ $asset->pic }}</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap"><div class="text-sm text-gray-900 dark:text-white">{{ $asset->lokasi }}</div><div class="text-sm text-gray-500 dark:text-gray-400">{{ $asset->project }}</div></td>
                                        <td class="px-6 py-4 whitespace-nowrap"><div class="text-sm text-gray-900 dark:text-white">Rp {{ number_format($asset->harga_beli, 0, ',', '.') }}</div><div class="text-sm text-gray-500 dark:text-gray-400">Thn. {{ $asset->tahun_beli }} ({{ date('Y') - $asset->tahun_beli }} thn)</div></td>
                                        @if(auth()->user()->role === 'admin')
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <button class="edit-btn p-2 text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300" data-id="{{ $asset->id }}" title="Ubah Aset"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z" /><path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd" /></svg></button>
                                                <button class="delete-btn p-2 text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300" data-id="{{ $asset->id }}" title="Hapus Aset"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" /></svg></button>
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr><td colspan="{{ auth()->user()->role === 'admin' ? '5' : '4' }}" class="text-center text-gray-500 dark:text-gray-400 py-16">Tidak ada data aset yang cocok dengan filter.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-6">{{ $assets->links() }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal untuk Tambah/Ubah Aset -->
    <div id="asset-modal" class="fixed inset-0 bg-gray-900 bg-opacity-75 overflow-y-auto h-full w-full z-50 hidden" >
        <div class="relative top-10 mx-auto p-1 border w-full max-w-2xl shadow-lg rounded-xl bg-white dark:bg-gray-800">
            <form id="asset-form">
                @csrf
                <input type="hidden" id="asset-id" name="id">
                <input type="hidden" id="form-method" name="_method" value="POST">

                <div class="flex justify-between items-center p-4 border-b dark:border-gray-700">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white" id="modal-title"></h3>
                    <button type="button" class="cancel-btn p-2 rounded-full text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto custom-scrollbar">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div><label for="modal-tipe" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tipe</label><select id="modal-tipe" name="tipe" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-md shadow-sm" required><option value="">Pilih Tipe</option>@foreach($filters['tipe']->unique() as $option)<option value="{{ $option }}">{{ $option }}</option>@endforeach</select></div>
                        <div><label for="modal-jenis_aset" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jenis Aset</label><select id="modal-jenis_aset" name="jenis_aset" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-md shadow-sm" required><option value="">Pilih Jenis</option>@foreach($filters['jenis']->unique() as $option)<option value="{{ $option }}">{{ $option }}</option>@endforeach</select></div>
                        <div><label for="modal-merk" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Merk</label><input type="text" id="modal-merk" name="merk" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-md shadow-sm" required></div>
                        <div><label for="modal-nomor_sn" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nomor SN</label><input type="text" id="modal-nomor_sn" name="nomor_sn" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-md shadow-sm" required></div>
                        <div><label for="modal-pic" class="block text-sm font-medium text-gray-700 dark:text-gray-300">PIC</label><input type="text" id="modal-pic" name="pic" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-md shadow-sm" placeholder="Ketik 'Available' jika tersedia" required></div>
                        <div><label for="modal-project" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Project</label><select id="modal-project" name="project" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-md shadow-sm" required><option value="">Pilih Project</option>@foreach($filters['project']->unique() as $option)<option value="{{ $option }}">{{ $option }}</option>@endforeach</select></div>
                        <div><label for="modal-lokasi" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Lokasi</label><select id="modal-lokasi" name="lokasi" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-md shadow-sm" required><option value="">Pilih Lokasi</option>@foreach($filters['lokasi']->unique() as $option)<option value="{{ $option }}">{{ $option }}</option>@endforeach</select></div>
                        <div><label for="modal-tahun_beli" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tahun Beli</label><input type="number" id="modal-tahun_beli" name="tahun_beli" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-md shadow-sm" required></div>
                        <div><label for="modal-harga_beli" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Harga Beli</label><input type="number" id="modal-harga_beli" name="harga_beli" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-md shadow-sm" required></div>
                        <div><label for="modal-harga_sewa" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Harga Sewa</label><input type="number" id="modal-harga_sewa" name="harga_sewa" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-md shadow-sm" required></div>
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800/50 text-right rounded-b-xl">
                    <button type="button" class="cancel-btn inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600">Batal</button>
                    <button type="submit" id="save-asset-btn" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 disabled:opacity-50 ml-4">
                        <svg id="loading-spinner" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span id="save-btn-text">Simpan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const BASE_URL = '{{ url('/') }}';
            let projectChart, typeChart;

            const formatCurrency = (number) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);

            const showToast = (message, type = 'success') => {
                const toast = document.getElementById('toast-notification');
                const icon = document.getElementById('toast-icon');
                const messageEl = document.getElementById('toast-message');
                messageEl.textContent = message;

                icon.innerHTML = '';
                icon.className = 'inline-flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-lg';
                if (type === 'success') {
                    icon.classList.add('text-green-500', 'bg-green-100', 'dark:bg-green-800', 'dark:text-green-200');
                    icon.innerHTML = `<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/></svg>`;
                } else {
                    icon.classList.add('text-red-500', 'bg-red-100', 'dark:bg-red-800', 'dark:text-red-200');
                    icon.innerHTML = `<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 11.793a1 1 0 1 1-1.414 1.414L10 11.414l-2.293 2.293a1 1 0 0 1-1.414-1.414L8.586 10 6.293 7.707a1 1 0 0 1 1.414-1.414L10 8.586l2.293-2.293a1 1 0 0 1 1.414 1.414L11.414 10l2.293 2.293Z"/></svg>`;
                }
                toast.classList.remove('translate-x-full');
                setTimeout(() => toast.classList.add('translate-x-full'), 3000);
            };
            document.getElementById('toast-close-btn').addEventListener('click', () => document.getElementById('toast-notification').classList.add('translate-x-full'));

            const initCharts = () => {
                const pieCtx = document.getElementById('project-chart')?.getContext('2d');
                {{-- PERBAIKAN: Mengubah posisi legenda ke 'bottom' --}}
                if (pieCtx) projectChart = new Chart(pieCtx, { type: 'doughnut', data: { labels: [], datasets: [{ data: [], backgroundColor: ['#4f46e5', '#f59e0b', '#10b981', '#ef4444', '#3b82f6'] }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } } });
                const barCtx = document.getElementById('type-chart')?.getContext('2d');
                if (barCtx) typeChart = new Chart(barCtx, { type: 'bar', data: { labels: [], datasets: [{ label: 'Jumlah Aset', data: [], backgroundColor: '#6366f1' }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } } });
            };

            const updateDashboard = () => {
                fetch(`${BASE_URL}/assets/data`).then(res => res.json()).then(data => {
                    document.getElementById('total-value').textContent = formatCurrency(data.totalValue);
                    document.getElementById('total-assets').textContent = data.totalAssets;
                    document.getElementById('used-assets').textContent = data.usedAssets;
                    document.getElementById('available-assets').textContent = data.availableAssets;
                    if (projectChart) { projectChart.data.labels = data.projectChart.labels; projectChart.data.datasets[0].data = data.projectChart.data; projectChart.update(); }
                    if (typeChart) { typeChart.data.labels = data.typeChart.labels; typeChart.data.datasets[0].data = data.typeChart.data; typeChart.update(); }
                }).catch(error => console.error('Error fetching dashboard data:', error));
            };

            const modal = document.getElementById('asset-modal');
            const showModal = (assetId = null) => {
                const form = document.getElementById('asset-form');
                form.reset();
                document.getElementById('asset-id').value = '';

                if (assetId) {
                    document.getElementById('modal-title').textContent = 'Ubah Data Aset';
                    document.getElementById('form-method').value = 'PUT';
                    fetch(`${BASE_URL}/assets/${assetId}`).then(res => res.json()).then(asset => {
                        for (const key in asset) {
                            const input = form.querySelector(`[name="${key}"]`);
                            if (input) {
                                input.value = asset[key];
                            }
                        }
                        document.getElementById('asset-id').value = asset.id;
                    });
                } else {
                    document.getElementById('modal-title').textContent = 'Tambah Aset Baru';
                    document.getElementById('form-method').value = 'POST';
                }
                modal.classList.remove('hidden');
            };
            const hideModal = () => modal.classList.add('hidden');

            document.getElementById('asset-form').addEventListener('submit', function(e) {
                e.preventDefault();
                const assetId = document.getElementById('asset-id').value;
                const url = assetId ? `${BASE_URL}/assets/${assetId}` : `${BASE_URL}/assets`;
                const saveBtn = document.getElementById('save-asset-btn');
                const spinner = document.getElementById('loading-spinner');
                const btnText = document.getElementById('save-btn-text');

                spinner.classList.remove('hidden');
                btnText.textContent = 'Menyimpan...';
                saveBtn.disabled = true;

                fetch(url, {
                    method: 'POST',
                    body: new FormData(this),
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                }).then(res => res.json()).then(data => {
                    if (data.errors) {
                        const errorMessages = Object.values(data.errors).map(e => e[0]);
                        showToast('Gagal menyimpan: ' + errorMessages.join(', '), 'error');
                    } else {
                        showToast(data.message, 'success');
                        hideModal();
                        setTimeout(() => window.location.reload(), 1500);
                    }
                }).catch(err => {
                    showToast('Terjadi kesalahan teknis.', 'error');
                }).finally(() => {
                    spinner.classList.add('hidden');
                    btnText.textContent = 'Simpan';
                    saveBtn.disabled = false;
                });
            });

            document.querySelectorAll('.edit-btn').forEach(btn => btn.addEventListener('click', (e) => showModal(e.currentTarget.dataset.id)));
            document.querySelectorAll('.delete-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    if (confirm('Anda yakin ingin menghapus aset ini? Aksi ini tidak bisa dibatalkan.')) {
                        fetch(`${BASE_URL}/assets/${e.currentTarget.dataset.id}`, {
                            method: 'DELETE',
                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                        }).then(res => res.json()).then(data => {
                            showToast(data.message, 'success');
                            setTimeout(() => window.location.reload(), 1500);
                        }).catch(err => showToast('Gagal menghapus aset.', 'error'));
                    }
                });
            });

            const addBtn = document.getElementById('add-asset-btn');
            if(addBtn) addBtn.addEventListener('click', () => showModal());
            document.querySelectorAll('.cancel-btn').forEach(btn => btn.addEventListener('click', hideModal));

            initCharts();
            updateDashboard();
        });
    </script>
    @endpush
</x-app-layout>
