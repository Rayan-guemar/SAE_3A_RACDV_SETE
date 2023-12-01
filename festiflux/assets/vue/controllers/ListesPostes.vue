<script setup lang="ts">
import { onMounted, ref } from "vue";
import { Backend } from "../../scripts/Backend";
import PosteForm from "./PosteForm.vue";
import { Poste, Preference } from "../../scripts/types";

interface Props {
  festivalId: number;
  festivalName: string;
  isOrgaOrResp: boolean;
}

const props = defineProps<Props>();

const defaultPoste = {
  id: undefined,
  nom: "",
  couleur: "#347deb",
  description: "",
};

const posteOpened = ref(false);
const currentPoste = ref<Poste>({...defaultPoste});
const posteList = ref<Poste[]>([]);
const preferences = ref<Preference[]>([]);
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

const getPreference = (p: Poste) => {
  return preferences.value.find((pref) => pref.poste == p.id + "")?.degree || 0;
};

const getPostes = async () => {
  const listeOfPostes = await Backend.getPostes(props.festivalId);
  posteList.value = listeOfPostes;
};

const getPreferences = async () => {
  const listeOfPreferences = await Backend.getPreferences(props.festivalId);
  preferences.value = listeOfPreferences;
};

onMounted(async () => {
  await getPostes();
  if (!props.isOrgaOrResp)  {
    await getPreferences();
  }
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
            'position': 'relative'
          }"
          class="poste pointer"
          @click="(e) => openPoste(poste)"
        >
          {{ poste.nom }}
          <div v-if="getPreference(poste) == 1 || getPreference(poste) == -1" class="pref-pastille" :class="{'pref-like': getPreference(poste) == 1, 'pref-dislike': getPreference(poste) == -1}">
            {{  getPreference(poste) == 1 ? 'J\'aime' : getPreference(poste) == -1 ? 'Je n\'aime pas' : '' }}
          </div>
        </div>
      </div>

      <div v-if="isOrgaOrResp && !posteOpened" class="new-poste-btn pointer" @click="newPoste">
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
        :isOrgaOrResp="isOrgaOrResp"
        @close="() => setPosteOpened(false)"
        @reloadPostes="getPreferences"
    />
  </div>
</template>


