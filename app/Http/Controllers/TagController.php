<?php 
namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function index()
    {
        $tags = Tag::all();
        return view('tags.index', compact('tags'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:tags,tag_name',
        ]);

        Tag::create([
            'tag_name' => $request->name
        ]);

        return redirect()->route('tags.index')->with('success', 'เพิ่มแท็กเรียบร้อยแล้ว');
    }

    public function update(Request $request, Tag $tag)
    {
        $request->validate([
            'name' => 'required|unique:tags,tag_name,' . $tag->id,
        ]);

        $tag->update([
            'tag_name' => $request->name
        ]);

        return redirect()->route('tags.index')->with('success', 'อัปเดตแท็กเรียบร้อยแล้ว');
    }

    public function destroy(Tag $tag)
    {
        $tag->delete();
        return redirect()->route('tags.index')->with('success', 'ลบแท็กเรียบร้อยแล้ว');
    }
}
