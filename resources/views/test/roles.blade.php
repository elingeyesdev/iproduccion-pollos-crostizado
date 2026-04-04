<x-app-layout>
    <div class="container">
        <h1>Prueba de roles-Spatie</h1>
        @role('administrador')
            <p style="color: red; font-weight: bold;">
                Hola, eres un administrador.</p>
        @endrole

        @role('venta')
            <p style="color: blue; font-weight: bold;">
                Hola, eres un usuario de venta.</p>  
        @endrole
        @hasanyrole('administrador|venta')
            <p style="color: green; font-weight: bold;">
                Hola, tienes al menos uno de los roles: administrador o venta.</p>
        @endhasanyrole

        @unlessrole('administrador')
            <p style="color: orange; font-weight: bold;">
                Hola, no eres un administrador.</p>
        @endunlessrole
    </div>
></x-app-layout>