{{-- File: resources/views/assets/partials/modal-form.blade.php --}}
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
                {{-- Konten form akan diisi oleh JavaScript --}}
            </div>

            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800/50 text-right rounded-b-xl">
                <button type="button" class="cancel-btn inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600">Batal</button>
                <button type="submit" id="save-asset-btn" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 disabled:opacity-50 ml-4">
                    <span id="save-btn-text">Simpan</span>
                </button>
            </div>
        </form>
    </div>
</div>
