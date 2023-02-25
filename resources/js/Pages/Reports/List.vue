<script setup>
    import AppLayout from '@/Layouts/AppLayout.vue';
</script>
<template>
    <AppLayout title="Reportes">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Reportes
            </h2>
        </template>
        <div class="relative p-4">
            <div class="grid grid-cols-4 gap-4">
                <div class="col-span-4 sm:col-span-1">
                    <div class="max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
                        <h2 class="mb-2 text-lg font-semibold text-gray-900 dark:text-white">Ventas</h2>
                        <ul class="max-w-md space-y-1 text-gray-500 list-disc list-inside dark:text-gray-400">
                            <li>
                                <a :href="route('sale_report')">Reporte de ventas entre fechas</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-span-4 sm:col-span-1">
                    <div class="max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
                        <h2 class="mb-2 text-lg font-semibold text-gray-900 dark:text-white">Inventario</h2>
                        <ul class="max-w-md space-y-1 text-gray-500 list-disc list-inside dark:text-gray-400">
                            <li>
                                <a :href="route('inventory_report')" target="_blank">Reporte de todos los productos(todas los locales)</a>
                            </li>

                            <li>
                                <a :href="route('inventory_report_by_local', 1)" target="_blank">Reporte de productos(Local: Principal con id 1)</a>
                            </li>

                            <li>
                                <a :href="route('inventory_report_by_local', 2)" target="_blank">Reporte de productos(Local: algun lado con id 2)</a>
                            </li>

                            <li>
                                <a :href="route('inventory_report_by_local', 3)" target="_blank">Reporte de productos(Local: Principal con id 3), falta hacer un for o foreach</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
