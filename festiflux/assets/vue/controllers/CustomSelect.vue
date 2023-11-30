<script setup lang="ts">
    import { ref } from 'vue';
    import { SelectOption } from '../../scripts/types'

    interface Props {
        options: SelectOption[]
        selected: string
    }

    const {options, selected} = defineProps<Props>()
    const emits = defineEmits(['select'])

    const showOptions = ref(false)

    const select = (option: string | number) => {
        emits('select', option)
    }

    const customSelect = ref<HTMLDivElement>()

    window.addEventListener('click', (e) => {
        if (customSelect.value && !customSelect.value.contains(e.target as Node)) {
            showOptions.value = false
        }
    })
</script>

<template>
    <div ref="customSelect" class="custom-select" @click="() => {showOptions = !showOptions}" :class="{show: showOptions}">
        <div class="selected">{{ options.find(v  => v.value === selected)?.label }}</div>
        <div class="options" v-if="showOptions" :class="{show: showOptions}">
            <div v-for="option of options" @click="() => select(option.value)">
                {{ option.label }}
            </div>
        </div>
    </div>
</template>

<style lang="scss">
    .custom-select {
        height: fit-content;
        width: fit-content;
        position: relative;

        .options {
            opacity: 0;
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1;
            width: max-content;
            background-color: white;
            box-shadow: 0 2px 4px 0 rgba(0,0,0,0.2);
            transition: all 1s;

            &.show {
                opacity: 1;
            }

            & > div {
                padding: 5px;
                cursor: pointer;
        
                &:hover {
                    background-color: #eee;
                }
            }
        }

        .selected {
            cursor: pointer;
            width: 100%;
            height: 100%;
        }
    }
</style>