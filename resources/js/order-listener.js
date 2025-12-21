/**
 * Order Real-time Tracker
 * Listen for order status updates and update the UI
 */

window.setupOrderTracker = (orderId) => {
    if (!window.Echo) {
        console.error('Laravel Echo is not initialized.');
        return;
    }

    window.Echo.private(`order.${orderId}`)
        .listen('OrderStatusUpdated', (e) => {
            console.log('Order status updated:', e);

            // Update status text
            const statusLabels = document.querySelectorAll(`[data-order-status-label="${orderId}"]`);
            statusLabels.forEach(el => {
                el.innerText = e.status_label;

                // Optional: Add an animation or highlight
                el.classList.add('animate-pulse', 'text-blue-600');
                setTimeout(() => el.classList.remove('animate-pulse'), 3000);
            });

            // Update status badges if they exist
            const statusBadges = document.querySelectorAll(`[data-order-status-badge="${orderId}"]`);
            statusBadges.forEach(el => {
                // You could map status to CSS classes here
                el.innerText = e.status_label;
            });

            // Notification toast
            if (window.showNotification) {
                window.showNotification(`Pedido #${orderId} actualizado: ${e.status_label}`);
            } else {
                // Fallback toast if no custom notification system
                const toast = document.createElement('div');
                toast.className = 'fixed bottom-4 right-4 bg-gray-900 text-white px-6 py-3 rounded-lg shadow-xl z-50 transform transition-all duration-500 translate-y-20';
                toast.innerHTML = `
                    <div class="flex items-center space-x-3">
                        <div class="w-2 h-2 rounded-full bg-green-500 animate-ping"></div>
                        <p class="font-medium">Pedido #${orderId}: ${e.status_label}</p>
                    </div>
                `;
                document.body.appendChild(toast);

                // Show toast
                setTimeout(() => toast.classList.remove('translate-y-20'), 100);

                // Hide and remove toast
                setTimeout(() => {
                    toast.classList.add('opacity-0');
                    setTimeout(() => toast.remove(), 500);
                }, 5000);
            }

            // If we are on the order detail page, we might want to refresh part of the UI
            // or perform actions based on specific statuses (e.g. reload if delivered)
            if (e.status === 'delivered' || e.status === 'rejected_by_cook') {
                // reload after a short delay
                // setTimeout(() => window.location.reload(), 2000);
            }
        });
};

// Auto-initialize for elements on the page
document.addEventListener('DOMContentLoaded', () => {
    const orderElements = document.querySelectorAll('[data-order-id]');
    const trackedIds = new Set();

    orderElements.forEach(el => {
        const orderId = el.getAttribute('data-order-id');
        if (orderId && !trackedIds.has(orderId)) {
            window.setupOrderTracker(orderId);
            trackedIds.add(orderId);
        }
    });
});
