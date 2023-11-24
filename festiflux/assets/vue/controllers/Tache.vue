<script setup lang="ts">
import { ref } from 'vue';
import { encodedStr, getDateHours2Digits, hashCode } from '../../scripts/utils';
import { Tache, Creneau, Poste } from '../../scripts/types';


interface Props {
    tache: Tache,
    position: number,
    total: number,
}

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

const {tache} = defineProps<Props>()

</script>

<template>
    <div class="task" :id="''+tache.id" :style="{
      top: `${((tache.creneau.debut.getHours() * 60 + tache.creneau.debut.getMinutes()) / (24 * 60)) * 100}%`,
      height: `${((tache.creneau.fin.getHours() * 60 + tache.creneau.fin.getMinutes() - (tache.creneau.debut.getHours() * 60 + tache.creneau.debut.getMinutes())) / (24 * 60)) * 100}%`,
      width: `calc(${100 / total}% - 4px)`,
      margin: `0 2px`,
      left: `${(100 / total) * position}%`,
      transform: `translateX(0%)`,
      borderColor: `rgb(${posteToColor(tache.poste).join(',')})`,
      backgroundColor: `rgb(${posteToColor(tache.poste).join(',')}, 0.1)`,
      color: `rgb(${posteToColor(tache.poste).join(',')})`
    }" >
    <div class="name">{{ encodedStr(tache.poste.nom) }}</div>
    <div class="tache">
      {{ encodedStr(`${getDateHours2Digits(tache.creneau.debut)} - ${getDateHours2Digits(tache.creneau.fin)}`) }}
    </div>
  </div>
</template>