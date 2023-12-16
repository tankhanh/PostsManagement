<?php

namespace App\Http\Controllers;

use App\Http\Requests\Post\StoreRequest;
use App\Http\Requests\Post\UpdateRequest;
use App\Models\Posts;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;

class PostsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $perPage = 5; 
        $search = $request->input('search');
        $entriesCount = Posts::where('title', 'like', '%' . $search . '%')->count();
        $posts = Posts::orderBy('created_at', 'DESC')->where('title', 'like', '%' . $search . '%')->paginate($perPage);

        return response()->view('posts.index', compact('entriesCount', 'posts', 'search'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return response()->view('posts.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        $imagePaths = [];
        
        try {
            $post = new Posts();
            
            $post->title = $request->title;
            $post->slug = $request->slug;
            $post->is_featured = $request->is_featured;
            $post->status = $request->status;
            $post->excerpt = $request->excerpt;
            $post->content = $request->content;
            $contentPaths = $this->getImagePathsFromContent($request->content);
            $post->posted_at = $request->posted_at;
            
            $file = $request->image;

            if (!empty($file)) {
                // Kiểm tra xem tệp hình ảnh cũ có tồn tại không
                if (!empty($post->image)) {
                    $old_image_path = public_path('uploads/image/' . $post->image);
                    if (File::exists($old_image_path)) {
                        File::delete($old_image_path); // Xóa tệp hình ảnh cũ
                    }
                }
                
                $request->validate([
                    'image' => 'required|mimes:jpeg,png,jpg,svg|max:2048'
                ], [
                    'image.required' => 'Please enter Image',
                    'image.mimes' => 'Images must be jpeg, png, jpg, svg',
                ]);
                
                $fileName = time() . '-' . $file->getClientOriginalName();
                $post->image = $fileName;
                $file->move(public_path('uploads/image/'), $fileName);
            }
            
            $request->session()->put('tempImages', $imagePaths);
            $post->save();
            
            return redirect()->route('posts.index')->with('success', 'Create post successfully');
        } catch (\Exception $e) {
            // Nếu có lỗi, xóa ảnh từ danh sách tạm thời và chuyển hướng với thông báo lỗi
            $tempImages = $request->session()->get('tempImages', []);
            $this->deleteImages($tempImages);
            return redirect()->back()->withInput()->with('error', 'Error creating post: ' . $e->getMessage());
        }
    }
    protected function getImagePathsFromContent($content)
    {
        $pattern = '/<img[^>]+src\s*=\s*["\']([^"\']+)["\']/';

        preg_match_all($pattern, $content, $matches);

        // Lấy ra danh sách đường dẫn hình ảnh
        return isset($matches[1]) ? $matches[1] : [];
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
        return response()->view('posts.edit', compact('id', 'posts'));
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

        $post->title = $request->title;
        $post->is_featured = $request->is_featured;
        $post->status = $request->status;
        $post->excerpt = $request->excerpt;
        $post->content = $request->content;
        $post->posted_at = $request->posted_at;

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
            if (!empty($post->image)) {
                $old_image_path = public_path('uploads/image/' . $post->image);
                if (file_exists($old_image_path)) {
                    unlink($old_image_path);
                }
            }

            $request->validate([
                'image' => 'required|mimes:jpeg,png,jpg,svg|max:2048'
            ], [
                'image.required' => 'Please enter Image',
                'image.mimes' => 'Images must be jpeg, png, jpg, svg',
            ]);

            $fileName = time() . '-' . $file->getClientOriginalName();
            $post->image = $fileName;
            $file->move(public_path('uploads/image/'), $fileName);
        }
        $post->save();

        return response()->redirect()->route('posts.detail', ['slug' => $post->slug])->with('success', 'Update post successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function destroy($id)
    // {
    //     $post = Posts::findOrFail($id);

    //     // Xóa tệp hình ảnh nếu tồn tại
    //     $postPath = public_path('uploads/image/' . $post->image);
    //     if (file_exists($postPath)) {
    //         unlink($postPath);
    //     }

    //     // Xóa bài đăng
    //     // $post->delete();

    //     return redirect()->route('posts.index')->with('success', 'Delete post successfully');
    // }

    public function deleteMultiple(Request $request)
    {
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
                $contentPaths = array_merge($contentPaths, $this->getImagePathsFromContent($post->content));
            }

            // Xóa bài viết
            Posts::whereIn('id', $selectedPosts)->delete();

            // Xóa ảnh từ trường image
            $this->deleteImages($imagePaths);

            // Xóa ảnh từ trường content
            $this->deleteContent($contentPaths);

            return redirect()->route('posts.index')->with('success', 'Delete selected posts successfully');
        }

        return redirect()->route('posts.index')->with('warning', 'No posts selected for deletion');
    }       
    // Hàm xóa ảnh
    // Hàm xóa ảnh
    protected function deleteImages($imagePaths)
    {
        foreach ($imagePaths as $imagePath) {
            $fullImagePath = public_path('uploads/image/' . $imagePath);

            if (File::exists($fullImagePath)) {
                File::delete($fullImagePath);
            }
        }
    }

// Hàm xóa nội dung
    protected function deleteContent($contentPaths)
        {
            foreach ($contentPaths as $contentPath) {
                $fullContentPath = public_path('uploads/gallery/' . $contentPath);

                if (File::exists($fullContentPath)) {
                    File::delete($fullContentPath);
                }
            }
        }
}