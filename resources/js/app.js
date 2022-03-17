import {createApp} from "vue";
import Contact from "./components/Contact";
import PostcodeManagement from "./components/PostcodeManagement";
import DeliveryServiceEdit from "./components/DeliveryServiceEdit";
import ManageOrders from "./components/customer/ManageOrders";
import Notifications from '@kyvg/vue3-notification'

const app = createApp({
    components: {
        Contact, PostcodeManagement, DeliveryServiceEdit, ManageOrders
    }
});
app.use(Notifications)
app.mount("#app");

require('./bootstrap');
