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
            <div class="pointer-events-auto w-[25.625rem] rounded-lg bg-white text-[0.8125rem] leading-5 text-slate-700 shadow-xl shadow-black/5 ring-1 ring-slate-700/10">
                <div class="flex items-center py-2.5 px-3.5 text-slate-400">
                    <svg class="mr-2 h-5 w-5 stroke-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z">

                        </path>
                    </svg>Search projects...
                </div>
                <div class="border-t border-slate-400/20 py-3 px-3.5">
                    <div class="mb-1.5 text-[0.6875rem] font-semibold text-slate-500">Recent searches</div>
                    <div class="flex items-center rounded-md p-1.5">
                        <svg class="mr-2.5 h-5 w-5 flex-none stroke-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                        </svg>Tailwind Labs / Website Redesign
                    </div>
                    <div class="flex items-center rounded-md p-1.5">
                        <svg class="mr-2.5 h-5 w-5 flex-none stroke-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                        </svg>Laravel LLC / Conference Branding
                    </div>
                </div>
                <div class="border-t border-slate-400/20 py-3 px-3.5">
                    <div class="flex items-center rounded-md p-1.5 bg-indigo-600 text-white">
                        <svg class="mr-2.5 h-5 w-5 flex-none stroke-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>Add new file...
                    </div>
                    <div class="flex items-center rounded-md p-1.5">
                        <svg class="mr-2.5 h-5 w-5 flex-none stroke-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                        </svg>Add new folder...
                    </div>
                    <div class="flex items-center rounded-md p-1.5">
                        <svg class="mr-2.5 h-5 w-5 flex-none stroke-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                        </svg>Add hashtag...
                    </div>
                    <div class="flex items-center rounded-md p-1.5">
                        <svg class="mr-2.5 h-5 w-5 flex-none stroke-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>Add label...
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>