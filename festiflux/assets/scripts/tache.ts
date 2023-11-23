export const sortTachesByOverriding = taches => {
    /**
     * @type {Tache[][]}
     */
    const overridingTaches = [];

    for (const t of taches) {
        const a = overridingTaches.find(ts => ts.some(_t => _t.overrides(t)));
        if (a) a.push(t);
        else overridingTaches.push([t]);
    }
    return overridingTaches;
};