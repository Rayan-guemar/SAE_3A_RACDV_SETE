<script setup lang="ts">
import { calculCharge, dateDiff, displayHoursMinutes } from "../../scripts/utils";
import { VNodeRef, ref, onMounted, computed } from "vue";
import {
  Tache as TacheType,
  Festival,
  Poste,
  TacheCreateData,
  Benevole,
  Creneau,
  ID,
} from "../../scripts/types";
import { Backend } from "../../scripts/Backend";
import Tache from "./Tache.vue";
import Modal from "./Modal.vue";
import TacheForm from "./TacheForm.vue";
import { sortTachesByOverriding } from "../../scripts/tache";
import PlageHoraireForm from "./PlageHoraireForm.vue";
import PlageHoraire from "./PlageHoraire.vue";
import IndispoForm from "./IndispoForm.vue";

type FromArray<T extends any[]> = T extends (infer U)[] ? U : never;

type Props = {
  festID: number;
  title: string;
  dateDebut: string;
  dateFin: string;
  isOrgaOrResp: boolean;
  userId: number;
};

const props = defineProps<Props>();

const festival = ref<Festival>({
  festID: props.festID,
  title: props.title,
  dateDebut: new Date(props.dateDebut),
  dateFin: new Date(props.dateFin),
  isOrgaOrResp: props.isOrgaOrResp,
});

const numberOfDays =
  dateDiff(festival.value.dateDebut, festival.value.dateFin).day + 1;
const days = Array.from(
  { length: numberOfDays },
  (_, i) =>
    new Date(
      festival.value.dateDebut.getFullYear(),
      festival.value.dateDebut.getMonth(),
      festival.value.dateDebut.getDate() + i
    )
);
const dayNames = [
  "Dimanche",
  "Lundi",
  "Mardi",
  "Mercredi",
  "Jeudi",
  "Vendredi",
  "Samedi",
];
const monthNames = [
  "Janvier",
  "Février",
  "Mars",
  "Avril",
  "Mai",
  "Juin",
  "Juillet",
  "Août",
  "Septembre",
  "Octobre",
  "Novembre",
  "Décembre",
];
const daysDiv = ref<HTMLDivElement>();

const taches = ref<TacheType[]>([]);
const sortedTaches = ref<ReturnType<typeof sortTachesByOverriding>>([]);
const postes = ref<Poste[]>([]);
const crx = ref<Creneau[]>([]);

const benevoles = ref<Benevole[]>([]);

const chargesBenevole = computed(() => {
  const charges: Record<ID, number> = {};
  for (const benevole of benevoles.value) {
    charges[benevole.id] = calculCharge(benevole, taches.value);
  }
  return charges;
});

const loading = ref(true);
const wantsToCreateTache = ref(false);
const creatingTache = ref(false);
const creatingPlage = ref(false);
const addIndispo = ref(false);

const filterByPoste = ref("");

const displayTaches = computed(() => {
  const filters: ((tache: TacheType) => unknown)[] = [];
  if (vuePerso.value)
    filters.push((tache) => tache.benevoles?.includes(props.userId));
  if (filterByPoste.value)
    filters.push((tache) => tache.poste.id == filterByPoste.value);

  let tachesToDisplay = [...taches.value];
  for (const filter of filters) {
    tachesToDisplay = tachesToDisplay.filter((tache) => filter(tache));
  }

  return sortTachesByOverriding(tachesToDisplay);
});
const modeAffectation = ref(false);

const toggleModeAffectation = () => {
  modeAffectation.value = !modeAffectation.value;
};

const newTacheName = ref("");
const newTacheStart = ref(new Date());
const newTacheEnd = ref(new Date());

const getTaches = async () => {
  const res = await Backend.getTaches(festival.value.festID);

  if (res) {
    taches.value = res;
  }
};

const getBenevoles = async () => {
  const res = await Backend.getBenevoles(festival.value.festID);
  console.log(1, res);

  if (res) {
    benevoles.value = res;
  }
};

const getPlagesHoraires = async () => {
  const res = await Backend.getPlagesHoraires(festival.value.festID);
  if (res) {
    crx.value = res;
  }
};

const getPostes = async () => {
  const res = await Backend.getPostes(festival.value.festID);
  if (res) {
    postes.value = res;
  }
};

const scrollDaysLeft = () => {
  let div = daysDiv.value;
  if (!div) return;
  let daysWidth = div.getBoundingClientRect().width;
  let dayWidth = div.querySelector(".day")?.getBoundingClientRect().width || 0;
  let scroll = div.scrollLeft - Math.floor(daysWidth / dayWidth) * dayWidth;
  if (scroll < 0) {
    scroll = 0;
  }
  div.scrollTo({
    left: scroll,
    behavior: "smooth",
  });
};

const scrollDaysRight = () => {
  let div = daysDiv.value;
  if (!div) return;
  let daysWidth = div.getBoundingClientRect().width;
  let dayWidth = div.querySelector(".day")?.getBoundingClientRect().width || 0;
  let scroll = div.scrollLeft + Math.floor(daysWidth / dayWidth) * dayWidth;
  if (scroll > div.scrollWidth) {
    scroll = div.scrollWidth;
  }
  div.scrollTo({
    left: scroll,
    behavior: "smooth",
  });
};

const startWantingToCreateTache = () => {
  wantsToCreateTache.value = true;
};

const stopWantingToCreateTache = () => {
  wantsToCreateTache.value = false;
}; 

const startCreatingTache = (e: MouseEvent, d: Date) => {
  if (!wantsToCreateTache.value) return;

  const div = daysDiv.value;
  const divY = div?.getBoundingClientRect().top || 0;
  const divH = div?.getBoundingClientRect().height || 0;

  const mouseYWithOffset = e.clientY - divY;
  
  const nbOfQuarters = Math.floor(mouseYWithOffset / divH * 96);
  const startDate = new Date(d);
  const endDate = new Date(startDate);
  startDate.setHours(Math.floor(nbOfQuarters / 4));
  if (startDate.getHours() >= 23) {
    startDate.setHours(22);
    startDate.setMinutes(30);
    endDate.setHours(23);
    endDate.setMinutes(30);
  } else {
    startDate.setMinutes((nbOfQuarters % 4) * 15);
    endDate.setHours(startDate.getHours() + 1);
    endDate.setMinutes(startDate.getMinutes());
  }
  console.log(startDate, endDate);
  newTacheStart.value = startDate;
  newTacheEnd.value = endDate;
  creatingTache.value = true;
  wantsToCreateTache.value = false;
};

const stopCreatingTache = (tache?: TacheCreateData) => {
  creatingTache.value = false;
  if (!tache) return;
  const poste = postes.value.find((p) => {
    return p.id == tache.poste_id;
  });
  if (!poste) {
    throw new Error("pas de poste trouvé");
  }

  const t: TacheType = {
    description: tache.description,
    nbBenevole: tache.nombre_benevole,
    poste: poste,
    creneau: {
      debut: tache.date_debut,
      fin: tache.date_fin,
    },
    benevoleAffecte: 0,
    lieu: tache.lieu,
  };

  taches.value.push(t);
};

const stopCreatingPlage = async (tempCreneau?: Creneau) => {
  creatingPlage.value = false;
};

const startAddIndispo = () => {
  addIndispo.value = true;
};

const stopAddIndispo = () => {
  addIndispo.value = false;
};

const askForICS = () => {
  Backend.getICS(festival.value.festID);
};

const updateTaches = async () => {
  await getTaches();
};
const updatePlages = async () => {
  await getPlagesHoraires();
};

onMounted(async () => {
  const promises = [];
  promises.push(getTaches());
  promises.push(getPostes());
  promises.push(getPlagesHoraires());
  promises.push(getBenevoles());
  await Promise.all(promises);
  loading.value = false;
});

const vuePerso = ref(false);

const toggleVuePerso = async () => {
  vuePerso.value = !vuePerso.value;
};

const startCreatingPlage = () => {
  creatingPlage.value = true;
};

console.log(props.isOrgaOrResp);
</script>

<template>
  <Teleport to="head">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
  </Teleport>
    <div v-if="loading" id="loader"></div>

  <h2 v-if="!loading">{{ title }}</h2>

  <div v-if="!loading" id="planning">
    <div class="hours">
      <div class="hour" v-for="i in parseInt('11')">
        {{ (i * 2 < 10 ? "0" + i * 2 : i * 2) + "h00" }}
      </div>
    </div>
    <div class="days" ref="daysDiv">
      <div class="day" v-for="day of days" ref="truc" v-on="!creatingTache ? { click: (e: MouseEvent) => {console.log('coucou'); startCreatingTache(e, day)} } : {}">
        <div class="day">
          <div class="heading">
            {{
              dayNames[day.getDay()].substring(0, 3) +
              " " +
              day.getDate() +
              " " +
              monthNames[day.getMonth()].substring(0, 4) +
              "."
            }}
          </div>
          <div
            class="line-break"
            v-for="i in parseInt('11')"
            :id="`line-break-${i * 2}`"
          ></div>
          <PlageHoraire
            v-for="creneauWithPos of crx.filter(
              (c) => new Date(c.debut).getDate() === day.getDate()
            )"
            :creneau="creneauWithPos"
          />
          <!-- <Tache /> -->
          <Tache
            v-for="tacheWithPos of displayTaches.filter(
              ({ tache }) => tache.creneau.debut.getDate() === day.getDate()
            )"
            :chargesBenevole="chargesBenevole"
            :benevoles="benevoles"
            :tache="tacheWithPos.tache"
            :modeAffectation="modeAffectation"
            :position="tacheWithPos.position"
            :total="tacheWithPos.total"
            @reloadBenevoles="
              async () => {
                await getTaches();
                await getBenevoles();
              }
            "
          />
          <div
            @mousedown="() => console.log('test')"
            v-if="creatingTache && day.getDay() === newTacheStart.getDay()" 
            class="task new-task"
            :style="{
              top: (newTacheStart.getHours() * 4 + newTacheStart.getMinutes() / 15) / (4 * 24) * 100 + '%',
              height: ((newTacheEnd.getHours() - newTacheStart.getHours()) * 4 + (newTacheEnd.getMinutes() - newTacheStart.getMinutes()) / 15) / (4 * 24) * 100 + '%'
            }"
          >
            <div @mousedown="() => { console.log('caca') }" id="change-date-btn-down" class="change-date-btn"></div>
            <div id="change-date-btn-up" class="change-date-btn"></div>
            <div
              class="task-text"
              style="width: 100%"
            >
              <div class="name">Aucun poste</div>
              <div class="tache">{{ displayHoursMinutes(newTacheStart) + ' - ' + displayHoursMinutes(newTacheEnd) }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="manage-interface">
      <div
        v-if="isOrgaOrResp"
        id="add-plage-btn"
        class="btn"
        @click="startCreatingPlage"
      >
        Ajouter les plages horaires des jours du festival
      </div>

      <div>
        <label for="poste_filter">Filtrer par:</label>
        <select
          name="poste_filter"
          class="btn"
          id="poste_filter"
          v-model="filterByPoste"
        >
          <option selected="true" value="">Tous les postes</option>
          <option v-for="poste of postes" :value="poste.id">
            {{ poste.nom }}
          </option>
        </select>
      </div>

      <div v-if="isOrgaOrResp" class="toggle-mode-affectation-wrapper">
        <div
          v-if="isOrgaOrResp"
          class="toggle-mode-affectation-wrapper btn flex-align-center"
        >
          <div>Mode affectation</div>
          <div
            class="toggle-mode-affectation"
            :class="{ on: modeAffectation }"
            @click="toggleModeAffectation"
          >
            <div class="toggle"></div>
          </div>
        </div>
      </div>
      <div
        v-if="isOrgaOrResp && !wantsToCreateTache"
        id="add-creneau-btn"
        class="btn"
        @click="startWantingToCreateTache"
      >
        Ajouter un créneau
      </div>
      <div
        v-if="isOrgaOrResp && wantsToCreateTache"
        id="add-creneau-btn"
        class="disabled btn"
        @click="startWantingToCreateTache"
      >
        Cliquez à l'endroit de votre choix
      </div>

      <div id="add-ics-btn" class="btn" @click="askForICS">
        Demander un fichier ics
      </div>

      <div v-if="!isOrgaOrResp" @click="toggleVuePerso" class="switch-vue btn">
        {{ vuePerso ? "Planning général" : " Mon planning" }}
      </div>

          <div v-if="!isOrgaOrResp" id="add-indispo-btn" class="btn" @click="startAddIndispo">Prévenir d'une indisponibilité</div>
        </div>
    </div>

  <div v-if="!loading" class="scroll-btn">
    <div id="scroll-btn-left" @click="scrollDaysLeft">
      <img src="../../../public/icons/fleche-gauche.png" alt="Gauche" />
    </div>
    <div id="scroll-btn-right" @click="scrollDaysRight">
      <img src="../../../public/icons/fleche-gauche.png" alt="Droite" />
    </div>
  </div>

  <Modal v-if="false" @close="stopCreatingTache">
    <TacheForm
      class="tache-form"
      :festID="festival.festID"
      :title="festival.title"
      :dateDebut="festival.dateDebut"
      :dateFin="festival.dateFin"
      :isOrgaOrResp="festival.isOrgaOrResp"
      :postes="postes"
      :update-taches="updateTaches"
      :close="stopCreatingTache"
    />
  </Modal>
  <Modal v-if="creatingPlage" @close="stopCreatingPlage">
    <PlageHoraireForm
      :festivalId="festID"
      :dateDebut="festival.dateDebut"
      :dateFin="festival.dateFin"
      :close="stopCreatingPlage"
      :updatePlages="updatePlages"
    />
  </Modal>

  <Modal v-if="addIndispo" @close="stopAddIndispo">
    <IndispoForm
      :festivalId="festID"
      :dateDebut="festival.dateDebut"
      :dateFin="festival.dateFin"
      @close="stopAddIndispo"
    />
  </Modal>
</template>
