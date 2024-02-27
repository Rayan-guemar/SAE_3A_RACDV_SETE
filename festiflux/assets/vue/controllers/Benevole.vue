<script setup lang="ts">

import { computed,ref} from 'vue';
import {Benevole, Poste, Festival, Tache} from '../../scripts/types';
import { getColorHexByRatio, hexToBrighterHex } from '../../scripts/utils';


interface Props {
    charge : number;
    benevole: Benevole;
    affected: boolean;
    poste: Poste;
    lang: string;
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

function translate(key: string) {
  if (props.lang === 'fr') {
    switch (key) {
      case 'affect':
        return 'Affecter';
      case 'unaffect':
        return "Désaffecter";
      case 'love':
        return "j'aime";
      case 'hate':
        return "je n'aime pas";
    }
  } else {
    switch (key) {
      case 'affect':
        return 'Affect';
      case 'unaffect':
        return "Unaffect";
      case 'love':
        return "love";
      case 'hate':
        return "hate";
    }
  }
}

</script>

<template>
    <div class="benevole-element drag-el" draggable="true">
        <!-- <input style="position: absolute;" type="color" v-model="color"> -->
        <div class="benevole-name">{{ benevole.nom }}</div>
        <div class="pastille-benevole-wrapper">
            <div 
                class="pastille-benevole charge" 
                :style="{
                    backgroundColor: hexToBrighterHex(getColorHexByRatio(charge/30), 0.2),
                    color: 'black',
                    outlineColor: getColorHexByRatio(charge/30)
                }"
            >
                {{ charge }}h
            </div>
            <div 
                v-if="pref === 1 || pref == -1" 
                class="pastille-benevole"
                :class="{
                    'pref-like': pref === 1,
                    'pref-dislike': pref === -1
                }"
            >
                {{ pref === 1 ? translate("love") : translate("hate") }}
            </div>
        </div>
        <div v-if="!affected" class="btn affect" @click="() => $emit('addBenevole')">
          {{ translate("affect")}}
        </div>
        <div v-else class="btn unaffect" @click="() => $emit('removeBenevole')">
          {{ translate("unaffect") }}
        </div>
    </div>
</template>