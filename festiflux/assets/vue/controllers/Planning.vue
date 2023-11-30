<script setup lang="ts">
    import { calculCharge, dateDiff } from '../../scripts/utils';
    import { VNodeRef, ref, onMounted, computed } from 'vue';
    import { Tache as TacheType, Festival, Poste, TacheCreateData, Benevole, Creneau, ID } from '../../scripts/types';
    import { Backend } from '../../scripts/Backend';
    import Tache from './Tache.vue';
    import Modal from './Modal.vue';
    import TacheForm from './TacheForm.vue';
    import { sortTachesByOverriding } from '../../scripts/tache';
    import PlageHoraireForm from "./PlageHoraireForm.vue";
    import PlageHoraire from "./PlageHoraire.vue";
    import IndispoForm from "./IndispoForm.vue";

type FromArray<T extends any[]> = T extends (infer U)[] ? U : never ; 

    type Props = {
        festID: number,
        title: string,
        dateDebut: string,
        dateFin: string,
        isOrgaOrResp: boolean,
        userId: number,
    }

    const props = defineProps<Props>();

    const festival = ref<Festival>({
        festID: props.festID,
        title: props.title,
        dateDebut: new Date(props.dateDebut),
        dateFin: new Date(props.dateFin),
        isOrgaOrResp: props.isOrgaOrResp,
    })

    

    const numberOfDays = dateDiff(festival.value.dateDebut, festival.value.dateFin).day + 1;
    const days = Array.from({ length: numberOfDays }, (_, i) => new Date(festival.value.dateDebut.getFullYear(), festival.value.dateDebut.getMonth(), festival.value.dateDebut.getDate() + i));
    const dayNames = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
    const monthNames = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
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
    })

    const loading = ref(true);
    const creatingTache = ref(false);
    const creatingPlage = ref(false);
    const addIndispo = ref(false);

    const filterByPoste = ref("");

    const displayTaches = computed (() => {
        
        const filters: ((tache: TacheType) => unknown)[] = []
        if (vuePerso.value) filters.push((tache) => tache.benevoles?.includes(props.userId))
        if (filterByPoste.value) filters.push(tache => tache.poste.id == filterByPoste.value)
        
        
        let tachesToDisplay = [...taches.value]
        for (const filter of filters) {
            tachesToDisplay = tachesToDisplay.filter((tache) => filter(tache))
        }

        return sortTachesByOverriding(tachesToDisplay);
    })
    const modeAffectation = ref(false);

    const toggleModeAffectation = () => {
        modeAffectation.value = !modeAffectation.value;
        
    }


    const getTaches = async () => {
        const res = await Backend.getTaches(festival.value.festID);

        if (res) {
            taches.value = res;
        }
    }
    
    const getBenevoles = async () => {
        const res = await Backend.getBenevoles(festival.value.festID);
        console.log(1, res);
        
        if (res) {
            benevoles.value = res;
        }                
    }

    const getPlagesHoraires = async () => {
        const res = await Backend.getPlagesHoraires(festival.value.festID);
        if (res) {
            crx.value = res;
        }
    }

    const getPostes = async () => {
        const res = await Backend.getPostes(festival.value.festID);
        if (res) {
            postes.value = res;
        }
    }

    const scrollDaysLeft = () => {
        let div = daysDiv.value;
        if (!div) return;
        let daysWidth = div.getBoundingClientRect().width;
        let dayWidth = div.querySelector('.day')?.getBoundingClientRect().width || 0;
        let scroll = div.scrollLeft - Math.floor(daysWidth / dayWidth) * dayWidth;
        if (scroll < 0) {
            scroll = 0;
        }
        div.scrollTo({
            left: scroll,
            behavior: 'smooth'
        });
    }

    const scrollDaysRight = () => {
        let div = daysDiv.value;
        if (!div) return;
        let daysWidth = div.getBoundingClientRect().width;
        let dayWidth = div.querySelector('.day')?.getBoundingClientRect().width || 0;
        let scroll = div.scrollLeft + Math.floor(daysWidth / dayWidth) * dayWidth;
        if (scroll > div.scrollWidth) {
            scroll = div.scrollWidth;
        }
        div.scrollTo({
            left: scroll,
            behavior: 'smooth'
        });
    }

    const startCreatingTache = () => {
        creatingTache.value = true;
    }


    const stopCreatingTache = (tache?: TacheCreateData) => {
        creatingTache.value = false;
        if (!tache) return;
        const poste = postes.value.find(
            (p) => {
                return p.id == tache.poste_id
        });
        if (!poste) {
            throw new Error("pas de poste trouvé")
        }

        const t: TacheType = {
            description: tache.description,
            nbBenevole: tache.nombre_benevole,
            poste: poste,
            creneau: {
                debut: tache.date_debut,
                fin: tache.date_fin
            },
            benevoleAffecte: 0,
            lieu: tache.lieu,
        };
        
        taches.value.push(t);

    }

    const stopCreatingPlage = async (tempCreneau?: Creneau, update?: Promise<any>) => {
        creatingPlage.value = false;
        if (tempCreneau && update) {
            crx.value.push(tempCreneau as Creneau);
            await update;
            getPlagesHoraires(); 
        }
    }

    const startAddIndispo = () => {
      addIndispo.value = true;
    }

    const stopAddIndispo = () => {
      addIndispo.value = false;
    }

    const askForICS = () => {
        Backend.getICS(festival.value.festID);
    }

    const updateTaches = async () => {
        await getTaches();
    }

    onMounted(async () => {
        const promises = [];
        promises.push(getTaches());
        promises.push(getPostes());
        promises.push(getPlagesHoraires());
        promises.push(getBenevoles());
        await Promise.all(promises);
        loading.value = false;
    })

    const vuePerso = ref(false);

    const toggleVuePerso = async () => {
        vuePerso.value = !vuePerso.value;
    }

    const startCreatingPlage = () => {
        creatingPlage.value = true;
    }

    console.log(props.isOrgaOrResp);
    

</script>

<template>
    <div v-if="loading" id="loader"></div>

    <h2 v-if="!loading" :class="{'blurred': creatingTache}">{{ title }}</h2>

    <div v-if="!loading" id="planning" :class="{'blurred': creatingTache}">
        <div class="hours">
            <div class="hour" v-for="i in parseInt('11')">{{ ((i * 2) < 10 ? '0' + (i * 2) : (i * 2)) + 'h00' }}</div>
        </div>
        <div class="days" ref="daysDiv">
            <div class="day" v-for="day of days" ref="truc">
                <div class="day">
                    <div class="heading">
                        {{ dayNames[day.getDay()].substring(0, 3) + ' ' + day.getDate() + ' ' + monthNames[day.getMonth()].substring(0, 4) + '.' }}
                    </div>
                    <div class="line-break" v-for="i in parseInt('11')" :id="`line-break-${(i * 2)}`"></div>
                    <PlageHoraire v-for="creneauWithPos of crx.filter((c) => (new Date(c.debut)).getDate() === day.getDate())" :creneau="creneauWithPos" />
                    <!-- <Tache /> -->
                    <Tache 
                        v-for="tacheWithPos of displayTaches.filter(({tache}) => tache.creneau.debut.getDate() === day.getDate())"
                        :chargesBenevole="chargesBenevole"
                        :benevoles="benevoles" 
                        :tache="tacheWithPos.tache"
                        :modeAffectation="modeAffectation"
                        :position="tacheWithPos.position" 
                        :total="tacheWithPos.total" 
                        @reloadBenevoles="async () => {
                            await getTaches();
                            await getBenevoles();
                        }"
                    />
                </div>
            </div>
        </div>
        <div class="manage-interface">
            <div v-if="isOrgaOrResp" id="add-plage-btn" class="btn" @click="startCreatingPlage">Ajouter les plages horaires des jours du festival</div>

            <div>
                <label for="poste_filter">Filtrer par:</label>
                <select name="poste_filter" class="btn" id="poste_filter" v-model="filterByPoste"> 
                    <option selected="true" value="">Tous les postes</option>
                    <option v-for="poste of postes" :value="poste.id">{{ poste.nom }}</option>
                </select>
            </div>
            
            <div v-if="isOrgaOrResp" class="toggle-mode-affectation-wrapper">
                <div v-if="isOrgaOrResp" class="toggle-mode-affectation-wrapper btn flex-align-center">
                    <div>Mode affectation :</div>
                    <div class="toggle-mode-affectation" :class="{on: modeAffectation}" @click="toggleModeAffectation">
                        <div class="toggle"></div>
                    </div>
                </div>
            </div>
            <div v-if="isOrgaOrResp" id="add-creneau-btn" class="btn" @click="startCreatingTache">Ajouter un créneau</div>

            <div id="add-ics-btn" class="btn" @click="askForICS">Demander un fichier ics</div>

            <div v-if="!isOrgaOrResp" @click="toggleVuePerso" class="switch-vue btn "> {{ vuePerso ? 'Planning général' : ' Mon planning'}} </div>

          <div v-if="!isOrgaOrResp" id="add-indispo-btn" class="btn" @click="startAddIndispo">Prévenir d'une indisponibilité</div>
        </div>
    </div>

    <div v-if="!loading" class="scroll-btn" :class="{'blurred': creatingTache}">
        <div id="scroll-btn-left" @click="scrollDaysLeft" >
            <img src="../../../public/icons/fleche-gauche.png" alt="Gauche">
        </div>
        <div id="scroll-btn-right" @click="scrollDaysRight">
            <img src="../../../public/icons/fleche-gauche.png" alt="Droite">
        </div>
    </div>


    <Modal
        v-if="creatingTache"
     >
        <TacheForm
            :festID="festival.festID"
            :title="festival.title"
            :dateDebut="festival.dateDebut.toISOString()"
            :dateFin="festival.dateFin.toISOString()"
            :isOrgaOrResp="festival.isOrgaOrResp"
            :postes="postes"
            :update-taches="updateTaches"
            :close="stopCreatingTache"
        />
    </Modal>
  <Modal
        v-if="creatingPlage"
        id="add-plage"
        title="Ajout des plages horaires"
        :hideModal="stopCreatingPlage" >
        <PlageHoraireForm 
            :festivalId="festID" 
            :dateDebut="festival.dateDebut.toISOString()"
            :dateFin="festival.dateFin.toISOString()" 
            @close="stopCreatingPlage" />
        />
  </Modal>

  <Modal
      v-if="addIndispo"
      id="add-indispo"
      title="Ajout d'une indisponibilité'"
      >
    <IndispoForm
        :festivalId="festID"
        :dateDebut="festival.dateDebut.toISOString()"
        :dateFin="festival.dateFin.toISOString()" 
        @close="stopAddIndispo"
    />
  </Modal>
</template>