@push('scripts')
<script>
    // เก็บสินค้าที่ถูกเลือก
    let selectedItems = [];
    let currentProduct = null;

    // แสดง modal เลือกสี-ไซส์ โดยโหลดข้อมูล variant จาก API
    function showVariantModal(id, name, price) {
        currentProduct = { id, name, price };
        document.getElementById('selected-product-name').textContent = name;

        fetch(`/products/${id}/variants`)
            .then(res => res.json())
            .then(data => {
                console.log('Variant data:', data); // 🔍 debug ข้อมูลที่โหลดมา

                const select = document.getElementById('variant-select');
                select.innerHTML = '<option value="">-- เลือก --</option>';

                data.forEach(v => {
                    select.innerHTML += `<option 
                        value="${v.id}" 
                        data-stock="${v.quantity}" 
                        data-color-id="${v.color_id}" 
                        data-size-id="${v.size_id}"
                        data-color-name="${v.color_name || v.color?.name || ''}"
                        data-size-name="${v.size_name || v.size?.name || ''}">
                        ${v.display_name}
                    </option>`;
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
            product_name: currentProduct.name,
            unit_price: currentProduct.price,
            quantity,
            total_price: currentProduct.price * quantity,
            color_id: colorId,
            size_id: sizeId,
            color_name: colorName,
            size_name: sizeName,
            variant_name: variantName,
            max_stock: stock
        });

        bootstrap.Modal.getInstance(document.getElementById('variantModal')).hide();
        renderOrderItems();
    }

    // แสดงรายการสินค้าที่ถูกเลือก
    function renderOrderItems() {
        const tbody = document.getElementById('order-items-body');
        tbody.innerHTML = '';

        selectedItems.forEach((item, index) => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${item.product_name}</td>
                <td>${item.variant_name}</td>
                <td>
                    <input type="number" value="${item.quantity}" min="1" max="${item.max_stock}" 
                        onchange="updateQuantity(${index}, this.value)" class="form-control form-control-sm">
                </td>
                <td>${item.unit_price.toFixed(2)}</td>
                <td>${item.total_price.toFixed(2)}</td>
                <td><button class="btn btn-danger btn-sm" onclick="removeItem(${index})">ลบ</button></td>
            `;
            tbody.appendChild(row);
        });

        // แปลงเป็น JSON เพื่อส่งไป backend
        document.getElementById('items-json').value = JSON.stringify(selectedItems);
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
        renderOrderItems();
    }

    // ลบสินค้าออก
    function removeItem(index) {
        selectedItems.splice(index, 1);
        renderOrderItems();
    }

    // กดส่งออเดอร์
    function submitOrder() {
        renderOrderItems();
        if (selectedItems.length === 0) {
            alert('กรุณาเพิ่มสินค้าในออเดอร์ก่อน');
            return;
        }
        document.getElementById('order-form').submit();
    }

    // ระบบค้นหาสินค้า
    document.getElementById('product-search').addEventListener('keyup', function() {
        let q = this.value.trim();
        if (q.length < 2) return document.getElementById('search-results').innerHTML = '';

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
                            <button type="button" class="btn btn-sm btn-success" onclick="showVariantModal(${p.id}, '${p.name}', ${p.price})">เลือก</button>
                        </div>`;
                });
                document.getElementById('search-results').innerHTML = html;
            });
    });
</script>
@endpush
