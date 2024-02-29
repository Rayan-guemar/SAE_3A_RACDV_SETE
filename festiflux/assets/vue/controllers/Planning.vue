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
Plage,
} from "../../scripts/types";
import { Backend } from "../../scripts/Backend";
import Tache from "./Tache.vue";
import Modal from "./Modal.vue";
import TacheForm from "./TacheForm.vue";
import { sortTachesByOverriding } from "../../scripts/tache";
import PlageHoraireForm from "./PlageHoraireForm.vue";
import PlageHoraire from "./PlageHoraire.vue";
import IndispoForm from "./IndispoForm.vue";
/////////////translate///////////////

let lang = document.getElementById('app').getAttribute('data-locale');
if (lang ==='null') {
  lang = 'fr';
}
function translate(key: string) {
  if (lang === 'fr') {
    switch (key) {
      case 'add-plage-btn':
        return 'Ajouter les plages horaires';
      case 'filter-btn':
        return 'Filtrer par :';
      case 'all-postes':
        return 'Tous les postes';
      case 'add-creneau-btn':
        return 'Ajouter un créneau';
      case 'add-creneau-btn2':
        return "Cliquez à l'endroit de votre choix";
      case 'add-ics-btn':
        return 'Demander un fichier ics';
      case 'affect-switch':
        return 'Mode affectation';
      case 'add-indispo-btn':
        return "Prévenir d'une indisponibilité";
      default:
        return key;
    }
  } else {
    switch (key) {
      case 'add-plage-btn':
        return 'add time slots for festival';
      case 'filter-btn':
          return 'Filter by :';
      case 'all-postes':
        return 'All jobs';
      case 'add-creneau-btn':
        return 'Add a time job';
      case 'add-creneau-btn2':
        return 'Click where you want to add the job';
      case 'add-ics-btn':
        return 'Ask for an ics file';
      case 'affect-switch':
        return 'Affectation mode';
      case 'add-indispo-btn':
        return 'Warn of unavailability';
      default:
        return key;
    }
  }
}
////////////////////////////
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

const newTacheName = ref("Aucun poste sélectionné");
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

const updateCurrentPosteName = (posteName: string) => {
  console.log(posteName);
  console.log('test');
  newTacheName.value = posteName;
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

onMounted(async () => {
  const promises = [];
  promises.push(getTaches());
  promises.push(getPostes());
  promises.push(getBenevoles());
  await Promise.all(promises);
  loading.value = false;
});

const startResizingStart = (e: MouseEvent) => {
  const div = daysDiv.value;
  const divY = div?.getBoundingClientRect().top || 0;
  const divH = div?.getBoundingClientRect().height || 0;

  const listener = (e: MouseEvent) => {
    const mousePos = e.clientY - divY;
    let nbOfQuarters = Math.floor(mousePos / divH * 96);
    if (nbOfQuarters < 0) {
      nbOfQuarters = 0;
    } else if (nbOfQuarters > newTacheEnd.value.getHours() * 4 + newTacheEnd.value.getMinutes() / 15 - 4) {
      nbOfQuarters = newTacheEnd.value.getHours() * 4 + newTacheEnd.value.getMinutes() / 15 - 4;
    }
    const startDate = new Date(newTacheStart.value);
    startDate.setHours(Math.floor(nbOfQuarters / 4));
    startDate.setMinutes((nbOfQuarters % 4) * 15);
    newTacheStart.value = startDate;
  };

  document.addEventListener('mousemove', listener);
  document.addEventListener('mouseup', () => {
    document.removeEventListener('mousemove', listener);
  });
};

const startResizingEnd = (e: MouseEvent) => {
  const div = daysDiv.value;
  const divY = div?.getBoundingClientRect().top || 0;
  const divH = div?.getBoundingClientRect().height || 0;

  const listener = (e: MouseEvent) => {
    const mousePos = e.clientY - divY;
    let nbOfQuarters = Math.floor(mousePos / divH * 96);
    if (nbOfQuarters >= 24 * 4) {
      const endDate = new Date(newTacheEnd.value);
      endDate.setHours(23);
      endDate.setMinutes(59);
      newTacheEnd.value = endDate;
    } else {
      if (nbOfQuarters < newTacheStart.value.getHours() * 4 + newTacheStart.value.getMinutes() / 15 + 4) {
        nbOfQuarters = newTacheStart.value.getHours() * 4 + newTacheStart.value.getMinutes() / 15 + 4;
      }
      const endDate = new Date(newTacheEnd.value);
      endDate.setHours(Math.floor(nbOfQuarters / 4));
      endDate.setMinutes((nbOfQuarters % 4) * 15);
      newTacheEnd.value = endDate;
    }
  };

  document.addEventListener('mousemove', listener);
  document.addEventListener('mouseup', () => {
    document.removeEventListener('mousemove', listener);
  });
};

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
      <div class="day" v-for="day of days" ref="truc" v-on="!creatingTache ? { click: (e: MouseEvent) => {startCreatingTache(e, day)} } : {}">
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
          <!-- <Tache /> -->
          <Tache
            v-for="tacheWithPos of displayTaches.filter(
              ({ tache }) => tache.creneau.debut.getDate() === day.getDate()
            )"
            :festID="festID"
            :chargesBenevole="chargesBenevole"
            :benevoles="benevoles"
            :tache="tacheWithPos.tache"
            :modeAffectation="modeAffectation"
            :position="tacheWithPos.position"
            :total="tacheWithPos.total"
            @reloadTaches="
              async () => {
                await getTaches();
                await getBenevoles();
              }
            "
            :lang="lang"
            @reloadBenevoles="
              async () => {
                await getTaches();
                await getBenevoles();
              }
            "
          />
        </div>
      </div>
    </div>
    <div class="manage-interface">
      <div>
        <label for="poste_filter">{{ translate("filter-btn") }}</label>
        <select
          name="poste_filter"
          class="btn"
          id="poste_filter"
          v-model="filterByPoste"
        >
          <option selected="true" value="">{{ translate("all-postes") }}</option>
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
          <div>{{ translate("affect-switch") }}</div>
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
        {{ translate("add-creneau-btn") }}
      </div>
      <div
          v-if="isOrgaOrResp && wantsToCreateTache"
          id="add-creneau-btn"
          class="disabled btn"
          @click="startWantingToCreateTache"
      >
        {{ translate("add-creneau-btn2") }}
      </div>
      <div id="add-ics-btn" class="btn" @click="askForICS">
        {{ translate("add-ics-btn") }}
      </div>

      <div v-if="!isOrgaOrResp" @click="toggleVuePerso" class="switch-vue btn">
        {{ vuePerso ? "Planning général" : " Mon planning" }}
      </div>

          <div v-if="!isOrgaOrResp" id="add-indispo-btn" class="btn" @click="startAddIndispo">{{ translate("add-indispo-btn") }}</div>
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

  <TacheForm
    v-if="creatingTache"
    class="tache-form right"
    :festID="festival.festID"
    :title="festival.title"
    :dateDebut="newTacheStart"
    :dateFin="newTacheEnd"
    :isOrgaOrResp="festival.isOrgaOrResp"
    :postes="postes"
    :update-taches="updateTaches"
    :close="stopCreatingTache"
    @posteChange="updateCurrentPosteName"
    :lang="lang"
  />

  <!-- <Modal v-if="false" @close="stopCreatingTache">

  </Modal> -->
  <Modal v-if="addIndispo" @close="stopAddIndispo">
    <IndispoForm
      :festivalId="festID"
      :dateDebut="festival.dateDebut"
      :dateFin="festival.dateFin"
      @close="stopAddIndispo"
      :lang="lang"
    />
  </Modal>
</template>
