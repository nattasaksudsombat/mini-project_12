// order-edit.js

// 1. Declare the state object globally
const state = {
    orderId: null,
    items: [],
    products: [],
    colors: [],
    sizes: [],
    // You may need other state variables here
};

// 2. Main initialization function
async function initializeSystem(initialData) {
    console.log('🚀 Starting Order Edit System');
    console.log('📥 Loading initial data...');

    try {
        if (!initialData || !initialData.products || !initialData.order) {
            throw new Error('Initial data is missing or incomplete.');
        }

        state.products = initialData.products;
        state.colors = initialData.colors || [];
        state.sizes = initialData.sizes || [];
        state.orderId = initialData.order.id;

        console.log(`📦 Loaded ${state.products.length} products`);
        console.log(`📋 Order data loaded:`, initialData.order);

        if (initialData.order.items && initialData.order.items.length > 0) {
            await loadExistingOrderItems(initialData.order.items);
        } else {
            state.items = [];
            renderOrderItems();
        }

        setupEventListeners();
        console.log('✅ System initialized successfully');

    } catch (error) {
        console.error('❌ Error initializing the system:', error);
        Swal.fire('ข้อผิดพลาด', `เกิดข้อผิดพลาดในการเริ่มต้นระบบ: ${error.message}`, 'error');
    }
}

// 3. Load existing order items
async function loadExistingOrderItems(existingItems) {
    console.log('🔄 Processing existing order items:', existingItems);

    const processedItems = await Promise.all(
        existingItems.map(async (item) => {
            const colorSizeData = extractColorSizeFromRelationship(item);
            let finalColorSizeData = {
                color_id: colorSizeData.color_id || null,
                size_id: colorSizeData.size_id || null,
                color_size_id: colorSizeData.color_size_id || null,
                color_name: colorSizeData.color_name || '',
                size_name: colorSizeData.size_name || ''
            };

            if (!finalColorSizeData.color_size_id) {
                const variantNameParts = item.variant_name ? item.variant_name.split(' - ') : [];
                if (variantNameParts.length > 2) {
                    finalColorSizeData.color_name = variantNameParts[variantNameParts.length - 2];
                    finalColorSizeData.size_name = variantNameParts[variantNameParts.length - 1];
                }
            }

            const variant_name = createVariantName(item.product.name, finalColorSizeData.color_name, finalColorSizeData.size_name);
            
            // Get the product details from the state
            const product = state.products.find(p => p.id === item.product_id);
            const image = product ? product.image : '';

            return {
                id: item.id,
                product_id: item.product_id,
                product_name: item.product.name,
                image: image,
                price: item.price,
                quantity: item.quantity,
                color_id: finalColorSizeData.color_id,
                size_id: finalColorSizeData.size_id,
                color_size_id: finalColorSizeData.color_size_id,
                variant_name: variant_name,
                unit_price: item.price / item.quantity,
                stock: 9999,
                is_valid: true
            };
        })
    );

    state.items = processedItems;
    updateItemsJsonForBackend();
    renderOrderItems();
    console.log(`✅ Loaded ${processedItems.length} items successfully`);
}

// 4. Extract color/size data from the relationships
function extractColorSizeFromRelationship(item) {
    // This is a placeholder function. You'll need to adapt it to your data structure.
    return {
        color_size_id: item.product_color_size ? item.product_color_size.id : null,
        color_id: item.product_color_size && item.product_color_size.color ? item.product_color_size.color.id : null,
        size_id: item.product_color_size && item.product_color_size.size ? item.product_color_size.size.id : null,
        color_name: item.product_color_size && item.product_color_size.color ? item.product_color_size.color.name : '',
        size_name: item.product_color_size && item.product_color_size.size ? item.product_color_size.size.name : ''
    };
}

// 5. Create a variant name
function createVariantName(productName, colorName, sizeName) {
    if (colorName && sizeName) {
        return `${productName} - ${colorName} - ${sizeName}`;
    }
    return productName;
}

// 6. Validate items before submission
function validateItemsBeforeSubmit(event) {
    event.preventDefault();

    let hasMissingColorSize = state.items.some(item => !item.color_id || !item.size_id || !item.color_size_id);
    let hasZeroQuantity = state.items.some(item => item.quantity <= 0);

    if (hasZeroQuantity) {
        Swal.fire('ข้อผิดพลาด', 'มีรายการสินค้าที่จำนวนเป็นศูนย์ กรุณาแก้ไข', 'error');
        return;
    }

    if (hasMissingColorSize) {
        Swal.fire({
            title: 'พบสินค้าที่ไม่มี ID สี-ไซส์',
            text: 'ข้อมูลสี-ไซส์ของสินค้าเก่าอาจไม่สมบูรณ์ หากดำเนินการต่ออาจทำให้ข้อมูลไม่อัปเดต ต้องการดำเนินการต่อหรือไม่?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'ดำเนินการต่อ',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                updateItemsJsonForBackend();
                submitOrderForm();
            }
        });
    } else {
        updateItemsJsonForBackend();
        submitOrderForm();
    }
}

// 7. Update the hidden JSON input
function updateItemsJsonForBackend() {
    const backendItems = state.items.map(item => ({
        product_id: item.product_id,
        quantity: item.quantity,
        price: item.price,
        product_color_size_id: item.color_size_id,
        variant_name: item.variant_name,
    }));

    document.getElementById('items-json').value = JSON.stringify(backendItems);
    console.log('💾 Updated items JSON for backend:', backendItems);
}

// 8. Submit the form
function submitOrderForm() {
    document.getElementById('edit-order-form').submit();
}

// 9. Render the items to the UI (placeholder)
function renderOrderItems() {
    const container = document.getElementById('order-items-container');
    container.innerHTML = '';
    
    if (state.items.length === 0) {
        container.innerHTML = '<p class="text-muted">ไม่มีรายการสินค้าในคำสั่งซื้อนี้</p>';
        return;
    }

    state.items.forEach((item, index) => {
        // You'll need to create your own HTML structure here based on your design
        const itemHtml = `
            <div class="d-flex align-items-center mb-3 p-3 border rounded">
                <img src="${item.image}" alt="${item.product_name}" class="me-3" style="width: 50px;">
                <div class="flex-grow-1">
                    <p class="mb-0"><strong>${item.product_name}</strong> - ${item.variant_name}</p>
                    <p class="text-muted mb-0">ราคา: ${item.unit_price} บาท</p>
                </div>
                <input type="number" class="form-control text-center" style="width: 80px;" value="${item.quantity}" min="1" data-index="${index}">
                <button type="button" class="btn btn-danger btn-sm ms-3" data-index="${index}">ลบ</button>
            </div>
        `;
        container.innerHTML += itemHtml;
    });

    renderOrderSummary();
}

// 10. Render the order summary (placeholder)
function renderOrderSummary() {
    const summaryContainer = document.getElementById('order-summary');
    const total = state.items.reduce((sum, item) => sum + (item.quantity * item.unit_price), 0);
    summaryContainer.innerHTML = `<p class="mb-0"><strong>ยอดรวม:</strong> ${total.toFixed(2)} บาท</p>`;
}

// 11. Event listeners
function setupEventListeners() {
    const form = document.getElementById('edit-order-form');
    const saveButton = document.getElementById('save-button');
    const addItemButton = document.getElementById('add-item-button');
    
    if (saveButton) {
        saveButton.addEventListener('click', validateItemsBeforeSubmit);
    }

    if (addItemButton) {
        addItemButton.addEventListener('click', () => {
            // Your logic to add a new item goes here
            // e.g., show a modal to select a product
            console.log('เพิ่มสินค้า');
        });
    }

    // Add more event listeners for quantity changes, item removal, etc.
}

// 12. Entry point to run the code
document.addEventListener('DOMContentLoaded', () => {
    if (typeof initialOrderData !== 'undefined') {
        initializeSystem(initialOrderData);
    } else {
        console.error('❌ initialOrderData is not defined. Make sure it is passed from the Blade file.');
        Swal.fire('ข้อผิดพลาด', 'ไม่พบข้อมูลเริ่มต้นจากเซิร์ฟเวอร์', 'error');
    }
});