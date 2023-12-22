@extends('layouts.app')
@section('editprofile')
@section('module', 'Profile')
<!-- Page body -->
<div class="page-body">
    <div class="container-xl">
        <div class="row row-deck row-cards">
            <div class="col-12">
                <div class="card">
                    <div class="col d-flex flex-column">
                        <form method="POST" action="{{ route('update') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="card-body">
                                <div class="infomation">
                                    <div class="info-container">
                                        <div class="info-left">
                                            <div class="form-selectgroup-boxes row mb-3">
                                                <div class="col-lg-6">
                                                    <div class="mb-3">
                                                        <div class="form-group-profile">
                                                            <label class="form-label">First Name</label>
                                                            <input type="text" class="form-control"
                                                                placeholder="Enter first name" name="firstname"
                                                                value="{{old('firstname', Auth::user()->profile->firstname) }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class=" col-lg-6">
                                                    <div class="mb-3">
                                                        <div class="form-group-profile">
                                                            <label class="form-label">Last Name</label>
                                                            <input type="text" class="form-control"
                                                                placeholder="Enter last name" name="lastname"
                                                                value="{{old('lastname', Auth:: user()->profile->lastname) }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="mb-3">
                                                        <div class="form-group-profile">
                                                            <label class="form-label">Email</label>
                                                            <input type="text" class="form-control"
                                                                placeholder="Enter email" name="email"
                                                                {{ $edit_myself ? 'disabled': '' }}
                                                                value="{{ old('email', Auth::user()->email) }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class=" col-lg-6">
                                                    <div class="mb-3">
                                                        <div class="form-group-profile">
                                                            <label class="form-label">Role</label>
                                                            <input type="text" class="form-control"
                                                                placeholder="Enter last name" name="role"
                                                                {{ $edit_myself ? 'disabled': '' }}
                                                                value="{{old('role', Auth:: user()->role) }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="mb-3">
                                                        <div class="form-group-profile">
                                                            <label class="form-label">New password</label>
                                                            <input type="password" class="form-control"
                                                                placeholder="Enter password" name="password">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="mb-3">
                                                        <div class="form-group-profile">
                                                            <label class="form-label">Confirm Password</label>
                                                            <input type="password" class="form-control"
                                                                placeholder="Enter password"
                                                                name="password_confirmation">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="mb-3">
                                                        <div class="form-group-profile">
                                                            <label class="form-label">Gender</label>
                                                            <select class="form-control" name="gender">
                                                                <option value="1"
                                                                    {{ old('gender', Auth::user()->profile->gender) == 1 ? 'selected' : '' }}>
                                                                    Male</option>
                                                                <option value="2"
                                                                    {{ old('gender', Auth::user()->profile->gender) == 2 ? 'selected' : '' }}>
                                                                    Female</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="mb-3">
                                                        <div class="form-group-profile">
                                                            <label class="form-label">Birthday</label>
                                                            <input type="date" class="form-control" name="birthday"
                                                                value="{{ old('birthday', Auth::user()->profile->birthday) }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="info-right">
                                            <div class="form-selectgroup-boxes row mb-3">
                                                <div class="col-lg-6">
                                                    <div class="mb-3">
                                                        <div class="form-group-profile">
                                                            <label class="form-label">Avatar Preview</label>
                                                            <div class="avatar-preview" style="text-align:center">
                                                                <img id="profilePicPreview" class="img-fluid img-circle"
                                                                    src="{{ Storage::url('uploads/avatar/' . Auth::user()->profile->profile_pic) }}"
                                                                    alt="User profile picture" style="width:200px">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="mb-3">
                                                        <div class="form-group-profile">
                                                            <label class="form-label" for="profilePicInput">Change
                                                                Avatar</label>
                                                            <div class="custom-file">
                                                                <input type="file" class="custom-file-input"
                                                                    id="profilePicInput" name="profile_pic">
                                                                <!-- <label class="custom-file-label"
                                                                    for="profilePicInput">Choose
                                                                    file</label> -->
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent mt-auto">
                                <div class="btn-list justify-content-end">
                                    <a href="{{route('posts.index')}}" class="btn">
                                        Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">Update</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
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
});
</script>
@endsection