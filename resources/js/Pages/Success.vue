<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';
import GuestLayout from "@/Layouts/GuestLayout.vue";
</script>

<template>
    <Head title="Dashboard"/>

    <GuestLayout>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg text-center">


                    <div class="w-12 h-12 rounded-full bg-green-100 dark:bg-green-900 p-2 flex items-center justify-center mx-auto mb-3.5">
                        <svg aria-hidden="true" class="w-8 h-8 text-green-500 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                        <span class="sr-only">Success</span>
                    </div>
                    <p class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Minting Sucessful, check HashPack for NFT!</p>



                </div>
            </div>
        </div>
    </GuestLayout>
</template>
