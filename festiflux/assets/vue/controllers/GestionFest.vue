<script setup lang="ts">
import { defineProps } from 'vue';
import Planning from "./Planning.vue";
import ListesPostes from "./ListesPostes.vue";
import { Festival } from '../../scripts/types';


interface Props {
    festID: number,
    title: string,
    dateDebut: string,
    dateFin: string,
    isOrgaOrResp: boolean,
    userId: number,
}

const props = defineProps<Props>();

</script>

<template>
    <div class="page-container">
        <h1> Gestion du {{ title }} </h1>
        <!-- gestion plages horraires et créneaux  -->
        <section class="flex-column">
            
            <div class="planning-container flex-col">
                <div class="planning-wrapper">
                    <Planning :festID="festID" :title="title" :dateDebut="new Date(dateDebut).toString()" :dateFin="new Date(dateFin).toString()" :isOrgaOrResp="isOrgaOrResp" :userId="userId" />
                </div>
            </div>
        </section>
        <!-- gestion des postes  -->

        <section class="flex col">
            <h2> Gestion des postes </h2>
                <ListesPostes :festivalId="festID" :festivalName="''" :isOrgaOrResp="isOrgaOrResp" />
        </section>

    </div>
   

    
</template>
