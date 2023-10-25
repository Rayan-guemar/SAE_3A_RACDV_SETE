class Creneaux {
    constructor(dateDebut, dateFin, poste) {
        this.dateDebut = dateDebut;
        this.dateFin = dateFin;
        this.poste = poste;
    }

    html() {
        let creneaux = document.createElement('div');
        creneaux.classList.add('task');

        let stringStartHours = this.dateDebut.getHours().toLocaleString('en-US', {minimumIntegerDigits: 2, useGrouping: false});
        let stringStartMinutes = this.dateDebut.getMinutes().toLocaleString('en-US', {minimumIntegerDigits: 2, useGrouping: false});

        let stringEndHours = this.dateFin.getHours().toLocaleString('en-US', {minimumIntegerDigits: 2, useGrouping: false});
        let stringEndMinutes = this.dateFin.getMinutes().toLocaleString('en-US', {minimumIntegerDigits: 2, useGrouping: false});

        creneaux.innerHTML = `
            <div class="task">
                <div class="name">${ this.poste.nom }</div>
                <div class="creneau">${ stringStartHours }:${ stringStartMinutes } - ${ stringEndHours }:${ stringEndMinutes }</div>
            </div>
        `;

        return creneaux;
    }
}