import Headroom from "headroom.js";

export function initializeHeadroom() {
    // Headroom.js
    const header = document.querySelector(".js-header");
    if (header) {
        const headroom = new Headroom(header , {
            tolerance: 10,
            offset : 50,
        });
        headroom.init();
    }
}