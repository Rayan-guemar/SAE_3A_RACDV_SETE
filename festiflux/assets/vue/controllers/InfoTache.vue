<script setup lang="ts" >
import { Tache } from '../../scripts/types';


interface Props {
   tache : Tache
   show : boolean
}

defineProps<Props>();

</script>


<template>
    <div class="tache-info_wrapper" :class="{'hidden-task' : !show}" >
        <h1>InfoTache</h1>
        <p>{{ tache.description }}</p>
    </div>
</template>

