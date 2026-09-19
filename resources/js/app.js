import Alpine from 'alpinejs';

import blockViewer from './alpine/block-viewer';
import { accordion, filterable, tabs } from './alpine/collections';
import factoryMap from './alpine/factory-map';
import mobileNav from './alpine/mobile-nav';
import productFinder from './alpine/product-finder';
import siteHeader from './alpine/site-header';
import { initCountUp } from './modules/count-up';
import { initParallax } from './modules/parallax';
import { initProcessScroll } from './modules/process-scroll';
import { initReveal } from './modules/reveal';
import { initSmoothScroll } from './modules/smooth-scroll';

Alpine.data('siteHeader', siteHeader);
Alpine.data('mobileNav', mobileNav);
Alpine.data('productFinder', productFinder);
Alpine.data('blockViewer', blockViewer);
Alpine.data('factoryMap', factoryMap);
Alpine.data('filterable', filterable);
Alpine.data('accordion', accordion);
Alpine.data('tabs', tabs);

window.Alpine = Alpine;
Alpine.start();

const boot = () => {
    initSmoothScroll();
    initReveal();
    initCountUp();
    initProcessScroll();
    initParallax();
};

document.readyState === 'loading'
    ? document.addEventListener('DOMContentLoaded', boot)
    : boot();
