<script setup lang="ts">

import {Creneau, Festival, Poste, TacheCreateData} from "../../scripts/types";
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


async function createCreneau(e: Event) {

  const res = await Backend.addHeureDepartFin(props.festivalId, creneau.value);

  if (res) {
    creneau.value = res;
  }
}

</script>
<template>
  <form @submit.prevent="createCreneau">
    <h2>Ajout d'une plage horaire</h2>
    <div class="flex-column flex-align-center">
      <label for="start-creneau">Debut du créneaux</label>
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