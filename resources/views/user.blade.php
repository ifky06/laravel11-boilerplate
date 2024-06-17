@extends('layouts.template')

@section('title', 'Pengguna')

@section('content')

    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Pengguna</h1>
                </div>
                {{--                <div class="col-sm-6">--}}
                {{--                    <ol class="breadcrumb float-sm-right">--}}
                {{--                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>--}}
                {{--                        <li class="breadcrumb-item active">Dashboard</li>--}}
                {{--                    </ol>--}}
                {{--                </div>--}}
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">

        <div class="modal fade" id="modal-form">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form id="form" method="post" action="{{url('user')}}">
                        <div class="modal-header">
                            <h4 class="modal-title">Tambah User</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">

                            @csrf
                            <div class="form-group row">
                                <label for="name" class="col-sm-3 col-form-label">Nama</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="email" class="col-sm-3 col-form-label">Email</label>
                                <div class="col-sm-9">
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="password" class="col-sm-3 col-form-label">Password</label>
                                <div class="col-sm-9">
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                            </div>
{{--                            roles--}}
                            <div class="form-group row">
                                <label for="roles" class="col-sm-3 col-form-label">Role</label>
                                <div class="col-sm-9">
                                    <select name="roles" id="roles" class="form-control" required>
                                        <option value="">-- Pilih Role --</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>
                </div>
                <!-- /.modal-content -->
                <!-- /.modal-dialog -->
            </div>
        </div>
        <!-- /.modal -->

        <!-- Default box -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Pengguna</h3>
            </div>
            <div class="card-body">
                <a href="#" class="btn btn-sm btn-success my-2 btn-add" data-toggle="modal" data-target="#modal-form">Tambah
                    Data</a>
                <div class="row pt-1">
                    <p class="mx-2">Filter:</p>
                    <select class="form-control form-control-sm mr-1" style="width: 15%" id="roleFilter">
                    </select>
                    <button class="btn btn-sm btn-warning h-75" disabled id="clearButton">Clear</button>
                </div>
                <table id="dataTable" class="table table-bordered table-striped mb-3">
                    <thead>
                    <tr>
                        <th>No</th>
                        {{--                        <th>Username</th>--}}
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- /.card -->

    </section>
@endsection

@push('css')

@endpush

@push('scripts')

    <script>
        $(document).ready(function () {
            let table = $('#dataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('users/data') }}",
                    dataType: 'json',
                    type: 'POST',
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'index'},
                    // {data: 'username', name: 'username'},
                    {data: 'name', name: 'name'},
                    {data: 'email', name: 'email'},
                    {data: 'roles', name: 'roles'},
                    {
                        data: 'id', name: 'id', orderable: false, searchable: false,
                        render: function (data, type, full, meta) {
                            {{--let user_id = {{Auth::user()->id}};--}}
                            {{--if (user_id === data) {--}}
                            {{--    return '<a href="{{url('user')}}/'+data+'/edit" class="btn btn-primary btn-sm mr-1">Edit</a>';--}}
                            {{--}--}}
                                return '<a href="#" class="btn btn-primary btn-sm mr-1 btn-edit" data-id="' + data + '" data-toggle="modal" data-target="#modal-form">Edit</a>' +
                                '<button class="btn btn-danger btn-sm btn-delete" data-id="' + data + '">Delete</button>';
                        }
                    },
                ]
            });

            $(document).on('click', '.btn-add', function () {
                $('#modal-form').find('.modal-title').text('Tambah User');
                $('#form').attr('action', "{{ url('users') }}");
                $('#form').find('input[name=_method]').remove();
                $('#form').find('#id').val('');
                $('#form').find('#name').val('');
                $('#form').find('#email').val('');

                $.ajax({
                    url: "{{ url('getroles') }}",
                    method: "GET",
                    dataType: 'json',
                    success: function (data) {
                        $('#roles').empty();
                        $('#roles').append('<option value="">-- Pilih Role --</option>');
                        $.each(data, function (index, value) {
                            $('#roles').append('<option value="' + value.name + '">' + value.name + '</option>');
                        });
                    }
                });
            });

            $(document).on('click', '.btn-edit', function () {
                let id = $(this).data('id');
                $.ajax({
                    url: "{{ url('users') }}/" + id + "/edit",
                    method: "GET",
                    dataType: 'json',
                    success: function (data) {
                        $('#modal-form').find('.modal-title').text('Edit User');
                        $('#form').attr('action', "{{ url('users') }}/" + id);
                        $('#form').append('<input type="hidden" name="_method" value="PUT">');
                        $('#form').find('#id').val(data.data.id);
                        $('#form').find('#name').val(data.data.name);
                        $('#form').find('#email').val(data.data.email);

                        $('#roles').empty();
                        $('#roles').append('<option value="">-- Pilih Role --</option>');
                        $.each(data.roles, function (index, value) {
                            if (value.name === data.data.roles[0].name) {
                                $('#roles').append('<option value="' + value.name + '" selected>' + value.name + '</option>');
                                return;
                            }
                                $('#roles').append('<option value="' + value.name + '">' + value.name + '</option>');

                        });

                    }
                });
            });

            $.ajax({
                url: "{{ url('getroles') }}",
                method: "GET",
                dataType: 'json',
                success: function (data) {
                    $('#roleFilter').append('<option value="">-- Pilih Role --</option>');
                    $.each(data, function (index, value) {
                        $('#roleFilter').append('<option value="' + value.name + '">' + value.name + '</option>');
                    });
                }


            });

            // $.each(role, function (index, value) {
            //     $('#roleFilter').append('<option value="' + value + '">' + value + '</option>');
            // });

            $('#roleFilter').change(function () {
                table.column(3).search($(this).val()).draw()
            });

            $('#clearButton').click(function () {
                $('#roleFilter').val('');

                table.column(3).search('').draw();
                $(this).attr('disabled', true);
            });

            $('#roleFilter').change(function () {
                if ($('#roleFilter').val() != '') {
                    $('#clearButton').attr('disabled', false)
                } else {
                    $('#clearButton').attr('disabled', true)
                }
            })

            $(document).on('click', '.btn-delete', function () {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Apakah anda yakin?',
                    text: "Setelah dihapus, Anda tidak dapat memulihkan Data ini lagi!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6', // blue
                    cancelButtonColor: '#d33', // red
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        var form = $('<form>').attr({
                            action: "{{url('users')}}/" + id,
                            method: 'POST',
                            class: 'delete-form'
                        }).append('@csrf', '@method("DELETE")');

                        form.appendTo('body').submit();
                    }
                })
            });
        });
    </script>

@endpush
