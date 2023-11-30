<script setup lang="ts">

import {onMounted, ref} from 'vue';
import {Benevole, Festival} from '../../scripts/types';
import {Backend} from "../../scripts/Backend";

interface Props {
  festID: number;
  benevole: Benevole
  affected: boolean
  charge: number
}

const {benevole, affected, charge, festID} = defineProps<Props>();

let showChargeLine = ref(false);
let chargeHeures = ref(0);

const getCharge = async () => {
  const res = await Backend.getCharge(festID, benevole.id);
  if (res) {
    chargeHeures.value = res;
  }
  return res;
}

onMounted(async () => {
  await getCharge();
})

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