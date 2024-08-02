import {
    Chart,
    LineElement,
    BarElement,
    PointElement,
    BarController,
    LineController,
    CategoryScale,
    LinearScale,
    Tooltip,
    Legend,
} from 'chart.js';
import zoomPlugin from 'chartjs-plugin-zoom';

Chart.register(
    LineElement,
    BarElement,
    PointElement,
    BarController,
    LineController,
    CategoryScale,
    LinearScale,
    Tooltip,
    Legend
);
import { each } from 'chart.js/helpers';

Chart.register(zoomPlugin);

window.Chart = Chart;
window.Chart.helpers = {};
window.Chart.helpers.each = each;

import './emailListStatistics.js';
import './campaignStatistics.js';
import './dashboardChart.js';
