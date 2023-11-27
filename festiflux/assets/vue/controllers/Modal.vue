<script setup lang="ts"> 
const modalContainer = document.getElementById('modal-container') as HTMLDivElement | null;
if (!modalContainer) document.body.insertAdjacentHTML('beforeend', '<div id="modal-container"></div>');
</script>

<template>
        <Teleport to="#modal-container">
            <slot></slot>
        </Teleport>
</template>