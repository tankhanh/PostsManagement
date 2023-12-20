@extends('master')
@section('create')
@section('module', 'Create Post')
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
<form method="post" action="{{ route('posts.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <h5 class="modal-title">New post</h5>
            <br>
            <div class="modal-body">
                <div class="form-selectgroup-boxes row mb-3">
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input id="title" type="text" class="form-control" name="title" placeholder="Enter title"
                                onkeyup="ChangeToSlug();" value="">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class=" mb-3">
                            <label class="form-label">Slug</label>
                            <input id="slug" type="text" class="form-control" name="slug" value="">
                        </div>
                    </div>
                </div>
                <div class="form-selectgroup-boxes row mb-3">
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label>Featured</label>
                            <select class="form-control" name="is_featured">
                                <option value="1" {{ old('is_featured')==1 ? 'selected' : '' }}>Featured</option>
                                <option value="2" {{ old('is_featured')==2 ? 'selected' : '' }}>Default</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label>Status</label>
                            <select class="form-control" name="status">
                                <option value="1" {{ old('status')==1 ? 'selected' : '' }}>Show</option>
                                <option value="2" {{ old('status')==2 ? 'selected' : '' }}>Hidden</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="profilePicInput">Image</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="profilePicInput" name="image">
                        <label class="custom-file-label" for="profilePicInput">
                        </label>
                    </div>
                </div>
                <div class="mb-3">
                    <label>Image Preview</label>
                    <div class="avatar-preview" style="text-align:center">
                        <img style="width:500px" id="profilePicPreview" class="img-fluid img-circle"
                            src="{{ asset('uploads/image/default-image.png')}}" alt="Image">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Excerpt</label>
                    <textarea id="Excerpt" class="form-control" name="excerpt"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Content</label>
                    <textarea id="Content" class="form-control" name="content"></textarea>
                </div>
                <div class="col-lg-6">
                    <div class="mb-3">
                        <label class="form-label">Posted At</label>
                        <input type="date" class="form-control" name="posted_at">
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="{{route('posts.index')}}" class="btn btn-link link-secondary" data-bs-dismiss="modal">
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
                        Create
                    </button>
                </div>
            </div>
        </div>
</form>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const profilePicInput = document.getElementById('profilePicInput');
    const profilePicPreview = document.getElementById('profilePicPreview');
    const customFileLabel = document.querySelector('.custom-file-label');

    profilePicInput.addEventListener('change', function() {
        if (profilePicInput.files && profilePicInput.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
                profilePicPreview.src = e.target.result;
                customFileLabel.innerText = profilePicInput.files[0].name;
            };

            reader.readAsDataURL(profilePicInput.files[0]);
        }
    });


    const contentTextarea = document.querySelector('#Content');
    const uploadedImages = [];

    ClassicEditor
        .create(contentTextarea, {
            ckfinder: {
                uploadUrl: "{{route('ckeditor.upload', ['_token' => csrf_token()])}}",
            },
        })
        .then(editor => {
            editor.model.document.on('change:data', () => {
                // Lấy danh sách các ảnh đã upload
                const data = editor.getData();
                const regex = /<img[^>]+src="([^">]+)"/g;
                let match;
                const imagesInGallery = [];

                while ((match = regex.exec(data)) !== null) {
                    imagesInGallery.push(match[1]);
                }

                // So sánh với danh sách các ảnh đã upload trước đó
                const deletedImages = uploadedImages.filter(img => !imagesInGallery.includes(img));

                // Xóa các ảnh không còn tồn tại trong nội dung
                deleteUnusedImages(deletedImages);

                // Cập nhật danh sách các ảnh đã upload
                uploadedImages.length = 0;
                Array.prototype.push.apply(uploadedImages, imagesInGallery);
            });
        })
        .catch(error => {
            console.error(error);
        });

    function deleteUnusedImages(imagesInGallery) {
        if (imagesInGallery.length > 0) {
            axios.post("{{ route('ckeditor.deleteImages') }}", {
                    imagesInGallery: imagesInGallery
                })
                .then(response => {
                    console.log(response.data);
                })
                .catch(error => {
                    console.error(error);
                });
        }
    }

});
</script>
<script src="https://cdn.ckeditor.com/ckeditor5/40.2.0/classic/ckeditor.js"></script>
<script src="{{ asset('js/ckfinder.js') }}"></script>
<script>
ClassicEditor
    .create(document.querySelector('#Excerpt'), {
        toolbar: {
            items: [
                'heading',
                '|',
                'bold',
                'italic',
                'link',
                'bulletedList',
                'numberedList',
                '|',
                'blockQuote'
                // Bạn có thể thêm hoặc xóa các mục khác tùy ý ở đây
            ]
        },
    })
    .catch(error => {
        console.error(error);
    });
// ClassicEditor
//     .create(document.querySelector('#Content'), {
//         ckfinder: {
//             uploadUrl: "{{route('ckeditor.upload', ['_token' => csrf_token()])}}",
//         }
//     })
//     .catch(error => {
//         console.error(error);
//     });
</script>
@endsection