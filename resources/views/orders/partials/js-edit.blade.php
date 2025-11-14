<script>
let selectedItems = [];

function recalculateTotal() {
    let total = 0;
    document.querySelectorAll('#order-items-table tbody tr').forEach(row => {
        const qty = parseInt(row.querySelector('.item-qty').value) || 0;
        const price = parseFloat(row.cells[3].innerText.replace(',', '')) || 0;
        total += qty * price;
    });
    document.getElementById('total-amount').value = total.toFixed(2);
}

document.querySelectorAll('.item-qty').forEach(input => {
    input.addEventListener('input', recalculateTotal);
});

document.querySelectorAll('.remove-item').forEach(btn => {
    btn.addEventListener('click', e => {
        e.target.closest('tr').remove();
        recalculateTotal();
    });
});

function prepareItemsJson() {
    let items = [];
    document.querySelectorAll('#order-items-table tbody tr').forEach(row => {
        items.push({
            variant_id: row.dataset.variantId,
            quantity: row.querySelector('.item-qty').value
        });
    });
    document.getElementById('items-json').value = JSON.stringify(items);
    return true;
}

// ค้นหาสินค้า (ajax)
document.getElementById('product-search').addEventListener('keyup', function () {
    let keyword = this.value.trim();
    if (keyword.length < 2) return document.getElementById('search-results').innerHTML = '';

    fetch('/products/search?q=' + encodeURIComponent(keyword))
        .then(res => res.json())
        .then(products => {
            let html = '';
            products.forEach(p => {
                html += `
                    <div class="border p-2 mb-2 d-flex justify-content-between align-items-center">
                        <div><strong>${p.name}</strong><br><small>รหัส: ${p.id_stock} | ราคา: ${p.price} บาท</small></div>
                        <button type="button" class="btn btn-sm btn-success" onclick="showVariantModal(${p.id}, '${p.name}', ${p.price})">เลือกสี-ไซส์</button>
                    </div>
                `;
            });
            document.getElementById('search-results').innerHTML = html;
        });
});
    function calculateTotal() {
        let total = 0;
        document.querySelectorAll('#order-items-table tbody tr').forEach(row => {
            let qty = parseFloat(row.querySelector('input[type="number"]').value) || 0;
            let price = parseFloat(row.children[3].textContent.replace(/,/g, '')) || 0;
            total += qty * price;
        });

        let shipping = parseFloat(document.querySelector('input[name="shipping_fee"]').value) || 0;
        let discount = parseFloat(document.querySelector('input[name="discount"]').value) || 0;
        let netTotal = total + shipping - discount;

        document.getElementById('order-total').value = netTotal.toFixed(2);
    }

    document.querySelectorAll('input').forEach(input => {
        input.addEventListener('input', calculateTotal);
    });

    calculateTotal(); // run on page load

</script>
