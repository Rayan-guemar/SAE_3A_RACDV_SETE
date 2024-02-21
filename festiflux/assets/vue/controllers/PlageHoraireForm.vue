<script setup lang="ts">

import { Creneau, Festival, Poste, TacheCreateData } from "../../scripts/types";
import { ref } from "vue";
import { Backend } from "../../scripts/Backend";
import { getDateForInputAttribute } from "../../scripts/utils";

type Props = {
  festivalId: number;
  dateDebut: Date;
  dateFin: Date;
  close: (c?: Creneau) => void
  updatePlages: () => void
}

const props = defineProps<Props>();

const creneau = ref<Creneau>({
  debut: new Date(),
  fin: new Date()
})

const start = ref<HTMLInputElement>();
const end = ref<HTMLInputElement>();

function changeHandlerStart() {
  if (!start.value || !end.value) return;

  if (start.value.value) {
    ;
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



const createCreneau = async (e: Event) => {
  if (!creneau.value.debut || !creneau.value.fin) return;

  if (creneau.value.debut >= creneau.value.fin) {
    alert("La date de début doit être avant la date de fin");
    return;
  }
  await Backend.addHeureDepartFin(props.festivalId, creneau.value);
  props.close(creneau.value);
  props.updatePlages();

}

</script>

<template>
  <form class="planning-form" @submit.prevent="createCreneau">
    <h2>Ajout d'une plage horaire</h2>
    <div class="flex-column flex-align-center">
      <label for="start-creneau">Début du créneau</label>
      <input name="start" id="start-creneau" ref="start" type="datetime-local" :min="getDateForInputAttribute(dateDebut)"
        :max="getDateForInputAttribute(dateFin)" v-model="creneau.debut" @change="changeHandlerStart" />
    </div>
    <div class="flex-column flex-align-center">
      <label for="end-creneau">Fin du créneau</label>
      <input name="end" id="end-creneau" ref="end" type="datetime-local" :min="getDateForInputAttribute(dateDebut)"
        :max="getDateForInputAttribute(dateFin)" v-model="creneau.fin" @change="changeHandlerEnd" />
    </div>
    <div class="flex-column flex-align-center">
      <button class="btn add-plage" type="submit">Ajouter</button>
      <button id="cancel-creneau-btn" class="btn" @click="close()">Annuler</button>
    </div>
  </form>
</template>