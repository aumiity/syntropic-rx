import './bootstrap';

document.addEventListener('keydown', function (event) {
	if (!['ArrowDown', 'ArrowUp', 'Enter'].includes(event.key)) return;

	const input = event.target;
	if (input.tagName !== 'INPUT') return;

	let dropdown = null;
	let el = input.parentElement;
	while (el && el !== document.body) {
		dropdown = el.querySelector(
			':scope > div[id$="_dropdown"], :scope > div[id$="-dropdown"], ' +
			':scope > div.product-search-results, :scope > div#supplier_dropdown, ' +
			':scope > div#generic-name-dropdown'
		);
		if (dropdown) break;
		el = el.parentElement;
	}

	if (!dropdown || dropdown.classList.contains('hidden')) return;

	const items = Array.from(dropdown.querySelectorAll('button'));
	if (!items.length) return;

	const current = dropdown.querySelector('button.bg-emerald-100');
	let index = current ? items.indexOf(current) : -1;

	if (event.key === 'ArrowDown') {
		event.preventDefault();
		if (current) current.classList.remove('bg-emerald-100');
		index = (index + 1) % items.length;
		items[index].classList.add('bg-emerald-100');
		items[index].scrollIntoView({ block: 'nearest' });
	} else if (event.key === 'ArrowUp') {
		event.preventDefault();
		if (current) current.classList.remove('bg-emerald-100');
		index = (index - 1 + items.length) % items.length;
		items[index].classList.add('bg-emerald-100');
		items[index].scrollIntoView({ block: 'nearest' });
	} else if (event.key === 'Enter') {
		event.preventDefault();
		if (current) {
			current.classList.remove('bg-emerald-100');
			current.click();
		}
	}
});
