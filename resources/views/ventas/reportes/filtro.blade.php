<div class="card shadow mb-3 col-md-12">
    <div class="card-header">
        <h3>Filtros Disponibles</h3>
    </div>
    <div class="card-body">
        <form>
            <div class="form-row mb-1">
                <div class="col-sm-1" style="display: none; float: right;" id="esperando">
                    <div class="loader"></div>
                </div>
            </div>
            <div class="form-row mb-1">
                <div class="col-2">
                    <label for="">Cliente</label>
                </div>                
                <div class="col-2">
                    <label for="">Fecha Desde:</label>
                </div>
                <div class="col-2">
                    <label for="">Fecha Hasta:</label>
                </div>
                <div class="col-2">
                    <label for="">Estado:</label>
                </div>
            </div>
            <div class="form-row mb-1">
                <div class="col-2">
                    <select name="cliente_id" id="cliente_id" class="form-control select2" style="width: 100%" required>
                        <option value="">Todos</option>
                            @foreach ($clientes as $cli)
                            <option value="{{ $cli->id }}">{{ $cli->name }}</option>
                            @endforeach
                    </select>
                </div>
                <div class="col-2">
                    <input autocomplete="off" type="date" class="form-control form-control-sm datepicker" id="fecha_desde" value="{{date("Y-m-01")}}" name="fecha_desde" >
                </div>
                <div class="col-2">
                    <input autocomplete="off" type="date" class="form-control form-control-sm datepicker" id="fecha_hasta" value="{{date("Y-m-30")}}" name="fecha_hasta" >
                </div>
                <div class="col-2">
                    <select name="estado" id="estado" class="form-control " style="width: 100%" required>
                        <option value="">Todos</option>
                        <option value="pendiente">Pendiente</option>
                        <option value="entregado">Entregado</option>
                        <option value="cancelado">Cancelado</option>
                    </select>
                </div>
                <div class="col-2">
                    <button type="button" class="btn btn-primary" onclick="filtrar()">Filtrar</button>
                </div>
            </div>
        </form>
    </div>
</div>