<script setup lang="ts">

import {Creneau, Festival} from "../../scripts/types";
import {ref} from "vue";
import {Backend} from "../../scripts/Backend";
import { getDateForInputAttribute } from "../../scripts/utils";

type Props = {
  festivalId: number;
  dateDebut: Date;
  dateFin: Date;
  lang: string;
}

const props = defineProps<Props>();

const emits = defineEmits(['close'])

const creneau = ref<Creneau>({
  debut: new Date(),
  fin: new Date()
})


const start = ref<HTMLInputElement>();
const end = ref<HTMLInputElement>();

function changeHandlerStart() {
  if (!start.value || !end.value) return;

  if (start.value.value) {;
    end.value.setAttribute("min", start.value.value);
  } else {    
    end.value?.setAttribute("min", getDateForInputAttribute(props.dateDebut));
    end.value?.setAttribute("max", getDateForInputAttribute(props.dateFin));
  }
}

function changeHandlerEnd() {
  if (!start.value || !end.value) return;

  if (end.value.value) {
    start.value.setAttribute("max", end.value.value);
  } else {
    start.value?.setAttribute("min", getDateForInputAttribute(props.dateDebut));
    start.value?.setAttribute("max", getDateForInputAttribute(props.dateFin));
  }
}

function translate(key: string) {
  if (props.lang === 'fr') {
    switch (key) {
      case 'alertDate':
        return "La date de début doit être avant la date de fin";
      case 'title':
        return "Prévénir d'une indisponibilité";
      case 'begin':
        return "Début du créneaux";
      case 'end':
        return "Fin du créneaux";
      case 'cancel':
        return 'Annuler';
    }
  } else {
    switch (key) {
      case 'alertDate':
        return "The start date must be before the end date";
      case 'title':
        return "Add an unavailability";
      case 'begin':
        return 'Start of the slot';
      case 'end':
        return 'End of the slot';
      case 'cancel':
        return 'Cancel';
    }
  }
}

const createIndispo = async (e: Event) => {
  if (!creneau.value.debut || !creneau.value.fin) return;

  if (creneau.value.debut >= creneau.value.fin) {
    alert(translate('alertDate'));
    return;
  }

  await Backend.addIndispo(props.festivalId, creneau.value);
  emits('close');
}

</script>
<template>
  <div class="planning-form">
  <form @submit.prevent="createIndispo">
    <h2>{{ translate("title") }}</h2>
    <div class="flex-column flex-align-center">
      <label for="start-creneau">{{ translate("begin") }}</label>
      <input name="start" id="start-creneau" ref="start" type="datetime-local" :min="getDateForInputAttribute(dateDebut)" :max="getDateForInputAttribute(dateFin)" v-model="creneau.debut" @change="changeHandlerStart" required>
    </div>
    <div class="flex-column flex-align-center">
      <label for="end-creneau">{{ translate("end") }}</label>
      <input name="end" id="end-creneau" ref="end" type="datetime-local" :min="getDateForInputAttribute(dateDebut)" :max="getDateForInputAttribute(dateFin)" v-model="creneau.fin" @change="changeHandlerEnd" required>
    </div>
    <div class="flex-row flex-align-center" :style="{justifyContent: 'space-evenly', margin: '5px'}">
      <input type="submit" value="Ajouter">
      <button class="btn" @click="() => $emit('close')">{{ translate("cancel") }}</button>
    </div>
  </form>
  </div>
</template>