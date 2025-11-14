<?php 
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ProductOption;

class OptionController extends Controller
{
    public function index()
    {
        $options = DB::table('product_options')
            ->select(DB::raw('MIN(id) as id'), 'option_name', DB::raw('MAX(option_value) as option_value'), DB::raw('COUNT(DISTINCT product_id) as product_count'))
            ->groupBy('option_name')
            ->get();

        return view('options.index', compact('options'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'option_name' => 'required|string|max:255',
            'option_value' => 'required|string|max:255',
        ]);

        ProductOption::create([
            'option_name' => $request->option_name,
            'option_value' => $request->option_value,
            'product_id' => null,
        ]);

        return redirect()->back()->with('success', 'เพิ่ม Option สำเร็จ');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'option_name' => 'required|string|max:255',
            'option_value' => 'required|string|max:255',
        ]);

        $option = ProductOption::findOrFail($id);
        $option->update([
            'option_name' => $request->option_name,
            'option_value' => $request->option_value,
        ]);

        return back()->with('success', 'อัปเดต Option สำเร็จ');
    }

    public function destroy($id)
    {
        $option = ProductOption::findOrFail($id);

        $usage = ProductOption::where('option_name', $option->option_name)->count();

        if ($usage > 1) {
            return redirect()->back()->with('error', 'ไม่สามารถลบ Option ที่ถูกใช้งานได้');
        }

        $option->delete();

        return redirect()->back()->with('success', 'ลบ Option สำเร็จ');
    }
}
