{{-- File: resources/views/assets/partials/modal-detail.blade.php --}}
<div id="detail-modal" class="fixed inset-0 bg-gray-900 bg-opacity-75 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-xl bg-white dark:bg-gray-800">
        <div class="flex justify-between items-center pb-3 border-b dark:border-gray-700">
            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="detail-modal-title">Detail Aset</h3>
            <button id="close-detail-modal-btn" class="text-gray-400 hover:text-gray-600"><span class="sr-only">Close</span><svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
        </div>
        <div class="mt-4 space-y-6" id="detail-modal-content">
            {{-- Konten detail akan diisi oleh JavaScript --}}
        </div>
    </div>
</div>
