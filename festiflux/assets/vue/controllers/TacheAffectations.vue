<script setup lang="ts" >
import { computed, ref } from 'vue';
import {Benevole as BenevoleType, Festival, Tache} from '../../scripts/types';
import { Backend } from '../../scripts/Backend';
import { displayHoursMinutes } from '../../scripts/utils';
import Benevole from './Benevole.vue';
import { emit } from 'process';

interface Props {
   festID: number
   tache : Tache
   benevoles : BenevoleType[]
}

const props = defineProps<Props>();

const loading = ref(false);

const emits = defineEmits(['close', 'reloadBenevoles']);

let defaultAffected = [...(props.tache.benevoles?.map(b => b.id) ?? [])];

const affectedBenevoles = computed(() => {
    return props.tache.benevoles?.filter(b => props.benevoles.map(b => b.id).includes(b.id)) ?? [];
})

const unaffectedBenevoles = computed(() => {
    return props.benevoles?.filter(b => !props.tache.benevoles?.map(b => b.id).includes(b.id)) ?? [];
})

const addBenevole = (benevole: BenevoleType) => {
    props.tache.benevoles?.push(benevole);
}

const removeBenevole = (benevole: BenevoleType) => {
    props.tache.benevoles = props.tache.benevoles?.filter(b => b.id != benevole.id);
}

const clickable = computed(() => {
    const affectedId = affectedBenevoles.value.map(b => b.id);
    return affectedId.sort().join() != defaultAffected.sort().join();
})

const save = async () => {
    if (props.tache.id) {
        loading.value = true;
        const resp = await Backend.saveBenevole(props.tache.id, props.tache.benevoles ?? [],  props.benevoles?.filter(b => !props.tache.benevoles?.map(b => b.id).includes(b.id)) ?? []);
        
        if (resp.success) {
            emits('reloadBenevoles');
            defaultAffected = [...(props.tache.benevoles?.map(b => b.id) ?? [])];
            loading.value = false;
            emits('close');
        }
    }
}

</script>


<template>
    <Teleport to="body">
        <div class="task-affectation">
            <img class="btn-close-modal" @click="$emit('close')" src="/icons/fermer.png">
            <h3>{{ tache.poste.nom }}</h3>
            <div>{{  `${displayHoursMinutes(tache.creneau.debut)} - ${displayHoursMinutes(tache.creneau.fin)}` }}</div>
            <div class="info-wrapper">
                <div>
                    <h5>Description :</h5>
                    <div class="content">{{ tache.poste.description }}</div>
                </div>
                <div>
                    <h5>Remarque :</h5>
                    <div class="content">{{ tache.description }}</div>
                </div>
                <div>
                    <h5 v-if="tache.lieu">Lieu :</h5>
                    <div class="content" v-if="tache.lieu">{{ tache.lieu }}</div>
                </div>
            </div>
            <div class="benevole-lists">
                <div class="affected">
                    <h4>Bénévoles affectés ({{ affectedBenevoles.length + ' / '  + tache.nbBenevole }})</h4>
                    <div class="list">
                        <Benevole 
                            v-for="benevole of affectedBenevoles" 
                            :benevole="benevole"
                            :festID = "festID"
                            :affected="true"
                            @removeBenevole="removeBenevole(benevole)"
                        />
                    </div>
                </div>
                <div class="unaffected">
                    <h4>Bénévoles non affectés</h4>
                    <div class="list">
                        <Benevole 
                            v-for="benevole of unaffectedBenevoles"
                            :benevole="benevole"
                            :festID = "festID"
                            :affected="false"
                            :charge="benevole.charge"
                            @addBenevole="addBenevole(benevole)"
                        />
                    </div>
                </div>
            </div>
            <div class="save-edits" :class="{clickable: clickable, 'underline-loading': loading}" @click="() => {
                if (clickable) {
                    save();
                }
            }">
                Enregister
            </div>
        </div>
    </Teleport>
    
</template>

