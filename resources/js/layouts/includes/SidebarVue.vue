<template>
  <aside class="main-sidebar sidebar-dark-primary elevation-4 z-50">
    <!-- Brand Logo -->
    <router-link :to="{ name: 'Dashboard' }" class="brand-link">
      <img
        :src="adminLogo"
        alt="AdminLTE Logo"
        class="brand-image img-circle elevation-3"
        style="opacity: 0.8"
      />
      <span class="brand-text font-weight-light">NPCB</span>
    </router-link>

    <div class="sidebar">
      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" role="menu">
          <!-- Dashboard -->
          <li class="nav-item" v-if="authStore.hasPermission('view-dashboard')">
            <router-link
              :to="{ name: 'Dashboard' }"
              class="nav-link"
              :class="{ active: $route.name === 'Dashboard' }"
            >
              <i class="fas fa-tachometer-alt nav-icon"></i>
              <p>Dashboard</p>
            </router-link>
          </li>





          <!-- Manage Users -->
          <li
            class="nav-item"
            v-if="authStore.hasPermission('view-users')"
            :class="{ 'menu-open': isOpen('users') }"
          >
            <a href="#" class="nav-link" @click.prevent="toggle('users')">
              <i class="nav-icon fas fa-user-cog"></i>
              <p>Manage Users <i class="fas fa-angle-left right"></i></p>
            </a>
            <ul class="nav nav-treeview" v-show="isOpen('users')">
              <li class="nav-item">
                <router-link
                  :to="{ name: 'RolePermission' }"
                  class="nav-link"
                  :class="{ active: $route.name === 'RolePermission' }"
                >
                  <i class="far fa-circle nav-icon"></i>
                  <p>User List</p>
                </router-link>
              </li>
              <li class="nav-item" v-if="authStore.hasPermission('update-user-role')">
                <router-link
                  :to="{ name: 'RolePermissionManager' }"
                  class="nav-link"
                  :class="{ active: $route.name === 'RolePermissionManager' }"
                >
                  <i class="far fa-circle nav-icon"></i>
                  <p>Role & Permission</p>
                </router-link>
              </li>
            </ul>
          </li>

          <!-- Frontend -->
          <li
            class="nav-item"
            v-if="authStore.hasPermission('manage-frontend')"
            :class="{ 'menu-open': isOpen('frontend') }"
          >
            <a href="#" class="nav-link" @click.prevent="toggle('frontend')">
              <i class="nav-icon fas fa-desktop"></i>
              <p>
                Frontend
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview" v-show="isOpen('frontend')">
              <!-- Slider -->
              <li class="nav-item" v-if="authStore.hasPermission('manage-frontend')">
                <router-link
                  :to="{ name: 'Sliders' }"
                  class="nav-link"
                  :class="{ active: $route.name === 'Sliders' }"
                >
                  <i class="far fa-circle nav-icon"></i>
                  <p>Slider</p>
                </router-link>
              </li>
              <li class="nav-item" v-if="authStore.hasPermission('manage-frontend')">
                <router-link
                  :to="{ name: 'CreateSlider' }"
                  class="nav-link"
                  :class="{ active: $route.name === 'CreateSlider' }"
                >
                  <i class="far fa-circle nav-icon"></i>
                  <p>Create Slider</p>
                </router-link>
              </li>

              <!-- Sections -->
              <li class="nav-item" v-if="authStore.hasPermission('manage-frontend')">
                <router-link
                  :to="{ name: 'Section' }"
                  class="nav-link"
                  :class="{ active: $route.name === 'Section' }"
                >
                  <i class="far fa-circle nav-icon"></i>
                  <p>Sections</p>
                </router-link>
              </li>
            </ul>
          </li>

          <!-- Settings -->
          <li
            class="nav-item"
            v-if="authStore.hasPermission('view-settings')"
            :class="{ 'menu-open': isOpen('settings') }"
          >
            <a href="#" class="nav-link" @click.prevent="toggle('settings')">
              <i class="nav-icon fas fa-cog"></i>
              <p>Settings <i class="fas fa-angle-left right"></i></p>
            </a>
            <ul class="nav nav-treeview" v-show="isOpen('settings')">
              <li class="nav-item" v-if="authStore.hasPermission('update-settings')">
                <router-link
                  :to="{ name: 'GeneralSettings' }"
                  class="nav-link"
                  :class="{ active: $route.name === 'GeneralSettings' }"
                >
                  <i class="far fa-circle nav-icon"></i>
                  <p>General Settings</p>
                </router-link>
              </li>
              <li class="nav-item" v-if="authStore.hasPermission('update-settings')">
                <router-link
                  :to="{ name: 'MenuManager' }"
                  class="nav-link"
                  :class="{ active: $route.name === 'MenuManager' }"
                >
                  <i class="far fa-circle nav-icon"></i>
                  <p>Menu Settings</p>
                </router-link>
              </li>
            </ul>
          </li>
        </ul>
      </nav>
    </div>
  </aside>
</template>

<script setup>
import { ref, watch, onMounted } from "vue";
import { useAuthStore } from "../../store/auth";
import { useRouter, useRoute } from "vue-router";
import axios from "axios";
import adminLogo from "@/assets/dist/img/store_logo.png";

const authStore = useAuthStore();
const router = useRouter();
const route = useRoute();

// Track which sidebar sub-menus are currently open (allows multiple)
const openMenus = ref({});

/**
 * Toggle the open/closed state of a specific menu
 */
const toggle = (menu) => {
  openMenus.value[menu] = !openMenus.value[menu];
};

/**
 * Check if a specific menu is currently open
 */
const isOpen = (menu) => {
  return !!openMenus.value[menu];
};

/**
 * Automatically open parent menus based on the current route name
 */
const openParentMenus = () => {
  const map = {
    // Sliders
    Sliders: ["frontend", "sliders"],
    CreateSlider: ["frontend", "sliders"],
    ShowSlider: ["frontend", "sliders"],
    UpdateSlider: ["frontend", "sliders"],
    Section: ["frontend", "sections"],
    CreateSection: ["frontend", "sections"],
    ShowSection: ["frontend", "sections"],
    UpdateSection: ["frontend", "sections"],

    // Users / Roles
    RolePermission: ["users"],
    RolePermissionManager: ["users"],
    AdminUserUpdate: ["users"],

    // Settings
    GeneralSettings: ["settings"],
    MenuManager: ["settings"],
    ShowMenu: ["settings"],


  };

  const current = route.name;
  if (map[current]) {
    map[current].forEach((menu) => {
      openMenus.value[menu] = true;
    });
  }
};

// Open correct menus on initial load and when route changes
onMounted(openParentMenus);
watch(() => route.name, openParentMenus);

/**
 * Handle admin logout: clear auth data and redirect to login
 */
const logout = async () => {
  await axios.post("/api/admin/logout");
  localStorage.clear();
  delete axios.defaults.headers.common["Authorization"];
  router.push({ name: "AdminLogin" });
};
</script>
<style scoped>
.main-sidebar {
  z-index: 100 !important;
}
</style>
