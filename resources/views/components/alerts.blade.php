@if($errors->any() || Session::get('success') || Session::get('error'))
<div id="custom-alerts">
    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <h5 class="alert-heading"><i class="icon fas fa-ban"></i> Error!</h5>
        <ul>
            @foreach($errors->all() as $error)
            <li>{{$error}}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(Session::get('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <h5 class="alert-heading"><i class="icon fas fa-check"></i> Success!</h5>
        {{ Session::get('success')}}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(Session::get('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <h5 class="alert-heading"><i class="icon fas fa-check"></i> Error!</h5>
        {{ Session::get('error')}}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
</div>
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        var alerts = document.querySelectorAll('#custom-alerts .alert');
        alerts.forEach(function(alert) {
            alert.style.display = 'none';
        });
    }, 1000000000);
    var closeButtons = document.querySelectorAll('#custom-alerts .btn-close');
    closeButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            var alert = this.closest('.alert');
            alert.style.display = 'none';
        });
    });
});
</script>