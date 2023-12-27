<?php

namespace App\Http\Controllers;

use App\Http\Requests\Post\StoreRequest;
use App\Http\Requests\Post\UpdateRequest;
use App\Models\Account;
use App\Models\Posts;
use App\Models\Category;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Sunra\PhpSimple\HtmlDomParser;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\Datatables;

class PostsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Posts::with('category')->latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = '<a href="javascript:void(0)" class="edit btn btn-primary btn-sm">Edit</a> 
                    <a href="' . route('posts.detail', ['slug' => $row->slug]) . '" class="detail btn btn-info btn-sm">Detail</a> ';
                    return $actionBtn;
                })
                ->addColumn('DT_RowId', function($row) {
                    return $row->id;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('posts.index');
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::get();
        $this->authorize('createPost', Posts::class);
        return response()->view('posts.create',compact('categories'));
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
            $post = new Posts();

            $post->title = $request->title;
            $post->slug = $request->slug;
            $post->is_featured = $request->is_featured;
            $post->status = $request->status;
            $post->excerpt = $request->excerpt;
            $post->content = $request->content;
            $post->posted_at = $request->posted_at;
            $post->category_id = $request->category_id;

            if ($post->category_id == 0){
                return redirect()->back()->withInput()->with('error', 'Invalid category selected');
            }

            if ($post->status == 0)
            {
                return redirect()->back()->withInput()->with('error', 'Invalid category selected');
            }
            if ($post->is_featured == 0)
            {
                return redirect()->back()->withInput()->with('error', 'Invalid category selected');
            }
            $file = $request->image;

            if (!empty($file)) {
                // Kiểm tra xem tệp hình ảnh cũ có tồn tại không
                if (!empty($post->image)) {
                    $oldImagePath = 'uploads/image/' . $post->image;
                    if (Storage::disk('public')->exists($oldImagePath)) {
                        Storage::disk('public')->delete($oldImagePath);
                    }
                }

                $fileName = time() . '-' . $file->getClientOriginalName();
                $post->image = $fileName;
                Storage::disk('public')->put('uploads/image/' . $fileName, file_get_contents($file));
            }

            $post->save();
            session()->flash('success', 'Post created successfully');
            return redirect()->route('posts.index')->with('success', 'Create post successfully');
        } catch (ValidationException $e) {
            // Validation failed, delete any temporary images
            $contentImages = $request->input('contentImages', []);
            $this->deleteImagesInContent($contentImages);

            // Delete image if it exists
            if (!empty($post->content)) {
                // Lấy danh sách ảnh từ trường content
                $contentPaths = $this->getImagePathsFromContent($post->content);

                // Xóa ảnh từ trường content
                $this->deleteImagesInContent($contentPaths);
            }

            return redirect()->back()->withInput()->withErrors($e->validator)->with('error', 'Lỗi xác nhận. Vui lòng kiểm tra thông tin nhập.');
        } catch (\Exception $e) {
            // Xóa các hình ảnh tạm thời từ trường content
            $contentImages = $request->input('contentImages', []);
            $this->deleteImages($contentImages);
            session()->flash('error', 'Error creating category: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Error creating post: ' . $e->getMessage());
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
        $post = Posts::where('slug', $slug)->firstOrFail();
        return response()->view('posts.detail', compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $posts = Posts::findOrFail($id);
        $categories = Category::get();

        // Kiểm tra quyền truy cập sử dụng policy
        $this->authorize('editPost', $posts);
        
        return response()->view('posts.edit', compact('id', 'posts','categories'));
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
        $post = Posts::findOrFail($id);
        // Kiểm tra quyền truy cập sử dụng policy
        $this->authorize('updatePost', $post);

        $post->title = $request->title;
        $post->is_featured = $request->is_featured;
        $post->status = $request->status;
        $post->excerpt = $request->excerpt;
        $post->content = $request->content;
        $post->posted_at = $request->posted_at;
        $post->category_id = $request->category_id;

        if ($post->category_id == 0){
            return redirect()->back()->withInput()->with('error', 'Invalid category selected');
        }

        if ($post->status == 0)
        {
            return redirect()->back()->withInput()->with('error', 'Invalid category selected');
        }
        if ($post->is_featured == 0)
        {
            return redirect()->back()->withInput()->with('error', 'Invalid category selected');
        }

        if($post->category_id == 0)
        {
            $request->validate([
                'category_id' => 'required',
            ]);
        }

        // Kiểm tra xem giá trị mới của slug có khác với giá trị hiện tại hay không
        if ($request->slug != $post->slug) {
            $request->validate([
                'slug' => 'required|unique:posts,slug|max:255',
            ], [
                'slug.required' => 'Please enter Slug',
                'slug.unique' => 'The slug has already been taken',
            ]);

            // Nếu validation pass, cập nhật giá trị mới của slug
            $post->slug = $request->slug;
        }

        $file = $request->image;

        if (!empty($file)) {
            // Kiểm tra xem tệp hình ảnh cũ có tồn tại không
            if (!empty($post->image)) {
                $old_image_path = 'uploads/image/' . $post->image;
                if (Storage::disk('public')->exists($old_image_path)) {
                    Storage::disk('public')->delete($old_image_path);
                }
            }

            $fileName = time() . '-' . $file->getClientOriginalName();
            $post->image = $fileName;
            Storage::disk('public')->put('uploads/image/' . $fileName, file_get_contents($file));
        }

        $post->save();
        session()->flash('success', 'Post created successfully');
        return redirect()->route('posts.index', ['slug' => $post->slug])->with('success', 'Update post successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function deleteMultiple(Request $request)
    {
        // Kiểm tra quyền truy cập sử dụng policy
        $this->authorize('deleteMultiple', Posts::class);
        
        $selectedPosts = $request->input('selectedPosts', []);

        if (!is_array($selectedPosts)) {
            return response()->redirect()->route('posts.index')->with('error', 'Invalid data for deletion');
        }

        if (count($selectedPosts) > 0) {
            $postsToDelete = Posts::whereIn('id', $selectedPosts)->get();

            // Lấy danh sách ảnh từ trường image và content
            $imagePaths = [];
            $contentPaths = [];

            foreach ($postsToDelete as $post) {
                $imagePaths[] = $post->image;
                // dd($imagePaths);
                $contentPaths = array_merge($contentPaths, $this->getImagePathsFromContent($post->content));
                // dd($contentPaths);
            }

            

            // Xóa bài viết
            Posts::whereIn('id', $selectedPosts)->delete();

            // Xóa ảnh từ trường image
            $this->deleteImages($imagePaths);

            // Xóa ảnh từ trường content
            $this->deleteImagesInContent($contentPaths);

            return redirect()->route('posts.index')->with('success', 'Delete selected posts successfully');
        }

        return redirect()->route('posts.index')->with('warning', 'No posts selected for deletion');
    }
    protected function deleteImages($imagePaths)
    {
        foreach ($imagePaths as $imagePath) {
            $fullImagePath = 'uploads/image/' . $imagePath;

            if (Storage::disk('public')->exists($fullImagePath)) {
                Storage::disk('public')->delete($fullImagePath);
            }
        }
        // dd($imagePath);
    }
    protected function getImagePathsFromContent($content)
    {
        $imagePaths = [];

        $dom = new \DOMDocument;
        libxml_use_internal_errors(true);
        $dom->loadHTML($content);
        libxml_clear_errors();

        $images = $dom->getElementsByTagName('img');

        foreach ($images as $image) {
            $src = $image->getAttribute('src');

            if (!empty($src)) {
                $imageName = basename($src);

                $imagePaths[] = $imageName;
            }
        }

        return $imagePaths;
    }
    protected function deleteImagesInContent($imagePathsInContent)
    {
        dd($imagePathsInContent);
        foreach ($imagePathsInContent as $imagePath) {
            // Xây dựng đường dẫn đầy đủ của hình ảnh
            $fullImagePath = ('uploads/gallery/' . $imagePath);

            if (Storage::disk('public')->exists($fullImagePath)) {
                Storage::disk('public')->delete($fullImagePath);
            }
        }
        // dd($imagePathsInContent);
    }
    public function updatePostStatus(Request $request)
    {
        if ($request->has('statusData')) {
            $statusData = json_decode($request->input('statusData'), true);

            foreach ($statusData as $data) {
                $post = Posts::findOrFail($data['id']);

                // Kiểm tra quyền truy cập sử dụng policy
                $this->authorize('updateStatus', $post);
            
                $post->status = $data['status'];
                $post->save();
            }

            return redirect()->route('posts.index')->with('success', 'Cập nhật trạng thái thành công');
        } else {
            return redirect()->route('posts.index')->with('error', 'Không có dữ liệu trạng thái được cung cấp');
        }
    }

// Xóa một item

    public function destroy($id)
    {
        $post = Posts::findOrFail($id);
        $this->authorize('updatePost', $post);

        // Xóa tệp hình ảnh từ trường content nếu tồn tại
        if ($post->content) {
            $imagePathsInContent = $this->getPaths($post->content);
            $this->deletePic($imagePathsInContent);
        }

        // Xóa tệp hình ảnh từ trường image nếu tồn tại
        if ($post->image) {
            $imagePath = 'uploads/image/' . $post->image;
            if (Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
        }

        // Xóa bài đăng
        $post->delete();

        return response()->json(['message' => 'Delete post successfully']);
    }

    // Thêm hàm mới để lấy danh sách đường dẫn ảnh từ nội dung
    protected function getPaths($content)
    {
        $imagePaths = [];

        $dom = new \DOMDocument;
        libxml_use_internal_errors(true);
        $dom->loadHTML($content);
        libxml_clear_errors();

        $images = $dom->getElementsByTagName('img');

        foreach ($images as $image) {
            $src = $image->getAttribute('src');

            if (!empty($src)) {
                $imageName = basename($src);

                $imagePaths[] = $imageName;
            }
        }

        return $imagePaths;
    }

// Thêm hàm mới để xóa ảnh trong thư mục gallery
    protected function deletePic($imagePathsInContent)
    {
        foreach ($imagePathsInContent as $imagePath) {
            $fullImagePath = 'uploads/gallery/' . $imagePath;

            if (Storage::disk('public')->exists($fullImagePath)) {
                Storage::disk('public')->delete($fullImagePath);
            }
        }
    }
}