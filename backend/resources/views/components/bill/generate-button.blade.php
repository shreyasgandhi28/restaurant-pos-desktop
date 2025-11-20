@props(['order'])

<div class="flex justify-end space-x-1">
    @if(!$order->bill && in_array($order->status, ['pending', 'preparing', 'ready', 'served']))
        <form id="generateBillForm-{{ $order->id }}" action="{{ route('bills.store') }}" method="POST" class="inline">
            @csrf
            <input type="hidden" name="order_id" value="{{ $order->id }}">
            <input type="hidden" name="discount_percentage" id="discount_percentage-{{ $order->id }}" value="0">
            <input type="hidden" name="discount_reason" id="discount_reason-{{ $order->id }}" value="">
            
            <button type="button" 
                    onclick="openBillDialog('{{ $order->id }}')"
                    class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-1 focus:ring-offset-1 focus:ring-green-500">
                Generate
            </button>
        </form>
    @elseif($order->bill)
        <a href="{{ route('bills.show', $order->bill) }}" 
           class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-1 focus:ring-offset-1 focus:ring-indigo-500">
            View
        </a>
        <a href="{{ route('bills.print', $order->bill) }}" 
           target="_blank" rel="noopener" title="Print"
           class="inline-flex items-center px-2 py-1 border border-gray-300 text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-1 focus:ring-offset-1 focus:ring-indigo-500">
            <svg class="h-3 w-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
        </a>
        <a href="{{ route('bills.download', $order->bill) }}" 
           title="Download PDF"
           class="inline-flex items-center px-2 py-1 border border-gray-300 text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-1 focus:ring-offset-1 focus:ring-indigo-500">
            <svg class="h-3 w-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5m0 0l5-5m-5 5V4" />
            </svg>
        </a>
    @endif
    
    @if(!$order->bill && in_array($order->status, ['pending', 'preparing', 'ready', 'served']))
        <a href="{{ route('orders.show', $order) }}" 
           class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-1 focus:ring-offset-1 focus:ring-indigo-500">
            View
        </a>
    @endif
</div>

@push('modals')
    <div id="billDialog-{{ $order->id }}" class="fixed inset-0 z-50 hidden" aria-labelledby="billDialogTitle-{{ $order->id }}" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50" onclick="closeBillDialog('{{ $order->id }}')"></div>
        <div class="relative z-10 flex min-h-screen items-center justify-center px-4">
            <div class="w-full max-w-sm rounded-lg bg-white shadow-xl">
                <div class="flex items-center justify-between border-b px-4 py-3">
                    <div>
                        <h3 id="billDialogTitle-{{ $order->id }}" class="text-base font-semibold text-gray-900">Generate Bill</h3>
                        <p class="text-xs text-gray-500">Add an optional discount before generating the bill.</p>
                    </div>
                    <button type="button" onclick="closeBillDialog('{{ $order->id }}')" class="text-gray-400 hover:text-gray-600 focus:outline-none" aria-label="Close dialog">&times;</button>
                </div>
                <div class="space-y-3 px-4 py-4">
                    <div>
                        <label for="billDialogDiscount-{{ $order->id }}" class="block text-xs font-medium text-gray-700">Discount Percentage</label>
                        <input type="number" id="billDialogDiscount-{{ $order->id }}" min="0" max="100" step="0.01" value="0"
                               class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label for="billDialogReason-{{ $order->id }}" class="block text-xs font-medium text-gray-700">Discount Reason <span class="text-[10px] text-gray-400">(required when discount &gt; 0)</span></label>
                        <textarea id="billDialogReason-{{ $order->id }}" rows="2"
                                  class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                  placeholder="Enter reason"></textarea>
                    </div>
                    <p id="billDialogError-{{ $order->id }}" class="hidden rounded bg-red-50 px-2 py-1 text-xs text-red-600"></p>
                </div>
                <div class="flex items-center justify-end gap-2 border-t bg-gray-50 px-4 py-3">
                    <button type="button" onclick="closeBillDialog('{{ $order->id }}')" class="rounded border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-100">Cancel</button>
                    <button type="button" onclick="submitBillDialog('{{ $order->id }}')" class="rounded bg-green-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">Generate</button>
                </div>
            </div>
        </div>
    </div>
@endpush

@push('scripts')
<script>
function getBillDialogElements(orderId) {
    return {
        modal: document.getElementById(`billDialog-${orderId}`),
        discountInput: document.getElementById(`billDialogDiscount-${orderId}`),
        reasonInput: document.getElementById(`billDialogReason-${orderId}`),
        errorBox: document.getElementById(`billDialogError-${orderId}`)
    };
}

function openBillDialog(orderId) {
    const { modal, discountInput, reasonInput, errorBox } = getBillDialogElements(orderId);
    errorBox.classList.add('hidden');
    errorBox.textContent = '';
    discountInput.value = document.getElementById(`discount_percentage-${orderId}`).value || 0;
    reasonInput.value = document.getElementById(`discount_reason-${orderId}`).value || '';
    modal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
    setTimeout(() => discountInput.focus(), 0);
}

function closeBillDialog(orderId) {
    const { modal } = getBillDialogElements(orderId);
    modal.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
}

function submitBillDialog(orderId) {
    const { discountInput, reasonInput, errorBox } = getBillDialogElements(orderId);
    const discountValue = parseFloat(discountInput.value);

    errorBox.textContent = '';
    errorBox.classList.add('hidden');

    if (isNaN(discountValue) || discountValue < 0 || discountValue > 100) {
        errorBox.textContent = 'Please enter a valid discount percentage between 0 and 100.';
        errorBox.classList.remove('hidden');
        return;
    }

    const reason = reasonInput.value.trim();
    if (discountValue > 0 && !reason) {
        errorBox.textContent = 'Discount reason is required when a discount is applied.';
        errorBox.classList.remove('hidden');
        return;
    }

    document.getElementById(`discount_percentage-${orderId}`).value = discountValue;
    document.getElementById(`discount_reason-${orderId}`).value = reason;

    closeBillDialog(orderId);
    document.getElementById(`generateBillForm-${orderId}`).submit();
}

document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const openModal = document.querySelector('[id^="billDialog-"]:not(.hidden)');
        if (openModal) {
            const orderId = openModal.id.replace('billDialog-', '');
            closeBillDialog(orderId);
        }
    }
});
</script>
@endpush
