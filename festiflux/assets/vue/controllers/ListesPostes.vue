<script setup lang="ts">
import { onMounted, ref, watch } from "vue";
import { Backend } from "../../scripts/Backend";
import PosteForm from "./PosteForm.vue";
import { Poste, Preference, User } from "../../scripts/types";



interface Props {
  festivalId: number;
  festivalName: string;
  isOrgaOrResp: boolean;
  status: boolean;
  isFestivalOpen: boolean;
  watchingOtherUserPreferences: boolean;
	otherUserId: number;
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
const otherUser = ref<User>();
const showName = ref(false);

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
  if (props.watchingOtherUserPreferences) return;
  if (askingForDelete.value) return;

  currentPoste.value = {...poste};

  posteOpened.value = true;
};

const getUserName = (id: number) => {
  return Backend.getUser(id);
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
  const listeOfPreferences = await Backend.getPreferences(props.festivalId, props.otherUserId);
  preferences.value = listeOfPreferences;
};

let lang = document.getElementById('app')?.getAttribute('data-locale');
if (lang ==='null') {
  lang = 'fr';
}

function translate(key: string) {
  if (lang === 'fr') {
    switch (key) {
      case 'title':
        return 'Liste des postes';
      case 'void':
        return "Il n'y a aucun poste pour le moment";
      case 'add':
        return 'Ajouter un poste';
      case 'love' : return "J'aime";
      case 'hate' : return "Je n'aime pas";
    }
  } else {
    switch (key) {
      case 'title':
        return 'List of posts';
      case 'void':
        return "There is no jobs";
      case 'add':
        return 'Add a job';
      case 'love' : return "Love";
      case 'hate' : return "Dislike";
    }
  }
}

onMounted(async () => {
  await getPostes();
  if (!props.isOrgaOrResp || props.watchingOtherUserPreferences)  {
    await getPreferences();
  }
  if (props.watchingOtherUserPreferences) {
    otherUser.value = await getUserName(props.otherUserId);
    showName.value = true;
  }
});
</script>

<template>
  <h2 class="poste-section">
    {{ festivalName }}
  </h2>

  <div class="poste-list-wrapper">
    <div class="poste-list" >
      <h3>        {{ translate("title") }} </h3>
      <h4 v-if="watchingOtherUserPreferences && showName">( Préférence de {{  otherUser?.name }})</h4>
      <div v-if="posteList.length === 0">
        {{ translate("void") }}
      </div>
      <div v-else class="postes">
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
            {{
              watchingOtherUserPreferences ? (
                getPreference(poste) == 1 ? translate('love') : getPreference(poste) == -1 ? translate('hate') : ''
              ) : (
                getPreference(poste) == 1 ? translate('love') : getPreference(poste) == -1 ? translate('hate') : ''
              )
            }}
          </div>
        </div>
      </div>
      <div v-if="isOrgaOrResp && !posteOpened && !isFestivalOpen && !watchingOtherUserPreferences" class="new-poste-btn pointer" @click="newPoste">
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
        :lang="lang ?? 'fr'"
    />
  </div>
</template>


