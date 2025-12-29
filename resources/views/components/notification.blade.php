{{-- Floating Notification Button --}}
<div id="notificationFloat" class="notification-float">
    <button class="notification-btn" id="notificationBtn">
        <i class="ri-notification-3-line"></i>
        <span class="notification-badge" id="totalBadge" style="display: none;">0</span>
    </button>
</div>

{{-- Notification Panel --}}
<div class="notification-panel" id="notificationPanel" style="display: none;">
    <div class="notification-header">
        <h6 class="mb-0">
            <i class="ri-notification-3-fill me-2"></i>Notifikasi
        </h6>
        <button class="btn-close-panel" id="closePanel">
            <i class="ri-close-line"></i>
        </button>
    </div>
    
    <div class="notification-body" id="notificationContent">
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="text-muted mt-2 mb-0">Memuat notifikasi...</p>
        </div>
    </div>
</div>

<style>
/* Floating Button */
.notification-float {
    position: fixed;
    bottom: 30px;
    right: 30px;
    z-index: 1050;
    cursor: move;
}

.notification-btn {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
    font-size: 24px;
    box-shadow: 0 4px 20px rgba(102, 126, 234, 0.4);
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
}

.notification-btn:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 25px rgba(102, 126, 234, 0.6);
}

.notification-btn:active {
    transform: scale(0.95);
}

.notification-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background: #dc3545;
    color: white;
    font-size: 11px;
    font-weight: 600;
    padding: 2px 6px;
    border-radius: 10px;
    min-width: 20px;
    text-align: center;
    border: 2px solid white;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}

/* Notification Panel */
.notification-panel {
    position: fixed;
    bottom: 100px;
    right: 30px;
    width: 420px;
    max-height: 600px;
    background: white;
    border-radius: 15px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.15);
    z-index: 1049;
    display: flex;
    flex-direction: column;
    animation: slideUp 0.3s ease;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.notification-header {
    padding: 20px;
    border-bottom: 1px solid #e9ecef;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 15px 15px 0 0;
}

.notification-header h6 {
    color: white;
    font-weight: 600;
}

.btn-close-panel {
    background: rgba(255,255,255,0.2);
    border: none;
    color: white;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s;
}

.btn-close-panel:hover {
    background: rgba(255,255,255,0.3);
    transform: rotate(90deg);
}

.notification-body {
    padding: 15px;
    overflow-y: auto;
    max-height: 500px;
}

.notification-body::-webkit-scrollbar {
    width: 6px;
}

.notification-body::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.notification-body::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 3px;
}

.notification-category {
    margin-bottom: 20px;
}

.category-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 12px;
    background: #f8f9fa;
    border-radius: 8px;
    margin-bottom: 10px;
    cursor: pointer;
    transition: all 0.3s;
}

.category-header:hover {
    background: #e9ecef;
}

.category-title {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    font-size: 14px;
    color: #495057;
}

.category-badge {
    background: white;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
}

.category-items {
    padding-left: 10px;
}

.notification-item {
    padding: 12px;
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    margin-bottom: 8px;
    cursor: pointer;
    transition: all 0.3s;
}

.notification-item:hover {
    background: #f8f9fa;
    border-color: #667eea;
    transform: translateX(5px);
}

/* Active notification item */
.notification-item.active {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
    border-color: #667eea;
    border-width: 2px;
    position: relative;
}

.notification-item.active::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 4px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 8px 0 0 8px;
}

.active-indicator {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    color: #667eea;
    font-size: 11px;
    font-weight: 600;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.notification-item-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 5px;
}

.notification-item-title {
    font-weight: 600;
    color: #667eea;
    font-size: 13px;
}

.notification-item-badge {
    font-size: 10px;
    padding: 2px 6px;
}

.notification-item-content {
    font-size: 12px;
    color: #6c757d;
    line-height: 1.5;
}

.notification-item-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 8px;
    padding-top: 8px;
    border-top: 1px solid #e9ecef;
    font-size: 11px;
    color: #adb5bd;
}

.deadline-warning {
    color: #dc3545;
    font-weight: 600;
    animation: blink 1.5s infinite;
}

@keyframes blink {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

.empty-state {
    text-align: center;
    padding: 40px 20px;
}

.empty-state i {
    font-size: 48px;
    color: #dee2e6;
    margin-bottom: 10px;
}

.empty-state p {
    color: #adb5bd;
    margin: 0;
}

/* Responsive */
@media (max-width: 768px) {
    .notification-panel {
        width: calc(100vw - 40px);
        right: 20px;
    }
    
    .notification-float {
        bottom: 20px;
        right: 20px;
    }
}

/* Color variations for categories */
.color-secondary { color: #6c757d; }
.color-warning { color: #ffc107; }
.color-info { color: #0dcaf0; }
.color-success { color: #198754; }
.color-danger { color: #dc3545; }

.bg-color-secondary { background: #6c757d; }
.bg-color-warning { background: #ffc107; }
.bg-color-info { background: #0dcaf0; }
.bg-color-success { background: #198754; }
.bg-color-danger { background: #dc3545; }
</style>

{{-- Include this component in layouts/app.blade.php before closing body tag --}}

@push('scripts')
<script>
let isDragging = false;
let currentX;
let currentY;
let initialX;
let initialY;
let xOffset = 0;
let yOffset = 0;
let dragThreshold = 5;
let startX = 0;
let startY = 0;

document.addEventListener('DOMContentLoaded', function() {
    const floatBtn = document.getElementById('notificationFloat');
    const notifBtn = document.getElementById('notificationBtn');
    const panel = document.getElementById('notificationPanel');
    const closeBtn = document.getElementById('closePanel');
    
    // Check if panel should be open from session
    const panelState = sessionStorage.getItem('notificationPanelOpen');
    if (panelState === 'true') {
        panel.style.display = 'block';
    }
    
    // Load notifications on page load
    loadNotifications();
    
    // Refresh every 30 seconds
    setInterval(loadNotifications, 30000);
    
    // Toggle panel
    notifBtn.addEventListener('click', function(e) {
        if (!isDragging) {
            const isVisible = panel.style.display === 'block';
            panel.style.display = isVisible ? 'none' : 'block';
            sessionStorage.setItem('notificationPanelOpen', !isVisible);
            if (!isVisible) {
                loadNotifications();
            }
        }
    });
    
    closeBtn.addEventListener('click', function() {
        panel.style.display = 'none';
        sessionStorage.setItem('notificationPanelOpen', 'false');
    });
    
    // Close panel when clicking outside
    document.addEventListener('click', function(e) {
        if (!floatBtn.contains(e.target) && !panel.contains(e.target)) {
            panel.style.display = 'none';
            sessionStorage.setItem('notificationPanelOpen', 'false');
        }
    });
    
    // Draggable functionality
    floatBtn.addEventListener('mousedown', dragStart);
    floatBtn.addEventListener('touchstart', dragStart);
    
    document.addEventListener('mousemove', drag);
    document.addEventListener('touchmove', drag);
    
    document.addEventListener('mouseup', dragEnd);
    document.addEventListener('touchend', dragEnd);
});

function dragStart(e) {
    if (e.type === "touchstart") {
        startX = e.touches[0].clientX;
        startY = e.touches[0].clientY;
        initialX = startX - xOffset;
        initialY = startY - yOffset;
    } else {
        startX = e.clientX;
        startY = e.clientY;
        initialX = startX - xOffset;
        initialY = startY - yOffset;
    }

    isDragging = false;
}

function drag(e) {
    let currentClientX, currentClientY;

    if (e.type === "touchmove") {
        currentClientX = e.touches[0].clientX;
        currentClientY = e.touches[0].clientY;
    } else {
        currentClientX = e.clientX;
        currentClientY = e.clientY;
    }

    const dx = Math.abs(currentClientX - startX);
    const dy = Math.abs(currentClientY - startY);

    if (dx > dragThreshold || dy > dragThreshold) {
        isDragging = true;
    }

    if (!isDragging) return;

    e.preventDefault();

    currentX = currentClientX - initialX;
    currentY = currentClientY - initialY;

    xOffset = currentX;
    yOffset = currentY;

    setTranslate(currentX, currentY, document.getElementById('notificationFloat'));
}

function dragEnd() {
    setTimeout(() => {
        isDragging = false;
    }, 50);
}

function setTranslate(xPos, yPos, el) {
    el.style.transform = `translate(${xPos}px, ${yPos}px)`;
}

function loadNotifications() {
    fetch('/notifications')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateBadge(data.total_badge);
                renderNotifications(data.notifications);
            }
        })
        .catch(error => {
            console.error('Error loading notifications:', error);
        });
}

function updateBadge(count) {
    const badge = document.getElementById('totalBadge');
    if (count > 0) {
        badge.textContent = count > 99 ? '99+' : count;
        badge.style.display = 'block';
    } else {
        badge.style.display = 'none';
    }
}

function renderNotifications(notifications) {
    const container = document.getElementById('notificationContent');
    let html = '';
    
    let hasNotifications = false;
    
    // Get active notification from session
    const activeNotification = sessionStorage.getItem('activeNotification');
    
    // Render each category
    for (const [key, category] of Object.entries(notifications)) {
        if (category.count > 0) {
            hasNotifications = true;
            html += renderCategory(key, category, activeNotification);
        }
    }
    
    if (!hasNotifications) {
        html = `
            <div class="empty-state">
                <i class="ri-notification-off-line"></i>
                <p>Tidak ada notifikasi baru</p>
            </div>
        `;
    }
    
    container.innerHTML = html;
    
    // Scroll to active item if exists
    if (activeNotification) {
        setTimeout(() => {
            const activeItem = document.querySelector('.notification-item.active');
            if (activeItem) {
                activeItem.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }, 100);
    }
    
    // Add click handlers
    attachClickHandlers();
}

function renderCategory(key, category, activeNotification) {
    let html = `
        <div class="notification-category">
            <div class="category-header" onclick="toggleCategory('${key}')">
                <div class="category-title">
                    <i class="${category.icon} color-${category.color}"></i>
                    <span>${category.label}</span>
                </div>
                <span class="category-badge bg-color-${category.color} text-white">
                    ${category.count}
                </span>
            </div>
            <div class="category-items" id="category-${key}">
    `;
    
    category.items.forEach(item => {
        html += renderNotificationItem(key, item, category.color, activeNotification);
    });
    
    html += `
            </div>
        </div>
    `;
    
    return html;
}

function renderNotificationItem(type, item, color, activeNotification) {
    let content = '';
    let footer = '';
    
    // Determine item ID and create unique identifier
    const itemId = type.includes('tagihan') || type === 'pending_payment' || type === 'overdue' || type === 'completed' ? 
                   item.id_tagihan : item.id_po;
    const uniqueId = `${type}-${itemId}`;
    const isActive = activeNotification === uniqueId;
    
    switch(type) {
        case 'draft':
        case 'approved':
        case 'cancelled':
            content = `
                <div>Unit: <strong>${item.unit_pemohon}</strong></div>
                <div>Total: <strong class="text-success">Rp ${formatNumber(item.grand_total)}</strong></div>
            `;
            footer = `
                <span><i class="ri-calendar-line"></i> ${formatDate(item.tanggal_permintaan)}</span>
                ${isActive ? '<span class="active-indicator"><i class="ri-eye-line"></i> Sedang dilihat</span>' : ''}
            `;
            break;
            
        case 'pending_kepala_gudang':
        case 'pending_kasir':
            content = `
                <div>Unit: <strong>${item.unit_pemohon}</strong></div>
                <div>Total: <strong class="text-success">Rp ${formatNumber(item.grand_total)}</strong></div>
            `;
            footer = `
                <span><i class="ri-time-line"></i> ${formatDate(item.tanggal_permintaan)}</span>
                ${isActive ? '<span class="active-indicator"><i class="ri-eye-line"></i> Sedang dilihat</span>' : 
                    `<span class="${item.hours_left < 6 ? 'deadline-warning' : 'text-warning'}">
                        <i class="ri-alarm-warning-line"></i> ${Math.round(item.hours_left)} jam lagi
                    </span>`
                }
            `;
            break;
            
        case 'pending_payment':
        case 'overdue':
            const poNo = item.purchase_order?.no_gr || item.purchase_order?.no_po || '-';
            content = `
                <div>No PO: <strong>${poNo}</strong></div>
                <div>Supplier: <strong>${item.supplier?.nama_supplier || '-'}</strong></div>
                <div>Sisa: <strong class="text-danger">Rp ${formatNumber(item.sisa_tagihan)}</strong></div>
            `;
            footer = `
                <span><i class="ri-calendar-line"></i> J.Tempo: ${formatDate(item.tanggal_jatuh_tempo)}</span>
                ${isActive ? '<span class="active-indicator"><i class="ri-eye-line"></i> Sedang dilihat</span>' :
                    (item.is_overdue ? 
                        `<span class="deadline-warning"><i class="ri-error-warning-line"></i> Lewat ${Math.abs(item.days_left)} hari</span>` :
                        `<span class="text-warning">${item.days_left} hari lagi</span>`
                    )
                }
            `;
            break;
            
        case 'completed':
            const completedPoNo = item.purchase_order?.no_gr || item.purchase_order?.no_po || '-';
            content = `
                <div>No PO: <strong>${completedPoNo}</strong></div>
                <div>Supplier: <strong>${item.supplier?.nama_supplier || '-'}</strong></div>
                <div>Total: <strong class="text-success">Rp ${formatNumber(item.grand_total)}</strong></div>
            `;
            footer = `
                <span><i class="ri-check-line"></i> ${formatDate(item.updated_at)}</span>
                ${isActive ? '<span class="active-indicator"><i class="ri-eye-line"></i> Sedang dilihat</span>' : ''}
            `;
            break;
    }
    
    const itemNo = type.includes('tagihan') || type === 'pending_payment' || type === 'overdue' || type === 'completed' ? 
                   item.no_tagihan : item.no_po;
    
    return `
        <div class="notification-item ${isActive ? 'active' : ''}" data-type="${type}" data-id="${itemId}" data-unique-id="${uniqueId}">
            <div class="notification-item-header">
                <span class="notification-item-title">${itemNo}</span>
                <span class="badge notification-item-badge bg-color-${color} text-white">
                    ${item.status || type.replace('_', ' ')}
                </span>
            </div>
            <div class="notification-item-content">
                ${content}
            </div>
            <div class="notification-item-footer">
                ${footer}
            </div>
        </div>
    `;
}

function toggleCategory(key) {
    const category = document.getElementById(`category-${key}`);
    if (category.style.display === 'none') {
        category.style.display = 'block';
    } else {
        category.style.display = 'none';
    }
}

function attachClickHandlers() {
    document.querySelectorAll('.notification-item').forEach(item => {
        item.addEventListener('click', function() {
            const type = this.dataset.type;
            const id = this.dataset.id;
            const uniqueId = this.dataset.uniqueId;
            handleNotificationClick(type, id, uniqueId);
        });
    });
}

function handleNotificationClick(type, id, uniqueId) {
    let url = '';
    
    switch(type) {
        case 'draft':
        case 'approved':
        case 'cancelled':
        case 'pending_kepala_gudang':
        case 'pending_kasir':
            url = `/po/${id}`;
            break;
            
        case 'pending_payment':
        case 'overdue':
            url = `/tagihan/${id}`;
            break;
            
        case 'completed':
            url = `/tagihan/${id}`;
            break;
    }
    
    if (url) {
        // Save active notification to session
        sessionStorage.setItem('activeNotification', uniqueId);
        // Keep panel open
        sessionStorage.setItem('notificationPanelOpen', 'true');
        // Navigate to URL
        window.location.href = url;
    }
}

function formatNumber(num) {
    return new Intl.NumberFormat('id-ID').format(num);
}

function formatDate(date) {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
    });
}
</script>
@endpush