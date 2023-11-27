<script setup lang="ts">
import { ref } from 'vue';
import { encodedStr, getDateHours2Digits, hashCode } from '../../scripts/utils';
import { Tache, Creneau, Poste } from '../../scripts/types';


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
console.log(tache.poste.nom, position, total, ((total - position) / total));
</script>

<template>
    <div class="task" :id="''+tache.id"  :style="{
      top: `${((tache.creneau.debut.getHours() * 60 + tache.creneau.debut.getMinutes()) / (24 * 60)) * 100}%`,
      height: `max(50px, ${((tache.creneau.fin.getHours() * 60 + tache.creneau.fin.getMinutes() - (tache.creneau.debut.getHours() * 60 + tache.creneau.debut.getMinutes())) / (24 * 60)) * 100}%)`,
      width: `calc(${((total - (position-1)) / total) * 100}% - 4px)`,
      margin: `0 2px`,
      left: `${(100 / total) * (position-1)}%`,
      transform: `translateX(0%)`,
      borderColor: `rgb(${posteToColor(tache.poste).join(',')})`,
      backgroundColor: `rgb(${posteToColorBright(tache.poste).join(',')})`,
      color: 'black',//color: `rgb(${posteToColor(tache.poste).join(',')})`,
   
   
    }" >
    <div class="task-text" :style="{
      width: `${total == 1 ? 100 : (1 / ((total-(position-1)))*100)}%`,
    }">
      <div class="name">{{ tache.poste.nom }}</div>
      <div class="tache">
        {{ `${getDateHours2Digits(tache.creneau.debut)} - ${getDateHours2Digits(tache.creneau.fin)}` }}
      </div>
    </div>
  </div>
</template>