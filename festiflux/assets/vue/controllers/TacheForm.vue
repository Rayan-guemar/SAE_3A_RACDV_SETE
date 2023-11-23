<script setup lang="ts">
    import { ref } from 'vue';
    import { Festival } from '../../scripts/types';

    type Props = {
        festID: number,
        title: string,
        dateDebut: string,
        dateFin: string,
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

    const startTache = ref<HTMLInputElement>();
    const endTache = ref<HTMLInputElement>();

    function changeHandlerStart() {
        startTache.value?.setAttribute("value", startTache.value.value);
        if (startTache.value && endTache.value) {
            endTache.value.setAttribute("min", startTache.value.value);
        }
    }

    function changeHandlerEnd() {
        endTache.value?.setAttribute("value", endTache.value.value);
        if (startTache.value && endTache.value) {
            startTache.value.setAttribute("max", endTache.value.value);
        }
    }
</script>

<template>
    <h2>Création d'un créneaux</h2>
    <div class="flex-column flex-align-center">
        <label for="description">Description</label>
        <input name="description" id="creneau-description" type="text">
    </div>
    <div class="flex-column flex-align-center">
        <label for="nombre_benevole">Nombre de benevole nécessaire
        </label>
        <input name="nombre_benevole" id="creneau-nombre-benevole" type="text">
    </div>
    <div class="flex-column flex-align-center">
        <label for="start-creneau">Debut du créneaux</label>
        <input name="start" id="start-creneau" ref="startTache" @change="changeHandlerStart" type="datetime-local" :value="festival.dateDebut" :min="festival.dateDebut.toISOString().split('T')[0]" :max="festival.dateFin.toISOString().split('T')[0]">
    </div>
    <div class="flex-column flex-align-center">
        <label for="end-creneau">Fin du créneaux</label>
        <input name="end" id="end-creneau" ref="endTache" @change="changeHandlerEnd" type="datetime-local" :value="festival.dateFin" :min="festival.dateDebut.toISOString().split('T')[0]" :max="festival.dateFin.toISOString().split('T')[0]">
    </div>
    <div class="flex-column flex-align-center">
        <label for="poste">Choisissez un poste</label>
        <select name="poste" id="creneau-poste-select"></select>
    </div>
    <div id="create-creneau-btn" class="btn">Créer</div>
</template>