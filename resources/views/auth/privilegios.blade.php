<div class="box">
    <div class="box-body no-padding">
        <table class="table table-striped">
            <tbody>
                @foreach ($foo['menus'] as $menu)
                    <tr>
                        <th colspan="2" class="bg bg-secondary" style="color: white">{{ $menu->name }}</th>
                    </tr>
                    @foreach ($foo['sub_menus'] as $subMenu)
                        @if ($subMenu->padre == $menu->id)
                            <tr>
                                <td>{{ $subMenu->name }}</td>
                                <th>
                                    <input type="checkbox" class="opcion" id="op_{{ $foo['perfil'] }}_{{ $subMenu->id }}"
                                        name="op_{{ $foo['perfil'] }}_{{ $menu->id }}">
                                </th>
                            </tr>
                        @endif
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
    $(document).ready(function() {
        @php
            foreach ($foo['permisos'] as $permiso) {
                echo "$('#" . $permiso->opcion . "').attr('checked', true); ";
            }
        @endphp

        $('.opcion').change(function() {
            var ruta = '';
            var metodo = '';
            metodo = ($(this).prop('checked')) ? 'put' : 'delete';
            //alert(metodo); 

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: "{{ url('/privilegios') }}",
                method: metodo,
                data: {
                    opcion: $(this).attr('id')
                },
                success: function(result) {
                    console.log('Permiso Concedido/Revocado Exitosamente');
                    console.log(result);
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    console.log('Error al intentar Conceder/Revocar Permiso');
                    console.log(xhr.responseText);
                }
            });
        });
    });
</script>
