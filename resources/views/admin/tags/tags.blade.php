<!-- Tabla -->
<div>
    <div class="card">
        <div class="card-body">
            <table class="table table-striped" id="tablaCrud" style="width:100%">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Tags</th>
                        <th width='120px'>Acciones</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<!-- Tabla -->
<!-- Modal -->
<div class="modal fade" id="formCrudModal"  role="dialog" aria-labelledby="formCrudModalTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form id="formCrud">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="formCrudModalTitle">Nuevo Tag</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="col-xs-12">
                        <div id="alert_message_type" class="collapse">
                        </div>
                    </div>
                    @csrf
                    <input type="hidden" id="id" name="id">
                    <div class="form-group">
                        <label for="name" class="col-form-label">Tag: <small>(<span
                                    class="text-danger">*</span>)</small></label>
                        <input type="text" class="form-control" id="name" name="name" autofocus required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="sumbitButton" class="btn btn-primary" type="submit">
                        <span id="submitSpinner" class="spinner-border-sm" role="status" aria-hidden="true"></span>
                        Guardar
                    </button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>

                </div>
            </div>
        </form>
    </div>
</div>