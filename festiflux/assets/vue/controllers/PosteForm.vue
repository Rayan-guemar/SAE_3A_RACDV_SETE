<template>
    <div>
      <div class="poste-form-wrapper" :class="{ blurred: askingForDelete }" v-fade>
        <div class="poste-form">
          <h2 class="top-heading">Poste</h2>
  
          <input
            type="text"
            class="poste-name"
            :class="{ 'missing-field': currentPoste.name === '' }"
            v-model.trim="currentPoste.name"
            placeholder="ex: Accueil artiste"
          />
          <div class="color-wrapper">
            <div>Couleur :</div>
            <input type="color" v-model="currentPoste.color" />
          </div>
          <textarea v-model.trim="currentPoste.desc" placeholder="ex: Accueillir les artistes"></textarea>
          <div v-if="editing">
            <div class="pointer edit-poste" :class="{ loading: updating }" @click="updatePoste">Modifier</div>
            <div class="pointer delete-poste" :class="{ loading: deleting }" @click="() => askingForDelete = true">
              Supprimer
            </div>
          </div>
          <div v-else>
            <div
              class="pointer create-poste"
              :class="{ 'missing-field': currentPoste.name === '', loading: creating }"
              @click="createPoste"
            >
              Créer
            </div>
          </div>
          <div class="pointer cancel-poste" @click="closePoste">Annuler</div>
        </div>
  
        <div class="task-preview-wrapper">
          <h4 class="top-heading">Aperçu d'une tache</h4>
          <div
            class="task-preview"
            :style="{
              backgroundColor: currentPoste.color + '1A',
              borderColor: currentPoste.color,
              color: currentPoste.color,
            }"
          >
            <div class="name">{{ currentPoste.name !== '' ? currentPoste.name : 'Nom poste' }}</div>
            <div class="tache">22h00 - 23h00</div>
          </div>
        </div>
      </div>
  
      <div v-if="askingForDelete" class="delete-poste-confirm">
        <div class="delete-poste-confirm-text">Voulez-vous vraiment supprimer ce poste ?</div>
        <div class="delete-poste-confirm-text">Cela entraînera la suppression de tous les créneaux associés à ce poste</div>
        <div class="delete-poste-confirm-btns">
          <div class="pointer delete-poste-confirm-yes" @click="deletePoste">Oui</div>
          <div class="pointer delete-poste-confirm-no" @click="() => askingForDelete = false">Non</div>
        </div>
      </div>
    </div>
  </template>
  
  <script lang="ts">
  import { ref, computed, watchEffect } from 'vue';
//   import { posteOpened, currentPoste, askingForDelete, posteList } from '../stores/posteStores';
  import { Backend } from '../../scripts/Backend';
//   import { toCSS } from '../../scripts/utils';
  
  export default {
    props: {
      festivalId: String,
    },
    setup(props) {
      const editing = computed(() => currentPoste.value.id !== null);
      const creating = ref(false);
      const deleting = ref(false);
      const updating = ref(false);
  
      const deletePoste = async () => {
        askingForDelete.value = false;
        deleting.value = true;
  
        await Backend.deletePoste(props.festivalId, currentPoste.value);
  
        posteOpened.value = false;
  
        const listOfPostes = await Backend.getPostes(parseInt(props.festivalId));
        posteList.value = listOfPostes;
  
        deleting.value = false;
      };
  
      const updatePoste = async () => {
        if (askingForDelete.value) return;
        updating.value = true;
  
        currentPoste.value.name = currentPoste.value.name.trim();
        currentPoste.value.desc = currentPoste.value.desc.trim();
  
        await Backend.updatePoste(props.festivalId, {
          id: currentPoste.value.id,
          nom: currentPoste.value.name,
          couleur: currentPoste.value.color,
          description: currentPoste.value.desc,
        });
  
        posteOpened.value = false;
  
        const listOfPostes = await Backend.getPostes(parseInt(props.festivalId));
        posteList.value = listOfPostes;
  
        updating.value = false;
      };
  
      const createPoste = async () => {
        if (askingForDelete.value) return;
  
        creating.value = true;
  
        currentPoste.value.name = currentPoste.value.name.trim();
        currentPoste.value.desc = currentPoste.value.desc.trim();
  
        await Backend.addPoste(props.festivalId, {
          nom: currentPoste.value.name,
          couleur: currentPoste.value.color,
          description: currentPoste.value.desc,
        });
  
        posteOpened.value = false;
  
        const listOfPostes = await Backend.getPostes(parseInt(props.festivalId));
        posteList.value = listOfPostes;
  
        creating.value = false;
      };
  
      const closePoste = () => {
        if (askingForDelete.value) return;
        posteOpened.value = false;
      };
  
      return {
        askingForDelete,
        currentPoste,
        createPoste,
        updatePoste,
        deletePoste,
        closePoste,
        editing,
        creating,
        deleting,
        updating,
      };
    },
  };
  </script>
  