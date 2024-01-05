<script setup lang="ts">

interface Props {
  festivalId: number;
  festivalName: string;
}

defineProps<Props>();

</script>

<template>
    <div>
        <h1>Gestion du {{ festivalName }}</h1>
    </div>

    
</template>
