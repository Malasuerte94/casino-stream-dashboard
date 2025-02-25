<template>
    <div class="flex flex-col sm:justify-center items-center pt-6 sm:pt-0 w-full">
        <div>
            <slot name="logo" />
        </div>
        <div class="rounded-lg backdrop-blur-xl p-4 bg-white/30 shadow-lg shadow-black/80 border border-white/20 w-full">
            <slot />
        </div>
    </div>
</template>
