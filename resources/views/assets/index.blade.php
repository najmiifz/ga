<x-app-layout>
    {{-- Slot untuk header --}}
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Manajemen Aset Perusahaan
            </h2>
            <p class="text-gray-500 mt-1">Selamat datang, <span class="font-semibold">{{ auth()->user()->role === 'admin' ? 'Admin' : 'Pengguna' }}</span>!</p>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{-- KODE HTML ASLI ANDA MULAI DARI SINI --}}
                    {{-- Cukup salin isi dari <div id="app-view"> ... </div> --}}

                    <div id="dashboard" class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                        </div>

                    <form method="GET" action="{{ route('assets.index') }}" class="mb-6">
                        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
                            <div class="flex flex-wrap items-end gap-4 w-full">
                                {{-- Contoh satu filter --}}
                                <div class="flex-grow min-w-[150px]">
                                    <label for="search-pic" class="block text-sm font-medium text-gray-700 mb-1">Cari PIC</label>
                                    <input type="text" id="search-pic" name="search_pic" placeholder="Ketik nama..." class="w-full p-2 border border-gray-300 rounded-md" value="{{ request('search_pic') }}">
                                </div>
                                <div class="flex-grow min-w-[150px]">
                                    <label for="filter-tipe" class="block text-sm font-medium text-gray-700 mb-1">Filter Tipe</label>
                                    <select id="filter-tipe" name="filter_tipe" class="w-full p-2 border border-gray-300 rounded-md">
                                        <option value="semua">Semua Tipe</option>
                                        @foreach($filters['tipe'] as $tipe)
                                            <option value="{{ $tipe }}" {{ request('filter_tipe') == $tipe ? 'selected' : '' }}>{{ $tipe }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                {{-- Tambahkan filter lainnya (project, lokasi, jenis) dengan cara yang sama --}}
                            </div>
                            <div class="flex-shrink-0 flex gap-2 pt-4 lg:pt-0">
                                <button type="submit" class="w-full md:w-auto inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md">Filter</button>
                                @if(auth()->user()->role === 'admin')
                                    <a href="{{ route('assets.export') }}" id="export-csv-btn" class="w-full md:w-auto inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md">Ekspor CSV</a>
                                    <button type="button" id="add-asset-btn" class="w-full md:w-auto inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600">Tambah Aset</button>
                                @endif
                            </div>
                        </div>
                    </form>

                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                {{-- Header Tabel --}}
                            </thead>
                            <tbody id="asset-table-body" class="bg-white divide-y divide-gray-200">
                                @forelse ($assets as $asset)
                                    <tr>
                                        <td class="px-6 py-4">{{ $asset->tipe }}</td>
                                        <td class="px-6 py-4">{{ $asset->jenis_aset }}</td>
                                        <td class="px-6 py-4">
                                            @if(strtolower($asset->pic) == 'available')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Available</span>
                                            @else
                                                {{ $asset->pic }}
                                            @endif
                                        </td>
                                        {{-- Lanjutkan untuk kolom lain --}}
                                        <td class="px-6 py-4">{{ $asset->merk }}</td>
                                        <td class="px-6 py-4">{{ $asset->nomor_sn }}</td>
                                        <td class="px-6 py-4">{{ $asset->project }}</td>
                                        <td class="px-6 py-4">{{ $asset->lokasi }}</td>
                                        <td class="px-6 py-4">{{ $asset->tahun_beli }}</td>
                                        <td class="px-6 py-4">{{ date('Y') - $asset->tahun_beli }}</td>
                                        <td class="px-6 py-4">Rp {{ number_format($asset->harga_beli, 0, ',', '.') }}</td>
                                        <td class="px-6 py-4">Rp {{ number_format($asset->harga_sewa, 0, ',', '.') }}</td>
                                        @if(auth()->user()->role === 'admin')
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <button class="text-indigo-600 hover:text-indigo-900 edit-btn" data-id="{{ $asset->id }}">Ubah</button>
                                                <button class="text-red-600 hover:text-red-900 ml-4 delete-btn" data-id="{{ $asset->id }}">Hapus</button>
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="13" class="text-center text-gray-500 py-8">Tidak ada data.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $assets->links() }}
                    </div>

                    {{-- Modal untuk Tambah/Ubah Aset (Struktur HTML sama) --}}
                    <div id="asset-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" style="display:none;">
                        {{-- ... Salin struktur modal dari HTML asli ... --}}
                        <form id="asset-form">
                            @csrf {{-- PENTING untuk keamanan --}}
                            <input type="hidden" id="asset-id" name="id">
                            <input type="hidden" id="form-method" name="_method" value="POST">
                            {{-- ... Isi form lainnya ... --}}
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // LOGIKA JAVASCRIPT BARU AKAN ADA DI SINI
    </script>
    @endpush
</x-app-layout>
