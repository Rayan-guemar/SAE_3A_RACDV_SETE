<script setup lang="ts">
    import { ref } from 'vue';
    import { Festival, Poste, TacheCreateData } from '../../scripts/types';
    import { Backend } from '../../scripts/Backend';

    type Props = {
        festID: number;
        title: string;
        dateDebut: string;
        dateFin: string;
        isOrgaOrResp: boolean;
        postes: Poste[];
        updateTaches: (t:TacheCreateData) => void
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

     const createTache = async (e: Event) => {
        const formData = new FormData(e.target as HTMLFormElement)
        
        const debut = new Date(formData.get("start") + "");
        const fin = new Date(formData.get("end") + "");
        const description = formData.get('description') + "";
        const nbBenevole = +(formData.get('nombre_benevole') + "");
        const posteId = formData.get('poste') + "";

        const tache: TacheCreateData = {
            description: description,
            nombre_benevole: nbBenevole,
            poste_id: posteId,
            date_debut: debut,
            date_fin: fin,
            lieu: formData.get('creneau-lieu') + "",
            adresse: formData.get('creneau-lieu-address') + ""
        };

        await Backend.addTache(festival.value.festID, tache);
        props.updateTaches(tache)
    };
</script>

<template>
    <form @submit.prevent="createTache">
        <h2>Création d'un créneaux</h2 >
        <div class="flex-column flex-align-center">
            <label for="description">Description</label>
            <input name="description" id="creneau-description" type="text">
        </div>
        <div class="flex-column flex-align-center">
            <label for="nombre_benevole">Nombre de benevole nécessaire
            </label>
            <input name="nombre_benevole" id="creneau-nombre-benevole" type="number">
        </div>
        <div class="flex-column flex-align-center">
            <label for="start-creneau">Debut du créneaux</label>
            <input name="start" id="start-creneau" ref="startTache" type="datetime-local" :value="festival.dateDebut" @change="changeHandlerStart">
        </div>
        <div class="flex-column flex-align-center">
            <label for="end-creneau">Fin du créneaux</label>
            <input name="end" id="end-creneau" ref="endTache" type="datetime-local" :value="festival.dateFin" @change="changeHandlerEnd">
        </div>

        <div class="flex-column flex-align-center">
            <label for="lieuTache">Lieu du créneau</label>
            <input type='text' name='creneau-lieu' id="creneau-lieu">
        </div>

        <div class="flex-column flex-align-center">
            <label for="lieuTache">Addresse du Lieu (optionnelle) </label>
            <input type='text' name='creneau-lieu-address' id="creneau-lieu-address" >
        </div>


        <div class="flex-column flex-align-center">
            <label for="poste">Choisissez un poste</label>
            <select name="poste" id="creneau-poste-select">
                <option v-for="poste in postes" :value="poste.id">{{ poste.nom }}</option> 
            </select>
        </div>
        <button type="submit" id="create-creneau-btn" class="btn" value="Créer un créneau">Créer</button>
    </form>
</template>