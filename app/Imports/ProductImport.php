<?php

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductImport implements ToModel,WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // ทำความสะอาด key (ลบ BOM และ trim ช่องว่าง)
        $row = $this->cleanKeys($row);

        return new Product([
            'category_id' => $row['category_id'] ?? null,
            'id_stock'    => $row['id_stock'] ?? null,
            'name'        => $row['name'] ?? null,
            'description' => $row['description'] ?? null,
            'price'       => $row['price'] ?? 0,
            'cost'        => $row['cost'] ?? 0,
            'is_active'   => $row['is_active'] ?? 1,
        ]);
    }

    private function cleanKeys(array $row): array
    {
        $cleaned = [];
        foreach ($row as $key => $value) {
            $cleanKey = trim(str_replace("\xEF\xBB\xBF", '', $key)); // ลบ BOM + trim
            $cleaned[$cleanKey] = $value;
        }
        return $cleaned;
    }

}
