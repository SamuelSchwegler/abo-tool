
import { createApp } from "vue";
import Contact from "./components/Contact";
import PostcodeManagement from "./components/PostcodeManagement";
import DeliveryServiceEdit from "./components/DeliveryServiceEdit";
const app = createApp({
    components: {
        Contact, PostcodeManagement, DeliveryServiceEdit
    }
});
app.mount("#app");

require('./bootstrap');
