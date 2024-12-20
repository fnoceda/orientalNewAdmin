<div class="card shadow mb-3 col-md-12">
    <div class="card-header">
        <x-cabecera>
            <x-slot name='title'>
                Filtros
            </x-slot>
            <x-slot name='subtitle'>
            </x-slot>
        </x-cabecera>
    </div>
    <div class="card-body"> 
        <div class="form-row">
            <div class="col-2">
                <table class="table table-bordered" id="tablaCrudResumen">
                    <thead >
                        <tr>
                            <th colspan="2" style="background: gray">Resumen</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th>Total Ctas.</th>
                            <td id="total_cuenta"></td>
                        </tr>
                        <tr>
                            <th>Total Cobrado.</th>
                            <td id="total_cobrado">0</td>
                        </tr>
                        <tr>
                            <th>Total Descuentos.</th>
                            <td id="total_descuento">0</td>
                        </tr>
                        <tr>
                            <th>Total Facturado.</th>
                            <td id="total_facturado">0</td>
                        </tr>
                        <tr>
                            <th>Total Cerrados.</th>
                            <td id="total_cerrados">0</td>
                        </tr>
                        <tr>
                            <th>Total Pendientes.</th>
                            <td id="total_pendientes">0</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>