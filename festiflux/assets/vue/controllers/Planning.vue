<script setup lang="ts">
    import { VNodeRef, ref, onMounted } from 'vue';
    import { dateDiff } from '../../scripts/utils';
    import { Tache as TacheType, Festival, Poste, TacheCreateData } from '../../scripts/types';
    import { Backend } from '../../scripts/Backend';
    import Tache from './Tache.vue';
    import Modal from './Modal.vue';
    import TacheForm from './TacheForm.vue';
    import { sortTachesByOverriding } from '../../scripts/tache';


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

    const loading = ref(true);
    const creatingTache = ref(false);


    const getTaches = async () => {
        const res = await Backend.getTaches(festival.value.festID);

        if (res) {
            taches.value = res;
            sortedTaches.value = sortTachesByOverriding(res);
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
        console.log("test");
        creatingTache.value = false;
        if (tache) {
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
            }
    
            
            sortedTaches.value = sortTachesByOverriding([...taches.value, t]);
        }

    }

    const askForICS = () => {
        Backend.getICS(festival.value.festID);
    }

    const updateTaches = async () => {
        await getTaches();
    }

    onMounted(async () => {
        await getTaches();
        await getPostes();
        loading.value = false;
    })

    const getTachesByBenevole = async () => {
        const res = await Backend.getTacheByBenevole(festival.value.festID, props.userId );

        console.log(res);
        

        if (res) {
            taches.value = res;
            sortedTaches.value = sortTachesByOverriding(res);
        }
    }

    const vuePerso = ref(false);

    const toggleVuePerso = async () => {

        if(vuePerso) {
            await getTachesByBenevole();
        }
        else {
            await getTaches();
        }
        vuePerso.value = !vuePerso.value;
    }


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
                    <!-- <Tache /> -->
                    <Tache v-for="tacheWithPos of sortedTaches.filter(({tache}) => tache.creneau.debut.getDate() === day.getDate())" :tache="tacheWithPos.tache" :position="tacheWithPos.position" :total="tacheWithPos.total" />
                </div>
            </div>
        </div>
        <div class="manage-interface">
            <div v-if="isOrgaOrResp" id="add-creneau-btn" class="btn" @click="startCreatingTache">Ajouter un créneau</div>

            <div id="add-ics-btn" class="btn" @click="askForICS">Demander un fichier ics</div>

            <div @click="toggleVuePerso" class="switch-vue btn "> {{ vuePerso ? 'Planning général' : ' Mon planning'}} </div>
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
        id="add-poste"
        title="Ajout d'un poste"
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
</template>