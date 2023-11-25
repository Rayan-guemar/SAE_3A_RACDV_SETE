<template>
    <div>
      <h2 class="poste-section" :class="{ blurred: askingForDelete }">{{ festivalName }}</h2>
  
      <div class="poste-list-wrapper">
        <div class="poste-list" :class="{ blurred: askingForDelete }" v-fade>
          <h3>Liste des postes</h3>
          <div class="postes">
            <div
              v-for="poste in posteList"
              :key="poste.id"
              :style="{ borderColor: poste.couleur, backgroundColor: poste.couleur + '1A' }"
              class="poste pointer"
              @click="openPoste(poste)"
              v-fade
            >
              {{ poste.nom }}
            </div>
          </div>
  
          <div v-if="!posteOpened" class="new-poste-btn pointer" @click="newPoste" v-fade :style="{ fontWeight: '500' }">
            Ajouter un poste
          </div>
        </div>
  
        <PosteForm v-if="posteOpened" :festivalId="festivalId" />
      </div>
    </div>
  </template>
  
  <script lang="ts">
  import { ref, onMounted } from 'vue';
//   import { fade } from 'vue-transition-component'; // Assuming you have a suitable Vue transition library
  import { Backend } from '../../scripts/Backend';
//   import { posteOpened, currentPoste, posteList, askingForDelete } from '../stores/posteStores';
  import PosteForm from './PosteForm.vue';
  
  export default {
    props: {
      festivalId: String,
      festivalName: String,
    },
    setup(props) {
      const openPoste = (poste) => {
        if (askingForDelete.value) return;
  
        currentPoste.value.id = poste.id;
        currentPoste.value.name = poste.nom;
        currentPoste.value.color = poste.couleur;
        currentPoste.value.desc = poste.description;
  
        posteOpened.value = true;
      };
  
      const newPoste = () => {
        if (askingForDelete.value) return;
        currentPoste.value = {
          id: null,
          name: '',
          color: '#347deb',
          desc: '',
        };
        posteOpened.value = true;
      };
  
      onMounted(async () => {
        const listOfPostes = await Backend.getPostes(parseInt(props.festivalId));
        posteList.value = listOfPostes;
      });
  
      return {
        posteOpened,
        currentPoste,
        posteList,
        askingForDelete,
        openPoste,
        newPoste,
      };
    },
  };
  </script>
  
  <style>
  * {
    transition: filter 0.2s;
  }
  </style>