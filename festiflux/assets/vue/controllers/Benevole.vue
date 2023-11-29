<script setup lang="ts">
    import { ref } from 'vue';
    import { Benevole } from '../../scripts/types';

    interface Props {
        benevole: Benevole
        affected: boolean
    }

    const { benevole, affected } = defineProps<Props>();
</script>

<template>
    <div class="benevole-element">
        <div class="benevole-name">{{ benevole.nom }}</div>

        <div v-if="!affected" class="btn affect" @click="() => $emit('addBenevole')">
            Affecter
        </div>
        <div v-else class="btn unaffect" @click="() => $emit('removeBenevole')">
            Désaffecter
        </div>
    </div>
</template>