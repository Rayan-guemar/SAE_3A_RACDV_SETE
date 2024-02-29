<script setup lang="ts">
import { ref, computed } from 'vue';
import { getDateHours2Digits, hashCode, hexToBrighterHex } from '../../scripts/utils';
import {Tache, Creneau, Poste, Benevole, Festival, ID} from '../../scripts/types';
import { Backend } from '../../scripts/Backend';
import Modal from './Modal.vue';
import TacheAffectations from './TacheAffectations.vue';


interface Props {
  festID: ID,
  chargesBenevole: Record<ID, number>,
  tache: Tache,
  position: number,
  total: number,
  modeAffectation: boolean,
  benevoles: Benevole[],
  lang: string
}

const props = defineProps<Props>();
const emit = defineEmits();

const deleteBtn = ref<HTMLDivElement>();

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

const taskHover = ref(false);

const deleting = ref(false);

const deleteTache = () => {
  Backend.deleteTache(props.festID, props.tache).then(() => {
    emit('reloadTaches');
  });
  deleting.value = false;
}

const nbDispo = computed(() => {
  let nb = 0;
  props.benevoles.forEach((b) => {
    if (b.indisponibilites.length == 0){
      nb++;
    } else {
      b.indisponibilites.every((i) => i.debut.getTime() > props.tache.creneau.fin.getTime() || i.fin.getTime() < props.tache.creneau.debut.getTime()) && nb++;
    }
  }); 
  
  return nb
})

const nbLike = computed(() => {
  let nb = 0;
  props.benevoles.forEach((b) => {
    b.preferences.forEach((p) => {
      if (p.poste == props.tache.poste.id){
        if (p.degree == 1 ) nb++;
      }
    })
  }); 
  
  return nb
})

const switchMode = (e: MouseEvent) => {
  if (e.target === deleteBtn.value) {
    return
  }
  props.modeAffectation ;
}

function translate(key: string) {
  if (props.lang === 'fr') {
    switch (key) {
      case 'desc': return 'Description :';
      case 'note' : return 'Remarque :';
      case 'location' : return 'Lieu :';
      case 'affectedVolun' : return 'Bénévoles affectés';
    }
  } else {
    switch (key) {
      case 'desc': return 'Description :';
      case 'note' : return 'Note :';
      case 'location' : return 'Location :';
      case 'affectedVolun' : return 'Affected volunteers';
    }
  }
}
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
      zIndex: showingInfo ? 99 : 0,
      overflow: showingInfo ? 'visible' : 'hidden',
    }"
    @click="(switchMode)"
  >
    <div ref="deleteBtn" class="delete" @click.prevent="() => deleting = true">
      <img src="../../../public/icons/delete.svg" alt="">
    </div>
    <div
      @mouseover="() => (taskHover = true)"
      @mouseleave="() => (taskHover = false)"
      class="task-text"
      :style="{
        width: taskHover || showingInfo ? '100%' : `${total == 1 ? 100 : (1 / (total - (position - 1))) * 100}%`,
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

    <Modal
      v-if="showingInfo && !modeAffectation"
      @close="() => (showingInfo = false)"
    >
      <!-- <div class="tache-info_wrapper">
        <div class="tache-info_nom">{{ tache.poste.nom }}</div>
        <div class="tache-info_desc">{{ tache.description }}</div>
        <div class="tache-info_lieu">{{ tache.lieu }}</div>
        <div class="tache-info_benevoles">
          {{ tache.nbBenevole }} bénévoles requis
        </div>
        
      </div> -->
      <div class="info-wrapper">
          <div>
              <h5>        {{ translate("desc") }}
              </h5>
              <div class="content">{{ tache.poste.description || (tache.poste.description.length === 0 ? 'Il n\'y a pas de description' : tache.poste.description) }}</div>
          </div>
          <div>
              <h5>        {{ translate("note") }}
              </h5>
              <div class="content">{{ tache.description || (tache.description.length === 0 ? 'Il n\'y a pas de remarque' : tache.description) }}</div>
          </div>
          <div>
              <h5 v-if="tache.lieu">        {{ translate("location") }}
              </h5>
              <div class="content" v-if="tache.lieu">{{ tache.lieu }}</div>
          </div>
          <div class="tache-info_benevoles">
            {{ tache.benevoleAffecte }}         {{ translate("affectedVolun") }}

          </div>
      </div>
    </Modal>
    
    <Modal
      v-if="showingAffectionMode && modeAffectation"
      @close="() => (showingAffectionMode = false)"
    >
      <TacheAffectations 
        @close="() => showingAffectionMode = false" 
        :tache="tache"
        :benevoles="benevoles"
        :chargesBenevole = "chargesBenevole"
        @reloadBenevoles="() => $emit('reloadBenevoles')"
        :lang = "lang"
      />
    </Modal>

    <Modal v-if="deleting">
      <form class="planning-form">
        <h5>Voulez vous vraiment supprimer cette tache ?</h5>
        <div class="flex-row">
          <div class="btn" @click="deleteTache">Oui</div>
          <div class="btn" @click="() => deleting = false">Non</div>
        </div>
      </form>
    </Modal>

    <div v-if="modeAffectation" class="pastille-wrapper">
      <div class="pastille benevole__number">
        {{ tache.benevoleAffecte }} / {{ tache.nbBenevole }}
      </div>
      <div class="pastille benevole__dipo"> {{ nbDispo }} </div>
      <div class="pastille benevole__like"> {{ nbLike }} </div>
    </div>

  </div>
</template>
