<script>
    import { onMount } from 'svelte';
    import { fade, fly } from 'svelte/transition';
    import { Backend } from '../../scripts/Backend.ts'
    import { posteOpened, currentPoste, posteList, askingForDelete } from '../stores/posteStores.js'
    import PosteForm from './PosteForm.svelte';

    export let festivalId;
    export let festivalName;

    const openPoste = (poste) => {
        if ($askingForDelete) return;

        currentPoste.update((value) => {
            value.id = poste.id;
            value.name = poste.nom;
            value.color = poste.couleur;
            value.desc = poste.description;
            return value;
        });
        
        posteOpened.set(true);
    }

    const newPoste = () => {
        if ($askingForDelete) return;
        currentPoste.set({
            id: null,
            name: '',
            color: '#347deb',
            desc: '',
        });
        posteOpened.set(true);
    }

    onMount(async () => {
        const listeOfPostes = await Backend.getPostes(parseInt(festivalId));
        posteList.set(listeOfPostes);
        console.log(listeOfPostes);
    });
</script>

<h2 class="poste-section" class:blurred={$askingForDelete}>{ festivalName }</h2>

<div class="poste-list-wrapper">
    <div class="poste-list" class:blurred={$askingForDelete} transition:fly>
        <h3>Liste des postes</h3>
        <div class="postes">
            {#each $posteList as poste}
                <div 
                    style:border-color={poste.couleur}
                    style:background-color={poste.couleur + '1A'}
                    class="poste pointer" transition:fade 
                    on:click={() => openPoste(poste)}
                >{poste.nom}</div>
            {/each}
        </div>

        {#if !$posteOpened}
            <div class="new-poste-btn pointer" transition:fade on:click={newPoste} style:font-weight="500">Ajouter un poste</div>
        {/if}
    </div>

    {#if $posteOpened}
        <PosteForm {festivalId} />
    {/if}
</div>

<style>
    * {
        transition: filter 0.2s;
    }
</style>