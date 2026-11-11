import { SettingsApp } from './settings-app.js';

const container = document.getElementById('rdct-settings-root');
if (container && window.rdctSettings && window.wp?.element) {
  const { createElement, createRoot } = window.wp.element;
  createRoot(container).render(createElement(SettingsApp, { config: window.rdctSettings }));
}
