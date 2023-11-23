<script setup lang="ts">
import { VueElement } from 'vue';
import { Input, SubmitBtn } from '../../scripts/types';

interface Props {
    id: string;
    title: string;
    element: VueElement;
    hideModal: () => void;
}

const { id, title, element, hideModal } = defineProps<Props>();

</script>

<template>
    <div :id="id" class="planning-form">
        <div>
            <div class="close-btn" @click="hideModal">
                <img src="../../../public/icons/x-mark.png" alt="Fermer">
            </div>
        </div>

        {{ element }}
    </div>
</template>