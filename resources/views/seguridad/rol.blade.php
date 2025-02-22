<x-app-layout>
    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- /.col-md-6 -->
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex align-items-center">
                                <h5 class="m-0">Roles</h5>
                                @can('rol-crear')
                                    <button id="btnAdd" class="ml-2 btn btn-primary btn-sm">
                                        <i class="fas fa-plus"></i>
                                        <span>Nuevo</span>
                                    </button>
                                @endcan
                            </div>
                        </div>
                        <div class="card-body table-responsive">
                            <div class="table-striped table-hover table-sm">
                                {{ $dataTable->table() }}
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.col-md-6 -->
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
    <!--Modal -->
    <div class="modal fade" id="modal-update">
        <div class="modal-dialog modal-lg">
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- Fin Modal -->
    @push('scripts')
        <script>
            //Marcando las opciones de menú
            $('#liSeguridad').addClass("menu-open");
            $('#aSeguridad').addClass("active");
            $('#liRol').addClass("active");
        </script>
        {{ $dataTable->scripts() }}
        <script>
            //Métodos CRUD
            //Obteniendo el click realizado en la tabla
            $('#table-listado').on('click', '.action', function() {
                let data = $(this).data();
                let id = data.id;
                let op = data.action;
                if (op == 'delete') {
                    Swal.fire({
                        title: 'Eliminar Registro',
                        text: "¿Esta seguro de querer eliminar el registro?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Si',
                        cancelButtonText: 'No'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            eliminar(id);
                        }
                    })
                }
                if (op == 'edit') {
                    $.ajax({
                        method: 'get',
                        url: `{{ url('admin/rol/') }}/${id}/edit`,
                        success: function(res) {
                            $('#modal-update').find('.modal-dialog').html(res);
                            $("#textoBoton").text("Actualizar");
                            $('#modal-update').modal("show");
                            guardar();
                        }
                    });
                }
            });
            //Obteniendo el click del botón crear
            $('#btnAdd').on('click', function() {
                $.ajax({
                    method: 'get',
                    url: `{{ url('admin/rol/create') }}`,
                    success: function(res) {
                        $('#modal-update').find('.modal-dialog').html(res);
                        $("#textoBoton").text("Guardar");
                        $('#modal-update').modal("show");
                        guardar();
                    }
                });
            })
            //Guardar y editar
            function guardar() {
                $('#formUpdate').on('submit', function(e) {
                    e.preventDefault();
                    const _form = this;
                    const formData = new FormData(_form);
                    const url = this.getAttribute('action');
                    $.ajax({
                        method: 'POST',
                        url,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(res) {
                            $('#modal-update').modal("hide");
                            window.LaravelDataTables["table-listado"].ajax.reload();
                            Swal.fire({
                                icon: res.status,
                                title: res.message,
                                showConfirmButton: false,
                                timer: 1500
                            });
                        },
                        error: function(res) {
                            let errors = res.responseJSON?.errors;
                            $(_form).find(`[name]`).removeClass('is-invalid');
                            $(_form).find('.invalid-feedback').remove();
                            if (errors) {
                                for (const [key, value] of Object.entries(errors)) {
                                    $(_form).find(`[name='${key}']`).addClass('is-invalid')
                                    $(`[name='${key}']`).parent().append(
                                        `<span class="invalid-feedback">${value}</span>`);
                                }
                            }
                        }
                    });
                })
            }
            //Eliminar
            function eliminar(id) {
                $.ajax({
                    method: 'DELETE',
                    url: `{{ url('admin/rol/') }}/${id}`,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        window.LaravelDataTables["table-listado"].ajax.reload();
                        Swal.fire({
                            icon: res.status,
                            title: res.message,
                            showConfirmButton: false,
                            timer: 1500
                        });
                    },
                    error: function(res) {

                    }
                });
            }
        </script>
    @endpush
</x-app-layout>
