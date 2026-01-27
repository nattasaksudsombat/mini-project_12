@push('scripts')
<script>
    // เก็บสินค้าที่ถูกเลือก
    let selectedItems = [];
    let currentProduct = null;

    // ✅ เมื่อโหลดหน้าเว็บ ให้ดึงข้อมูลจาก localStorage กลับมา
    document.addEventListener('DOMContentLoaded', function() {
        // โหลดสินค้าที่เคยเลือกไว้กลับมา
        const savedItems = localStorage.getItem('orderSelectedItems');
        if (savedItems) {
            try {
                selectedItems = JSON.parse(savedItems);
                renderOrderItems();
                console.log('✅ โหลดสินค้าจาก localStorage สำเร็จ:', selectedItems.length, 'รายการ');
            } catch (e) {
                console.error('❌ localStorage parse error:', e);
                selectedItems = [];
            }
        }

        // Event listener สำหรับแสดงสต็อก
        const variantSelect = document.getElementById('variant-select');
        if (variantSelect) {
            variantSelect.addEventListener('change', function() {
                const option = this.options[this.selectedIndex];
                const stock = option.dataset.stock;
                const stockHint = document.getElementById('stock-hint');
                
                if (stock) {
                    stockHint.textContent = `จองได้ทั้งหมด ${stock} ชิ้น`;
                    stockHint.style.color = '#28a745';
                } else {
                    stockHint.textContent = '';
                }
            });
        }

        // ระบบค้นหาสินค้า
        const productSearch = document.getElementById('product-search');
        if (productSearch) {
            productSearch.addEventListener('keyup', function() {
                let q = this.value.trim();
                if (q.length < 2) {
                    document.getElementById('search-results').innerHTML = '';
                    return;
                }

                fetch(`/products/search?q=${encodeURIComponent(q)}`)
                    .then(res => res.json())
                    .then(products => {
                        let html = '';
                        products.forEach(p => {
                            html += `
                                <div class="border p-2 d-flex justify-content-between mb-2">
                                    <div>
                                        <strong>${p.name}</strong><br>
                                        <small>รหัส: ${p.id_stock} | ราคา: ${p.price} บาท</small>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-success" onclick="showVariantModal(${p.id}, '${p.name}', ${p.price}, '${p.id_stock}')">เลือก</button>
                                </div>`;
                        });
                        
                        if (html === '') {
                            html = '<div class="text-center text-muted p-3">ไม่พบสินค้า</div>';
                        }
                        
                        document.getElementById('search-results').innerHTML = html;
                    });
            });
        }
    });

    // แสดง modal เลือกสี-ไซส์ โดยโหลดข้อมูล variant จาก API
    function showVariantModal(id, name, price, id_stock) {
        currentProduct = { id, name, price, id_stock };
        document.getElementById('selected-product-name').textContent = name;

        fetch(`/products/${id}/variants`)
            .then(res => res.json())
            .then(data => {
                console.log('Variant data:', data); // 🔍 debug ข้อมูลที่โหลดมา

                const select = document.getElementById('variant-select');
                select.innerHTML = '<option value="">-- เลือก --</option>';

                data.forEach(v => {
                    // ⭐ ใช้ available (จำนวนที่จองได้จริง) แทน quantity
                    const availableQty = v.available || 0;
                    
                    // กรองเฉพาะ variant ที่จองได้ > 0
                    if (availableQty > 0) {
                        select.innerHTML += `<option 
                            value="${v.id}" 
                            data-stock="${availableQty}" 
                            data-color-id="${v.color_id}" 
                            data-size-id="${v.size_id}"
                            data-color-name="${v.color_name || v.color?.name || ''}"
                            data-size-name="${v.size_name || v.size?.name || ''}">
                            ${v.display_name} (จองได้ ${availableQty} ชิ้น)
                        </option>`;
                    }
                });

                new bootstrap.Modal(document.getElementById('variantModal')).show();
            });
    }

    // ยืนยันการเลือกสินค้าและเพิ่มเข้า order
    function confirmAddProduct() {
        const select = document.getElementById('variant-select');
        const quantity = parseInt(document.getElementById('variant-quantity').value);
        const variantId = parseInt(select.value);
        const option = select.options[select.selectedIndex];
        const stock = parseInt(option.dataset.stock);

        if (!variantId || quantity < 1) return alert('กรุณาเลือกสี-ไซส์และจำนวน');
        if (quantity > stock) return alert(`สต็อกไม่พอ มีแค่ ${stock}`);

        const colorId = parseInt(option.dataset.colorId);
        const sizeId = parseInt(option.dataset.sizeId);
        const colorName = option.getAttribute('data-color-name');
        const sizeName = option.getAttribute('data-size-name');
        const variantName = `${colorName} - ${sizeName}`;

        if (selectedItems.some(i => i.product_id === currentProduct.id && i.color_id === colorId && i.size_id === sizeId)) {
            return alert('สินค้านี้ (สี-ไซส์เดียวกัน) ถูกเพิ่มแล้ว');
        }

        selectedItems.push({
            product_id: currentProduct.id,
            product_id_stock: currentProduct.id_stock,
            product_name: currentProduct.name,
            unit_price: currentProduct.price,
            stock_id: currentProduct.id_stock,
            quantity,
            total_price: currentProduct.price * quantity,
            color_id: colorId,
            size_id: sizeId,
            color_name: colorName,
            size_name: sizeName,
            variant_name: variantName,
            max_stock: stock
        });

        // ✅ บันทึกลง localStorage ทุกครั้งที่เพิ่มสินค้า
        saveItemsToLocalStorage();

        bootstrap.Modal.getInstance(document.getElementById('variantModal')).hide();
        renderOrderItems();
    }

    // แสดงรายการสินค้าที่ถูกเลือก
    function renderOrderItems() {
        const tbody = document.getElementById('order-items-body');
        if (!tbody) return;
        
        tbody.innerHTML = '';

        selectedItems.forEach((item, index) => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${item.stock_id}</td>
                <td>${item.variant_name}</td>
                <td>
                    <input type="number" value="${item.quantity}" min="1" max="${item.max_stock}" 
                        onchange="updateQuantity(${index}, this.value)" class="form-control form-control-sm">
                </td>
                <td>${item.unit_price.toFixed(2)}</td>
                <td>${item.total_price.toFixed(2)}</td>
                <td><button type="button" class="btn btn-danger btn-sm" onclick="removeItem(${index})">ลบ</button></td>
            `;
            tbody.appendChild(row);
        });

        // แปลงเป็น JSON เพื่อส่งไป backend
        const itemsJsonField = document.getElementById('items-json');
        if (itemsJsonField) {
            itemsJsonField.value = JSON.stringify(selectedItems);
        }
    }

    // แก้ไขจำนวนสินค้า
    function updateQuantity(index, qty) {
        qty = parseInt(qty);
        if (qty < 1 || qty > selectedItems[index].max_stock) {
            alert(`จำนวนต้องระหว่าง 1 ถึง ${selectedItems[index].max_stock}`);
            renderOrderItems();
            return;
        }
        selectedItems[index].quantity = qty;
        selectedItems[index].total_price = qty * selectedItems[index].unit_price;
        
        // ✅ บันทึกลง localStorage เมื่อแก้ไขจำนวน
        saveItemsToLocalStorage();
        
        renderOrderItems();
    }

    // ลบสินค้าออก
    function removeItem(index) {
        selectedItems.splice(index, 1);
        
        // ✅ บันทึกลง localStorage เมื่อลบสินค้า
        saveItemsToLocalStorage();
        
        renderOrderItems();
    }

    // ✅ ฟังก์ชันบันทึกสินค้าลง localStorage
    function saveItemsToLocalStorage() {
        try {
            localStorage.setItem('orderSelectedItems', JSON.stringify(selectedItems));
            console.log('💾 บันทึกสินค้าลง localStorage:', selectedItems.length, 'รายการ');
        } catch (e) {
            console.error('❌ localStorage save error:', e);
        }
    }

    // ✅ ฟังก์ชันลบข้อมูลใน localStorage (เมื่อบันทึกสำเร็จ)
    function clearLocalStorage() {
        localStorage.removeItem('orderSelectedItems');
        console.log('🗑️ ลบข้อมูลใน localStorage แล้ว');
    }

    // กดส่งออเดอร์
    function submitOrder() {
        renderOrderItems();
        if (selectedItems.length === 0) {
            alert('กรุณาเพิ่มสินค้าในออเดอร์ก่อน');
            return;
        }
        
        // ✅ บันทึกลง localStorage ก่อน submit (กรณี error จะได้ไม่หาย)
        saveItemsToLocalStorage();
        
        document.getElementById('order-form').submit();
    }
</script>
@endpush