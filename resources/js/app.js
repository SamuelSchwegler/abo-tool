import {createApp} from "vue";
import Contact from "./components/Contact";
import PostcodeManagement from "./components/PostcodeManagement";
import DeliveryServiceEdit from "./components/DeliveryServiceEdit";
import CustomerOrders from "./components/customer/CustomerOrders";
import Notifications from '@kyvg/vue3-notification'

const app = createApp({
    components: {
        Contact, PostcodeManagement, DeliveryServiceEdit, CustomerOrders
    }
});
app.use(Notifications)
app.mount("#app");

require('./bootstrap');
