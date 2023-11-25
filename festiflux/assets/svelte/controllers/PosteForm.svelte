<script>
    import { fade } from "svelte/transition";
    import { posteOpened, currentPoste, askingForDelete, posteList } from "../stores/posteStores.js";
    import { toCSS } from "../../scripts/utils.ts"
    import { Backend } from "../../scripts/Backend.ts";

    export let festivalId;

    $: editing = $currentPoste.id !== null;

    let creating = false;
    let deleting = false;
    let updating = false;

    const deletePoste = async () => {
        $askingForDelete = false;
        deleting = true;

        await Backend.deletePoste(festivalId, $currentPoste);

        posteOpened.set(false);

        const listeOfPostes = await Backend.getPostes(parseInt(festivalId));
        posteList.set(listeOfPostes);

        deleting = false;
    }

    const updatePoste = async () => {
        if ($askingForDelete) return;
        updating = true;

        currentPoste.update((value) => {
            value.name = value.name.trim();
            value.desc = value.desc.trim();
            return value;
        })

        await Backend.updatePoste(festivalId, {
            id: $currentPoste.id,
            nom: $currentPoste.name,
            couleur: $currentPoste.color,
            description: $currentPoste.desc
        });

        posteOpened.set(false);

        const listeOfPostes = await Backend.getPostes(parseInt(festivalId));
        posteList.set(listeOfPostes);

        updating = false;
    }

    const createPoste = async () => {
        if ($askingForDelete) return;

        creating = true;

        currentPoste.update((value) => {
            value.name = value.name.trim();
            value.desc = value.desc.trim();
            return value;
        })
        
        await Backend.addPoste(festivalId, {
            nom: $currentPoste.name,
            couleur: $currentPoste.color,
            description: $currentPoste.desc
        })

        posteOpened.set(false);

        const listeOfPostes = await Backend.getPostes(parseInt(festivalId));
        posteList.set(listeOfPostes);

        creating = false;
    }

    const closePoste = () => {
        if ($askingForDelete) return;
        posteOpened.set(false);
    }
</script>

<div class="poste-form-wrapper" class:blurred={$askingForDelete} transition:fade>
    <div class="poste-form">
        <h2 class="top-heading">Poste</h2>

        <input type="text" class="poste-name" class:missing-field={$currentPoste.name === ''} bind:value={$currentPoste.name} placeholder="ex: Accuei artiste">
        <div class="color-wrapper">
            <div>Couleur :</div>
            <input type="color" bind:value={$currentPoste.color}>
        </div>
        <textarea bind:value={$currentPoste.desc} placeholder="ex: Accueillir les artistes"></textarea>
        {#if editing}
            <div class="pointer edit-poste" class:loading={updating} on:click={updatePoste}>Modifier</div>
            <div class="pointer delete-poste" class:loading={deleting} on:click={() => $askingForDelete = true}>Supprimer</div>
        {:else}
            <div class="pointer create-poste" class:missing-field={$currentPoste.name === ''} class:loading={creating} on:click={createPoste}>Créer</div>
        {/if}
        <div class="pointer cancel-poste" on:click={closePoste}>Annuler</div>
    </div>

    <div class="task-preview-wrapper">
        <h4 class="top-heading">Aperçu d'une tache</h4>
        <div class="task-preview" style={toCSS({
            backgroundColor: $currentPoste.color + "1A",
            borderColor: $currentPoste.color,
            color: $currentPoste.color
        })}>
            <div class="name">{ $currentPoste.name !== '' ? $currentPoste.name : 'Nom poste' }</div>
            <div class="tache">
                22h00 - 23h00
            </div>
        </div>
    </div>
</div>

{#if $askingForDelete}
    <div class="delete-poste-confirm">
        <div class="delete-poste-confirm-text">Voulez-vous vraiment supprimer ce poste ?</div>
        <div class="delete-poste-confirm-text">Cela entrainera la suppression de tous les créneaux associés à ce poste</div>
        <div class="delete-poste-confirm-btns">
            <div class="pointer delete-poste-confirm-yes" on:click={deletePoste}>Oui</div>
            <div class="pointer delete-poste-confirm-no" on:click={() => $askingForDelete = false}>Non</div>
        </div>
    </div>
{/if}