<script setup lang="ts">
import { Ref } from 'vue';
import { Input, Attribute, SubmitBtn } from '../../scripts/types';

interface Props {
    id: string;
    title: string;
    inputs: Input[];
    submitBtn: SubmitBtn;
    active: Ref<boolean>;
}

const { id, title, inputs, submitBtn, active } = defineProps<Props>();

function close() {
    active.value = false;
}

</script>

<template>
    <div :id="id" class="planning-form">
        <div>
            <div class="close-btn">
                <img src="../../../public/icons/x-mark.png" alt="Fermer">
            </div>
        </div>

        <h2>{{ title }}</h2>

        <div v-for="input in inputs" class="flex-column flex-align-center">
            <label v-if="!!input.label">{{ input.label }}</label>
            <div :type="input.type" :value="input.value" :id="input.id" :name="input.name"></div>
        </div>

        <div :id="submitBtn.id" class="btn">
            {{ submitBtn.text }}
        </div>
    </div>
</template>