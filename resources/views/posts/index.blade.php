@extends('layouts.app')
@section('posts.index')
@section('module', 'Dashboard')
<!-- Page body -->
<script>
function confirmationDelete(module) {
    return confirm(`Are you sure you want to delete this ${module} ?`);
}

function clearCheckboxes() {
    var checkboxes = document.querySelectorAll('.datatable tbody td input[type="checkbox"]');
    var deleteAllBtn = document.getElementById('deleteAllBtn');

    checkboxes.forEach(function(checkbox) {
        checkbox.checked = false;
    });
    deleteAllBtn.classList.add('d-none');
}

function updateDeleteAllBtn() {
    var checkboxes = document.querySelectorAll('.datatable tbody td input[type="checkbox"]');
    var deleteAllBtn = document.getElementById('deleteAllBtn');
    var checkedCheckboxes = document.querySelectorAll('.datatable tbody td input[type="checkbox"]:checked');

    deleteAllBtn.textContent = `Delete ${checkedCheckboxes.length} Item${checkedCheckboxes.length > 1 ? 's' : ''}`;

    deleteAllBtn.classList.toggle('d-none', checkedCheckboxes.length === 0);
}
</script>
<div class="page-body">
    <div class="container-xl">
        <div class="row row-deck row-cards">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">List of posts</h3>
                        <div class="col-auto ms-auto d-print-none">
                            <div class="btn-list">
                                <a href="{{ route('posts.create') }}" class="btn btn-primary d-none d-sm-inline-block"
                                    data-bs-toggle="modal" data-bs-target="#modal-report">
                                    <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M12 5l0 14" />
                                        <path d="M5 12l14 0" />
                                    </svg>
                                    Create new post
                                </a>
                                <a href="{{ route('posts.create') }}" class="btn btn-primary d-sm-none btn-icon"
                                    data-bs-toggle="modal" data-bs-target="#modal-report"
                                    aria-label="Create new report">
                                    <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M12 5l0 14" />
                                        <path d="M5 12l14 0" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body border-bottom py-3">
                        <div class="d-flex">
                            <div class="text-muted">
                                Show
                                <div class="mx-2 d-inline-block">
                                    <span class="form-control form-control-sm" size="3"
                                        aria-label="Invoices count">{{ $entriesCount }}</span>
                                </div>
                                entries
                            </div>
                            <div class="ms-auto text-muted">
                                Search:
                                <div class="ms-2 d-inline-block">
                                    <input type="text" id="searchInput" class="form-control form-control-sm"
                                        aria-label="Search invoice">
                                </div>
                            </div>
                            <div id="searchResults"></div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <form method="POST" action="{{ route('posts.deleteMultiple') }}" id="deleteMultipleForm">
                            @csrf
                            @method('DELETE')
                            <table class="table card-table table-vcenter text-nowrap datatable">
                                <thead>
                                    <tr>
                                        <th class="w-1"><input id="checkboxAll"
                                                class="form-check-input m-0 align-middle" type="checkbox"
                                                aria-label="Select all invoices"></th>
                                        <th class="w-1">ID
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm icon-thick"
                                                width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                                stroke="currentColor" fill="none" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M6 15l6 -6l6 6" />
                                            </svg>
                                        </th>
                                        <th>Title</th>
                                        <th>Slug</th>
                                        <th>Category</th>
                                        <th>is_featured</th>
                                        <th>status</th>
                                        <th>image</th>
                                        <!-- <th>excerpt</th>
                                        <th>content</th> -->
                                        <th>posted_at</th>
                                        <th>created_at</th>
                                        <th>updated_at</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($posts as $post)
                                    <tr>
                                        <!-- <td><input class="form-check-input m-0 align-middle" type="checkbox"
                                                aria-label="Select invoice"></td> -->
                                        <td><input name="selectedPosts[]" value="{{ $post->id }}"
                                                class="form-check-input m-0 align-middle" type="checkbox"
                                                aria-label="Select invoice" data-status="{{ $post->status }}"
                                                onchange="updateDeleteAllBtn()"></td>
                                        <td><span class="text-muted">{{ $loop->iteration}}</span></td>
                                        <td><a href="{{ route('posts.detail', ['id' => $post->id, 'slug' => $post->slug]) }}"
                                                class="text-reset" tabindex="-1">{{ $post->title }}</a>
                                        </td>
                                        <td>{{$post->slug}}</td>
                                        <td>{{$post->category->name}}</td>
                                        <td>
                                            @if($post->is_featured == 0)
                                            N/A
                                            @elseif($post->is_featured == 1)
                                            <span class="right badge bg-success">Featured</span>
                                            @else
                                            <span class="right badge badge-danger">Default</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($post->status == 0)
                                            N/A
                                            @elseif($post->status == 1)
                                            <span class="right badge bg-success">Public</span>
                                            @else
                                            <span class="right badge badge-danger">Private</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($post->image == null)
                                            N/A
                                            @else
                                            <img src="{{ Storage::url('uploads/image/'.$post->image)}}"
                                                alt="Image Current" width="50px" height="50px">
                                            @endif
                                        </td>
                                        <!-- <td>{!! $post->excerpt !!}</td>
                                        <td>{!! $post->content !!}</td> -->
                                        <td>{{ date('d/m/y - H:i:s', strtotime($post->posted_at))}}</td>
                                        <td>{{ date('d/m/y - H:i:s', strtotime($post->updated_at))}}</td>
                                        <td>{{ date('d/m/y - H:i:s', strtotime($post->updated_at))}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </form>
                    </div>
                    <form method="POST" action="{{ route('posts.updatePostStatus') }}" id="updateStatusForm">
                        @csrf
                        @method('PUT')
                        <button type="button" id="updateStatusBtn" class="btn btn-primary ml-auto d-none"
                            style="width: 100%;" onclick="updatePostStatus()">Update Status</button>
                    </form>
                    <button type="button" id="deleteAllBtn" class="btn btn-danger ml-auto d-none" style="float: right;"
                        onclick="deleteMultiple()">Delete All</button>
                    <div class="card-footer d-flex align-items-center">
                        <p class="m-0 text-muted">Showing <span>{{ $posts->firstItem() }}</span> to
                            <span>{{ $posts->lastItem() }}</span> of <span>{{ $entriesCount }}</span> entries
                        </p>
                        {{ $posts->links() }}
                    </div>
                    @foreach($posts as $post)
                    <a href="{{ route('posts.destroy', ['id' => $post->id]) }}" id="deleteAllBtn"
                        class="btn btn-danger ml-auto d-none" style="float: right;"
                        onclick=" return confirmationDelete('post'); clearCheckboxes();">Delete
                        All</a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
<script>
function updateDeleteAllBtn() {
    var checkboxes = document.querySelectorAll('.datatable tbody td input[type="checkbox"]');
    var updateStatusBtn = document.getElementById('updateStatusBtn');

    var checkedCheckboxes = document.querySelectorAll('.datatable tbody td input[type="checkbox"]:checked');

    // Nếu có ít nhất một checkbox được chọn, hiển thị nút "Update Status"
    updateStatusBtn.classList.toggle('d-none', checkedCheckboxes.length === 0);
}

function updatePostStatus() {
    var form = document.getElementById('updateStatusForm');
    var checkedCheckboxes = document.querySelectorAll('.datatable tbody td input[type="checkbox"]:checked');

    if (checkedCheckboxes.length > 0) {
        if (confirm(`Are you sure you want to update status of selected posts?`)) {
            // Lưu trạng thái mới vào mảng để gửi đi
            var statusData = Array.from(checkedCheckboxes).map(function(checkbox) {
                return {
                    id: checkbox.value,
                    status: checkbox.dataset.status == 1 ? 2 : 1 // Đảo ngược trạng thái
                };
            });

            // Thêm mảng statusData vào form để gửi đi
            var statusInput = document.createElement('input');
            statusInput.type = 'hidden';
            statusInput.name = 'statusData';
            statusInput.value = JSON.stringify(statusData);
            form.appendChild(statusInput);

            form.submit();
        }
    } else {
        alert('Please select at least one category to update status.');
    }
}
</script>
<script>
function deleteMultiple() {
    var form = document.getElementById('deleteMultipleForm');
    if (confirm(`Are you sure you want to delete selected posts?`)) {
        form.submit();
    }
}
document.getElementById('searchInput').addEventListener('input', function() {
    var searchText = this.value.toLowerCase();

    // Lặp qua từng hàng trong bảng để ẩn/hiện dựa trên nội dung tìm kiếm
    var tableRows = document.querySelectorAll('.datatable tbody tr');
    tableRows.forEach(function(row) {
        var title = row.querySelector('td:nth-child(3)').textContent
            .toLowerCase();
        if (title.includes(searchText)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});

document.getElementById('checkboxAll').addEventListener('change', function() {
    var checkboxes = document.querySelectorAll('.datatable tbody td input[type="checkbox"]');
    var deleteAllBtn = document.getElementById('deleteAllBtn');

    checkboxes.forEach(function(checkbox) {
        checkbox.checked = this.checked;
    }, this);

    deleteAllBtn.classList.toggle('d-none', !Array.from(checkboxes).some(checkbox => checkbox.checked));

    this.indeterminate = false;
    if (this.checked && Array.from(checkboxes).every(checkbox => checkbox.checked)) {
        this.indeterminate = true;
    }
    updateDeleteAllBtn();
});

// Lắng nghe sự kiện change cho mỗi checkbox trong tbody
document.querySelectorAll('.datatable tbody td input[type="checkbox"]').forEach(function(checkbox) {
    checkbox.addEventListener('change', function() {
        var checkboxes = document.querySelectorAll('.datatable tbody td input[type="checkbox"]');
        var deleteAllBtn = document.getElementById('deleteAllBtn');

        deleteAllBtn.classList.toggle('d-none', !Array.from(checkboxes).some(checkbox => checkbox
            .checked));

        updateDeleteAllBtn();
    });
});
</script>
@endsection