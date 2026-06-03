@if(session('success'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
    class="mb-5 flex items-center gap-3 bg-sage-50 border border-sage-200 text-sage-700 rounded-2xl px-4 py-3 text-sm">
    <i class="fa-solid fa-circle-check text-sage-500 flex-shrink-0"></i>
    <span>{{ session('success') }}</span>
    <button onclick="this.closest('div').remove()" class="ml-auto text-sage-400 hover:text-sage-600"><i class="fa-solid fa-xmark"></i></button>
</div>
@endif
@if(session('error'))
<div class="mb-5 flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 rounded-2xl px-4 py-3 text-sm">
    <i class="fa-solid fa-circle-exclamation text-red-500 flex-shrink-0"></i>
    <span>{{ session('error') }}</span>
    <button onclick="this.closest('div').remove()" class="ml-auto text-red-400 hover:text-red-600"><i class="fa-solid fa-xmark"></i></button>
</div>
@endif
