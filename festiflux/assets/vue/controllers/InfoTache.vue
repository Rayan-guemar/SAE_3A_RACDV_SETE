<script setup lang="ts" >
import { Tache } from '../../scripts/types';

// export interface Tache {
// 	id?: number;
// 	description: string;
// 	nbBenevole: number;
// 	benevoleAffecte: number;
// 	lieu?: string;
// 	poste: Poste;
// 	creneau: Creneau;
// }

interface Props {
   tache : Tache
}

defineProps<Props>();

</script>


<template>
    <div class="tache-info_wrapper" >
        <h1>{{ tache.poste.nom }}</h1>
        <div>{{ tache.description }}</div>
        <div>{{ tache.lieu }}</div>
        <div>{{ tache.nbBenevole }} bénévoles</div>
    </div>
</template>

