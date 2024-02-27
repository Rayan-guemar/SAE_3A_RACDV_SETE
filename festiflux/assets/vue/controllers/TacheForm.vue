<script setup lang="ts">
import { ref } from 'vue';
import { Festival, Poste, TacheCreateData } from '../../scripts/types';
import { Backend } from '../../scripts/Backend';
import { getDateFromLocale, getDateForInputAttribute } from '../../scripts/utils';

type Props = {
  festID: number;
  title: string;
  dateDebut: Date;
  dateFin: Date;
  isOrgaOrResp: boolean;
  postes: Poste[];
  updateTaches: () => void
  close: (t?: TacheCreateData) => void
  lang: string
}

const props = defineProps<Props>();

const festival = ref<Festival>({
  festID: props.festID,
  title: props.title,
  dateDebut: new Date(props.dateDebut),
  dateFin: new Date(props.dateFin),
  isOrgaOrResp: props.isOrgaOrResp,
})

const startTache = ref<HTMLInputElement>();
const endTache = ref<HTMLInputElement>();

function changeHandlerStart() {

  if (!startTache.value || !endTache.value) return;

  if (startTache.value.value) {
    ;
    endTache.value.setAttribute("min", startTache.value.value);
  } else {
    endTache.value?.setAttribute("min", getDateForInputAttribute(props.dateDebut));
    endTache.value?.setAttribute("max", getDateForInputAttribute(props.dateFin));
  }
}

function changeHandlerEnd() {
  if (!startTache.value || !endTache.value) return;

  if (endTache.value.value) {
    startTache.value.setAttribute("max", endTache.value.value);
  } else {
    startTache.value?.setAttribute("min", getDateForInputAttribute(props.dateDebut));
    startTache.value?.setAttribute("max", getDateForInputAttribute(props.dateFin));
  }
}

function translate(key: string) {
  if (props.lang === 'fr') {
    switch (key) {
      case 'alertDate':
        return "La date de début doit être avant la date de fin";
      case 'title':
        return "Création d'un créneau";
      case 'job':
        return 'Choisissez un poste';
      case 'note':
        return 'Remarque';
      case 'nbrVolun':
        return 'Nombre de bénévoles';
      case 'begin':
        return "Début du créneaux";
      case 'end':
        return "Fin du créneaux";
      case 'location':
        return "Lieu";
      case 'address' :
        return "Adresse du lieu (optionnelle)";
      case 'add':
        return 'Ajouter';
      case 'cancel':
        return 'Annuler';
    }
  } else {
    switch (key) {
      case 'alertDate':
        return "The start date must be before the end date";
      case 'title':
        return "Create a slot for a job";
      case 'job':
        return 'Choose a job';
      case 'note':
        return 'Note';
      case 'nbrVolun':
        return 'Number of volunteers';
      case 'begin':
        return 'Start of the slot';
      case 'end':
        return 'End of the slot';
      case 'location':
        return 'Location';
      case 'address' :
        return "Address of the location (optional)";
      case 'add':
        return 'Add';
      case 'cancel':
        return 'Cancel';
    }
  }
}
const createTache = async (e: Event) => {
  const formData = new FormData(e.target as HTMLFormElement)

  const debut = new Date(formData.get("start") + "");
  const fin = new Date(formData.get("end") + "");
  const description = formData.get('description') + "";
  const nbBenevole = +(formData.get('nombre_benevole') + "");
  const posteId = formData.get('poste') + "";

  if (debut >= fin) {
    alert(translate('alertDate'));
    return;
  }


  const tache: TacheCreateData = {
    description: description,
    nombre_benevole: nbBenevole,
    poste_id: posteId,
    date_debut: debut,
    date_fin: fin,
    lieu: formData.get('creneau-lieu') + "",
    adresse: formData.get('creneau-lieu-address') + ""
  };
  props.close(tache);
  await Backend.addTache(festival.value.festID, tache);
  props.updateTaches();
};
</script>

<template>
  <div class="planning-form">
    <form @submit.prevent="createTache">
      <h2>{{ translate("title") }}</h2>
      <div class="flex-column flex-align-center">

        <label for="poste">{{ translate("job") }}</label>
        <select name="poste" id="creneau-poste-select">
          <option v-for="poste in postes" :value="poste.id">{{ poste.nom }}</option>
        </select>

      </div>
      <div class="flex-column flex-align-center">
        <label for="description">{{ translate("note") }}</label>
        <input name="description" id="creneau-description" type="text">
      </div>
      <div class="flex-column flex-align-center">
        <label for="nombre_benevole">{{ translate("nbrVolun") }}
        </label>
        <input name="nombre_benevole" id="creneau-nombre-benevole" type="number">
      </div>
      <div class="creneau-container">
        <div class="flex-column flex-align-center">
          <label for="start-creneau">{{ translate("begin") }}</label>
          <input name="start" id="start-creneau" ref="startTache" type="datetime-local"
            :min="getDateForInputAttribute(dateDebut)" :max="getDateForInputAttribute(dateFin)"
            :value="festival.dateDebut" @change="changeHandlerStart">
        </div>
        <div class="flex-column flex-align-center">
          <label for="end-creneau">{{ translate("end") }}</label>
          <input name="end" id="end-creneau" ref="endTache" type="datetime-local"
            :min="getDateForInputAttribute(dateDebut)" :max="getDateForInputAttribute(dateFin)" :value="festival.dateFin"
            @change="changeHandlerEnd">
        </div>
      </div>

      <div class="flex-column flex-align-center">
        <label for="lieuTache">{{ translate("location") }}</label>
        <input type='text' name='creneau-lieu' id="creneau-lieu">
      </div>

      <div class="flex-column flex-align-center">
        <label for="lieuTache">{{ translate("address") }}</label>
        <input type='text' name='creneau-lieu-address' id="creneau-lieu-address">
      </div>

      <div class="flex-row flex-align-center" :style="{ justifyContent: 'space-evenly', margin: '5px' }">
        <button type="submit" id="create-creneau-btn" class="btn" value="Créer un créneau">{{ translate("add") }}</button>
        <button id="cancel-creneau-btn" class="btn" @click="close()">{{ translate("cancel") }}</button>
      </div>
    </form>
  </div>
</template>
