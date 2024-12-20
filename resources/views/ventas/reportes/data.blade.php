<div class="card shadow  col-12">
    <div class="card-header">
        <x-cabecera>
            <x-slot name='title'>
                Cuentas
            </x-slot>
            <x-slot name='subtitle'>
            </x-slot>
        </x-cabecera>
    </div>
    <div class="card-body">
        <div class="form-row">
            <div  id="responsive_table" class="col-12">
                <table class="table" id="tablaListado">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Fecha</th>
                            <th>Hora Inicio</th>
                            <th>Hora Fin</th>
                            <th>Cliente</th>
                            <th>Nro Factura</th>
                            <th>Estado</th>
                            <th>Total de la Cta</th>
                            <th>Total Cobrado</th>
                            <th>Descuento</th>
                            <th>Anticipo</th>
                            <th>Giftcard</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th colspan="8">Total</th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>