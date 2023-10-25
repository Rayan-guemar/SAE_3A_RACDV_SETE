const festId = document.getElementById('id_festival').value;
const checkboxs = document.getElementsByClassName('switch');

[...checkboxs].forEach(checkbox => {
	const userId = checkbox.id;
	checkbox.addEventListener('change', async function () {
		const isChecked = checkbox.checked;
		const URL = Routing.generate(isChecked ? 'app_festival_add_responsable' : 'app_festival_remove_responsable', { festId: festId, userId: userId });
		try {
			await fetch(URL);
		} catch (error) {
			console.log(error);
			checkbox.checked = !isChecked;
		}
	});
});
