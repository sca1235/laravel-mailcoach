import Coloris from '@melloware/coloris';

document.addEventListener('livewire:navigated', () => {
    Coloris.init();
});

Coloris.init();
window.Coloris = Coloris;
