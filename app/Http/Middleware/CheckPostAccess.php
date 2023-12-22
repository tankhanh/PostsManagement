<?php

namespace App\Http\Middleware;

use App\Models\Posts;
use App\Models\Account;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPostAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
            if ($request->is('posts/create')) {
                if(Auth::user()->role != 1)
                {
                    abort(403, 'Unauthorized action.');
                }
        }
        elseif($request->is('posts/delete-multiple')){
            if(Auth::user()->role != 1)
                {
                    abort(403, 'Unauthorized action.');
                }
        } 
        else {
            // Nếu không phải là yêu cầu tạo, tiếp tục với logic hiện tại của bạn
            $postId = $request->route('id');
            $post = Posts::findOrFail($postId);

            // Kiểm tra xem người dùng có là admin hay không
            if (Auth::user()->role == 1) { // Role = 1 là Admin
                return $next($request); // Admin có toàn quyền
            }

            // Kiểm tra xem người dùng có quyền sửa đổi bài viết hay không
            if (Auth::id() !== $post->user_id) {
                abort(403, 'Unauthorized action.');
            }
        }

        return $next($request);
    }
}