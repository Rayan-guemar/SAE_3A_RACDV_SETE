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
const selectedSort = ref<"preference croissante" | "charge croissante" | "charge decroissante" | "preference decroissante">("charge croissante");

const sortedBenevoles = computed(() => {

    if (selectedSort.value == "preference decroissante") {
        return [...props.benevoles].sort((a, b) => {
            const a_degree = a.preferences.find((p) => p.poste == (props.tache.poste.id + ""))?.degree || 0;
            const b_degree = b.preferences.find((p) => p.poste == (props.tache.poste.id + ""))?.degree || 0;
            return b_degree - a_degree;
        });
    }
    if (selectedSort.value == "preference croissante") {
      return [...props.benevoles].sort((b, a) => {
        const a_degree = a.preferences.find((p) => p.poste == (props.tache.poste.id + ""))?.degree || 0;
        const b_degree = b.preferences.find((p) => p.poste == (props.tache.poste.id + ""))?.degree || 0;
        return b_degree - a_degree;
      });
    }
  if (selectedSort.value == "charge croissante") {
        // TODO
        return [...props.benevoles].sort((a, b) => {
            const a_charge = props.chargesBenevole[a.id] || 0;
            const b_charge = props.chargesBenevole[b.id] || 0;
            return a_charge - b_charge;
        })
    }
    if (selectedSort.value == "charge decroissante") {
      // TODO
      return [...props.benevoles].sort((b, a) => {
        const a_charge = props.chargesBenevole[a.id] || 0;
        const b_charge = props.chargesBenevole[b.id] || 0;
        return a_charge - b_charge;
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

const addBenevole = (benevoleID: ID) => {
  if (typeof benevoleID === "string") {
    console.log('add benevole', benevoleID);
    props.tache.benevoles?.push(parseInt(benevoleID));
  }
}

const removeBenevole = (benevoleID: ID) => {
  if (typeof benevoleID === "string") {
    console.log('remove benevole', benevoleID);
    props.tache.benevoles = props.tache.benevoles?.filter(id => id != parseInt(benevoleID));
  }
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

const affectedDispo = computed(() => {
    return benevoleDisponibilites(props.tache, affectedBenevoles.value);
})

const unaffectedDispo = computed(() => {
    return benevoleDisponibilites(props.tache, unaffectedBenevoles.value);
})

const benevoleDisponibilites = (t: Tache, list: BenevoleType[]) => {
    return list.filter(b => b.indisponibilites.every(i => i.debut > t.creneau.fin || i.fin < t.creneau.debut))
}

const startDrag = (event, item) => {
  console.log('start drag', item);
  event.dataTransfer.dropEffect = 'move';
  event.dataTransfer.effectAllowed = 'move';
  event.dataTransfer.setData('itemID', item.id);
}

const onDrop = (event, bool) => {
  const itemID = event.dataTransfer.getData('itemID');
  if(bool){
    console.log(itemID);
    addBenevole(itemID);
  } else {
    removeBenevole(itemID);
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
                    <div class="content">{{ (tache.poste.description.length === 0 ? 'Il n\'y a pas de description' : tache.poste.description) }}</div>
                </div>
                <div>
                    <h5>Remarque :</h5>
                    <div class="content">{{ (tache.description.length === 0 ? 'Il n\'y a pas de remarque' : tache.description) }}</div>
                </div>
                <div>
                    <h5 v-if="tache.lieu">Lieu :</h5>
                    <div class="content" v-if="tache.lieu">{{ tache.lieu }}</div>
                </div>
                <div>
                    <CustomSelect
                        class="select-sort"
                        :options="[
                            {label: 'Trier par charge croissante', value: 'charge croissante'},
                            {label: 'Trier par charge décroissantes', value: 'charge decroissante'},
                            {label: 'Trier par préférences croissante', value: 'preference croissante'},
                            {label: 'Trier par préférences décroissantes', value: 'preference decroissante'},
                        ]"
                        :selected="selectedSort"
                        @select="selectedSort = $event"
                    />

                </div>
            </div>
            <div class="benevole-lists">
                <div class="affected"
                    @drop="onDrop($event, true)"
                     @dragenter.prevent
                     @dragover.prevent
                >
                    <h4>Bénévoles affectés ({{ affectedBenevoles.length + ' / '  + tache.nbBenevole }})</h4>
                    <div class="list">
                        <Benevole 
                            v-for="benevole of affectedDispo" 
                            :benevole="benevole"
                            :charge="chargesBenevole[benevole.id]"
                            :affected="true"
                            @removeBenevole="removeBenevole(benevole.id)"
                            :poste="tache.poste"
                            @dragstart="startDrag($event, benevole)"
                        />
                    </div>
                </div>
                <div class="unaffected"
                     @drop="onDrop($event, false)"
                     @dragenter.prevent
                     @dragover.prevent
                >
                    <h4>Bénévoles non affectés</h4>
                    <div class="list">
                        <Benevole 
                            v-for="benevole of unaffectedDispo"
                            :benevole="benevole"
                            :charge="chargesBenevole[benevole.id]"
                            :affected="false"
                            @addBenevole="addBenevole(benevole.id)"
                            :poste="tache.poste"
                            @dragstart="startDrag($event, benevole)"
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
<style>

</style>

