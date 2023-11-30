<script setup lang="ts">
import { computed, ref, watchEffect, watch } from "vue";
import {
  getDateHours2Digits,
  hashCode,
  hexToBrighterHex,
} from "../../scripts/utils";
import { Tache, Creneau, Poste, Benevole } from "../../scripts/types";
import Modal from "./Modal.vue";
import TacheAffectations from "./TacheAffectations.vue";

interface Props {
  tache: Tache;
  position: number;
  total: number;
  modeAffectation: boolean;
  benevoles: Benevole[];
}

defineEmits(['reloadBenevoles'])

const props = defineProps<Props>();

const posteToColor = (poste: Poste) => {
  const colors = [
    [97, 26, 221],
    [255, 68, 84],
  ];

  let nameHash = hashCode(poste.nom);
  let mod = nameHash % colors.length;

  if (mod < 0) {
    mod = colors.length + mod;
  }
  let color = colors[mod];
  return color;
};

const posteToColorBright = (poste: Poste) => {
  const c = posteToColor(poste);
  c[0] = c[0] + 100 > 255 ? 255 : c[0] + 100;
  c[1] = c[1] + 100 > 255 ? 255 : c[1] + 100;
  c[2] = c[2] + 100 > 255 ? 255 : c[2] + 100;
  return c;
};

const showingInfo = ref(false);
const showingAffectionMode = ref(false);

const task = ref<HTMLDivElement>();

const showInfo = () => {
  showingInfo.value = true;
};

const showAffectionMode = () => {
  showingAffectionMode.value = true;
};

window.addEventListener("click", (e) => {
  if (props.modeAffectation) {
    return;
  }
  if (showingInfo.value) {
    if (
      (task.value && task.value.contains(e.target as Node)) ||
      props.modeAffectation
    ) {
      showingInfo.value = true;
    } else {
      showingInfo.value = false;
    }
  }
});




const nbDispo = computed(() => {
  let nb = 0;
  props.benevoles.forEach((b) => {
    if (b.indisponibilites.length == 0){
      nb++;
    } else {
      b.indisponibilites.forEach((i) => {
        if (i.debut < props.tache.creneau.debut && i.fin > props.tache.creneau.fin){
          nb++;
        }
      })
    }
  }); 
  
  return nb
})


// const nbDispo = ref(0)

// let nb = 0;

// benevoles.forEach((b) => {
//   if (b.indisponibilites.length == 0){
//     nb++;
//   }
// });

// benevoles.filter(b => b.indisponibilites.length == 0).length


</script>

<template>
  <div
    ref="task"
    class="task"
    :id="'' + tache.id"
    :style="{
      top: `${
        ((tache.creneau.debut.getHours() * 60 +
          tache.creneau.debut.getMinutes()) /
          (24 * 60)) *
        100
      }%`,
      height: `max(30px, ${
        ((tache.creneau.fin.getHours() * 60 +
          tache.creneau.fin.getMinutes() -
          (tache.creneau.debut.getHours() * 60 +
            tache.creneau.debut.getMinutes())) /
          (24 * 60)) *
        100
      }%)`,
      width: `calc(${((total - (position - 1)) / total) * 100}% - 4px)`,
      margin: `0 2px`,
      left: `${(100 / total) * (position - 1)}%`,
      transform: `translateX(0%)`,
      borderColor: tache.poste.couleur
        ? tache.poste.couleur
        : `rgb(${posteToColor(tache.poste).join(',')})`,
      backgroundColor: tache.poste.couleur
        ? hexToBrighterHex(tache.poste.couleur)
        : `rgb(${posteToColorBright(tache.poste).join(',')})`,
      color: 'black', //`rgb(${posteToColor(tache.poste).join(',')})`,
      zIndex: showingInfo ? 100 : 0,
    }"
    @click="() => (modeAffectation ? showAffectionMode() : showInfo())"
  >
    <div
      class="task-text"
      :style="{
        width: `${total == 1 ? 100 : (1 / (total - (position - 1))) * 100}%`,
      }"
    >
      <div class="name">{{ tache.poste.nom }}</div>
      <div class="tache">
        {{
          `${getDateHours2Digits(tache.creneau.debut)} - ${getDateHours2Digits(
            tache.creneau.fin
          )}`
        }}
      </div>
      
    </div>
    <div v-if="showingInfo && !modeAffectation" class="tache-info_wrapper">
      <div class="tache-info_nom">{{ tache.poste.nom }}</div>
      <div class="tache-info_desc">{{ tache.description }}</div>
      <div class="tache-info_lieu">{{ tache.lieu }}</div>
      <div class="tache-info_benevoles">
        {{ tache.nbBenevole }} bénévoles requis
      </div>
      <div class="tache-info_benevoles">
        {{ tache.benevoleAffecte }} bénévoles affectés
      </div>
    </div>
    <Modal
      v-if="showingAffectionMode && modeAffectation"
      @close="() => (showingAffectionMode = false)"
    >
      <TacheAffectations
        @close="() => (showingAffectionMode = false)"
        :tache="tache"
        :benevoles="benevoles"
        @reloadBenevoles="() => $emit('reloadBenevoles')"
      />
    </Modal>

    <div v-if="modeAffectation" class="pastille-wrapper">
      <div class="pastille benevole__number">
        {{ tache.benevoleAffecte }} / {{ tache.nbBenevole }}
      </div>
      <div class="pastille benevole__dipo"> {{ nbDispo }} </div>
      <div class="pastille benevole__like"></div>
    </div>

  </div>
</template>
