
import { createApp } from "vue";
import Contact from "./components/Contact";
const app = createApp({
    components: {
        Contact
    }
});
app.mount("#app");

require('./bootstrap');
