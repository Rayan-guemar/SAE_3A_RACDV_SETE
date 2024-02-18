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
    currentPoste: Poste,
    setCurrentPoste: (value: Poste) => void,
    updateCurrentPoste: (f: (p: Poste) => void) => void,
    posteList: Poste[],
    setPosteList: (value: Poste[]) => void,
    setPosteOpened: (value: boolean) => void,
    isOrgaOrResp: boolean,
}

const props = defineProps<Props>();

const emits = defineEmits(['close', 'reloadPostes']);

const creating = ref(false);
const updating = ref(false);
const deleting = ref(false);
const missingName = ref(false);
const sayingNo = ref(false);
const becomingRed = ref(false);
const liking = ref(false);
const neutraling = ref(false);
const disliking = ref(false);


function editing() {
    return !!props.currentPoste.id;
}   

const deletePoste = async () => {
    props.setAskingForDelete(false);
    deleting.value = true;

    await Backend.deletePoste(props.festivalId, props.currentPoste);

    props.setPosteOpened(false);

    const listeOfPostes = await Backend.getPostes(props.festivalId);
    props.setPosteList(listeOfPostes);

    deleting.value = false;
}

const updatePoste = async () => {
    if (props.askingForDelete) return;
    if (props.currentPoste.nom === '') {
        missingName.value = true;
        sayingNo.value = true;
        becomingRed.value = true;
        setTimeout(() => {
            sayingNo.value = false;
            becomingRed.value = false;
        }, 700);
        return;
    }

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
    if (props.currentPoste.nom === '') {
        missingName.value = true;
        sayingNo.value = true;
        becomingRed.value = true;
        setTimeout(() => {
            sayingNo.value = false;
            becomingRed.value = false;
        }, 700);
        return;
    }

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

const like = async () => {
    liking.value = true;
    await Backend.addPrefDegree(props.currentPoste, 1);
    liking.value = false;
    emits('close');
    emits('reloadPostes');
}

const neutral = async () => {
    neutraling.value = true;
    await Backend.addPrefDegree(props.currentPoste, 0);
    neutraling.value = false;
    emits('close');
    emits('reloadPostes');
}

const dislike = async () => {
    disliking.value = true;
    await Backend.addPrefDegree(props.currentPoste, -1);
    disliking.value = false;
    emits('close');
    emits('reloadPostes');
}

</script>

<template>
    <div class="poste-form-wrapper" >
        <div class="poste-form">
            <h3 class="top-heading">Poste</h3>  

            <input :readonly="!isOrgaOrResp" type="text" class="poste-name" :class="{ 'missing-field': missingName, 'becoming-red': becomingRed }"
                v-model="currentPoste.nom" placeholder="ex: Accueil artiste" @input="() => missingName = false" >
            <div v-if="isOrgaOrResp" class="color-wrapper">
                <div>Couleur :</div>
                <input type="color" v-model="currentPoste.couleur">
            </div>
            <textarea :readonly="!isOrgaOrResp" v-model="currentPoste.description" :placeholder="isOrgaOrResp ? 'ex: Accueillir les artistes' : 'Il n\'y a pas de description pour ce poste'"></textarea>

            <div v-if='isOrgaOrResp && editing()' class="pointer poste-action edit-poste" :class="{ loading: updating, 'saying-no': sayingNo }" @click="updatePoste">Modifier</div>
            <div v-if='isOrgaOrResp && editing()' class="pointer poste-action delete-poste" :class="{ loading: deleting }"
                @click="(e) => setAskingForDelete(true)">Supprimer</div>
            <div v-else-if="isOrgaOrResp" class="pointer poste-action create-poste" :class="{ 'missing-field': currentPoste.nom === '', loading: creating, 'saying-no': sayingNo }" @click="createPoste">Créer</div>

            <div v-if="!isOrgaOrResp && editing()" class="pointer poste-action like-poste" :class="{ loading: liking }" @click="like">J'aime</div>
            <div v-if="!isOrgaOrResp && editing()" class="pointer poste-action neutral-poste" :class="{ loading: neutraling }" @click="neutral">Indifférent</div>
            <div v-if="!isOrgaOrResp && editing()" class="pointer poste-action dislike-poste" :class="{ loading: disliking }" @click="dislike">Je n'aime pas</div>

            <div class="pointer poste-action cancel-poste" @click="closePoste">Annuler</div>
        </div>

        <div v-if="isOrgaOrResp" class="task-preview-wrapper">
            <h4 class="top-heading">Aperçu d'une tâche</h4>
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


    <Teleport v-if="askingForDelete" to="body">
        <div class="delete-poste-confirm">
            <div class="delete-poste-confirm-text">Voulez-vous vraiment supprimer ce poste ?</div>
            <div class="delete-poste-confirm-text">Cela entrainera la suppression de tous les créneaux associés à ce poste</div>
            <div class="delete-poste-confirm-btns">
                <div class="pointer delete-poste-confirm-yes" @click="deletePoste">Oui</div>
                <div class="pointer delete-poste-confirm-no" @click="(e) => setAskingForDelete(false)">Non</div>
            </div>
        </div>

        <div class="darkbackground">

        </div>
    </Teleport>
</template>