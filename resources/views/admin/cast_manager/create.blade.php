@extends('layouts.admin-navbar')
@section('content')
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Thêm diễn viên mới</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{route('cast.index')}}" class="btn btn-primary">Trở lại</a>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="container-fluid">
            <form action="" method="post" id="castForm" name="castForm">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name">Họ & Tên</label>
                                    <input type="text" name="name" id="name" class="form-control" placeholder="Name">
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="slug">Slug</label>
                                    <input type="text" readonly name="slug" id="slug" class="form-control" placeholder="Slug">
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <input type="hidden" id="image_id" name="image_id" value="">
                                    <label for="image">Ảnh</label>
                                    <div id="image" class="dropzone dz-clickable">
                                        <div class="dz-message needsclick">
                                            <br>Thả tập tin vào đây hoặc bấm vào để tải lên
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="mb-3">
                                    <label for="status">Trạng thái</label>
                                    <select name="status" id="status" class="btn btn-dark bi bi-caret-down">
                                        <option value="1">Hoạt động</option>
                                        <option value="0">Không hoạt động</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="pb-5 pt-3">
                    <button class="btn btn-primary" type="submit">Tạo</button>
                    <a href="{{route('cast.index')}}" class="btn btn-outline-dark ml-3">Hủy</a>
                </div>
            </form>
        </div>
        <!-- /.card -->
    </section>
@endsection
@section('customJs')
    <script>
        $("#castForm").submit(function (event){
            event.preventDefault();
            var element = $(this);
            $("button[type=submit]").prop('disabled', true)
            $.ajax({
                url: '{{route('cast.store')}}',
                type: 'post',
                data: element.serializeArray(),
                dataType: 'json',
                success: function (response){
                    $("button[type=submit]").prop('disabled', false)

                    if (response["status"] == true){
                        window.location.href='{{route('cast.index')}}'

                        $("#name").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
                        $("#slug").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
                    }else {
                        var errors = response['errors'];
                        if (errors['name']){
                            $("#name").addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors['name']);
                        }else {
                            $("#name").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
                        }

                        if (errors['slug']){
                            $("#slug").addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors['slug']);
                        }else {
                            $("#slug").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
                        }
                    }

                }, error: function (jqXHR, exception){
                    console.log("Something went wrong");
                }
            })
        })

        $("#name").change(function (){
            element = $(this);
            $("button[type=submit]").prop('disabled', true)
            $.ajax({
                url: '{{route("getSlug")}}',
                type: 'get',
                data: {title: element.val()},
                dataType: 'json',
                success: function (response) {
                    $("button[type=submit]").prop('disabled', false)
                    if (response["status"] == true){
                        $("#slug").val(response["slug"])
                    }
                }
            });
        })

        Dropzone.autoDiscover = false;
        const dropzone = $("#image").dropzone({
            init: function () {
                this.on('addedfile', function (file) {
                    if (this.files.length > 1) {
                        this.removeFile(this.files[0]);
                    }
                })
            },
            url: "{{ route('cast.temp-images.create') }}",
            maxFiles: 1,
            paramName: 'image',
            addRemoveLinks: true,
            acceptedFiles: "image/jpeg,image/png,image/gif",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }, success: function (file, response){
                $("#image_id").val(response.image_id);
                //console.log(response)
            }
        })
    </script>
@endsection
