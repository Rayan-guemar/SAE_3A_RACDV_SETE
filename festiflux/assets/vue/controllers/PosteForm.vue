<script setup lang="ts" >
import { ref } from 'vue';
import type { Ref } from 'vue';
import { Backend } from "../../scripts/Backend";
import { Poste } from '../../scripts/types';

interface Props {
    festivalId: number,
    askingForDelete: boolean,
    setAskingForDelete: (value: boolean) => void
    posteOpened: boolean,
    setPosteOpened: (value: boolean) => void,
    currentPoste: Poste,
    setCurrentPoste: (value: Poste) => void,
    updateCurrentPoste: (f: (p: Poste) => void) => void,
    posteList: Poste[],
    setPosteList: (value: Poste[]) => void,
}

const props = defineProps<Props>();

const creating = ref(false);
const updating = ref(false);
const deleting = ref(false);


function editing() {
    return props.currentPoste.id !== null;
}

const deletePoste = async () => {
    console.log(props);

    if (props.askingForDelete) return;
    deleting.value = true;

    await Backend.deletePoste(props.festivalId, props.currentPoste);

    props.setPosteOpened(false);

    const listeOfPostes = await Backend.getPostes(props.festivalId);
    props.setPosteList(listeOfPostes);

    deleting.value = false;
}

const updatePoste = async () => {
    if (props.askingForDelete) return;
    updating.value = true;

    props.updateCurrentPoste((p) => {
        p.nom = props.currentPoste.nom.trim();
        p.description = props.currentPoste.description.trim();
    })

    await Backend.updatePoste(props.festivalId, props.currentPoste);

    props.setPosteOpened(false);

    const listeOfPostes = await Backend.getPostes(props.festivalId);
    props.setPosteList(listeOfPostes);

    updating.value = false;
}

const createPoste = async () => {
    if (props.askingForDelete) return;
    creating.value = true;

    props.updateCurrentPoste((p) => {
        p.nom = props.currentPoste.nom.trim();
        p.description = props.currentPoste.description.trim();
    })

    await Backend.addPoste(props.festivalId, props.currentPoste);

    props.setPosteOpened(false);

    const listeOfPostes = await Backend.getPostes(props.festivalId);
    props.setPosteList(listeOfPostes);

    creating.value = false;
}

const closePoste = () => {
    if (props.askingForDelete) return;
    props.setPosteOpened(false);
}

</script>

<template>
    <div class="poste-form-wrapper" :class="{ blurred: askingForDelete }">
        <div class="poste-form">
            <h2 class="top-heading">Poste</h2>

            <input type="text" class="poste-name" :class="{ 'missing-field': currentPoste.nom === '' }"
                v-bind="currentPoste.nom" placeholder="ex: Accuei artiste">
            <div class="color-wrapper">
                <div>Couleur :</div>
                <input type="color" v-bind="currentPoste.couleur">
            </div>
            <textarea v-bind="currentPoste.description" placeholder="ex: Accueillir les artistes"></textarea>

            <div v-if='editing()' class="pointer edit-poste" :class="{ loading: updating }" @click="updatePoste">Modifier
            </div>
            <div v-if='editing()' class="pointer delete-poste" :class="{ loading: deleting }"
                @click="(e) => setAskingForDelete(true)">Supprimer</div>
            <div v-else class="pointer create-poste"
                :class="{ 'missing-field': currentPoste.nom === '', loading: creating }" @click="createPoste">Créer</div>

            <div class="pointer cancel-poste" @click="closePoste">Annuler</div>
        </div>

        <div class="task-preview-wrapper">
            <h4 class="top-heading">Aperçu d'une tache</h4>
            <div class="task-preview" :style="{
                'border-color': currentPoste.couleur,
                'background-color': currentPoste.couleur + '1A',
                'color': currentPoste.couleur
            }">

                <div class="name">{{ currentPoste.nom !== '' ? currentPoste.nom : 'Nom poste' }}</div>
                <div class="tache">
                    22h00 - 23h00
                </div>
            </div>
        </div>
    </div>


    <div v-if="askingForDelete" class="delete-poste-confirm">
        <div class="delete-poste-confirm-text">Voulez-vous vraiment supprimer ce poste ?</div>
        <div class="delete-poste-confirm-text">Cela entrainera la suppression de tous les créneaux associés à ce poste</div>
        <div class="delete-poste-confirm-btns">
            <div class="pointer delete-poste-confirm-yes" @click="deletePoste">Oui</div>
            <div class="pointer delete-poste-confirm-no" @click="(e) => setAskingForDelete(false)">Non</div>
        </div>
    </div>
</template>