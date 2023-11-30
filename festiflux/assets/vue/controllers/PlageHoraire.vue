<script setup lang="ts">
    import { ref } from 'vue';
    import { encodedStr, getDateHours2Digits, hashCode } from '../../scripts/utils';
    import { Tache, Creneau, Poste } from '../../scripts/types';
    
    interface Props {
        creneau: Creneau
    }
    const {creneau} = defineProps<Props>()


    const ajoutIndispo = ref(false);
    const plage = ref<HTMLDivElement>();
    const addIndispo = () => {
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
    let debut = new Date(creneau.debut);
    let fin = new Date(creneau.fin);

</script>
<template>
    <div ref="plage" class="plage" :id="creneau.id + ''" :style="{
      top: `${((debut.getHours() * 60 + debut.getMinutes()) / (24 * 60)) * 100}%`,
      height: `${( ( (fin.getHours() * 60 + fin.getMinutes()) - (debut.getHours() * 60 + debut.getMinutes()) ) / (24 * 60) ) * 100}%`,
      width:  `100%`,
      left: `0%`,
      transform: `translateX(0%)`,
      backgroundColor: `rgb(216, 230, 243)`,
      zIndex: ajoutIndispo ? 1000 : -10,
    }" @click="(e) => addIndispo()" >
    </div>
</template>