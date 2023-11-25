<script>
  import { fade } from "svelte/transition";
    import { posteOpened, currentPoste } from "../stores/posteOpened.js";

    export let festivalId;

    const deletePoste = () => {
        console.log("deletePoste");
    }

    const editPoste = () => {
        console.log("editPoste");
    }

    const createPoste = () => {
        console.log("createPoste");
    }

    const closePoste = () => {
        posteOpened.set(false);
    }
</script>

<div class="poste-form-wrapper" transition:fade>
    <h3>{ $currentPoste.name }</h3>

    <input type="text" bind:value={$currentPoste.name} placeholder="ex: Accuei artiste">
    <input type="color" bind:value={$currentPoste.color} on:input={() => console.log($currentPoste.color)}>
    <textarea bind:value={$currentPoste.desc} placeholder="ex: Accueillir les artistes"></textarea>
    
    <div on:click={closePoste}>Lol</div>
</div>