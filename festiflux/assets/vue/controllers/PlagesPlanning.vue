<script setup lang="ts">
import { calculCharge, dateDiff, getDateHours2Digits } from '../../scripts/utils';
import { VNodeRef, ref, onMounted, computed } from 'vue';
import { Tache as TacheType, Festival, Poste, TacheCreateData, Benevole, Creneau, ID, Plage } from '../../scripts/types';
import { Backend } from '../../scripts/Backend';
import PlageHoraire from './PlageHoraire.vue';

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

const plages = ref<Plage[]>([]);

const loading = ref(true);
const modeCreation = ref(false);
const creatingPlage = ref(false);


const newPlage = ref<Creneau>({
	debut: new Date(),
	fin: new Date()
});

const startCreatingPlage = (e: MouseEvent, d: Date) => {
	if (!modeCreation.value) return;
	if (creatingPlage.value || props.isOrga) return;

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
	creatingPlage.value = true;
	newPlage.value = {
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
		} else if (nbOfQuarters > newPlage.value.fin.getHours() * 4 + newPlage.value.fin.getMinutes() / 15 - 4) {
			nbOfQuarters = newPlage.value.fin.getHours() * 4 + newPlage.value.fin.getMinutes() / 15 - 4;
		}
		const startDate = new Date(newPlage.value.debut);
		startDate.setHours(Math.floor(nbOfQuarters / 4));
		startDate.setMinutes((nbOfQuarters % 4) * 15);
		newPlage.value.debut = startDate;
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
			const endDate = new Date(newPlage.value.fin);
			endDate.setHours(23);
			endDate.setMinutes(59);
			newPlage.value.fin = endDate;
		} else {
			if (nbOfQuarters < newPlage.value.debut.getHours() * 4 + newPlage.value.debut.getMinutes() / 15 + 4) {
				nbOfQuarters = newPlage.value.debut.getHours() * 4 + newPlage.value.debut.getMinutes() / 15 + 4;
			}
			const endDate = new Date(newPlage.value.fin);
			endDate.setHours(Math.floor(nbOfQuarters / 4));
			endDate.setMinutes((nbOfQuarters % 4) * 15);
			newPlage.value.fin = endDate;
		}
	};

	document.addEventListener('mousemove', listener);
	document.addEventListener('mouseup', () => {
		document.removeEventListener('mousemove', listener);
	});
};

const createPlage = async () => {
	if (!creatingPlage.value) return;

	await Backend.addPlageHoraire(festival.value.festID, newPlage.value);
	plages.value.push({ id: -1, ...newPlage.value });
	getPlagesHoraires();
	creatingPlage.value = false;
	modeCreation.value = false;
};
const handleDeletePlage = async (plage: Plage) => {
	plages.value = plages.value.filter(p => p.id !== plage.id);
	await Backend.deletePlageHoraire(plage);
	getPlagesHoraires();
}
const cancelPlage = () => {
	creatingPlage.value = false;
};


const getPlagesHoraires = async () => {
	const res = await Backend.getPlagesHoraires(festival.value.festID);
	console.log("aaAAAAAAAAAAAAAaaAAAAAAAAAAAAAaaAAAAAAAAAAAAAaaAAAAAAAAAAAAAaaAAAAAAAAAAAAA")
	console.log('old', plages.value)
	if (res) {
		plages.value = res;
	}
	console.log('new', plages.value)
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
	await getPlagesHoraires();

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
			<div class="day" v-for="day of days" ref="truc" @click="(e: MouseEvent) => startCreatingPlage(e, day)">
				<div class="day">
					<div class="heading">
						{{ dayNames[day.getDay()].substring(0, 3) + ' ' + day.getDate() + ' ' + monthNames[day.getMonth()].substring(0, 4) + '.' }}
					</div>
					<div class="line-break" v-for="i in parseInt('11')" :id="`line-break-${i * 2}`"></div>
					<div
						v-if="creatingPlage && day.getDay() === newPlage.debut.getDay()"
						class="plage"
						:style="{
							top: ((newPlage.debut.getHours() * 4 + newPlage.debut.getMinutes() / 15) / (4 * 24)) * 100 + '%',
							height: (((newPlage.fin.getHours() - newPlage.debut.getHours()) * 4 + (newPlage.fin.getMinutes() - newPlage.debut.getMinutes()) / 15) / (4 * 24)) * 100 + '%',
							width: `99%`,
							left: `0%`,
							transform: `translateX(0%)`,
							zIndex: 1000,
							backgroundColor: `rgb(216, 230, 243)`
						}"
						style="overflow: visible; z-index: 100">
						<div @mousedown="startResizingStart" id="change-date-btn-up" class="change-date-btn"></div>
						<div @mousedown="startResizingEnd" id="change-date-btn-down" class="change-date-btn"></div>
						{{ `${getDateHours2Digits(newPlage.debut)} - ${getDateHours2Digits(newPlage.fin)}` }}
					</div>
					<PlageHoraire v-for="p of plages.filter(c => new Date(c.debut).getDate() === day.getDate())" :plage="p" @delete="(_p) => handleDeletePlage(_p)" :can-delete="true"  />
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
			</div>
			
			<div class="tooltip"  v-if="creatingPlage">
				<span class="tooltipText">Cliquer pour ajouter la plage</span>
				<div @click="createPlage()" class="btn">Ajouter</div>
			</div>
			<div class="tooltip" v-if="creatingPlage">
				<span class="tooltipText">Cliquer annuler </span>
				<div @click="cancelPlage()" class="btn">Annuler</div>
			</div>

				
			<div v-if="modeCreation  && !creatingPlage">Cliquer pour ajouter une plage</div>
			<div class="tooltip" v-if="modeCreation  && !creatingPlage">
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
