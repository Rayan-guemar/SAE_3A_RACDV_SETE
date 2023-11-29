<script setup lang="ts">

import {Creneau, Festival} from "../../scripts/types";
import {ref} from "vue";
import {Backend} from "../../scripts/Backend";

type Props = {
  festivalId: number;
}

const props = defineProps<Props>();

const creneau = ref<Creneau>({
  debut: new Date(),
  fin: new Date()
})


const createIndispo = async (e: Event) => {
  await Backend.addHeureDepartFin(props.festivalId, creneau.value);
}

</script>
<template>
  <form @submit.prevent="createIndispo">
    <h2>Prévénir d'une indisponibilité</h2>
    <div class="flex-column flex-align-center">
      <label for="start-creneau">Début du créneaux</label>
      <input name="start" id="start-creneau" type="datetime-local" v-model="creneau.debut">
    </div>
    <div class="flex-column flex-align-center">
      <label for="end-creneau">Fin du créneaux</label>
      <input name="end" id="end-creneau" type="datetime-local" v-model="creneau.fin">
    </div>
    <div class="flex-column flex-align-center">
      <input type="submit" value="Ajouter">
    </div>
  </form>
</template>