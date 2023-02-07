import { usePage } from "@inertiajs/vue3";

export default {
    
    install: (app) => {
        app.mixin({
            mounted(){
                //console.log( usePage().props.auth);
                let authRoles = usePage().props.auth;
                let authPermissions;
                if(authRoles !== null){
                    authRoles = usePage().props.auth.roles;
                    authPermissions = usePage().props.auth.permissions;
                    this.$gates.setRoles(authRoles);
                    this.$gates.setPermissions(authPermissions);
                }
            }
        })
    }
}