<script setup lang="ts">
    import { VNodeRef, ref } from 'vue';
    import { dateDiff, assetsPath } from '../../scripts/utils';
    import { Tache as TacheType } from '../../scripts/types';
    import { Backend } from '../../scripts/Backend';
    import Tache from './Tache.vue';
    import ModalForm from './ModalForm.vue';


    type Props = {
        festID: number,
        title: string,
        dateDebut: string,
        dateFin: string,
        isOrgaOrResp: boolean,
    }

    interface Festival {
        festID: number,
        title: string,
        dateDebut: Date,
        dateFin: Date,
        isOrgaOrResp: boolean,
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
    const loading = ref(true);
    const creatingPoste = ref(false);
    const creatingTache = ref(false);

    const getTaches = async () => {
        const res = await Backend.getTaches(festival.value.festID);
        if (res) {
            taches.value = res;
        }
    }

    function scrollDaysLeft() {
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

    function scrollDaysRight() {
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

    (async () => {
        await getTaches();
        loading.value = false;
    })()
</script>

<template>
    <div v-if="loading" id="loader"></div>

    <h2 v-if="!loading">{{ title }}</h2>

    <div v-if="!loading" id="planning" class="loading">
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
                    <Tache v-for="tache of taches.filter(t => t.creneau.debut.getDate() === day.getDate())" :tache="tache" :position="tache.poste.id" :total="taches.length" />
                </div>
            </div>
        </div>
        <div class="manage-interface">
            <h4>Liste des postes</h4>
            <div class="postes"></div>

            <div v-if="isOrgaOrResp" id="add-poste-btn" class="btn" @click="() => creatingPoste = true">Créer un poste</div>
            <div v-if="isOrgaOrResp" id="add-creneau-btn" class="btn">Ajouter un créneau</div>

            <div id="add-ics-btn" class="btn">Demander un fichier ics</div>
        </div>
    </div>

    <div v-if="!loading" class="scroll-btn">
        <div id="scroll-btn-left" @click="scrollDaysLeft" >
            <img src="../../../public/icons/fleche-gauche.png" alt="Gauche">
        </div>
        <div id="scroll-btn-right" @click="scrollDaysRight">
            <img src="../../../public/icons/fleche-gauche.png" alt="Droite">
        </div>
    </div>

    <ModalForm 
        v-if="creatingPoste"
        id="add-poste"
        title="Ajout d'un poste"
        :inputs="[
            {
                label: 'Intitulé du poste',
                type: 'text',
                id: 'poste-name',
                name: 'poste-n',
            }
        ]"
        :submitBtn="{
            id: 'create-poste-btn',
            text: 'Créer',
        }"
        :active="creatingPoste"
    />
</template>