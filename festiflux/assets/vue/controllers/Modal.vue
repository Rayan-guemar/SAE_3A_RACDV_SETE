<script setup lang="ts"> 
const modalContainer = document.getElementById('modal-container') as HTMLDivElement | null;
if (!modalContainer) document.body.insertAdjacentHTML('afterbegin', '<div id="modal-container"></div>');
</script>

<template>
        <Teleport to="#modal-container">
            <slot></slot>
            <div class="modal-background" @click="$emit('close')"></div>
        </Teleport>
</template>