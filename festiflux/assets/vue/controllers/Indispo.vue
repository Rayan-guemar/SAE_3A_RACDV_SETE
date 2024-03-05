<script setup lang="ts">
import { ref } from 'vue';
import { encodedStr, getDateHours2Digits, hashCode } from '../../scripts/utils';
import { Tache, Creneau, Poste, Indisponibilite } from '../../scripts/types';
import { Backend } from '../../scripts/Backend';
import Modal from './Modal.vue';

interface Props {
	indispo: Indisponibilite;
	canDelete: boolean;
}
const props = defineProps<Props>();
const deleting = ref(false);
const emit = defineEmits<{
	(e: 'delete', indispo: Indisponibilite): void;
}>();

const deleteIndispo = async () => {
	emit('delete', props.indispo);
	deleting.value = false;
};

const startDeleting = () => {
	deleting.value = true;
};

const ajoutIndispo = ref(false);
const indispo = ref<HTMLDivElement>();

let debut = new Date(props.indispo.debut);
let fin = new Date(props.indispo.fin);
</script>
<template>
	<div
		ref="indispo"
		class="indispo"
		:id="props.indispo.id + ''"
		:style="{
			top: `${((debut.getHours() * 60 + debut.getMinutes()) / (24 * 60)) * 100}%`,
			height: `${((fin.getHours() * 60 + fin.getMinutes() - (debut.getHours() * 60 + debut.getMinutes())) / (24 * 60)) * 100}%`,
			width: `99%`,
			left: `0%`,
			transform: `translateX(0%)`,
			zIndex: 2
		}">
		{{ `${getDateHours2Digits(debut)} - ${getDateHours2Digits(fin)}` }}
		<div v-if="canDelete" ref="deleteBtn" class="delete" @click.prevent="() => startDeleting()">
			<img src="../../../public/icons/delete.svg" alt="" />
		</div>
	</div>
	<Modal v-if="deleting">
		<form class="planning-form">
			<h4>Voulez vous vraiment supprimer cette indisponibilité ?</h4>
			<div class="flex-row">
				<div class="btn" @click="deleteIndispo">Oui</div>
				<div class="btn" @click="() => (deleting = false)">Non</div>
			</div>
		</form>
	</Modal>
</template>
