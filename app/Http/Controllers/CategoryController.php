<?php

namespace App\Http\Controllers;

use App\Http\Requests\Category\StoreRequest;
use App\Http\Requests\Category\UpdateRequest;
use App\Models\Category;
use App\Models\Posts;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\Datatables;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function index(Request $request)
    // {
    //     //
    //     $perPage = 5;
    //     $search = $request->input('search');
    //     $entriesCount = Category::where('name', 'like', '%' . $search . '%')->count();
    //     $categories = Category::orderBy('created_at', 'DESC')->where('name', 'like', '%' . $search . '%')->paginate($perPage);

    //     return response()->view('categories.index', compact('entriesCount', 'categories', 'search'));
    // }
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Category::latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = '<a href="javascript:void(0)" class="edit btn btn-primary btn-sm">Edit</a> 
                    <a href="' . route('categories.detail', ['slug' => $row->slug]) . '" class="detail btn btn-info btn-sm">Detail</a> ';
                    return $actionBtn;
                })
                ->addColumn('DT_RowId', function($row) {
                    return $row->id;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('categories.index');

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $categories = Category::all(); // hoặc bất kỳ logic lấy categories nào khác
        $this->authorize('createCategory', Category::class);
        return response()->view('categories.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        try {
            $category = new Category();

            $category->name = $request->name;
            $category->slug = $request->slug;
            $category->status = $request->status;

            if ($category->status == 0) {
                return redirect()->back()->withInput()->with('error', 'Invalid status selected');
            }

            $category->save();
            session()->flash('success', 'Category created successfully');
            return redirect()->route('categories.index')->with('success', 'Create category successfully');
        } catch (\Exception $e) {
            // Nếu có lỗi, lưu thông điệp lỗi vào Session
            session()->flash('error', 'Error creating category: ' . $e->getMessage());

            // Chuyển hướng về trang create với thông điệp lỗi
            return redirect()->route('categories.create')->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function detail($slug)
    {
        //
        $category = Category::where('slug', $slug)->firstOrFail();
        return response()->view('categories.detail', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $categories = Category::findOrFail($id);
        $allCategories = Category::all();
        $this->authorize('editCategory', $categories);
        return response()->view('categories.edit', compact('id', 'categories', 'allCategories'));
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, $id)
    {
        if ($request->has('statusData')) {
            // Xử lý cập nhật trạng thái từ dữ liệu gửi lên
            $statusData = json_decode($request->input('statusData'), true);

            foreach ($statusData as $data) {
                $category = Category::findOrFail($data['id']);
                $category->status = $data['status'];
                $category->save();
            }
            session()->flash('success', 'Category updated successfully');
            return redirect()->route('categories.index')->with('success', 'Update status successfully');
        }

        // Tiếp tục xử lý như trước nếu không có dữ liệu statusData
        $category = Category::findOrFail($id);
        $category->name = $request->name;
        $category->slug = $request->slug;
        $category->status = $request->status;

        if ($category->status == 0) {
            return redirect()->back()->withInput()->with('error', 'Invalid status selected');
        }
        $category->save();

        return redirect()->route('categories.index')->with('success', 'Update category successfully');
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $this->authorize('deleteCategory', $category);

        // Xóa bài đăng
        $category->delete();

        return response()->json(['message' => 'Delete category successfully']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteMultiple(Request $request)
    {
        $this->authorize('deleteMultiple', Category::class);
        $categoryIds = $request->input('selectedPosts');

        // Lấy danh sách category có posts
        $categoriesWithPosts = Category::whereIn('id', $categoryIds)
            ->whereHas('posts')
            ->with(['posts' => function ($query) {
                $query->select('id', 'title');
            }])
            ->get();

        if ($categoriesWithPosts->isNotEmpty()) {
            // Nếu có category chứa post, trả về lỗi
            $errorCategories = $categoriesWithPosts->map(function ($category) {
                return $category->name;
            })->implode(', ');

            return redirect()->route('categories.index')->with('error', 'Cannot delete categories. They have associated posts. Categories with posts: ' . $errorCategories);
        }

        // Nếu không có bài viết liên quan, xóa các category được chọn
        Category::whereIn('id', $categoryIds)->delete();

        return redirect()->route('categories.index')->with('success', 'Categories deleted successfully');
    }   
    public function updateStatus(Request $request)
    {
        if ($request->has('statusData')) {
            // Xử lý cập nhật trạng thái từ dữ liệu gửi lên
            $statusData = json_decode($request->input('statusData'), true);

            foreach ($statusData as $data) {
                $category = Category::findOrFail($data['id']);

                $this->authorize('updateStatus', $category);

                $category->status = $data['status'];
                $category->save();
            }

            return redirect()->route('categories.index')->with('success', 'Update status successfully');
        } else {
            // Xử lý nếu không có dữ liệu statusData
            // ...

            return redirect()->route('categories.index')->with('error', 'No status data provided');
        }
    }
}