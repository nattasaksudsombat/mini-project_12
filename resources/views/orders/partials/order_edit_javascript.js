// 4. JavaScript (ไฟล์แยก หรือใส่ในส่วนท้ายของ view)

// ตัวแปรสำหรับเก็บข้อมูล
let orderItems = [];
let products = [];
let currentProduct = null;
let selectedItems = [];

// โหลดข้อมูลจาก backend
document.addEventListener('DOMContentLoaded', function() {
    loadInitialData();
    setupEventListeners();
});

// โหลดข้อมูลเริ่มต้น (แก้ไขให้ปลอดภัยกว่า)
function loadInitialData() {
    try {
        // โหลดข้อมูลออเดอร์เดิม
        const orderDataElement = document.getElementById('order-data');
        if (orderDataElement && orderDataElement.textContent.trim()) {
            const orderData = JSON.parse(orderDataElement.textContent);
            if (orderData && orderData.items && orderData.items.length > 0) {
                loadExistingOrderItems(orderData.items);
            }
        }
        
        // โหลดข้อมูลสินค้าทั้งหมด
        const productsDataElement = document.getElementById('products-data');
        if (productsDataElement && productsDataElement.textContent.trim()) {
            const productsData = JSON.parse(productsDataElement.textContent);
            if (productsData && productsData.length > 0) {
                products = productsData;
            }
        }
        
        calculateTotals();
        console.log('Initial data loaded:', {
            orderItems: selectedItems.length,
            products: products.length
        });
    } catch (error) {
        console.error('Error loading initial data:', error);
        showAlert('ไม่สามารถโหลดข้อมูลเริ่มต้นได้ กรุณาลองใหม่อีกครั้ง', 'danger');
    }
}

// โหลดรายการสินค้าเดิมในออเดอร์
function loadExistingOrderItems(existingItems) {
    if (!existingItems || !Array.isArray(existingItems)) {
        console.warn('No existing items found');
        updateOrderItemsTable();
        return;
    }
    
    selectedItems = []; // รีเซ็ตข้อมูล
    
    existingItems.forEach(item => {
        const colorName = (item.product_color_size && item.product_color_size.color) 
            ? item.product_color_size.color.name : '';
        const sizeName = (item.product_color_size && item.product_color_size.size) 
            ? item.product_color_size.size.name : '';
        
        let variantName = 'ไม่ระบุ';
        if (colorName || sizeName) {
            variantName = [colorName, sizeName].filter(Boolean).join(' - ');
        }

        selectedItems.push({
            product_id: item.product_id,
            product_name: item.product_name || (item.product ? item.product.name : '') || 'สินค้าไม่ระบุ',
            unit_price: parseFloat(item.unit_price || item.price) || 0,
            quantity: parseInt(item.quantity) || 1,
            total_price: (parseFloat(item.unit_price || item.price) || 0) * (parseInt(item.quantity) || 1),
            color_id: item.product_color_size ? item.product_color_size.color_id : null,
            size_id: item.product_color_size ? item.product_color_size.size_id : null,
            color_size_id: item.color_size_id || null,
            color_name: colorName,
            size_name: sizeName,
            variant_name: variantName,
            max_stock: 999 // ค่าเริ่มต้น
        });
    });
    
    updateOrderItemsTable();
}

// ตั้งค่า Event Listeners
function setupEventListeners() {
    // ค้นหาสินค้า
    const searchInput = document.getElementById('product-search');
    let searchTimeout;
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                searchProducts(this.value);
            }, 300);
        });
    }

    // คำนวณยอดรวมเมื่อมีการเปลี่ยนแปลง
    const shippingFee = document.getElementById('shipping-fee');
    const discount = document.getElementById('discount');
    
    if (shippingFee) {
        shippingFee.addEventListener('input', calculateTotals);
    }
    if (discount) {
        discount.addEventListener('input', calculateTotals);
    }
    
    // เมื่อเลือก variant ใน modal
    const variantSelect = document.getElementById('variant-select');
    if (variantSelect) {
        variantSelect.addEventListener('change', function() {
            updateStockInfo();
            updateVariantPrice();
        });
    }
}

// ค้นหาสินค้า
function searchProducts(query) {
    const resultsDiv = document.getElementById('search-results');
    
    if (!resultsDiv) return;
    
    if (query.length < 2) {
        resultsDiv.innerHTML = '';
        return;
    }
    
    // ใช้ API ค้นหา
    fetch(`/products/search?q=${encodeURIComponent(query)}`)
        .then(res => {
            if (!res.ok) {
                throw new Error('Network response was not ok');
            }
            return res.json();
        })
        .then(products => {
            displaySearchResults(products, resultsDiv);
        })
        .catch(error => {
            console.error('Error searching products:', error);
            resultsDiv.innerHTML = '<div class="alert alert-warning">เกิดข้อผิดพลาดในการค้นหา</div>';
        });
}

// แสดงผลการค้นหา
function displaySearchResults(filteredProducts, resultsDiv) {
    if (filteredProducts.length === 0) {
        resultsDiv.innerHTML = '<div class="alert alert-info">ไม่พบสินค้าที่ค้นหา</div>';
        return;
    }
    
    let html = '<div class="list-group">';
    filteredProducts.forEach(product => {
        const price = parseFloat(product.price || 0);
        const displayPrice = price.toLocaleString('th-TH', {minimumFractionDigits: 2});
        
        html += `
            <div class="list-group-item list-group-item-action" onclick="showVariantModal(${product.id}, '${escapeHtml(product.name)}', ${price})">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1">${escapeHtml(product.name || 'ไม่ระบุชื่อ')}</h6>
                        <small class="text-muted">รหัส: ${escapeHtml(product.sku || 'ไม่ระบุ')} | ราคา: ${displayPrice} บาท</small>
                    </div>
                    <span class="badge bg-primary rounded-pill">เลือก</span>
                </div>
            </div>
        `;
    });
    html += '</div>';
    
    resultsDiv.innerHTML = html;
}

// ฟังก์ชันป้องกัน XSS
function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}

// แสดง modal เลือกสี-ไซส์
function showVariantModal(id, name, price) {
    currentProduct = { id, name, price };
    const productNameEl = document.getElementById('selected-product-name');
    if (productNameEl) {
        productNameEl.textContent = name;
    }

    // โหลดข้อมูล variants
    fetch(`/products/${id}/variants`)
        .then(res => {
            if (!res.ok) {
                throw new Error('Network response was not ok');
            }
            return res.json();
        })
        .then(data => {
            const select = document.getElementById('variant-select');
            const priceInput = document.getElementById('variant-price');
            
            if (select) {
                select.innerHTML = '<option value="">-- เลือก --</option>';

                data.forEach(v => {
                    select.innerHTML += `<option 
                        value="${v.id}" 
                        data-stock="${v.quantity}" 
                        data-color-id="${v.color_id || ''}" 
                        data-size-id="${v.size_id || ''}"
                        data-color-name="${escapeHtml(v.color_name || '')}"
                        data-size-name="${escapeHtml(v.size_name || '')}">
                        ${escapeHtml(v.display_name)}
                    </option>`;
                });

                // ตั้งราคาเริ่มต้น
                if (priceInput) {
                    priceInput.value = price.toFixed(2);
                }

                // แสดง modal
                const modal = new bootstrap.Modal(document.getElementById('variantModal'));
                modal.show();
            }
        })
        .catch(error => {
            console.error('Error loading variants:', error);
            showAlert('ไม่สามารถโหลดข้อมูลสี-ไซส์ได้', 'danger');
        });
}

// ยืนยันการเลือกสินค้าและเพิ่มเข้า order
function confirmAddProduct() {
    const select = document.getElementById('variant-select');
    const quantityEl = document.getElementById('variant-quantity');
    const priceEl = document.getElementById('variant-price');
    
    if (!select || !quantityEl || !priceEl) {
        alert('ไม่พบฟิลด์ที่จำเป็น');
        return;
    }
    
    const quantity = parseInt(quantityEl.value);
    const variantId = parseInt(select.value);
    const price = parseFloat(priceEl.value);
    const option = select.options[select.selectedIndex];
    
    if (!variantId || quantity < 1) {
        alert('กรุณาเลือกสี-ไซส์และจำนวน');
        return;
    }
    
    if (price < 0) {
        alert('ราคาต้องไม่ติดลบ');
        return;
    }
    
    const stock = parseInt(option.dataset.stock);
    if (quantity > stock) {
        alert(`สต็อกไม่พอ มีแค่ ${stock}`);
        return;
    }

    const colorId = parseInt(option.dataset.colorId) || null;
    const sizeId = parseInt(option.dataset.sizeId) || null;
    const colorName = option.getAttribute('data-color-name') || '';
    const sizeName = option.getAttribute('data-size-name') || '';
    const variantName = [colorName, sizeName].filter(Boolean).join(' - ') || 'ไม่ระบุ';

    // ตรวจสอบว่ามีสินค้าซ้ำหรือไม่
    const existingIndex = selectedItems.findIndex(i => 
        i.product_id === currentProduct.id && 
        i.color_id === colorId && 
        i.size_id === sizeId
    );

    if (existingIndex !== -1) {
        alert('สินค้านี้ (สี-ไซส์เดียวกัน) ถูกเพิ่มแล้ว');
        return;
    }

    // เพิ่มสินค้าใหม่
    const newItem = {
        product_id: currentProduct.id,
        product_name: currentProduct.name,
        unit_price: price,
        quantity: quantity,
        total_price: price * quantity,
        color_id: colorId,
        size_id: sizeId,
        color_size_id: variantId,
        color_name: colorName,
        size_name: sizeName,
        variant_name: variantName,
        max_stock: stock
    };

    selectedItems.push(newItem);

    // ปิด modal และอัปเดตตาราง
    const modal = bootstrap.Modal.getInstance(document.getElementById('variantModal'));
    if (modal) {
        modal.hide();
    }
    
    renderOrderItems();
    calculateTotals();
    
    // ล้างการค้นหา
    clearSearch();
}

// แสดงรายการสินค้าที่ถูกเลือก
function renderOrderItems() {
    const tbody = document.getElementById('order-items-body');
    if (!tbody) return;
    
    tbody.innerHTML = '';

    selectedItems.forEach((item, index) => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>
                <div class="fw-bold">${escapeHtml(item.product_name)}</div>
                <small class="text-muted">ID: ${item.product_id}</small>
            </td>
            <td>
                <span class="badge bg-secondary">${escapeHtml(item.variant_name)}</span>
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <button type="button" class="btn btn-outline-secondary" onclick="changeItemQuantity(${index}, -1)">
                        <i class="fas fa-minus"></i>
                    </button>
                    <input type="number" class="form-control text-center" value="${item.quantity}" 
                           min="1" max="${item.max_stock}" 
                           onchange="updateQuantity(${index}, this.value)">
                    <button type="button" class="btn btn-outline-secondary" onclick="changeItemQuantity(${index}, 1)">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <input type="number" class="form-control" value="${item.unit_price.toFixed(2)}" 
                           step="0.01" min="0" onchange="updateItemPrice(${index}, this.value)">
                </div>
            </td>
            <td class="fw-bold">${item.total_price.toFixed(2)}</td>
            <td>
                <button class="btn btn-danger btn-sm" onclick="removeItem(${index})">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </td>
        `;
        tbody.appendChild(row);
    });
    
    if (selectedItems.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center text-muted py-4">
                    <i class="fas fa-box-open fa-2x mb-2"></i>
                    <div>ยังไม่มีสินค้าในออเดอร์</div>
                </td>
            </tr>
        `;
    }

    // แปลงเป็น JSON เพื่อส่งไป backend
    updateItemsJson();
}

// อัปเดตตารางสินค้า
function updateOrderItemsTable() {
    renderOrderItems();
}

// อัปเดต JSON สำหรับส่งไปยัง backend
function updateItemsJson() {
    const itemsForBackend = selectedItems.map(item => ({
        product_id: item.product_id,
        color_size_id: item.color_size_id,
        name: item.product_name,
        quantity: item.quantity,
        price: item.unit_price
    }));
    
    const itemsJsonEl = document.getElementById('items-json');
    if (itemsJsonEl) {
        itemsJsonEl.value = JSON.stringify(itemsForBackend);
    }
}

// เปลี่ยนจำนวนสินค้าในตาราง
function changeItemQuantity(index, change) {
    if (index >= 0 && index < selectedItems.length) {
        const newQuantity = selectedItems[index].quantity + change;
        if (newQuantity >= 1 && newQuantity <= selectedItems[index].max_stock) {
            selectedItems[index].quantity = newQuantity;
            selectedItems[index].total_price = newQuantity * selectedItems[index].unit_price;
            
            renderOrderItems();
            calculateTotals();
        }
    }
}

// แก้ไขจำนวนสินค้า
function updateQuantity(index, qty) {
    qty = parseInt(qty);
    if (isNaN(qty) || qty < 1 || qty > selectedItems[index].max_stock) {
        alert(`จำนวนต้องระหว่าง 1 ถึง ${selectedItems[index].max_stock}`);
        renderOrderItems();
        return;
    }
    selectedItems[index].quantity = qty;
    selectedItems[index].total_price = qty * selectedItems[index].unit_price;
    
    renderOrderItems();
    calculateTotals();
}

// อัปเดตราคาสินค้า
function updateItemPrice(index, value) {
    const price = parseFloat(value);
    if (isNaN(price) || price < 0) {
        alert('ราคาต้องเป็นตัวเลขและไม่ติดลบ');
        renderOrderItems();
        return;
    }
    selectedItems[index].unit_price = price;
    selectedItems[index].total_price = selectedItems[index].quantity * price;
    
    renderOrderItems();
    calculateTotals();
}

// ลบสินค้าออก
function removeItem(index) {
    if (confirm('คุณต้องการลบสินค้านี้ออกจากออเดอร์หรือไม่?')) {
        selectedItems.splice(index, 1);
        renderOrderItems();
        calculateTotals();
    }
}

// คำนวณยอดรวม
function calculateTotals() {
    let subtotal = 0;
    
    selectedItems.forEach(item => {
        subtotal += item.quantity * item.unit_price;
    });
    
    const shippingEl = document.getElementById('shipping-fee');
    const discountEl = document.getElementById('discount');
    const subtotalDisplayEl = document.getElementById('subtotal-display');
    const totalAmountEl = document.getElementById('total-amount');
    
    const shipping = shippingEl ? parseFloat(shippingEl.value) || 0 : 0;
    const discount = discountEl ? parseFloat(discountEl.value) || 0 : 0;
    const total = subtotal + shipping - discount;
    
    if (subtotalDisplayEl) {
        subtotalDisplayEl.textContent = subtotal.toLocaleString('th-TH', {minimumFractionDigits: 2});
    }
    if (totalAmountEl) {
        totalAmountEl.value = total.toLocaleString('th-TH', {minimumFractionDigits: 2});
    }
}

// อัปเดตข้อมูลสต็อก
function updateStockInfo() {
    const variantSelect = document.getElementById('variant-select');
    const stockInfo = document.getElementById('stock-info');
    const quantityInput = document.getElementById('variant-quantity');
    
    if (variantSelect && variantSelect.value) {
        const selectedOption = variantSelect.options[variantSelect.selectedIndex];
        const quantity = parseInt(selectedOption.dataset.stock);
        if (stockInfo) {
            stockInfo.textContent = quantity;
        }
        if (quantityInput) {
            quantityInput.max = quantity;
            if (parseInt(quantityInput.value) > quantity) {
                quantityInput.value = quantity;
            }
        }
    } else {
        if (stockInfo) {
            stockInfo.textContent = '-';
        }
        if (quantityInput) {
            quantityInput.max = '';
        }
    }
}

// อัปเดตราคาสินค้า
function updateVariantPrice() {
    const variantSelect = document.getElementById('variant-select');
    const priceInput = document.getElementById('variant-price');
    
    if (variantSelect && variantSelect.value && currentProduct && priceInput) {
        priceInput.value = currentProduct.price.toFixed(2);
    }
}

// เปลี่ยนจำนวนสินค้า
function changeQuantity(change) {
    const quantityInput = document.getElementById('variant-quantity');
    if (quantityInput) {
        let newValue = parseInt(quantityInput.value) + change;
        
        if (newValue < 1) newValue = 1;
        if (quantityInput.max && newValue > parseInt(quantityInput.max)) {
            newValue = parseInt(quantityInput.max);
        }
        
        quantityInput.value = newValue;
    }
}

// แสดงข้อความแจ้งเตือน
function showAlert(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        <i class="fas fa-${type === 'danger' ? 'exclamation-triangle' : 'info-circle'}"></i>
        ${escapeHtml(message)}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    const container = document.querySelector('.container');
    if (container) {
        container.insertBefore(alertDiv, container.firstChild);
    }
    
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

// ล้างการค้นหา
function clearSearch() {
    const searchInput = document.getElementById('product-search');
    const searchResults = document.getElementById('search-results');
    
    if (searchInput) {
        searchInput.value = '';
    }
    if (searchResults) {
        searchResults.innerHTML = '';
    }
}

// รีเซ็ตฟอร์ม
function resetForm() {
    if (confirm('คุณต้องการรีเซ็ตข้อมูลทั้งหมดกลับเป็นค่าเดิมหรือไม่?')) {
        location.reload();
    }
}

// กดส่งออเดอร์
function submitOrder() {
    // ตรวจสอบข้อมูลพื้นฐาน
    const customerNameEl = document.querySelector('input[name="customer[name]"]');
    const customerAddressEl = document.querySelector('textarea[name="customer[address]"]');
    
    if (!customerNameEl || !customerNameEl.value.trim()) {
        alert('กรุณาระบุชื่อลูกค้า');
        if (customerNameEl) customerNameEl.focus();
        return;
    }
    
    if (!customerAddressEl || !customerAddressEl.value.trim()) {
        alert('กรุณาระบุที่อยู่ลูกค้า');
        if (customerAddressEl) customerAddressEl.focus();
        return;
    }
    
    if (selectedItems.length === 0) {
        alert('กรุณาเพิ่มสินค้าอย่างน้อย 1 รายการ');
        return;
    }
    
    // ตรวจสอบข้อมูลสินค้า
    for (let i = 0; i < selectedItems.length; i++) {
        const item = selectedItems[i];
        if (item.quantity <= 0) {
            alert(`จำนวนสินค้า "${item.product_name}" ต้องมากกว่า 0`);
            return;
        }
        if (item.unit_price < 0) {
            alert(`ราคาสินค้า "${item.product_name}" ต้องไม่ติดลบ`);
            return;
        }
    }
    
    // อัปเดต JSON ล่าสุด
    updateItemsJson();
    
    // แสดงการยืนยัน
    const totalAmountEl = document.getElementById('total-amount');
    const totalAmount = totalAmountEl ? totalAmountEl.value : '0';
    
    if (confirm(`คุณต้องการบันทึกการแก้ไขออเดอร์นี้หรือไม่?\n\nยอดรวม: ${totalAmount} บาท\nจำนวนสินค้า: ${selectedItems.length} รายการ`)) {
        // แสดง loading
        const submitBtn = document.querySelector('button[onclick="submitOrder()"]');
        if (submitBtn) {
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> กำลังบันทึก...';
            
            // ส่งฟอร์ม
            setTimeout(() => {
                const form = document.getElementById('order-form');
                if (form) {
                    form.submit();
                } else {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                    alert('ไม่พบฟอร์มสำหรับส่งข้อมูล');
                }
            }, 500);
        }
    }
}

// ป้องกันการส่งฟอร์มโดยไม่ตั้งใจ
document.addEventListener('DOMContentLoaded', function() {
    const orderForm = document.getElementById('order-form');
    if (orderForm) {
        orderForm.addEventListener('submit', function(e) {
            e.preventDefault();
            return false;
        });
    }
});