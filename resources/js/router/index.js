import AdminLayout from "@/layouts/AdminLayout.vue";
import AdminUserUpdate from "@/pages/admin/users/AdminUserUpdate.vue";
import AdminLogin from "@/pages/admin/auth/Login.vue";
import AdminDashboard from "@/pages/admin/auth/Profile.vue";
import Dashboard from "@/pages/admin/Dashboard.vue";
import RolePermission from "@/pages/admin/users/ManageUser.vue";

import RolePermissionManager from "@/pages/admin/users/RolePermissionManager.vue";
import GeneralSettings from "@/pages/admin/Settings/GeneralSettings.vue";

import NotFound from "@/pages/NotFound.vue";
import Unauthorized from "@/pages/Unauthorized.vue";
import {
    useAuthStore
} from "@/store/auth";
import {
    createRouter,
    createWebHistory
} from "vue-router";
import MenuManager from "../pages/admin/menu/MenuManager.vue";
import MenusIndex from "../pages/admin/menu/MenusIndex.vue";

import UpdateSection from "../pages/admin/Section/UpdateSection.vue";
import Section from "../pages/admin/Section/Section.vue";
import Sliders from "../pages/admin/Sliders/Sliders.vue";
import CreateSlider from "../pages/admin/Sliders/CreateSlider.vue";
import UpdateSlider from "../pages/admin/Sliders/UpdateSlider.vue";


const routes = [{
        path: "/admin",
        children: [{
                path: "login",
                name: "AdminLogin",
                component: AdminLogin
            },
            {
                path: "",
                prefix: "admin",
                component: AdminLayout,
                meta: {
                    requiresAuth: true,
                    role: "admin"
                },

                children: [

                    {
                        path: "",
                        name: "Dashboard",
                        component: Dashboard,
                        meta: {
                            permission: "view-dashboard"
                        },
                    },

                    {
                        path: "admin-profile",
                        name: "adminProfile",
                        component: AdminDashboard,
                        meta: {
                            permission: "view-dashboard"
                        },
                    },

                    // Role & Permission Management
                    {
                        path: "manage-admins-roles",
                        name: "RolePermission",
                        component: RolePermission,
                        meta: {
                            permission: "view-users"
                        }, // viewing admins/roles
                    },
                    {
                        path: "admins/:id/edit",
                        name: "AdminUserUpdate",
                        component: AdminUserUpdate,
                        meta: {
                            permission: "update-users"
                        },
                    },
                    {
                        path: "manage-role-permissions",
                        name: "RolePermissionManager",
                        component: RolePermissionManager,
                        meta: {
                            permission: "update-user-role"
                        }, // updating role permissions
                    },



                    // Settings
                    {
                        path: "generale-settings",
                        name: "GeneralSettings",
                        component: GeneralSettings,
                        meta: {
                            permission: "update-settings"
                        },
                    },


                    // Menu manager
                    {
                        path: "menus",
                        name: "MenuManager",
                        component: MenusIndex,
                        meta: {
                            permission: "manage-menus",
                        },
                    },
                    {
                        path: "menu/view/:id",
                        name: "ShowMenu",
                        component: MenuManager,
                        meta: {
                            permission: "manage-menus",
                        },
                        props: true,
                    },

                    {
                        path: "manage-sections",
                        name: "Section",
                        component: Section,
                        meta: {
                            permission: "manage-frontend"
                        },
                    },
                    {
                        path: "edit-sections/:id",
                        name: "UpdateSection",
                        component: UpdateSection,
                        props: true,
                        meta: {
                            permission: "manage-frontend"
                        },
                    },
                    {
                        path: "banner-sliders",
                        name: "Sliders",
                        component: Sliders,
                        props: true,
                        meta: {
                            permission: "manage-frontend"
                        },
                    },
                    {
                        path: "edit-sliders/:id",
                        name: "UpdateSlider",
                        component: UpdateSlider,
                        props: true,
                        meta: {
                            permission: "manage-frontend"
                        },
                    },
                    {
                        path: "create-sliders",
                        name: "CreateSlider",
                        component: CreateSlider,
                        meta: {
                            permission: "manage-frontend"
                        },
                    },

                ],
            },
        ],
    },
    {
        path: "/unauthorized",
        name: "Unauthorized",
        component: Unauthorized,
    },
    {
        path: "/:pathMatch(.*)*",
        name: "NotFound",
        component: NotFound,
    },
];
const router = createRouter({
    history: createWebHistory(),
    routes,
});
router.beforeEach(async (to, from, next) => {
    const authStore = useAuthStore();

    const token = localStorage.getItem("token");
    const role = localStorage.getItem("role");

    authStore.token = token;
    authStore.role = role;

    // 1. Require authentication
    if (to.meta.requiresAuth && !token) {
        // redirect to role‑specific login, or fallback
        const loginRoute = to.meta.role ? `/${to.meta.role}/login` : "/login";
        return next(loginRoute);
    }

    // 2. Role mismatch
    if (to.meta.role && role !== to.meta.role) {
        const loginRoute = `/${to.meta.role}/login`;
        return next(loginRoute);
    }

    // 3. Permission check
    const requiredPermission = to.meta.permission;
    if (requiredPermission) {
        try {
            await authStore.fetchRoleAndPermissions();
        } catch (err) {
            const loginRoute = role ? `/${role}/login` : "/login";
            return next(loginRoute);
        }
    }

    if (requiredPermission && !authStore.hasPermission(requiredPermission)) {
        return next({
            name: "Unauthorized"
        });
    }
    next();
});
export default router;
