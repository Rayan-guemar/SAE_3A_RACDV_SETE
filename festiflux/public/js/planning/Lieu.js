
export class Lieu {

    constructor(nom, adresse){
        this.nom = nom;
        this.adresse = adresse;
    }

    getNom() {
        return this.nom;
    }

    setNom(nom) {
        this.nom = nom;
    }

    getAdresse() {
        return this.adresse;
    }

    setAdresse(adresse) {
        this.adresse = adresse;
    }
    

}