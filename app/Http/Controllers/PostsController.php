<?php

namespace App\Http\Controllers;

use App\Http\Requests\Post\StoreRequest;
use App\Http\Requests\Post\UpdateRequest;
use App\Models\Posts;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Sunra\PhpSimple\HtmlDomParser;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;

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
        try {
            $post = new Posts();

            $post->title = $request->title;
            $post->slug = $request->slug;
            $post->is_featured = $request->is_featured;
            $post->status = $request->status;
            $post->excerpt = $request->excerpt;
            $post->content = $request->content;
            $post->posted_at = $request->posted_at;

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

        return redirect()->route('posts.detail', ['slug' => $post->slug])->with('success', 'Update post successfully');
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
        foreach ($imagePathsInContent as $imagePath) {
            // Xây dựng đường dẫn đầy đủ của hình ảnh
            $fullImagePath = ('uploads/gallery/' . $imagePath);

            if (Storage::disk('public')->exists($fullImagePath)) {
                Storage::disk('public')->delete($fullImagePath);
            }
        }
        // dd($imagePathsInContent);
    }
}