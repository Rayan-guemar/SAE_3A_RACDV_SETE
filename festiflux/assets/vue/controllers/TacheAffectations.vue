<script setup lang="ts" >
import { computed, ref } from 'vue';
import {Benevole as BenevoleType, Festival, Tache, ID} from '../../scripts/types';
import { Backend } from '../../scripts/Backend';
import { calculCharge, displayHoursMinutes } from '../../scripts/utils';
import Benevole from './Benevole.vue';
import CustomSelect from './CustomSelect.vue';

interface Props {
    chargesBenevole: Record<ID, number>,
    tache : Tache
    benevoles : BenevoleType[]
}

const getBenevoleFromID = (id:ID) => {
    return props.benevoles.find((b) => b.id == id);
}

const getBenevoleFromIDs = (ids: ID[]) => {
    return ids.map(getBenevoleFromID).filter(id => id) as (BenevoleType )[];
}

const props = defineProps<Props>();

const loading = ref(false);

const emits = defineEmits(['close', 'reloadBenevoles']);

let defaultAffected = getBenevoleFromIDs(props.tache.benevoles || []);
const selectedSort = ref<"preference" | "charge" | "">("");

const sortedBenevoles = computed(() => {

    if (selectedSort.value == "preference") {
        return [...props.benevoles].sort((a, b) => {
            const a_degree = a.preferences.find((p) => p.poste == (props.tache.poste.id + ""))?.degree || 0;
            const b_degree = b.preferences.find((p) => p.poste == (props.tache.poste.id + ""))?.degree || 0;
            return b_degree - a_degree;
        });
    } 
    if (selectedSort.value == "charge") {
        // TODO
        return [...props.benevoles].sort((a, b) => {
            const a_charge = props.chargesBenevole[a.id] || 0;
            const b_charge = props.chargesBenevole[b.id] || 0;
            return b_charge - a_charge;
        })
    }

    return [...props.benevoles];
})

const affectedBenevoles = computed(() => {
    return sortedBenevoles.value.filter(b => props.tache.benevoles?.includes(b.id));
})

const unaffectedBenevoles = computed(() => {
    // return getBenevoleFromIDs(props.benevoles?.filter(b => affectedBenevoles.value.includes(b.id)).map(b => b.id) || []) ?? [];
    return sortedBenevoles.value.filter(b => !props.tache.benevoles?.includes(b.id)) || [];
})

const addBenevole = (benevole: BenevoleType) => {
    props.tache.benevoles?.push(benevole.id);
}

const removeBenevole = (benevole: BenevoleType) => {
    props.tache.benevoles = props.tache.benevoles?.filter(id => id != benevole.id);
}

const clickable = computed(() => {
    return affectedBenevoles.value.map(b => b.id).sort().join() != defaultAffected.map(b => b.id).sort().join();
})

const save = async () => {
    if (props.tache.id) {
        loading.value = true;
        const resp = await Backend.saveBenevole(props.tache.id, affectedBenevoles?.value.map(b => b.id) || [], unaffectedBenevoles.value.map(b => b.id) || []);
        
        if (resp.success) {
            emits('reloadBenevoles');
            defaultAffected = getBenevoleFromIDs(props.tache.benevoles || []);
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
                <div>
                    <CustomSelect
                        class="select-sort"
                        :options="[
                            {label: 'Ne pas trier', value: ''},
                            {label: 'Trier par charge', value: 'charge'},
                            {label: 'Trier par préférences', value: 'preference'},
                        ]"
                        :selected="selectedSort"
                        @select="selectedSort = $event"
                    />
                    <!-- <select class="select-sort" v-model="selectedSort">
                        <option value="">Ne pas trier</option>
                        <option value="charge">Trier par charge</option>
                        <option value="preference">Trier par préférences</option>
                    </select> -->
                </div>
            </div>
            <div class="benevole-lists">
                <div class="affected">
                    <h4>Bénévoles affectés ({{ affectedBenevoles.length + ' / '  + tache.nbBenevole }})</h4>
                    <div class="list">
                        <Benevole 
                            v-for="benevole of affectedBenevoles" 
                            :benevole="benevole"
                            :charge="chargesBenevole[benevole.id]"
                            :affected="true"
                            @removeBenevole="removeBenevole(benevole)"
                            :poste="tache.poste"
                        />
                    </div>
                </div>
                <div class="unaffected">
                    <h4>Bénévoles non affectés</h4>
                    <div class="list">
                        <Benevole 
                            v-for="benevole of unaffectedBenevoles"
                            :benevole="benevole"
                            :charge="chargesBenevole[benevole.id]"
                            :affected="false"
                            @addBenevole="addBenevole(benevole)"
                            :poste="tache.poste"
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

