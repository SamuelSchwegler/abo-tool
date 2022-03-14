import {createApp} from "vue";
import Contact from "./components/Contact";
import PostcodeManagement from "./components/PostcodeManagement";
import DeliveryServiceEdit from "./components/DeliveryServiceEdit";
import ManageOrders from "./components/customer/ManageOrders";

const app = createApp({
    components: {
        Contact, PostcodeManagement, DeliveryServiceEdit, ManageOrders
    }
});
app.mount("#app");

require('./bootstrap');
