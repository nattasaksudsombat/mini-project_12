@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">📦 รายการสินค้า (สำหรับฝ่ายขาย)</h1>
    </div>

    <div class="bg-white p-4 rounded-lg shadow-sm mb-6">
        <form action="{{ route('sales.products.index') }}" method="GET" class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" 
                placeholder="ค้นหาชื่อสินค้า หรือ รหัสสต็อก..." 
                class="flex-1 border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                ค้นหา
            </button>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">รูปภาพ</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">รหัสสต็อก</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ชื่อสินค้า</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">หมวดหมู่</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ราคาขาย</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">จัดการ</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($products as $product)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <img src="{{ $product->productImages->first() ? asset('storage/'.$product->productImages->first()->image_url) : 'https://placehold.co/50' }}" 
                             class="h-12 w-12 rounded object-cover border">
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap font-mono text-sm text-blue-600">
                        {{ $product->id_stock }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $product->category->category_name ?? '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                        ฿{{ number_format($product->price, 2) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <a href="{{ route('sales.products.show', $product->id) }}" 
                           class="text-blue-600 hover:text-blue-900 bg-blue-50 px-3 py-1 rounded-full text-sm font-semibold">
                           ดูรายละเอียด
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-10 text-center text-gray-500">ไม่พบข้อมูลสินค้า</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="mt-4">
        {{ $products->links() }}
    </div>
</div>
@endsection