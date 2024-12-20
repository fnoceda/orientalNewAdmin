<div id="modal_art_des_img" class="modal fade in" role="dialog">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <div class="form-row">
                    <div class="col-9">
                        <h4>Agregar Imagenes y Descripcion</h4>
                    </div>
                    <div class="col">
                        <button type="button" id="add_img_des" name="add_img_des" class="btn btn-primary">
                            <i class="fas fa-plus" type="button"></i>
                        </button>
                    </div>
                </div>
                <div class="col-xs-12">
                    <div id="alert_message_type" class="collapse">
                    </div>
                </div>
            </div>
            <form class="form-horizontal" role="form" id="form-art_des_img">
                <input type="hidden" id="art_des_id" name="art_des_id">
                <div class="modal-body" id="img_example">

                </div>
                <div class="modal-footer">
                    <button type="button" id="guardar_art_des_img" onclick="validarFormImg()" name="guardar_art_des_img" class="btn btn-primary">
                        <span class="fa fa-save"></span><span class="hidden-xs"> Guardar</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
