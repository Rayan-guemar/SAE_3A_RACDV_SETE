<script setup lang="ts" >
import { Tache } from '../../scripts/types';

interface Props {
   tache : Tache
}

defineProps<Props>();

</script>


<template>
    <div class="tache-info_wrapper" >
        <div class="tache-info_nom"  >{{ tache.poste.nom }}</div>
        <div class="tache-info_desc" >{{ tache.description }}</div>
        <div class="tache-info_lieu " >{{ tache.lieu }}</div>
        <div class="tache-info_benevoles" >{{ tache.nbBenevole }} bénévoles requis</div>
        <div class="tache-info_benevoles" >{{ tache.benevoleAffecte }} bénévoles affectés</div>
    </div>
</template>

