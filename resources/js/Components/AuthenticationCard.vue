<template>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
        <div>
            <slot name="logo" />
        </div>
        <div class="bg-gray-800 p-6 rounded border border-gray-700 shadow">
            <slot />
        </div>
    </div>
</template>
