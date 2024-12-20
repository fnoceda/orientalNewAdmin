
<div class="botonera">
    <div style="float: left; padding: 10px 0px 10px 0px;">
    </div> 
</div>

<table class="table dataTable tablaExistencia table-striped table-bordered" id="clientesTable">
    <thead>
    <tr>
        <th scope="col">CODIGO</th>
        <th scope="col">EMAIL</th>
        <th scope="col">NOMBRE</th>
        <th scope="col">RUC</th>
        <th scope="col">PERFIL</th>
        <th scope="col">DIRECCION</th>
        
    </tr>
    </thead>
    <tbody id="cuerpoExistencia">
    @foreach($usuarios as $p)
        <tr 
onclick="abrirModalEditarCliente('{{ $p->id }}','{{ $p->name }}','{{ $p->email }}','{{ $p->ruc}}','{{ $p->telefono}}','{{ $p->direccion}}','{{ $p->ciudad_id}}','{{ $p->direccion_delivery}}','{{ $p->latitud}}','{{ $p->longitud}}','{{ $p->perfil}}',{{ $p->perfil_id}},{{ $p->empresa_id}})"
        >
            <td>{{ $p->id}}</td>
            <td>{{ $p->email}}</td>
            <td>{{ $p->name}}</td>
            <td>{{ $p->ruc}}</td>
            <td>{{ $p->perfil}}</td>
            <td>{{ $p->direccion}}</td>            
        </tr>
    @endforeach
    </tbody>
</table>
<div id="export_div" style="visibility:hidden; height:0;"></div>

