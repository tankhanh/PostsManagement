@extends('layouts.app')
@section('title', 'Edit Category')
@section('categories.edit')
@section('module', 'Edit Category')
<!-- Parsley CSS -->
<link rel="stylesheet" href="https://parsleyjs.org/src/parsley.css">

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<!-- Parsley JS -->
<script src="https://parsleyjs.org/dist/parsley.min.js"></script>
<style>
.btn-list {
    visibility: hidden;
}
</style>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script language="javascript">
function ChangeToSlug() {
    var title, slug;

    //Lấy text từ thẻ input title 
    title = document.getElementById("title").value;

    //Đổi chữ hoa thành chữ thường
    slug = title.toLowerCase();

    //Đổi ký tự có dấu thành không dấu
    slug = slug.replace(/á|à|ả|ạ|ã|ă|ắ|ằ|ẳ|ẵ|ặ|â|ấ|ầ|ẩ|ẫ|ậ/gi, 'a');
    slug = slug.replace(/é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ/gi, 'e');
    slug = slug.replace(/i|í|ì|ỉ|ĩ|ị/gi, 'i');
    slug = slug.replace(/ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ/gi, 'o');
    slug = slug.replace(/ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự/gi, 'u');
    slug = slug.replace(/ý|ỳ|ỷ|ỹ|ỵ/gi, 'y');
    slug = slug.replace(/đ/gi, 'd');
    //Xóa các ký tự đặt biệt
    slug = slug.replace(/\`|\~|\!|\@|\#|\||\$|\%|\^|\&|\*|\(|\)|\+|\=|\,|\.|\/|\?|\>|\<|\'|\"|\:|\;|_/gi, '');
    //Đổi khoảng trắng thành ký tự gạch ngang
    slug = slug.replace(/ /gi, "-");
    //Đổi nhiều ký tự gạch ngang liên tiếp thành 1 ký tự gạch ngang
    //Phòng trường hợp người nhập vào quá nhiều ký tự trắng
    slug = slug.replace(/\-\-\-\-\-/gi, '-');
    slug = slug.replace(/\-\-\-\-/gi, '-');
    slug = slug.replace(/\-\-\-/gi, '-');
    slug = slug.replace(/\-\-/gi, '-');
    //Xóa các ký tự gạch ngang ở đầu và cuối
    slug = '@' + slug + '@';
    slug = slug.replace(/\@\-|\-\@|\@/gi, '');
    //In slug ra textbox có id “slug”
    document.getElementById('slug').value = slug;
}
</script>
<form method="post" action="{{ route('categories.update',['id' => $id]) }}" data-parsley-validate>
    @csrf
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <h5 class="modal-title">Edit category</h5>
            <br>
            <div class="modal-body">
                <div class="form-selectgroup-boxes row mb-3">
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input id="title" type="text" class="form-control" name="name" placeholder="Enter title"
                                onkeyup="ChangeToSlug();" value="{{ old('name', $categories->name) }}"
                                data-parsley-required="true">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class=" mb-3">
                            <label class="form-label">Slug</label>
                            <input id="slug" type="text" class="form-control" name="slug"
                                value="{{ old('slug', $categories->slug) }}">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-control" name="status" id="status" data-parsley-required="true">
                                <option value="0" {{ old('status', $categories->status) == 0 ? 'selected' : ''}}>
                                    ---Root ---
                                </option>
                                <option value="1" {{ old('status', $categories->status) == 1 ? 'selected' : '' }}>
                                    Show
                                </option>
                                <option value="2" {{ old('status', $categories->status) == 2 ? 'selected' : '' }}>
                                    Hidden
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="{{route('categories.index')}}" class="btn btn-link link-secondary" data-bs-dismiss="modal">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary ms-auto" data-bs-dismiss="modal">
                        <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                            stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 5l0 14" />
                            <path d="M5 12l14 0" />
                        </svg>
                        Update
                    </button>
                </div>
            </div>
        </div>
</form>
<script>
$(document).ready(function() {
    // Khởi tạo Parsley cho toàn bộ biểu mẫu
    var form = $('form[data-parsley-validate]');
    form.parsley();

    // Kiểm tra tính hợp lệ của tất cả các trường biểu mẫu khi trang tải
    // form.parsley().validate();
});
$('form[data-parsley-validate]').submit(function(e) {
    // Kiểm tra giá trị của trường "status"
    var statusValue = $('#status').val();

    // Nếu giá trị là 0, hiển thị thông báo và ngăn chặn form được submit
    if (statusValue == 0) {
        alert('Please select a status other than --- Root ---');
        e.preventDefault(); // Ngăn chặn form được submit
    }
});
</script>
@endsection