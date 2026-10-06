// ACFE layout previews in wp-admin. Previews are injected after page load, so
// only Alpine is loaded (it picks up new DOM itself). Motion is left out: its
// in-view items start hidden and would stay that way. <iconify-icon> is already
// loaded on every admin screen by enqueue_iconify_for_admin().
import { initializeAlpine } from './modules/js/module-alpine';

initializeAlpine();
