<?php

namespace App\Policies;

use App\Models\Account;
use App\Models\Posts;
use Illuminate\Auth\Access\HandlesAuthorization;

class PostPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function editPost(Account $user, Posts $post)
    {
        if ($user->role == 1) {
            return true; // Admin có toàn quyền
        }
        // Kiểm tra xem người dùng có quyền chỉnh sửa bài viết hay không
        return $user->id === $post->user_id;
    }

    public function deleteMultiple(Account $user)
    {
        return $user->role == 1; // Chỉ admin có thể xóa nhiều bài viết
    }
    

    public function updateStatus(Account $user, Posts $post)
    {
        return $user->role == 1; // Chỉ admin có thể cập nhật trạng thái
    }

    public function createPost(Account $user)
    {
        return $user->role == 1;
    }
}