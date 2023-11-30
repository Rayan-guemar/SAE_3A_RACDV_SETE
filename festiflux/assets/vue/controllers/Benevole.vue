<script setup lang="ts">
    import { computed, ref } from 'vue';
    import { Benevole, Poste } from '../../scripts/types';
    import { hexToBrighterHex } from '../../scripts/utils';

    interface Props {
        benevole: Benevole
        affected: boolean
        poste: Poste
    }

    const props = defineProps<Props>();
    const pref = computed(() => {
        const degree = props.benevole.preferences.find(p => p.poste == props.poste.id + "")?.degree || 0;
        return degree
    })

    const colors = [
        '#C80000',
        '#3b3a39',
        '#00C800'
    ]

    // const color = ref('');
</script>

<template>
    <div class="benevole-element">
        <!-- <input style="position: absolute;" type="color" v-model="color"> -->
        <div class="benevole-name">{{ benevole.nom }}</div>
        <div v-if="pref === 1 || pref == -1" class="pref-pastille" :style="{
                backgroundColor: hexToBrighterHex(colors[pref + 1], 0.2),
                color: colors[pref + 1],
                outlineColor: colors[pref + 1]
            }"
        >
            {{ pref === 1 ? 'Aime' : 'N\'aime pas' }}
        </div>
        <div v-if="!affected" class="btn affect" @click="() => $emit('addBenevole')">
            Affecter
        </div>
        <div v-else class="btn unaffect" @click="() => $emit('removeBenevole')">
            Désaffecter
        </div>
    </div>
</template>