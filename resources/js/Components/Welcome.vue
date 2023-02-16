<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
</script>

<template>
    <div>
        <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
            <div>
                <ApplicationLogo class="block h-12 w-auto" />
            </div>
        </div>

        <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-4">
            <div v-role="'admin'" class="p-6">
                <a title="Productos" :href="route('products.index')" class="w-full inline-block px-6 py-2 border-2 border-blue-600 text-blue-600 font-medium text-xs leading-tight uppercase rounded hover:bg-black hover:bg-opacity-5 focus:outline-none focus:ring-0 transition duration-150 ease-in-out">
                    Productos
                    <div class="flex flex-wrap justify-center">
                        <img
                            src="img/zapatillas.png"
                            alt="Productos"
                            style="width: 100px;"
                        />
                    </div>
                </a>
            </div>
            <div class="p-6">
                <a :href="route('pettycash.index')" type="button" class="w-full inline-block px-6 py-2 border-2 border-blue-600 text-blue-600 font-medium text-xs leading-tight uppercase rounded hover:bg-black hover:bg-opacity-5 focus:outline-none focus:ring-0 transition duration-150 ease-in-out">
                    Caja Chica
                    <div class="flex flex-wrap justify-center">
                        <img
                            src="img/cartera.png"
                            alt="Productos"
                            style="width: 100px;"
                        />
                    </div>
                </a>
            </div>
            <div v-role="'admin'" class="p-6">
                <a :href="route('clients.index')" class="w-full inline-block px-6 py-2 border-2 border-blue-600 text-blue-600 font-medium text-xs leading-tight uppercase rounded hover:bg-black hover:bg-opacity-5 focus:outline-none focus:ring-0 transition duration-150 ease-in-out">
                    Clientes
                    <div class="flex flex-wrap justify-center">
                        <img
                            src="img/clientes.png"
                            alt="Productos"
                            style="width: 100px;"
                        />
                    </div>
                </a>
            </div>
            <div v-role="'admin'" class="p-6">
                <a :href="route('providers.index')" class="w-full inline-block px-6 py-2 border-2 border-blue-600 text-blue-600 font-medium text-xs leading-tight uppercase rounded hover:bg-black hover:bg-opacity-5 focus:outline-none focus:ring-0 transition duration-150 ease-in-out">
                    Proveedores
                    <div class="flex flex-wrap justify-center">
                        <img
                            src="img/proveedor.png"
                            alt="Productos"
                            style="width: 100px;"
                        />
                    </div>
                </a>
            </div>
        </div>
        <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-4">
            <div v-can="'punto_ventas'" class="p-6">
                <a :href="route('sales.index')" class="w-full inline-block px-6 py-2 border-2 border-yellow-500 text-yellow-500 font-medium text-xs leading-tight uppercase rounded hover:bg-black hover:bg-opacity-5 focus:outline-none focus:ring-0 transition duration-150 ease-in-out">
                    Punto de Ventas
                    <div class="flex flex-wrap justify-center">
                        <img
                            src="img/caja-registradora.png"
                            alt="Productos"
                            style="width: 100px;"
                        />
                    </div>
                </a>
            </div>
            <div v-role="'admin'" class="p-6">
                <a :href="route('users.index')" type="button" class="w-full inline-block px-6 py-2 border-2 border-yellow-500 text-yellow-500 font-medium text-xs leading-tight uppercase rounded hover:bg-black hover:bg-opacity-5 focus:outline-none focus:ring-0 transition duration-150 ease-in-out">
                    Usuarios
                    <div class="flex flex-wrap justify-center">
                        <img
                            src="img/empleados.png"
                            alt="Productos"
                            style="width: 100px;"
                        />
                    </div>
                </a>
            </div>
            <div v-role="'admin'" class="p-6">
                <a :href="route('establishments.index')" type="button" class="w-full inline-block px-6 py-2 border-2 border-yellow-500 text-yellow-500 font-medium text-xs leading-tight uppercase rounded hover:bg-black hover:bg-opacity-5 focus:outline-none focus:ring-0 transition duration-150 ease-in-out">
                    Tiendas
                    <div class="flex flex-wrap justify-center">
                        <img
                            src="img/tienda.png"
                            alt="Productos"
                            style="width: 100px;"
                        />
                    </div>
                </a>
            </div>
            <div class="p-6">

                <a :href="route('reports.index')"  type="button" class="w-full inline-block px-6 py-2 border-2 border-yellow-500 text-yellow-500 font-medium text-xs leading-tight uppercase rounded hover:bg-black hover:bg-opacity-5 focus:outline-none focus:ring-0 transition duration-150 ease-in-out">
                    Reportes
                    <div class="flex flex-wrap justify-center">
                        <img
                            src="img/reporte.png"
                            alt="Productos"
                            style="width: 100px;"
                        />
                    </div>

                </a>
            </div>
        </div>
    </div>
</template>
