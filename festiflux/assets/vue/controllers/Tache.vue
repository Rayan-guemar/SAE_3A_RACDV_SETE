<script setup lang="ts">
import { ref } from 'vue';
import { getDateHours2Digits, hashCode } from '../../scripts/utils';
import { Tache, Creneau, Poste } from '../../scripts/types';
import InfoTache from './InfoTache.vue';


interface Props {
  tache: Tache,
  position: number,
  total: number,
}

const {tache, position, total} = defineProps<Props>();


const posteToColor = (poste:Poste) => {
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
} 

const posteToColorBright = (poste:Poste) => {
  const c = posteToColor(poste);
  c[0] =  c[0] + 100 > 255 ? 255 : c[0] + 100
  c[1] =  c[1] + 100 > 255 ? 255 : c[1] + 100
  c[2] =  c[2] + 100 > 255 ? 255 : c[2] + 100
  return c;
}

const showingInfo = ref(false);

const task = ref<HTMLDivElement>();

const showInfo = () => {
  showingInfo.value = true;
}

window.addEventListener('click', (e) => {
  if (showingInfo.value) {
    if (task.value && task.value.contains(e.target as Node)) {
      showingInfo.value = true;
    } else {
      showingInfo.value = false;
    }
  }
});
</script>

<template>
    <div ref="task" class="task" :id="''+tache.id"  :style="{
      top: `${((tache.creneau.debut.getHours() * 60 + tache.creneau.debut.getMinutes()) / (24 * 60)) * 100}%`,
      height: `max(30px, ${((tache.creneau.fin.getHours() * 60 + tache.creneau.fin.getMinutes() - (tache.creneau.debut.getHours() * 60 + tache.creneau.debut.getMinutes())) / (24 * 60)) * 100}%)`,
      width: `calc(${((total - (position-1)) / total) * 100}% - 4px)`,
      margin: `0 2px`,
      left: `${(100 / total) * (position-1)}%`,
      transform: `translateX(0%)`,
      borderColor: `rgb(${posteToColor(tache.poste).join(',')})`,
      backgroundColor: `rgb(${posteToColorBright(tache.poste).join(',')})`,
      color:'black', //`rgb(${posteToColor(tache.poste).join(',')})`,
      zIndex: showingInfo ? 100 : 0,
    }" @click="showInfo()"  >
   
    <div class="task-text" :style="{
      width: `${total == 1 ? 100 : (1 / ((total-(position-1)))*100)}%`,
    }">
      <div class="name">{{ tache.poste.nom }}</div>
      <div class="tache">
        {{ `${getDateHours2Digits(tache.creneau.debut)} - ${getDateHours2Digits(tache.creneau.fin)}` }}
      </div>
      <div class="benevole__number">
        {{ tache.benevoleAffecte }} / {{ tache.nbBenevole }} bénévoles
      </div>
    </div>
    <InfoTache v-if="showingInfo" :tache="tache" />
    </div>
</template>