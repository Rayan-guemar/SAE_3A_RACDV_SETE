<script setup lang="ts" >
import { computed } from 'vue';
import { Tache } from '../../scripts/types';

interface Props {
   tache : Tache
}

const props = defineProps<Props>();

const affectedBenevole = computed(() => {
    return props.tache.benevoles?.filter(b => b.affecte);
})

</script>


<template>
    <Teleport to="body">
        <div class="task-affectation">
            <h4>{{ tache.poste.nom }}</h4>
            <div class="benevole-lists">
                <div class="affected">
                    <h5>Bénévoles affectés</h5>
                    <div class="benevole" v-for="benevole of tache">
                        <!-- <div class="benevole-name">{{ benevole.nom }}</div>
                        <div class="benevole-delete" @click="removeBenevole(benevole)">X</div> -->
                    </div>
                </div>
                <div class="unaffected">
                    <h5>Bénévoles non affectés</h5>
                    <div class="benevole" v-for="benevole of tache.benevolesNonAffectes">
                        <div class="benevole-name">{{ benevole.nom }}</div>
                        <div class="benevole-add" @click="addBenevole(benevole)">+</div>
                    </div>
                </div>
            </div>
        </div>
    </Teleport>
    
</template>

