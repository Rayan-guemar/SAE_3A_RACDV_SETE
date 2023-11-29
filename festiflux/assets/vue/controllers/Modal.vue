<script setup lang="ts"> 
defineEmits<{
    (event:'close'): void
}>();
const modalContainer = document.getElementById('modal-portal') as HTMLDivElement | null;
if (!modalContainer) document.body.insertAdjacentHTML('afterbegin', '<div id="modal-portal"></div>');
</script>

<template>
        <Teleport to="#modal-portal">
            <div class="modal-container">
                <slot></slot>
            </div>
            <div class="modal-background" @click="$emit('close')"></div>
        </Teleport>
</template>