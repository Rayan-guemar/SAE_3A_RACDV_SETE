<script setup lang="ts">
import { calculCharge, dateDiff, getDateHours2Digits } from '../../scripts/utils';
import { VNodeRef, ref, onMounted, computed } from 'vue';
import { Tache as TacheType, Festival, Poste, TacheCreateData, Benevole, Creneau, ID, Plage, Indisponibilite } from '../../scripts/types';
import { Backend } from '../../scripts/Backend';
import Tache from './Tache.vue';
import { sortTachesByOverriding } from '../../scripts/tache';
import PlageHoraire from './PlageHoraire.vue';
import Indispo from './Indispo.vue';

type Props = {
	festID: number;
	title: string;
	isOrga: boolean;
	dateDebut: string;
	dateFin: string;
	userId: number;
};

const props = defineProps<Props>();


const festival = ref<Festival>({
	festID: props.festID,
	title: props.title,
	dateDebut: new Date(props.dateDebut),
	dateFin: new Date(props.dateFin),
	isOrgaOrResp: props.isOrga
});

const numberOfDays = dateDiff(festival.value.dateDebut, festival.value.dateFin).day + 1;
const days = Array.from({ length: numberOfDays }, (_, i) => new Date(festival.value.dateDebut.getFullYear(), festival.value.dateDebut.getMonth(), festival.value.dateDebut.getDate() + i));
const dayNames = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
const monthNames = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
const daysDiv = ref<HTMLDivElement>();

const indispos = ref<Indisponibilite[]>([]);
const plages = ref<Plage[]>([]);

const loading = ref(true);
const modeCreation = ref(false);
const creatingIndispo = ref(false);

const newIndispo = ref<Creneau>({
	debut: new Date(),
	fin: new Date()
});

const startCreatingIndispo = (e: MouseEvent, d: Date) => {
	if (!modeCreation.value) return;
	if (creatingIndispo.value || props.isOrga) return;

	const div = daysDiv.value;
	const divY = div?.getBoundingClientRect().top || 0;
	const divH = div?.getBoundingClientRect().height || 0;

	const mouseYWithOffset = e.clientY - divY;

	const nbOfQuarters = Math.floor((mouseYWithOffset / divH) * 96);
	const startDate = new Date(d);
	const endDate = new Date(startDate);
	startDate.setHours(Math.floor(nbOfQuarters / 4));
	if (startDate.getHours() >= 23) {
		startDate.setHours(22);
		startDate.setMinutes(30);
		endDate.setHours(23);
		endDate.setMinutes(30);
	} else {
		startDate.setMinutes((nbOfQuarters % 4) * 15);
		endDate.setHours(startDate.getHours() + 1);
		endDate.setMinutes(startDate.getMinutes());
	}
	console.log('aaa', startDate, endDate);
	console.log(startDate.toISOString(), endDate.toISOString());
	creatingIndispo.value = true;
	newIndispo.value = {
		debut: startDate,
		fin: endDate
	};
};

const startResizingStart = (e: MouseEvent) => {
	const div = daysDiv.value;
	const divY = div?.getBoundingClientRect().top || 0;
	const divH = div?.getBoundingClientRect().height || 0;

	const listener = (e: MouseEvent) => {
		const mousePos = e.clientY - divY;
		let nbOfQuarters = Math.floor((mousePos / divH) * 96);
		if (nbOfQuarters < 0) {
			nbOfQuarters = 0;
		} else if (nbOfQuarters > newIndispo.value.fin.getHours() * 4 + newIndispo.value.fin.getMinutes() / 15 - 4) {
			nbOfQuarters = newIndispo.value.fin.getHours() * 4 + newIndispo.value.fin.getMinutes() / 15 - 4;
		}
		const startDate = new Date(newIndispo.value.debut);
		startDate.setHours(Math.floor(nbOfQuarters / 4));
		startDate.setMinutes((nbOfQuarters % 4) * 15);
		newIndispo.value.debut = startDate;
	};

	document.addEventListener('mousemove', listener);
	document.addEventListener('mouseup', () => {
		document.removeEventListener('mousemove', listener);
	});
};

const startResizingEnd = (e: MouseEvent) => {
	const div = daysDiv.value;
	const divY = div?.getBoundingClientRect().top || 0;
	const divH = div?.getBoundingClientRect().height || 0;

	const listener = (e: MouseEvent) => {
		const mousePos = e.clientY - divY;
		let nbOfQuarters = Math.floor((mousePos / divH) * 96);
		if (nbOfQuarters >= 24 * 4) {
			const endDate = new Date(newIndispo.value.fin);
			endDate.setHours(23);
			endDate.setMinutes(59);
			newIndispo.value.fin = endDate;
		} else {
			if (nbOfQuarters < newIndispo.value.debut.getHours() * 4 + newIndispo.value.debut.getMinutes() / 15 + 4) {
				nbOfQuarters = newIndispo.value.debut.getHours() * 4 + newIndispo.value.debut.getMinutes() / 15 + 4;
			}
			const endDate = new Date(newIndispo.value.fin);
			endDate.setHours(Math.floor(nbOfQuarters / 4));
			endDate.setMinutes((nbOfQuarters % 4) * 15);
			newIndispo.value.fin = endDate;
		}
	};

	document.addEventListener('mousemove', listener);
	document.addEventListener('mouseup', () => {
		document.removeEventListener('mousemove', listener);
	});
};

const createIndispo = async () => {
	console.log('éééé', 1);
	if (!creatingIndispo.value) return;
	console.log('éééé', 2);

	await Backend.addUserIndispo(festival.value.festID, props.userId, newIndispo.value);
	indispos.value.push({ id: -1, ...newIndispo.value });
	getIndispos();
	creatingIndispo.value = false;
	modeCreation.value = false;
};

const cancelIndispo = () => {
	creatingIndispo.value = false;
};
const getIndispos = async () => {
	const res = await Backend.getUserIndispos(festival.value.festID, props.userId);
	if (res) {
		indispos.value = res;
	}
};

const handleDeleteIndispo = async (indispo: Indisponibilite) => {
	indispos.value = indispos.value.filter(i => i.id !== indispo.id);
	await Backend.deleteIndispo(indispo);
	getIndispos();
};

const getPlagesHoraires = async () => {
	const res = await Backend.getPlagesHoraires(festival.value.festID);
	if (res) {
		plages.value = res;
	}
};

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
};

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
};

onMounted(async () => {
	const promises = [];
	promises.push(getIndispos());
	promises.push(getPlagesHoraires());
	await Promise.all(promises);
	loading.value = false;
});
</script>

<template>
	<Teleport to="head">
		<meta name="viewport" content="width=device-width, initial-scale=1" />
	</Teleport>
	<div v-if="loading" id="loader"></div>

	<h2 v-if="!loading">{{ title }}</h2>

	<div v-if="!loading" id="planning">
		<div class="hours">
			<div class="hour" v-for="i in parseInt('11')">
				{{ (i * 2 < 10 ? '0' + i * 2 : i * 2) + 'h00' }}
			</div>
		</div>
		<div class="days" ref="daysDiv">
			<div class="day" v-for="day of days" ref="truc" @click="(e: MouseEvent) => startCreatingIndispo(e, day)">
				<div class="day">
					<div class="heading">
						{{ dayNames[day.getDay()].substring(0, 3) + ' ' + day.getDate() + ' ' + monthNames[day.getMonth()].substring(0, 4) + '.' }}
					</div>
					<div class="line-break" v-for="i in parseInt('11')" :id="`line-break-${i * 2}`"></div>
					<div
						v-if="creatingIndispo && day.getDay() === newIndispo.debut.getDay()"
						class="indispo"
						:style="{
							top: ((newIndispo.debut.getHours() * 4 + newIndispo.debut.getMinutes() / 15) / (4 * 24)) * 100 + '%',
							height: (((newIndispo.fin.getHours() - newIndispo.debut.getHours()) * 4 + (newIndispo.fin.getMinutes() - newIndispo.debut.getMinutes()) / 15) / (4 * 24)) * 100 + '%',
							width: `99.9%`,
							left: `0%`,
							transform: `translateX(0%)`,
							zIndex: 1000
						}"
						style="overflow: visible; z-index: 100">
						<div @mousedown="startResizingStart" id="change-date-btn-up" class="change-date-btn"></div>
						<div @mousedown="startResizingEnd" id="change-date-btn-down" class="change-date-btn"></div>
						{{ `${getDateHours2Digits(newIndispo.debut)} - ${getDateHours2Digits(newIndispo.fin)}` }}
					</div>
					<PlageHoraire v-for="p of plages.filter(c => new Date(c.debut).getDate() === day.getDate())" :plage="p" :can-delete="false" />
					<Indispo v-for="i of indispos.filter(c => new Date(c.debut).getDate() === day.getDate())" :indispo="i" @delete="(_i) => handleDeleteIndispo(_i)" />
					<!-- <Tache /> -->
				</div>
			</div>
		</div>
		<div class="manage-interface">
			<div class="legend">
				<div class="legend-item">
					<div
						class="legend-color"
						:style="{
							backgroundColor: 'rgb(216, 230, 243)'
						}"></div>
					<div class="legend-label">Plage horaires du festival</div>
				</div>
				<div class="legend-item">
					<div
						class="legend-color"
						:style="{
							backgroundColor: '#ff4454'
						}"></div>
					<div class="legend-label">Indisponibilités</div>
				</div>
			</div>
			<div class="tooltip"  v-if="creatingIndispo">
				<span class="tooltipText">Cliquer pour ajouter l'indisponibilité</span>
				<div @click="createIndispo()" class="btn">Ajouter</div>
			</div>
			<div class="tooltip" v-if="creatingIndispo">
				<span class="tooltipText">Cliquer annuler </span>
				<div @click="cancelIndispo()" class="btn">Annuler</div>
			</div>

				
			<div v-if="modeCreation  && !creatingIndispo">Cliquer pour ajouter une indisponibilité</div>
			<div class="tooltip" v-if="modeCreation  && !creatingIndispo">
				<span class="tooltipText">Cliquer pour annuler </span>
				<div @click="modeCreation=false" class="btn">Annuler</div>
			</div>
			

			<div class="tooltip" v-if="!modeCreation">
				<span class="tooltipText">Cliquer commencer l'ajout </span>
				<div @click="modeCreation = true" class="btn">Ajouter</div>
			</div>
		</div>
	</div>

	<div v-if="!loading" class="scroll-btn">
		<div id="scroll-btn-left" @click="scrollDaysLeft">
			<img src="../../../public/icons/fleche-gauche.png" alt="Gauche" />
		</div>
		<div id="scroll-btn-right" @click="scrollDaysRight">
			<img src="../../../public/icons/fleche-gauche.png" alt="Droite" />
		</div>
	</div>
</template>
