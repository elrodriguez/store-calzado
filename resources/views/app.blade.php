<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @routes
        @php
            $parts = explode('::',$page['component']);
        @endphp
        @if(count($parts)>1)
            @vite(['resources/js/app.js', "Modules/{$parts[0]}/Resources/assets/js/Pages/{$parts[1]}.vue"])
        @else
            @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @endif
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
    <script>
        function generalFunctionTo(data, route, to) {
            
            if (to === 'destroy') {
                swal({
                    title: '¿Estas seguro?',
                    text: "¡No podrás revertir esto!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        swal({
                            title: 'Cargando...',
                            text: 'Por favor espera',
                            buttons: false,
                            closeOnEsc: false,
                            closeOnClickOutside: false,
                        });
                        const xhr = new XMLHttpRequest();
                        xhr.open('GET', route, true);
                        xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');

                        xhr.onload = function() {
                            if (xhr.status === 200) {
                                swal('Se elimino correctamente.');
                            } else {
                                swal('Error.');
                            }
                            swal.close();
                        };

                        xhr.onerror = function() {
                            swal('Error.');
                        };

                        xhr.send();
                    }
                });
            }
        }
    </script>
</html>
