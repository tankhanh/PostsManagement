@extends('layouts.app')
@section('title', 'Post')
@section('posts.index')
@section('module', 'Dashboard')
<!-- Page body -->
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
<!-- jQuery Toast Plugin CSS -->
<link rel="stylesheet" type="text/css"
    href="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.css">

<!-- DataTables JS -->
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js">
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.js"></script>
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
                    <div class="table-responsive">
                        <table id="myDataTable" class="table card-table table-vcenter text-nowrap datatable">
                            <thead>
                                <tr>
                                    <th class="w-1">
                                        ID
                                    </th>
                                    <th>Title</th>
                                    <th>Slug</th>
                                    <th>Category</th>
                                    <th>is_featured</th>
                                    <th>status</th>
                                    <!-- <th>image</th>
                                    <th>excerpt</th>
                                    <th>content</th> -->
                                    <th>posted_at</th>
                                    <!-- <th>created_at</th>
                                    <th>updated_at</th> -->
                                    <th width="105px">Action</th>
                                </tr>
                            </thead>
                        </table>
                        <script type="text/javascript">
                        $(document).ready(function() {
                            var detailRoute = "{{ route('posts.detail', ['slug' => ':slug']) }}";
                            $('#myDataTable').DataTable({
                                processing: true,
                                serverSide: true,
                                ajax: "{{ route('posts.index') }}",
                                columns: [{
                                        data: 'id',
                                        name: 'id',
                                    },
                                    {
                                        data: 'title',
                                        name: 'title'
                                    },
                                    {
                                        data: 'slug',
                                        name: 'slug'
                                    },
                                    {
                                        data: 'category.name',
                                        name: 'category.name'
                                    },
                                    {
                                        data: 'is_featured',
                                        name: 'is_featured',
                                        render: function(data, type, full, meta) {
                                            return data === 1 ? 'Featured' : (data === 2 ?
                                                'Default' : 'Unknown');
                                        }
                                    },
                                    {
                                        data: 'status',
                                        name: 'status',
                                        render: function(data, type, full, meta) {
                                            return data === 1 ? 'Public' : (data === 2 ?
                                                'Private' : 'Unknown');
                                        }
                                    },
                                    // {
                                    //     data: 'image',
                                    //     name: 'image'
                                    // },
                                    // {
                                    //     data: 'excerpt',
                                    //     name: 'excerpt'
                                    // },
                                    // {
                                    //     data: 'content',
                                    //     name: 'content'
                                    // },
                                    {
                                        data: 'posted_at',
                                        name: 'posted_at'
                                    },
                                    // {
                                    //     data: 'created_at',
                                    //     name: 'created_at'
                                    // },
                                    // {
                                    //     data: 'updated_at',
                                    //     name: 'updated_at'
                                    // },
                                    {
                                        data: 'action',
                                        name: 'action',
                                        orderable: false,
                                        searchable: false,
                                        render: function(data, type, full, meta) {
                                            return '<a href="' + detailRoute.replace(':slug',
                                                    full.slug) + '" class="text-reset">' +
                                                data + '</a>' +
                                                '<button class="btn btn-danger btn-sm delete-post" data-id="' +
                                                full.DT_RowId +
                                                '">Delete</button>'; // Thay đổi từ full.id thành full.DT_RowId
                                        }
                                    },
                                ],
                                "language": {
                                    "emptyTable": "There are no posts here"
                                },
                            });
                            // Xử lý sự kiện nhấp chuột vào nút "Chỉnh sửa"
                            $('#myDataTable tbody').on('click', 'a.edit', function() {
                                // Sử dụng DataTable để lấy dữ liệu trên hàng được chọn
                                var data = $('#myDataTable').DataTable().row($(this).closest('tr'))
                                    .data();

                                if (data) {
                                    // Đảm bảo rằng dữ liệu đã được lấy thành công
                                    var editUrl = "{{ route('posts.edit', ':id') }}".replace(':id',
                                        data.DT_RowId);
                                    window.location.href = editUrl;
                                } else {
                                    console.error('Error: Unable to retrieve data for editing.');
                                }
                            });
                            $('#myDataTable tbody').on('click', 'button.delete-post', function() {
                                var postId = $(this).data('id');
                                if (confirm('Are you sure you want to delete this post?')) {
                                    $.ajax({
                                        url: "{{ route('posts.destroy', ':id') }}".replace(
                                            ':id', postId),
                                        // Sửa đường dẫn
                                        type: 'DELETE',
                                        data: {
                                            _token: '{{ csrf_token() }}',
                                        },
                                        success: function(response) {
                                            $.toast({
                                                heading: 'Success',
                                                text: response.message,
                                                showHideTransition: 'slide',
                                                icon: 'success'
                                            });
                                            // Reload DataTable after successful deletion
                                            $('#myDataTable').DataTable().ajax.reload();
                                        },
                                        error: function(xhr, status, error) {
                                            console.error(xhr.responseText);
                                            $.toast({
                                                heading: 'Error',
                                                text: 'An error occurred while deleting the category.',
                                                showHideTransition: 'slide',
                                                icon: 'error'
                                            });
                                        }
                                    });
                                }
                            });
                        });
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection