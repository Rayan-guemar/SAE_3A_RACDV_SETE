<script setup lang="ts">
import { onMounted, ref } from "vue";
import { Backend } from "../../scripts/Backend";
import PosteForm from "./PosteForm.vue";
import { Poste } from "../../scripts/types";

interface Props {
  festivalId: number;
  festivalName: string;
}

const props = defineProps<Props>();

const defaultPoste = {
  nom: "",
  couleur: "#347deb",
  description: "",
};

const posteOpened = ref(false);
const currentPoste = ref<Poste>(defaultPoste);
const posteList = ref<Poste[]>([]);
const askingForDelete = ref(false);

const setAskingForDelete = (value: boolean) => {
  askingForDelete.value = value;
};

const setPosteOpened = (value: boolean) => {
  posteOpened.value = value;
};

const setCurrentPoste = (value: Poste) => {
  currentPoste.value = value;
};

const updateCurrentPoste = (f: (p: Poste) => void) => {
  f(currentPoste.value);
};

const setPosteList = (value: Poste[]) => {
  posteList.value = value;
};

const openPoste = (poste: Poste) => {
  if (askingForDelete.value) return;

  currentPoste.value = {...poste};

  posteOpened.value = true;
};

const newPoste = () => {
  if (askingForDelete.value) return;
  
  currentPoste.value = {...defaultPoste};
  
  
  posteOpened.value = true;
};

onMounted(async () => {
  const listeOfPostes = await Backend.getPostes(props.festivalId);
  posteList.value = listeOfPostes;
});
</script>

<template>
  <h2 class="poste-section">
    {{ festivalName }}
  </h2>

  <div class="poste-list-wrapper">
    <div class="poste-list" >
      <h3>Liste des postes</h3>
      <div class="postes">
        <div
          v-for="poste in posteList"
          :style="{
            'border-color': poste.couleur,
            'background-color': poste.couleur + '1A',
          }"
          class="poste pointer"
          @click="(e) => openPoste(poste)"
        >
          {{ poste.nom }}
        </div>
      </div>

      <div v-if="!posteOpened" class="new-poste-btn pointer" @click="newPoste">
        Ajouter un poste
      </div>
    </div>

    <PosteForm
        v-if="posteOpened"
        :festivalId="props.festivalId"
        :askingForDelete="askingForDelete"
        :setAskingForDelete="setAskingForDelete"
        :posteOpened="posteOpened"
        :setPosteOpened="setPosteOpened"
        :currentPoste="currentPoste"
        :setCurrentPoste="setCurrentPoste"
        :updateCurrentPoste="updateCurrentPoste"
        :posteList="posteList"
        :setPosteList="setPosteList"
    />
  </div>
</template>


