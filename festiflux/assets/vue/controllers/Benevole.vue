<script setup lang="ts">

import {ref} from 'vue';
import {Benevole, Festival, Tache} from '../../scripts/types';
import { calculCharge } from '../../scripts/utils';


interface Props {
  AllTachesBenevole : Tache[]
  benevole: Benevole
  affected: boolean
}

const {benevole, affected, AllTachesBenevole} = defineProps<Props>();

let showChargeLine = ref(false);
let chargeHeures = ref(0);
chargeHeures.value = calculCharge(AllTachesBenevole);


</script>

<template>
  <div class="benevole-element" @mouseover="()=>{(!showChargeLine)?showChargeLine=true:showChargeLine=false}" @mouseleave="()=>{(showChargeLine)?showChargeLine=false:showChargeLine=true}">
    <div class="benevole-name">{{ benevole.nom }}</div>

    <div v-if="!affected" class="btn affect" @click="() => $emit('addBenevole')">
      Affecter
    </div>
    <div v-else class="btn unaffect" @click="() => $emit('removeBenevole')">
      Désaffecter
    </div>
    <!-- Ajout de la ligne de charge -->
    <div v-if="showChargeLine" class="charge-line">
      La charge de ce bénévole est de {{ chargeHeures }} heures.
    </div>
  </div>
</template>