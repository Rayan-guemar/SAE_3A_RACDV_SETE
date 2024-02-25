<script setup lang="ts">
import { dateDiff } from '../../scripts/utils';
import { ref, onMounted } from 'vue';
import { Festival, Creneau } from '../../scripts/types';
import { Backend } from '../../scripts/Backend';
import PlageHoraire from './PlageHoraire.vue';


type Props = {
	festID: number;
	title: string;
	dateDebut: string;
	dateFin: string;
	isOrgaOrResp: boolean;
	userId: number;
};

interface Plage extends Creneau {}




const props = defineProps<Props>();

const festival = ref<Festival>({
    festID: props.festID,
	title: props.title,
	dateDebut: new Date(props.dateDebut),
	dateFin: new Date(props.dateFin),
	isOrgaOrResp: props.isOrgaOrResp
});

const numberOfDays = dateDiff(festival.value.dateDebut, festival.value.dateFin).day + 1;
const days = Array.from({ length: numberOfDays }, (_, i) => new Date(festival.value.dateDebut.getFullYear(), festival.value.dateDebut.getMonth(), festival.value.dateDebut.getDate() + i));
const dayNames = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
const monthNames = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
const daysDiv = ref<HTMLDivElement>();


const plages = ref<Plage[]>([
    {
        id: 1,
        // 28 oct 2023 8h
        debut: new Date(Date.parse('2023-10-28T08:00:00')),
        // 28 oct 2023 10h
        fin: new Date(Date.parse('2023-10-28T10:00:00')), 
    },
]);

const loading = ref(true);


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

const getPlagesHoraires = async () => {
  const res = await Backend.getPlagesHoraires(festival.value.festID);
  if (res) {
    plages.value = res;
  }
};


onMounted(async () => {
	// await getPlagesHoraires();
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
			<div class="day" v-for="day of days" ref="truc">
				<div class="day">
					<div class="heading">
						{{ dayNames[day.getDay()].substring(0, 3) + ' ' + day.getDate() + ' ' + monthNames[day.getMonth()].substring(0, 4) + '.' }}
					</div>
					<div class="line-break" v-for="i in parseInt('11')" :id="`line-break-${i * 2}`"></div>
					<PlageHoraire v-for="creneauWithPos of plages.filter(c => new Date(c.debut).getDate() === day.getDate())" :creneau="creneauWithPos" />
				</div>
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
