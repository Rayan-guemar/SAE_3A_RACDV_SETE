<script setup lang="ts" >
import { ref } from 'vue';
import type { Ref } from 'vue';
import { Backend } from "../../scripts/Backend";
import { Poste } from '../../scripts/types';
import {trans} from "../../../vendor/symfony/ux-translator/assets";

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
    lang: string,
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

function translate(key: string) {
  if (props.lang === 'fr') {
    switch (key) {
      case 'title': return 'Poste';
      case 'color' : return 'Couleur :';
      case 'ex' : return 'ex: Faire le run de Michael Jackson de la loge à la scène principale';
      case 'nodesc' : return 'Pas de description';
      case 'modify' : return 'Modifier';
      case 'delete' : return 'Supprimer';
      case 'create' : return 'Créer';
      case 'love' : return "J\'aime";
      case 'like' : return "Indifférent";
      case 'have' : return "Je n'aime pas";
      case 'cancel' : return "Annuler";
      case 'display' : return "Aperçu";
      case 'confirm' : return "Voulez-vous vraiment supprimer ce poste ?";
      case 'explain' : return "Cela entrainera la suppression de tous les créneaux associés à ce poste";
      case 'yes' : return "Oui";
      case 'no' : return "Non";
    }
  } else {
    switch (key) {
      case 'title': return 'Job';
      case 'color' : return 'Couleur :';
      case 'ex' : return 'ex: Run Michael jackson from the loge to the mail stage';
      case 'nodesc' : return 'No description';
      case 'modify' : return 'Modify';
      case 'delete' : return 'Delete';
      case 'create' : return 'Create';
      case 'love' : return "Love";
      case 'like' : return "Like";
      case 'have' : return "Dislike";
      case 'cancel' : return "Cancel";
      case 'display' : return "Preview";
      case 'confirm' : return "Do you really want to delete this job?";
      case 'explain' : return "This will delete all the slots associated with this job";
      case 'yes' : return "Yes";
      case 'no' : return "No";
    }
  }
}

</script>

<template>
    <div class="poste-form-wrapper" >
        <div class="poste-form">
            <h3 class="top-heading">{{ translate("title") }}</h3>

            <input :readonly="!isOrgaOrResp" type="text" class="poste-name" :class="{ 'missing-field': missingName, 'becoming-red': becomingRed }"
                v-model="currentPoste.nom" placeholder="ex : RUN" @input="() => missingName = false" >
            <div v-if="isOrgaOrResp" class="color-wrapper">
                <div>{{ translate('color') }}</div>
                <input type="color" v-model="currentPoste.couleur">
            </div>
            <textarea :readonly="!isOrgaOrResp" v-model="currentPoste.description" :placeholder="isOrgaOrResp ? translate('ex') : translate('nodesc')"></textarea>

            <div v-if='isOrgaOrResp && editing()' class="pointer poste-action edit-poste" :class="{ loading: updating, 'saying-no': sayingNo }" @click="updatePoste">
              {{ translate('modify') }}</div>
            <div v-if='isOrgaOrResp && editing()' class="pointer poste-action delete-poste" :class="{ loading: deleting }"
                @click="(e) => setAskingForDelete(true)">{{ translate('delete') }}</div>
            <div v-else-if="isOrgaOrResp" class="pointer poste-action create-poste" :class="{ 'missing-field': currentPoste.nom === '', loading: creating, 'saying-no': sayingNo }" @click="createPoste">{{ translate('create') }}</div>

            <div v-if="!isOrgaOrResp && editing()" class="pointer poste-action like-poste" :class="{ loading: liking }" @click="like">{{ translate('love') }}</div>
            <div v-if="!isOrgaOrResp && editing()" class="pointer poste-action neutral-poste" :class="{ loading: neutraling }" @click="neutral">{{ translate('like') }}</div>
            <div v-if="!isOrgaOrResp && editing()" class="pointer poste-action dislike-poste" :class="{ loading: disliking }" @click="dislike">{{ translate('have') }}</div>

            <div class="pointer poste-action cancel-poste" @click="closePoste">{{ translate('cancel') }}</div>
        </div>

        <div v-if="isOrgaOrResp" class="task-preview-wrapper">
            <h4 class="top-heading">{{ translate('display') }}</h4>
            <div class="task-preview" :style="{
                'border-color': currentPoste.couleur,
                'background-color': currentPoste.couleur + '1A',
                'color': currentPoste.couleur
            }">

                <div class="name">{{ currentPoste.nom !== '' ? currentPoste.nom : 'RUN' }}</div>
                <div class="tache">
                    22h00 - 23h00
                </div>
            </div>
        </div>
    </div>


    <Teleport v-if="askingForDelete" to="body">
        <div class="delete-poste-confirm">
            <div class="delete-poste-confirm-text">{{translate('confirm')}}</div>
            <div class="delete-poste-confirm-text">{{
                translate('explain')
              }}</div>
            <div class="delete-poste-confirm-btns">
                <div class="pointer delete-poste-confirm-yes" @click="deletePoste">{{ translate('yes') }}</div>
                <div class="pointer delete-poste-confirm-no" @click="(e) => setAskingForDelete(false)">{{translate('no')}}</div>
            </div>
        </div>

        <div class="darkbackground">

        </div>
    </Teleport>
</template>