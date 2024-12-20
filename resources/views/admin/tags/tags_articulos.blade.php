<div>
    <div class="card">
        <div class="card-body col-12">
            <div class="row ">
                <div class="col-xs-12">
                    <div id="alert_message_type" class="collapse">
                    </div>
                </div>
                <div class="col-6 ">
                    <div class="form-row">
                        <div class="col-8 mb-2">
                            <label for="">Tags</label>
                            <select class="js-data-tags-ajax" style="width: 100%" name="tag_id" id="tag_id">
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-8 mb-2">
                            <label for="">Articulos</label>
                            <select class="js-data-articulo-ajax" style="width: 100%" name="articulo_id" id="articulo_id">
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-6 ">
                    <div class="form-row">
                        <div class="col-8">
                            <label for="">#Tag Principal</label>
                            <input type="hidden" class="form-control" id="tag_id">
                            <input type="text" class="form-control" id="tag_principal" disabled>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-8">
                            <label>Selecciona un Tag y asocia los Articulos, se guardaran al seleccionar un articulo</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <table class="table table-striped" id="tabla_asociada" style="width:100%">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Tags</th>
                        <th>Articulo Asociado</th>
                        <th width='120px'>Acciones</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
