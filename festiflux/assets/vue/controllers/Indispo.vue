<script setup lang="ts">
    import { ref } from 'vue';
    import { encodedStr, getDateHours2Digits, hashCode } from '../../scripts/utils';
    import { Tache, Creneau, Poste } from '../../scripts/types';
    
    interface Props {
        creneau: Creneau
    }
    const {creneau} = defineProps<Props>()


    const ajoutIndispo = ref(false);
    const indispo = ref<HTMLDivElement>();
    
    let debut = new Date(creneau.debut);
    let fin = new Date(creneau.fin);

</script>
<template>
    <div ref="indispo" class="indispo" :id="creneau.id + ''" :style="{
      top: `${((debut.getHours() * 60 + debut.getMinutes()) / (24 * 60)) * 100}%`,
      height: `${( ( (fin.getHours() * 60 + fin.getMinutes()) - (debut.getHours() * 60 + debut.getMinutes()) ) / (24 * 60) ) * 100}%`,
      width:  `99.9%`,
      left: `0%`,
      transform: `translateX(0%)`,
      zIndex: ajoutIndispo ? 1000 : -10,
    }" >
    {{ `${getDateHours2Digits(debut)} - ${getDateHours2Digits(fin)}` }}
    </div>
</template>
