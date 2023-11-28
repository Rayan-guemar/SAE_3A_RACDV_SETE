<script setup lang="ts">
    import { ref } from 'vue';
    import { encodedStr, getDateHours2Digits, hashCode } from '../../scripts/utils';
    import { Tache, Creneau, Poste } from '../../scripts/types';
    import InfoTache from './InfoTache.vue';
    interface Props {
        creneau: Creneau
    }
    const {creneau} = defineProps<Props>()


    const ajoutIndispo = ref(false);
    const plage = ref<HTMLDivElement>();
    const addPlage = () => {
      ajoutIndispo.value = true;
    }
    window.addEventListener('click', (e) => {
        if (ajoutIndispo.value) {
            if (plage.value && plage.value.contains(e.target as Node)) {
              ajoutIndispo.value = true;
            } else {
              ajoutIndispo.value = false;
            }
        }
    });
</script>
<template>
    <div ref="plage" class="plage" :id="''+id" :style="{
      top: `${((creneau.debut.getHours() * 60 + creneau.debut.getMinutes()) / (24 * 60)) * 100}%`,
      height: `${((creneau.fin.getHours() * 60 + creneau.fin.getMinutes() - (creneau.debut.getHours() * 60 + creneau.debut.getMinutes())) / (24 * 60)) * 100}%`,
      width: `100%`,
      margin: `0 2px`,
      left: `${(100)}%`,
      transform: `translateX(0%)`,
      backgroundColor: `rgb(216, 230, 243)`,
      zIndex: ajoutIndispo ? 1000 : -10,
    }" @click="(e) => addPlage()" >
<!--        <div class="name">{{ encodedStr(poste.nom) }}</div>-->
<!--        <div class="tache">-->
<!--            {{ encodedStr(`${getDateHours2Digits(creneau.debut)} - ${getDateHours2Digits(creneau.fin)}`) }}-->
<!--        </div>-->
<!--        <InfoTache v-if="ajoutIndispo" :tache="tache" />-->
    </div>
</template>