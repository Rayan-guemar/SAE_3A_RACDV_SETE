<script setup lang="ts">
import { ref } from 'vue';
import { encodedStr, getDateHours2Digits, hashCode } from '../../scripts/utils';
import { Tache, Creneau, Poste, Plage } from '../../scripts/types';
import Modal from './Modal.vue';
import { Backend } from '../../scripts/Backend';

interface Props {
	plage: Plage;
	canDelete: boolean;
}
const props = defineProps<Props>();

const deleting = ref(false);
const emit = defineEmits<{
	(e: 'delete', plage: Plage): void;
}>();

const deletePlage = async () => {
	emit('delete', props.plage);
	deleting.value = false;
};

const startDeleting = () => {
	deleting.value = true;
};

const ajoutIndispo = ref(false);
const plageDiv = ref<HTMLDivElement>();
const addIndispo = () => {
	ajoutIndispo.value = true;
};
window.addEventListener('click', e => {
	if (ajoutIndispo.value) {
		if (plageDiv.value && plageDiv.value.contains(e.target as Node)) {
			ajoutIndispo.value = true;
		} else {
			ajoutIndispo.value = false;
		}
	}
});
let debut = new Date(props.plage.debut);
let fin = new Date(props.plage.fin);
</script>
<template>
	<div
		ref="plageDiv"
		class="plage"
		:id="plage.id + ''"
		:style="{
			top: `${((debut.getHours() * 60 + debut.getMinutes()) / (24 * 60)) * 100}%`,
			height: `${((fin.getHours() * 60 + fin.getMinutes() - (debut.getHours() * 60 + debut.getMinutes())) / (24 * 60)) * 100}%`,
			width: `99%`,
			left: `0%`,
			transform: `translateX(0%)`,
			backgroundColor: `rgb(216, 230, 243)`,
			zIndex: 1
		}"
		@click="e => addIndispo()">
		{{ `${getDateHours2Digits(debut)} - ${getDateHours2Digits(fin)}`}}
		<div  v-if="canDelete" ref="deleteBtn" class="delete" @click.prevent="() => startDeleting()">
			<img src="../../../public/icons/delete.svg" alt="" />
		</div>
	</div>

	<Modal v-if="deleting">
		<form class="planning-form">
			<h5>Voulez vous vraiment supprimer cette plage ?</h5>
			<div class="flex-row">
				<div class="btn" @click="deletePlage">Oui</div>
				<div class="btn" @click="() => (deleting = false)">Non</div>
			</div>
		</form>
	</Modal>
</template>
