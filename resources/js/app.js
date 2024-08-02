import Clipboard from '@ryangjchandler/alpine-clipboard';
import panzoom from 'panzoom';

window.panzoom = panzoom;

import './components/choices.js';
import './components/coloris.js';
import './components/htmlPreview.js';
import './components/charts';
import './components/modals.js';

Alpine.plugin(Clipboard);
