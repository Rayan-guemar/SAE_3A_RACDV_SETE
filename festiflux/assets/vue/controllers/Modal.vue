<script setup lang="ts">
import { VueElement } from 'vue';
import { Input, SubmitBtn } from '../../scripts/types';

interface Props {
    id: string;
    title: string;
}

const { id, title } = defineProps<Props>();

</script>

<template>
    <div :id="id" class="planning-form">
        <div>
            <div class="close-btn" @click="$emit('hideModal')">
                <img src="../../../public/icons/x-mark.png" alt="Fermer">
            </div>
        </div>
        <slot>

        </slot>
    </div>
</template>