<!-- REQUIRED JS SCRIPTS -->

<!-- JQuery and bootstrap are required by Laravel 5.3 in resources/assets/js/bootstrap.js-->
<!-- Laravel App
<script src="{{ asset('/js/app.js') }}" type="text/javascript"></script>
-->
<!-- Optionally, you can add Slimscroll and FastClick plugins.
      Both of these plugins are recommended to enhance the
      user experience. Slimscroll is required when using the
      fixed layout. -->
<script>
    window.Laravel = {!! json_encode([
        'csrfToken' => csrf_token(),
    ]) !!};


    $(document).ready(function () {
    // Detectar clic en el botón del dropdown
    $('#userDropdown').on('click', function (e) {
        e.preventDefault(); // Evitar la acción predeterminada del enlace
        
        // Alternar el menú desplegable
        $(this).dropdown('toggle');
    });

    // Cerrar el menú si se hace clic fuera del dropdown
    $(document).on('click', function (e) {
        var target = $(e.target);
        if (!target.closest('#userDropdown').length && !target.closest('.dropdown-menu').length) {
            $('.dropdown-menu').removeClass('show'); // Cierra el menú si está abierto
        }
    });
});

</script>
