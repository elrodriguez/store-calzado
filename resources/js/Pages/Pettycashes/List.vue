<script setup>
    import AppLayout from '@/Layouts/AppLayout.vue';
    import { useForm } from '@inertiajs/vue3';
    import { faTrashAlt, faPencilAlt, faPrint, faCashRegister, faFileExcel} from "@fortawesome/free-solid-svg-icons";
    import Pagination from '@/Components/Pagination.vue'
    import ModalCashCreate from './ModalCashCreate.vue';

    const props = defineProps({
        pettycashes: {
            type: Object,
            default: () => ({}),
        },
        filters: {
            type: Object,
            default: () => ({}),
        },
        locals: {
            type: Object,
            default: () => ({}),
        }
    });
    const form = useForm({
        search: {
            date_start: props.filters.date_start,
            date_end: props.filters.date_end
        },
    });

    const formDelete = useForm({});

    function destroy(id) {
        if (confirm("¿Estás seguro de que quieres eliminar?")) {
            formDelete.delete(route('pettycash.destroy', id));
        }
    }

    function closePettyCash(id, state){
if(state){
    alert("Esta caja ya se encuentra Cerrada");
}else{
    if (confirm("¿Estás seguro de que quieres Cerrar la Caja?")) {
            axios.post(route('close_petty_cash', {id}))
        .then(response => {
            location.reload();
        })
        .catch(error => console.log(error));
        }
}
    }

    function reportPettyCash(id){
        alert("esta en proceso el reporte "+id);
    }

</script>

<template>
    <AppLayout title="Create Team">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Caja Chica
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <div class="col-span-6 p-4 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <form @submit.prevent="form.get(route('pettycash.index'))">
                        <div class="grid grid-cols-4 gap-3 py-2">
                            <div>
                                <input
                                    type="text"
                                    class="form-control block w-full px-3 py-1.5 text-base font-normal text-gray-700 bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none"
                                    id="f1"
                                    placeholder="Fecha desde"
                                    v-mask="'##/##/####'"
                                />
                            </div>
                            <div>
                                <input
                                    type="text"
                                    class="form-control block w-full px-3 py-1.5 text-base font-normal text-gray-700 bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none"
                                    id="f2"
                                    placeholder="Fecha hasta"
                                    v-mask="'##/##/####'"
                                />
                            </div>
                            <div class="">
                                <button type='submit' class='inline-block px-6 py-2.5 bg-gray-800 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-gray-900 hover:shadow-lg focus:bg-gray-900 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-gray-900 active:shadow-lg transition duration-150 ease-in-out'>
                                    Buscar
                                </button>
                            </div>
                            <div class="text-right">
                                <ModalCashCreate :locals="locals" />
                            </div>
                        </div>
                    </form>

                    <table class="border mb-4" style="width: 100%;">
                        <thead class="border-b">
                            <tr>
                                <th scope="col" class="w-2.5 text-sm font-medium text-gray-900 px-6 py-4 border-r">
                                #
                                </th>
                                <th scope="col" class="w-4 text-sm font-medium text-gray-900 px-6 py-4 border-r">
                                    Acción
                                </th>
                                <th scope="col" class="w-4 text-sm font-medium text-gray-900 px-6 py-4 border-r">
                                    Usuario
                                </th>
                                <th scope="col" class="w-4 text-sm font-medium text-gray-900 px-6 py-4 border-r">
                                    Fecha Apertura
                                </th>
                                <th scope="col" class="w-4 text-sm font-medium text-gray-900 px-6 py-4 border-r">
                                    Fecha Cerrado
                                </th>
                                <th scope="col" class="text-left text-sm font-medium text-gray-900 px-6 py-4 border-r">
                                    Monto en Caja
                                </th>
                                <th scope="col" class="text-left text-sm font-medium text-gray-900 px-6 py-4">
                                    Ingreso por ventas
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(pettycash, index) in pettycashes.data" :key="pettycash.id" class="border-b">
                                <td class="text-center px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-r">
                                    {{ index + 1 }}
                                </td>
                                <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap border-r">
                                    <div class="flex space-x-2 justify-center">
                                        <div>
                                            <!-- <a :href="route('products.edit',pettycash.id)" class="mr-1 inline-block px-6 py-2.5 bg-green-500 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-green-600 hover:shadow-lg focus:bg-green-600 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-700 active:shadow-lg transition duration-150 ease-in-out">
                                                <font-awesome-icon :icon="faPencilAlt" />
                                            </a> -->
                                            <button type="button" class="inline-block px-6 py-2.5 bg-red-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-red-700 hover:shadow-lg focus:bg-red-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-red-800 active:shadow-lg transition duration-150 ease-in-out"
                                            @click="destroy(pettycash.id)"
                                            >
                                            <font-awesome-icon :icon="faTrashAlt" />
                                        </button>
                                        </div>

                                        <div>
                                            <!-- <a :href="route('products.edit',pettycash.id)" class="mr-1 inline-block px-6 py-2.5 bg-green-500 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-green-600 hover:shadow-lg focus:bg-green-600 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-700 active:shadow-lg transition duration-150 ease-in-out">
                                                <font-awesome-icon :icon="faPencilAlt" />
                                            </a> -->
                                            <button type="button" class="inline-block px-6 py-2.5 bg-blue-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-blue-700 hover:shadow-lg focus:bg-blue-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-blue-800 active:shadow-lg transition duration-150 ease-in-out"
                                            @click="closePettyCash(pettycash.id, pettycash.state)"
                                            title="Cerrar Caja"
                                            >
                                            <font-awesome-icon :icon="faCashRegister" />
                                        </button>
                                        </div>

                                        <div>
                                            <!-- <a :href="route('products.edit',pettycash.id)" class="mr-1 inline-block px-6 py-2.5 bg-green-500 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-green-600 hover:shadow-lg focus:bg-green-600 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-700 active:shadow-lg transition duration-150 ease-in-out">
                                                <font-awesome-icon :icon="faPencilAlt" />
                                            </a> -->
                                            <button type="button" class="inline-block px-6 py-2.5 bg-green-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-green-700 hover:shadow-lg focus:bg-green-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-800 active:shadow-lg transition duration-150 ease-in-out"
                                            @click="reportPettyCash(pettycash.id)"
                                            title="Reporte"
                                            >
                                            <font-awesome-icon :icon="faFileExcel" />
                                        </button>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap border-r">
                                    {{ pettycash.name_user }}
                                </td>
                                <td class="text-center text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap border-r">
                                    {{ pettycash.date_opening }} | {{ pettycash.time_opening.slice(0, -3) }} hrs
                                </td>
                                <td class="text-center text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap border-r">
                                    {{ pettycash.date_closed==null? "Abierto" : pettycash.date_closed+" | "+pettycash.time_closed.slice(0, -3)+" hrs"}}
                                </td>
                                <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap border-r">
                                    {{ pettycash.final_balance }}
                                </td>
                                <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
                                    {{ pettycash.income }}
                                </td>
                            </tr>

                        </tbody>
                    </table>
                    <Pagination :data="pettycashes" />
                </div>

            </div>
        </div>
    </AppLayout>
</template>
